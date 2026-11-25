<?php

namespace App\Services\Profile;

use App\Models\ProfileAnswer;
use App\Models\User;
use App\Services\AI\AIManager;
use App\Services\AI\ResponseValidator;
use Illuminate\Support\Facades\File;

class HumanInnerProfileService
{
    public function __construct(
        private readonly ProfileQuestionService $questions,
        private readonly ProfileSnapshotService $snapshots,
        private readonly ProfileObservationService $observations,
        private readonly ProfileConsentService $consents,
        private readonly AIManager $ai,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildStructuredAnswers(User $user): array
    {
        $answers = ProfileAnswer::query()
            ->with(['question.translations'])
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->get();

        $dimensions = [];
        foreach ($answers as $answer) {
            $dim = $answer->question->profile_dimension ?: 'general';
            $dimensions[$dim][] = [
                'key' => $answer->question->question_key,
                'question' => $answer->question->text('en'),
                'value' => $answer->decodedValue(),
                'evidence_type' => 'user_reported',
                'answered_at' => optional($answer->answered_at)?->toIso8601String(),
            ];
        }

        return [
            'user_id' => $user->id,
            'completion_percent' => $this->questions->completionPercent($user),
            'dimensions' => $dimensions,
            'gaps' => $this->identifyGaps($user),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function identifyGaps(User $user): array
    {
        $answeredIds = ProfileAnswer::query()
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->pluck('question_id');

        return $this->questions->stage1Questions()
            ->reject(fn ($q) => $answeredIds->contains($q->id))
            ->pluck('question_key')
            ->all();
    }

    /**
     * @return array{snapshot: \App\Models\ProfileSnapshot, payload: array<string, mixed>}
     */
    public function analyze(User $user): array
    {
        $structured = $this->buildStructuredAnswers($user);

        if (! filled(config('ai.openai.api_key'))) {
            $snapshot = $this->snapshots->createFromAnswers($user, [
                'profile_summary' => __('ملخص أولي من الإجابات المصرّح بها دون تحليل ذكي (مفتاح API غير مُعد).'),
                'profile_dimensions' => $structured['dimensions'],
                'information_gaps' => $structured['gaps'],
                'limitations' => ['ai_unavailable'],
            ], 'system');

            return ['snapshot' => $snapshot, 'payload' => $snapshot->structured_profile_json];
        }

        $system = File::get(resource_path('prompts/profile_analysis_system.txt'));
        $result = $this->ai->driver()->chat([
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => json_encode($structured, JSON_UNESCAPED_UNICODE)],
        ]);

        $payload = json_decode($result['content'], true);
        if (! is_array($payload) || ! isset($payload['profile_summary'])) {
            throw new \InvalidArgumentException('Invalid profile analysis JSON.');
        }

        $snapshot = $this->snapshots->createFromAnswers($user, $payload, 'ai');

        foreach ($payload['recurring_self_reported_themes'] ?? [] as $theme) {
            $text = is_string($theme) ? $theme : (string) json_encode($theme, JSON_UNESCAPED_UNICODE);
            $this->observations->createHypothesis($user, $text, 'themes', ['from' => 'profile_analysis'], 'medium');
        }

        foreach ($payload['possible_internal_conflicts'] ?? [] as $conflict) {
            $text = is_string($conflict) ? $conflict : (string) json_encode($conflict, JSON_UNESCAPED_UNICODE);
            $this->observations->createHypothesis($user, $text, 'conflicts', ['from' => 'profile_analysis'], 'low');
        }

        $user->profile?->update([
            'profile_last_reviewed_at' => now(),
            'completion_percent' => $structured['completion_percent'],
        ]);

        return ['snapshot' => $snapshot, 'payload' => $payload];
    }

    public function refreshCompletion(User $user): void
    {
        $percent = $this->questions->completionPercent($user);
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'completion_percent' => $percent,
                'profile_status' => $percent >= 80 ? 'stage1_complete' : ($percent > 0 ? 'in_progress' : 'not_started'),
            ]
        );
    }
}
