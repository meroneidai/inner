<?php

namespace App\Services\Profile;

use App\Models\DreamEntry;
use App\Models\ProfileAnswer;
use App\Models\User;
use Illuminate\Support\Str;

class ProfileContextBuilder
{
    public function __construct(
        private readonly ProfileConsentService $consents,
        private readonly ProfileObservationService $observations,
    ) {}

    /**
     * Build compact, relevant, authorized context for dream analysis.
     *
     * @param  array<int, string>  $symbols
     * @param  array<int, string>  $emotions
     * @return array{items: array<int, array<string, mixed>>, excluded_reasons: array<int, string>}
     */
    public function forDream(User $user, DreamEntry $dream, array $symbols = [], array $emotions = []): array
    {
        if (! $this->consents->has($user, ProfileConsentService::ANALYTICAL_USE)) {
            return [
                'items' => [],
                'excluded_reasons' => ['analytical_consent_missing_or_revoked'],
            ];
        }

        $keywords = collect($symbols)
            ->merge($emotions)
            ->merge(Str::of($dream->displayText())->lower()->explode(' '))
            ->filter(fn ($w) => is_string($w) && mb_strlen($w) > 2)
            ->unique()
            ->values();

        $answers = ProfileAnswer::query()
            ->with(['question.section', 'question.translations'])
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->where('excluded_from_ai', false)
            ->get();

        $items = [];
        $excluded = [];

        foreach ($answers as $answer) {
            $question = $answer->question;
            if (! $question) {
                continue;
            }

            if ($question->is_sensitive) {
                if ($question->profile_dimension === 'beliefs' && ! $this->spiritualAllowed($answers)) {
                    $excluded[] = $question->question_key.':spiritual_not_authorized';
                    continue;
                }

                if ($question->question_key === 'J11' && ! $this->consents->has($user, ProfileConsentService::SLEEP_SUBSTANCE)) {
                    $excluded[] = 'J11:sleep_substance_consent_missing';
                    continue;
                }

                if ($question->section?->is_sensitive && $question->ai_context_relevance === 'low') {
                    $excluded[] = $question->question_key.':sensitive_low_relevance';
                    continue;
                }
            }

            $relevance = $this->scoreRelevance($question->profile_dimension, (string) $answer->answer_value, $keywords, $dream);
            if ($relevance < 0.25 && ! $question->include_in_stage1) {
                continue;
            }

            // Always include high-value stage1 context dimensions at a baseline
            if ($relevance < 0.25 && in_array($question->question_key, ['B01', 'B04', 'H03', 'I01', 'L01', 'G10', 'F01'], true)) {
                $relevance = 0.4;
            }

            if ($relevance < 0.25) {
                continue;
            }

            $items[] = [
                'question_key' => $question->question_key,
                'dimension' => $question->profile_dimension,
                'question' => $question->text($user->preferences?->locale ?: 'ar'),
                'value' => $answer->decodedValue(),
                'evidence_type' => 'user_reported',
                'source' => 'profile_answers:'.$answer->id,
                'relevance' => round($relevance, 2),
            ];
        }

        foreach ($this->observations->confirmedForContext($user) as $obs) {
            $items[] = [
                'dimension' => $obs->dimension,
                'value' => $obs->observation,
                'evidence_type' => 'user_confirmed',
                'source' => 'profile_observations:'.$obs->id,
                'relevance' => 0.7,
            ];
        }

        usort($items, fn ($a, $b) => ($b['relevance'] ?? 0) <=> ($a['relevance'] ?? 0));

        return [
            'items' => array_slice($items, 0, 25),
            'excluded_reasons' => $excluded,
        ];
    }

    private function spiritualAllowed($answers): bool
    {
        $g07 = $answers->first(fn ($a) => $a->question?->question_key === 'G07');
        $value = strtolower((string) ($g07?->answer_value ?? ''));

        return in_array($value, ['yes', '1', 'true'], true);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, string>  $keywords
     */
    private function scoreRelevance(?string $dimension, string $answerValue, $keywords, DreamEntry $dream): float
    {
        $score = 0.15;
        $haystack = Str::lower($answerValue.' '.$dream->displayText());

        foreach ($keywords as $keyword) {
            if (Str::contains($haystack, Str::lower((string) $keyword))) {
                $score += 0.12;
            }
        }

        $dimensionBoost = [
            'emotional' => 0.2,
            'fear_safety' => 0.2,
            'sleep_dreams' => 0.25,
            'current_life' => 0.15,
            'values' => 0.1,
            'beliefs' => 0.05,
            'childhood' => 0.05,
        ];

        $score += $dimensionBoost[$dimension] ?? 0.05;

        return min(1.0, $score);
    }
}
