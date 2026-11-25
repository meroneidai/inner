<?php

namespace App\Services\Care;

use App\Models\DailyCareMessage;
use App\Models\DreamAnalysis;
use App\Models\User;
use Illuminate\Support\Collection;

class DailyCareService
{
    /**
     * Build or refresh today's companion care messages for the user.
     *
     * @return Collection<int, DailyCareMessage>
     */
    public function forToday(User $user, bool $refresh = false): Collection
    {
        $date = now()->toDateString();

        if (! $refresh) {
            $existing = DailyCareMessage::query()
                ->where('user_id', $user->id)
                ->whereDate('care_date', $date)
                ->orderBy('id')
                ->get();

            if ($existing->isNotEmpty()) {
                return $existing;
            }
        } else {
            DailyCareMessage::query()
                ->where('user_id', $user->id)
                ->whereDate('care_date', $date)
                ->delete();
        }

        $messages = $this->compose($user);

        foreach ($messages as $message) {
            DailyCareMessage::query()->create([
                'user_id' => $user->id,
                'care_date' => $date,
                'emoji' => $message['emoji'],
                'title' => $message['title'],
                'body' => $message['body'],
                'tone' => $message['tone'],
                'focus' => $message['focus'],
                'source' => $message['source'],
                'meta' => $message['meta'] ?? null,
            ]);
        }

        return DailyCareMessage::query()
            ->where('user_id', $user->id)
            ->whereDate('care_date', $date)
            ->orderBy('id')
            ->get();
    }

    /**
     * Persist care tags from a completed dream analysis.
     */
    public function syncFromAnalysis(User $user, DreamAnalysis $analysis): void
    {
        $payload = $analysis->payload ?? [];
        $tags = $payload['care_tags'] ?? [];
        $encouragement = (string) ($payload['daily_encouragement'] ?? '');
        $focusList = $payload['support_focus'] ?? [];

        if ($encouragement !== '') {
            DailyCareMessage::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'care_date' => now()->toDateString(),
                    'source' => 'analysis',
                    'focus' => is_array($focusList) && isset($focusList[0]) ? (string) $focusList[0] : 'warm',
                    'title' => mb_substr($encouragement, 0, 80),
                ],
                [
                    'emoji' => '✨',
                    'body' => $encouragement,
                    'tone' => 'warm',
                    'meta' => ['dream_analysis_id' => $analysis->id, 'support_focus' => $focusList],
                ]
            );
        }

        foreach (array_slice(is_array($tags) ? $tags : [], 0, 4) as $tag) {
            if (! is_array($tag)) {
                continue;
            }
            $label = (string) ($tag['label'] ?? '');
            if ($label === '') {
                continue;
            }

            DailyCareMessage::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'care_date' => now()->toDateString(),
                    'source' => 'analysis_tag',
                    'title' => mb_substr($label, 0, 120),
                ],
                [
                    'emoji' => (string) ($tag['emoji'] ?? '💛'),
                    'body' => $label,
                    'tone' => (string) ($tag['tone'] ?? 'warm'),
                    'focus' => (string) ($tag['tone'] ?? 'warm'),
                    'meta' => ['dream_analysis_id' => $analysis->id],
                ]
            );
        }

        foreach ($payload['step_by_step_growth'] ?? [] as $step) {
            if (! is_array($step) || blank($step['title'] ?? null)) {
                continue;
            }
            DailyCareMessage::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'care_date' => now()->toDateString(),
                    'source' => 'growth_step',
                    'title' => mb_substr((string) $step['title'], 0, 120),
                ],
                [
                    'emoji' => '🌱',
                    'body' => (string) ($step['why'] ?? $step['title']),
                    'tone' => 'support',
                    'focus' => (string) ($step['focus'] ?? 'growth'),
                    'meta' => ['dream_analysis_id' => $analysis->id, 'step' => $step['step'] ?? null],
                ]
            );
        }
    }

    /**
     * @return array<int, array{emoji: string, title: string, body: string, tone: string, focus: string, source: string, meta?: array<string, mixed>}>
     */
    private function compose(User $user): array
    {
        $name = $user->profile?->preferred_name ?: $user->name;
        $todayDream = $user->dreams()->whereDate('dream_date', now()->toDateString())->latest()->first();
        $mood = mb_strtolower((string) ($todayDream?->mood_upon_waking ?? $user->profile?->emotional_state ?? ''));
        $completion = (int) ($user->profile?->completion_percent ?? 0);
        $messages = [];

        $messages[] = [
            'emoji' => '🌅',
            'title' => __('صباح الطمأنينة، :name', ['name' => $name]),
            'body' => __('مساحتك آمنة اليوم. خطوة صغيرة كافية.'),
            'tone' => 'warm',
            'focus' => 'belonging',
            'source' => 'rule',
        ];

        if ($this->moodLooksHard($mood)) {
            $messages[] = [
                'emoji' => '🤗',
                'title' => __('أجدك مثقلاً اليوم'),
                'body' => __('لا داعي للقلق — المشاعر تمرّ، وأنت لست وحدك. تنفّس بلطف.'),
                'tone' => 'support',
                'focus' => 'calm',
                'source' => 'rule',
            ];
        } elseif ($this->moodLooksBright($mood)) {
            $messages[] = [
                'emoji' => '🎉',
                'title' => __('أخبار جيدة اليوم'),
                'body' => __('مزاجك يحمل ضوءاً — احتفِ بلحظة فرح صغيرة.'),
                'tone' => 'celebrate',
                'focus' => 'confidence',
                'source' => 'rule',
            ];
        }

        $needs = $this->inferNeeds($user, $mood);
        foreach ($needs as $need) {
            $messages[] = $this->messageForNeed($need, $name);
        }

        if ($completion < 40) {
            $messages[] = [
                'emoji' => '🧭',
                'title' => __('كل معلومة عنك تجعل التحليل أدق'),
                'body' => __('أكمل جزءاً صغيراً من ملفك الداخلي متى شئت — بدون ضغط.'),
                'tone' => 'calm',
                'focus' => 'clarity',
                'source' => 'rule',
                'meta' => ['completion_percent' => $completion],
            ];
        }

        return array_slice($messages, 0, 5);
    }

    private function moodLooksHard(string $mood): bool
    {
        foreach (['قلق', 'حزن', 'خوف', 'تعب', 'غضب', 'ضغط', 'anxiety', 'sad', 'fear', 'tired', 'stress', 'angry'] as $needle) {
            if ($mood !== '' && str_contains($mood, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function moodLooksBright(string $mood): bool
    {
        foreach (['فرح', 'سعادة', 'راحة', 'أمل', 'طمأنينة', 'happy', 'calm', 'hope', 'peace', 'joy'] as $needle) {
            if ($mood !== '' && str_contains($mood, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int, string>
     */
    private function inferNeeds(User $user, string $mood): array
    {
        $needs = [];

        $latest = $user->analyses()->where('status', 'completed')->latest()->first();
        $fromAnalysis = data_get($latest?->payload, 'support_focus', []);
        if (is_array($fromAnalysis)) {
            foreach ($fromAnalysis as $item) {
                if (is_string($item) && $item !== '') {
                    $needs[] = $item;
                }
            }
        }

        $possible = data_get($latest?->payload, 'psychological_reflection.possible_needs', []);
        if (is_array($possible)) {
            foreach ($possible as $item) {
                $text = is_string($item) ? $item : (string) json_encode($item, JSON_UNESCAPED_UNICODE);
                $lower = mb_strtolower($text);
                if (str_contains($lower, 'حب') || str_contains($lower, 'love')) {
                    $needs[] = 'love';
                }
                if (str_contains($lower, 'ثقة') || str_contains($lower, 'confidence')) {
                    $needs[] = 'confidence';
                }
                if (str_contains($lower, 'هدوء') || str_contains($lower, 'calm') || str_contains($lower, 'أمن')) {
                    $needs[] = 'calm';
                }
            }
        }

        if ($this->moodLooksHard($mood) && ! in_array('calm', $needs, true)) {
            $needs[] = 'calm';
        }

        return array_values(array_unique(array_slice($needs, 0, 2)));
    }

    /**
     * @return array{emoji: string, title: string, body: string, tone: string, focus: string, source: string}
     */
    private function messageForNeed(string $need, string $name): array
    {
        return match ($need) {
            'love' => [
                'emoji' => '💗',
                'title' => __('كلمة حب لك اليوم'),
                'body' => __(':name، أنت تستحق الحنان دون أن تثبت شيئاً. قلبك مهم.', ['name' => $name]),
                'tone' => 'love',
                'focus' => 'love',
                'source' => 'need',
            ],
            'confidence' => [
                'emoji' => '💪',
                'title' => __('ثقة هادئة'),
                'body' => __('خطوة واحدة بصدق أقوى من عشرة وعود. أنت قادر على التقدم بلطف.'),
                'tone' => 'confidence',
                'focus' => 'confidence',
                'source' => 'need',
            ],
            'rest' => [
                'emoji' => '😴',
                'title' => __('إذن بالراحة'),
                'body' => __('الراحة ليست تراجعاً — هي جزء من الشفاء.'),
                'tone' => 'calm',
                'focus' => 'rest',
                'source' => 'need',
            ],
            default => [
                'emoji' => '🌿',
                'title' => __('هدوء صغير متاح الآن'),
                'body' => __('شهيق… زفير. أنت آمن في هذه اللحظة.'),
                'tone' => 'calm',
                'focus' => 'calm',
                'source' => 'need',
            ],
        };
    }
}
