<?php

namespace App\Services\Growth;

use App\Models\DevelopmentAction;
use App\Models\DevelopmentTheme;
use App\Models\DreamAnalysis;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PersonalGrowthService
{
    public function suggestFromAnalysis(User $user, DreamAnalysis $analysis): void
    {
        $actions = data_get($analysis->payload, 'personal_growth_actions', []);
        $steps = data_get($analysis->payload, 'step_by_step_growth', []);
        $themes = data_get($analysis->payload, 'psychological_reflection.possible_themes', []);

        $daily = null;
        $weekly = null;
        $monthly = null;

        if (is_array($steps) && $steps !== []) {
            foreach (array_slice($steps, 0, 3) as $index => $step) {
                if (! is_array($step)) {
                    continue;
                }
                $title = (string) ($step['title'] ?? '');
                if ($title === '') {
                    continue;
                }
                $full = $title.((string) ($step['why'] ?? '') !== '' ? ' — '.$step['why'] : '');
                match ($index) {
                    0 => $daily = $full,
                    1 => $weekly = $full,
                    default => $monthly = $full,
                };
            }
        }

        $daily ??= $this->asString($actions[0] ?? __('اليوم: خصّص 5 دقائق لملاحظة الشعور الأبرز من الحلم دون حكم.'));
        $weekly ??= $this->asString($actions[1] ?? __('هذا الأسبوع: اكتب سطراً واحداً يومياً عن رمز تكرر وما يعنيه لك شخصياً.'));
        $monthly ??= $this->asString($actions[2] ?? __('هذا الشهر: راجع هدفاً واحداً من نموك وعدّله بلطف.'));

        $this->ensureAnalysisAction($user, $analysis, 'daily', $daily);
        $this->ensureAnalysisAction($user, $analysis, 'weekly', $weekly);
        $this->ensureAnalysisAction($user, $analysis, 'monthly', $monthly);

        foreach (array_slice($themes, 0, 3) as $themeTitle) {
            $title = is_array($themeTitle) ? (string) ($themeTitle['theme'] ?? reset($themeTitle) ?: '') : (string) $themeTitle;
            if ($title === '') {
                continue;
            }

            $theme = DevelopmentTheme::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'title' => $title,
                    'status' => 'active',
                ],
                [
                    'description' => __('موضوع مقترح للتأمل بناءً على تحليل الحلم.'),
                    'why_suggested' => data_get($analysis->payload, 'overall_interpretation'),
                    'related_dream_ids' => [$analysis->dream_entry_id],
                    'review_date' => now()->addMonth()->toDateString(),
                ]
            );

            if (! $theme->wasRecentlyCreated) {
                $related = collect($theme->related_dream_ids ?? [])->push($analysis->dream_entry_id)->unique()->values()->all();
                $theme->update(['related_dream_ids' => $related]);
            }
        }
    }

    /**
     * @return Collection<int, DevelopmentAction>
     */
    public function actionsForAnalysis(DreamAnalysis $analysis): Collection
    {
        $actions = DevelopmentAction::query()
            ->where('dream_analysis_id', $analysis->id)
            ->orderByRaw("CASE cadence WHEN 'daily' THEN 1 WHEN 'weekly' THEN 2 ELSE 3 END")
            ->get();

        if ($actions->isEmpty()) {
            $this->suggestFromAnalysis($analysis->user, $analysis);
            $actions = DevelopmentAction::query()
                ->where('dream_analysis_id', $analysis->id)
                ->orderByRaw("CASE cadence WHEN 'daily' THEN 1 WHEN 'weekly' THEN 2 ELSE 3 END")
                ->get();
        }

        return $actions;
    }

    private function ensureAnalysisAction(User $user, DreamAnalysis $analysis, string $cadence, string $title): void
    {
        $theme = DevelopmentTheme::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'title' => __('خطوات بعد التحليل'),
            ],
            [
                'description' => __('مهام يومية وأسبوعية مرتبطة بتحليل حلم محدد.'),
                'status' => 'active',
            ]
        );

        DevelopmentAction::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'dream_analysis_id' => $analysis->id,
                'cadence' => $cadence,
            ],
            [
                'development_theme_id' => $theme->id,
                'title' => Str::limit($title, 180),
                'instructions' => $title,
                'status' => 'pending',
                'due_at' => match ($cadence) {
                    'daily' => now()->endOfDay(),
                    'weekly' => now()->endOfWeek(),
                    default => now()->endOfMonth(),
                },
            ]
        );
    }

    private function asString(mixed $value): string
    {
        return is_string($value) ? $value : (string) json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
