<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Models\MonthlyReport;
use App\Models\User;
use App\Services\Analytics\PatternDetectionService;
use Illuminate\Support\Str;

class MonthlyAnalysisService
{
    public function __construct(
        private readonly AIManager $ai,
        private readonly PatternDetectionService $patterns,
    ) {}

    public function generate(User $user, ?int $year = null, ?int $month = null): MonthlyReport
    {
        $year = $year ?: (int) now()->year;
        $month = $month ?: (int) now()->month;

        $report = MonthlyReport::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'year' => $year,
                'month' => $month,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'status' => 'pending',
            ]
        );

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));

        $dreams = $user->dreams()
            ->with(['latestAnalysis', 'emotions', 'symbolOccurrences.symbol'])
            ->whereBetween('dream_date', [$start, $end])
            ->get();

        $previousComparable = MonthlyReport::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->where(function ($q) use ($year, $month): void {
                $q->where('year', '<', $year)
                    ->orWhere(function ($q2) use ($year, $month): void {
                        $q2->where('year', $year)->where('month', '<', $month);
                    });
            })
            ->count();

        if ($dreams->count() < 2 || $previousComparable < 1) {
            $report->update([
                'status' => 'insufficient_data',
                'dream_count' => $dreams->count(),
                'summary' => __('يلزم فترتان قابلتان للمقارنة قبل عرض الاتجاهات.'),
                'payload' => [
                    'status' => 'insufficient_data',
                    'dream_count' => $dreams->count(),
                    'comparable_periods' => $previousComparable,
                ],
                'completed_at' => now(),
            ]);

            return $report->fresh();
        }

        $patterns = $this->patterns->detect($user, 60);
        $payload = [
            'year' => $year,
            'month' => $month,
            'dream_count' => $dreams->count(),
            'dream_frequency' => round($dreams->count() / max(1, (int) date('t', strtotime($start))), 3),
            'patterns' => $patterns,
            'emotions' => $dreams->flatMap->emotions->groupBy('emotion')->map->count(),
            'profile' => [
                'completion_percent' => $user->profile?->completion_percent,
                'confirmed_observations' => $user->profileObservations()->where('status', 'confirmed')->latest()->limit(15)->pluck('observation'),
                'goals' => $user->profileGoals()->get(['title', 'status', 'development_dimension']),
                'latest_snapshot_summary' => $user->profileSnapshots()->latest('version_number')->value('profile_summary'),
                'trend_sources' => ['dream_data', 'user_reported_mood', 'profile_answers', 'goal_completion', 'ai_interpretation'],
            ],
        ];

        if (AiSettings::isConfigured()) {
            try {
                $result = $this->ai->driver()->chat([
                    ['role' => 'system', 'content' => 'You are InnerPath AI. Produce a monthly development report as JSON with keys: monthly_summary, main_themes, changes_vs_previous, strongest_patterns, strengths, suggested_development_areas, next_month_goals, care_tags, support_focus, step_by_step_growth, limitations. Do not claim improvement/deterioration without supporting indicators. Prefer Arabic. Be warm and precise.'],
                    ['role' => 'user', 'content' => json_encode($payload, JSON_UNESCAPED_UNICODE)],
                ], [
                    'model' => AiSettings::analysisModel(),
                ]);
                $payload['ai'] = json_decode($result['content'], true) ?: [];

                AiUsageLog::query()->create([
                    'user_id' => $user->id,
                    'provider' => AiSettings::provider(),
                    'model' => $result['model'],
                    'operation' => 'monthly_report',
                    'prompt_tokens' => $result['usage']['prompt_tokens'],
                    'completion_tokens' => $result['usage']['completion_tokens'],
                    'total_tokens' => $result['usage']['total_tokens'],
                    'status' => 'success',
                ]);
            } catch (\Throwable $e) {
                $payload['ai_error'] = $e->getMessage();
            }
        }

        $report->update([
            'status' => 'completed',
            'dream_count' => $dreams->count(),
            'payload' => $payload,
            'summary' => data_get($payload, 'ai.monthly_summary') ?: __('تقرير شهري لـ :count حلم.', ['count' => $dreams->count()]),
            'completed_at' => now(),
        ]);

        return $report->fresh();
    }
}
