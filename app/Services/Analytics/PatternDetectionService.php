<?php

namespace App\Services\Analytics;

use App\Models\DreamEntry;
use App\Models\User;
use Illuminate\Support\Collection;

class PatternDetectionService
{
    /**
     * @return array{patterns: array<int, array<string, mixed>>, weights: array<string, float>}
     */
    public function detect(User $user, int $lookbackDays = 90): array
    {
        $weights = config('ai.pattern_weights');

        $dreams = $user->dreams()
            ->with(['symbolOccurrences.symbol', 'emotions'])
            ->where('dream_date', '>=', now()->subDays($lookbackDays)->toDateString())
            ->orderBy('dream_date')
            ->get();

        if ($dreams->count() < 2) {
            return [
                'patterns' => [],
                'weights' => $weights,
                'status' => 'insufficient_data',
            ];
        }

        $total = max(1, $dreams->count());
        $symbolMap = [];

        foreach ($dreams as $dream) {
            foreach ($dream->symbolOccurrences as $occurrence) {
                $key = $occurrence->dream_symbol_id;
                $symbolMap[$key] ??= [
                    'symbol_id' => $key,
                    'name' => $occurrence->symbol?->name_ar ?: $occurrence->symbol?->name_en,
                    'count' => 0,
                    'dates' => [],
                    'emotions' => [],
                ];
                $symbolMap[$key]['count']++;
                $symbolMap[$key]['dates'][] = $dream->dream_date?->toDateString();
                foreach ($dream->emotions as $emotion) {
                    $symbolMap[$key]['emotions'][] = $emotion->emotion;
                }
            }
        }

        $patterns = collect($symbolMap)->map(function (array $row) use ($total, $dreams, $weights) {
            $frequency = ($row['count'] / $total) * 100;
            $recentCutoff = now()->subDays(14)->toDateString();
            $recentCount = collect($row['dates'])->filter(fn ($d) => $d >= $recentCutoff)->count();
            $recent = min(100, ($recentCount / max(1, $row['count'])) * 100);
            $context = min(100, $row['count'] * 20);
            $emotionAssoc = min(100, count(array_unique($row['emotions'])) * 15);
            $dates = collect($row['dates'])->filter()->sort()->values();
            $temporal = $this->temporalContinuity($dates);

            $score = ($weights['frequency'] * $frequency)
                + ($weights['recent'] * $recent)
                + ($weights['context'] * $context)
                + ($weights['emotion'] * $emotionAssoc)
                + ($weights['temporal'] * $temporal);

            return [
                'symbol' => $row['name'],
                'symbol_id' => $row['symbol_id'],
                'occurrences' => $row['count'],
                'first_appearance' => $dates->first(),
                'last_appearance' => $dates->last(),
                'components' => [
                    'frequency' => round($frequency, 2),
                    'recent' => round($recent, 2),
                    'context' => round($context, 2),
                    'emotion' => round($emotionAssoc, 2),
                    'temporal' => round($temporal, 2),
                ],
                'pattern_score' => round($score, 2),
                'note' => 'Pattern prominence score, not psychological severity.',
            ];
        })->sortByDesc('pattern_score')->values()->all();

        return [
            'patterns' => $patterns,
            'weights' => $weights,
            'dream_count' => $dreams->count(),
            'status' => 'ok',
        ];
    }

    /**
     * @param  Collection<int, string>  $dates
     */
    private function temporalContinuity(Collection $dates): float
    {
        if ($dates->count() < 2) {
            return 0;
        }

        $gaps = [];
        for ($i = 1; $i < $dates->count(); $i++) {
            $gaps[] = (strtotime($dates[$i]) - strtotime($dates[$i - 1])) / 86400;
        }

        $avgGap = array_sum($gaps) / max(1, count($gaps));

        return max(0, min(100, 100 - ($avgGap * 5)));
    }
}
