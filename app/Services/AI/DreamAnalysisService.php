<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Models\DreamAnalysis;
use App\Models\DreamCulturalInterpretation;
use App\Models\DreamEmotion;
use App\Models\DreamEntry;
use App\Models\DreamEvent;
use App\Models\DreamSymbol;
use App\Models\DreamSymbolOccurrence;
use App\Models\User;
use App\Services\Profile\ProfileContextBuilder;
use App\Services\Profile\ProfileObservationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class DreamAnalysisService
{
    public function __construct(
        private readonly AIManager $ai,
        private readonly PromptManager $prompts,
        private readonly ResponseValidator $validator,
        private readonly ProfileContextBuilder $profileContext,
        private readonly ProfileObservationService $observations,
    ) {}

    public function analyze(DreamEntry $dream): DreamAnalysis
    {
        $dream->loadMissing(['user.profile', 'user.preferences']);

        $analysis = DreamAnalysis::query()->create([
            'dream_entry_id' => $dream->id,
            'user_id' => $dream->user_id,
            'prompt_version_id' => $this->prompts->activeDreamAnalysisPrompt()->id,
            'status' => 'processing',
            'provider' => AiSettings::provider(),
        ]);

        $dream->update(['processing_status' => 'analyzing']);

        try {
            $context = $this->buildContext($dream);
            $prompt = $this->prompts->activeDreamAnalysisPrompt();

            $result = $this->ai->driver()->chat([
                ['role' => 'system', 'content' => $prompt->system_prompt],
                ['role' => 'user', 'content' => $this->prompts->buildUserPrompt($context)],
            ], [
                'model' => AiSettings::analysisModel(),
            ]);

            $payload = $this->validator->validateDreamAnalysis($result['content']);

            DB::transaction(function () use ($analysis, $dream, $payload, $result): void {
                $analysis->update([
                    'status' => 'completed',
                    'model' => $result['model'],
                    'payload' => $payload,
                    'summary' => (string) ($payload['dream_summary'] ?? ''),
                    'analysis_confidence' => (float) ($payload['analysis_confidence'] ?? 0),
                    'completed_at' => now(),
                    'error_message' => null,
                ]);

                $this->persistStructuredResults($dream, $analysis, $payload);
                $this->storeProfileHypotheses($dream->user, $payload, $analysis->id);

                $dream->update([
                    'status' => 'analyzed',
                    'processing_status' => 'completed',
                ]);

                AiUsageLog::query()->create([
                    'user_id' => $dream->user_id,
                    'provider' => $analysis->provider,
                    'model' => $result['model'],
                    'operation' => 'dream_analysis',
                    'prompt_tokens' => $result['usage']['prompt_tokens'],
                    'completion_tokens' => $result['usage']['completion_tokens'],
                    'total_tokens' => $result['usage']['total_tokens'],
                    'status' => 'success',
                    'meta' => ['dream_entry_id' => $dream->id, 'analysis_id' => $analysis->id],
                ]);
            });
        } catch (Throwable $e) {
            $analysis->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $dream->update(['processing_status' => 'failed']);

            AiUsageLog::query()->create([
                'user_id' => $dream->user_id,
                'provider' => AiSettings::provider(),
                'model' => AiSettings::analysisModel(),
                'operation' => 'dream_analysis',
                'status' => 'failed',
                'meta' => ['error' => $e->getMessage(), 'dream_entry_id' => $dream->id],
            ]);

            report($e);

            return $analysis->fresh();
        }

        return $analysis->fresh();
    }

    /**
     * @return array<string, string>
     */
    private function buildContext(DreamEntry $dream): array
    {
        /** @var User $user */
        $user = $dream->user;
        $profile = $user->profile;
        $locale = $user->preferences?->locale ?: config('app.locale', 'ar');

        $previous = $user->dreams()
            ->where('id', '!=', $dream->id)
            ->where(function ($q): void {
                $q->whereNotNull('corrected_text')->orWhereNotNull('original_text');
            })
            ->latest('dream_date')
            ->limit(5)
            ->get()
            ->map(fn (DreamEntry $d) => trim(($d->dream_date?->toDateString() ?? '').': '.Str::limit($d->displayText(), 280)))
            ->filter()
            ->implode("\n");

        $legacyProfile = collect([
            'preferred_name' => $profile?->preferred_name,
            'cultural_background' => $profile?->cultural_background,
            'emotional_state' => $profile?->emotional_state,
            'life_circumstances' => $profile?->life_circumstances,
        ])->filter();

        $hipContext = $this->profileContext->forDream(
            $user,
            $dream,
            emotions: array_filter([(string) $dream->mood_upon_waking])
        );

        $userContext = [
            'legacy_profile' => $legacyProfile,
            'relevant_inner_profile' => $hipContext['items'],
            'excluded_reasons' => $hipContext['excluded_reasons'],
            'context_rules' => [
                'prioritize_user_associations' => true,
                'never_treat_hypothesis_as_fact' => true,
                'do_not_diagnose' => true,
            ],
        ];

        $goals = $user->profileGoals()
            ->where('status', 'active')
            ->pluck('title')
            ->implode(' | ');

        $snapshotSummary = $user->profileSnapshots()->latest('version_number')->value('profile_summary');

        $framework = collect($hipContext['items'])
            ->first(fn ($item) => ($item['question_key'] ?? null) === 'G10');

        $concerns = collect($hipContext['items'])
            ->first(fn ($item) => in_array($item['question_key'] ?? '', ['B04', 'H03'], true));

        return [
            'dream_text' => $dream->displayText(),
            'dream_date' => $dream->dream_date?->toDateString() ?? '',
            'mood' => (string) $dream->mood_upon_waking,
            'sleep_quality' => (string) $dream->sleep_quality,
            'emotional_intensity' => (string) $dream->emotional_intensity,
            'personal_notes' => (string) $dream->personal_notes,
            'preferred_language' => $locale,
            'user_context' => json_encode($userContext, JSON_UNESCAPED_UNICODE),
            'previous_dreams' => $previous ?: 'none',
            'preferred_name' => (string) ($profile?->preferred_name ?: $user->name),
            'profile_summary' => (string) ($snapshotSummary ?: ''),
            'profile_goals' => $goals ?: 'none',
            'interpretive_framework' => is_array($framework) ? (string) json_encode($framework['value'] ?? $framework, JSON_UNESCAPED_UNICODE) : '',
            'emotional_state' => (string) ($profile?->emotional_state ?? ''),
            'current_concerns' => is_array($concerns) ? (string) json_encode($concerns['value'] ?? $concerns, JSON_UNESCAPED_UNICODE) : (string) ($profile?->life_circumstances ?? ''),
            'profile_completeness' => (string) ($profile?->completion_percent ?? 0),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function storeProfileHypotheses(User $user, array $payload, int $analysisId): void
    {
        foreach ($payload['personal_context_connections'] ?? [] as $connection) {
            if (! is_array($connection)) {
                continue;
            }

            $text = (string) ($connection['possible_connection'] ?? $connection['description'] ?? '');
            if ($text === '') {
                continue;
            }

            $this->observations->createHypothesis(
                $user,
                $text,
                (string) ($connection['profile_dimension'] ?? 'dream_correlation'),
                [
                    'dream_analysis_id' => $analysisId,
                    'dream_element' => $connection['dream_element'] ?? null,
                    'evidence_source' => $connection['evidence_source'] ?? null,
                ],
                (string) ($connection['confidence'] ?? 'low')
            );
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function persistStructuredResults(DreamEntry $dream, DreamAnalysis $analysis, array $payload): void
    {
        DreamEvent::query()->where('dream_entry_id', $dream->id)->delete();

        foreach (array_values($payload['narrative_sequence'] ?? []) as $index => $event) {
            DreamEvent::query()->create([
                'dream_entry_id' => $dream->id,
                'dream_analysis_id' => $analysis->id,
                'sequence_order' => $index + 1,
                'event_type' => is_array($event) ? ($event['type'] ?? null) : null,
                'description' => is_array($event) ? (string) ($event['description'] ?? json_encode($event)) : (string) $event,
                'entities' => is_array($event) ? ($event['entities'] ?? null) : null,
            ]);
        }

        foreach ($payload['main_symbols'] ?? [] as $symbolData) {
            if (! is_array($symbolData) || blank($symbolData['symbol'] ?? null)) {
                continue;
            }

            $name = (string) $symbolData['symbol'];
            $key = Str::slug($name) ?: Str::random(8);

            $symbol = DreamSymbol::query()->firstOrCreate(
                ['normalized_key' => Str::lower($name)],
                [
                    'slug' => $key.'-'.Str::random(4),
                    'name_ar' => $name,
                    'name_en' => $name,
                    'description' => $symbolData['context'] ?? null,
                ]
            );

            DreamSymbolOccurrence::query()->create([
                'dream_entry_id' => $dream->id,
                'dream_analysis_id' => $analysis->id,
                'dream_symbol_id' => $symbol->id,
                'context' => $symbolData['context'] ?? null,
                'interpretations' => $symbolData,
                'interpretation_confidence' => $symbolData['interpretation_confidence'] ?? null,
                'evidence_level' => $symbolData['evidence_level'] ?? null,
            ]);
        }

        foreach ($payload['emotional_analysis']['identified_emotions'] ?? [] as $emotion) {
            $label = is_array($emotion) ? (string) ($emotion['emotion'] ?? $emotion['name'] ?? '') : (string) $emotion;
            if ($label === '') {
                continue;
            }

            DreamEmotion::query()->create([
                'dream_entry_id' => $dream->id,
                'dream_analysis_id' => $analysis->id,
                'emotion' => $label,
                'source' => 'ai',
                'intensity' => is_array($emotion) ? ($emotion['intensity'] ?? null) : null,
                'context' => is_array($emotion) ? ($emotion['context'] ?? null) : null,
            ]);
        }

        foreach ($payload['historical_and_cultural_analysis'] ?? [] as $item) {
            DreamCulturalInterpretation::query()->create([
                'dream_analysis_id' => $analysis->id,
                'tradition' => is_array($item) ? ($item['tradition'] ?? null) : null,
                'source_label' => is_array($item) ? ($item['source'] ?? null) : null,
                'interpretation' => is_array($item) ? (string) ($item['interpretation'] ?? json_encode($item)) : (string) $item,
                'confidence_note' => is_array($item) ? ($item['confidence_note'] ?? null) : null,
            ]);
        }
    }
}
