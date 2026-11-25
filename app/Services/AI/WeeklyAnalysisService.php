<?php

namespace App\Services\AI;

use App\Models\AiUsageLog;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Services\Analytics\PatternDetectionService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class WeeklyAnalysisService
{
    public function __construct(
        private readonly AIManager $ai,
        private readonly PatternDetectionService $patterns,
    ) {}

    public function generate(User $user, ?Carbon $weekStart = null): WeeklyReport
    {
        $weekStart = ($weekStart ?: now()->startOfWeek())->copy()->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $report = WeeklyReport::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'week_start' => $weekStart->toDateString(),
            ],
            [
                'uuid' => (string) Str::uuid(),
                'week_end' => $weekEnd->toDateString(),
                'status' => 'pending',
            ]
        );

        $dreams = $user->dreams()
            ->with(['latestAnalysis', 'emotions', 'symbolOccurrences.symbol'])
            ->whereBetween('dream_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        if ($dreams->isEmpty()) {
            $report->update([
                'status' => 'insufficient_data',
                'dream_count' => 0,
                'summary' => __('لا توجد أحلام كافية لهذا الأسبوع.'),
                'payload' => ['status' => 'insufficient_data'],
                'completed_at' => now(),
            ]);

            return $report->fresh();
        }

        $patterns = $this->patterns->detect($user, 30);
        $digest = $dreams->map(fn ($d) => [
            'date' => $d->dream_date?->toDateString(),
            'summary' => $d->latestAnalysis?->summary,
            'text' => Str::limit($d->displayText(), 400),
            'emotions' => $d->emotions->pluck('emotion')->unique()->values(),
            'symbols' => $d->symbolOccurrences->map(fn ($o) => $o->symbol?->name_ar)->filter()->values(),
        ])->values();

        $payload = [
            'dreams' => $digest,
            'patterns' => $patterns,
            'dream_frequency' => round($dreams->count() / 7, 3),
            'profile' => [
                'confirmed_observations' => $user->profileObservations()->where('status', 'confirmed')->latest()->limit(10)->pluck('observation'),
                'pending_hypotheses' => $user->profileObservations()->where('status', 'pending')->latest()->limit(10)->pluck('observation'),
                'goals' => $user->profileGoals()->where('status', 'active')->get(['title', 'development_dimension', 'importance']),
                'completion_percent' => $user->profile?->completion_percent,
                'sources' => [
                    'dream_data' => true,
                    'user_reported_mood' => true,
                    'profile_answers' => true,
                    'goal_completion' => true,
                    'ai_interpretation' => AiSettings::isConfigured(),
                ],
            ],
        ];

        if (AiSettings::isConfigured()) {
            try {
                $result = $this->ai->driver()->chat([
                    ['role' => 'system', 'content' => 'You are InnerPath AI. Produce a weekly dream reflection report as JSON with keys: weekly_overview, important_discoveries, emerging_patterns, persistent_themes, positive_developments, areas_for_reflection, recommended_exercises, questions_for_coming_week, care_tags, support_focus, limitations. Use authorized profile context. Do not diagnose. Prefer Arabic. Be warm and precise.'],
                    ['role' => 'user', 'content' => json_encode($payload, JSON_UNESCAPED_UNICODE)],
                ], [
                    'model' => AiSettings::analysisModel(),
                ]);
                $aiPayload = json_decode($result['content'], true) ?: [];
                $payload['ai'] = $aiPayload;

                AiUsageLog::query()->create([
                    'user_id' => $user->id,
                    'provider' => AiSettings::provider(),
                    'model' => $result['model'],
                    'operation' => 'weekly_report',
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
            'week_end' => $weekEnd->toDateString(),
            'dream_count' => $dreams->count(),
            'payload' => $payload,
            'summary' => data_get($payload, 'ai.weekly_overview') ?: __('ملخص أسبوعي لـ :count حلم.', ['count' => $dreams->count()]),
            'completed_at' => now(),
        ]);

        return $report->fresh();
    }
}
