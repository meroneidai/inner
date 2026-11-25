<?php

namespace App\Services\Profile;

use App\Models\ProfileAnswer;
use App\Models\ProfileQuestion;
use App\Models\ProfileSection;
use App\Models\User;
use Illuminate\Support\Collection;

class ProfileQuestionService
{
    /**
     * @return Collection<int, ProfileSection>
     */
    public function activeSections(?string $stage = null): Collection
    {
        return ProfileSection::query()
            ->where('is_active', true)
            ->when($stage, fn ($q) => $q->where('stage', $stage))
            ->with(['questions' => fn ($q) => $q->where('is_active', true)->with('translations')])
            ->orderBy('display_order')
            ->get();
    }

    /**
     * @return Collection<int, ProfileQuestion>
     */
    public function stage1Questions(): Collection
    {
        return ProfileQuestion::query()
            ->where('is_active', true)
            ->where('include_in_stage1', true)
            ->with(['translations', 'section'])
            ->orderBy('display_order')
            ->get();
    }

    /**
     * @return Collection<int, ProfileQuestion>
     */
    public function visibleQuestionsForUser(User $user, ProfileSection $section): Collection
    {
        $answers = ProfileAnswer::query()
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->pluck('answer_value', 'question_id');

        $keyed = ProfileQuestion::query()
            ->where('is_active', true)
            ->with('translations')
            ->get()
            ->keyBy('id');

        $answerByKey = [];
        foreach ($keyed as $q) {
            if ($answers->has($q->id)) {
                $answerByKey[$q->question_key] = $answers[$q->id];
            }
        }

        return $section->questions()
            ->where('is_active', true)
            ->with('translations')
            ->orderBy('display_order')
            ->get()
            ->filter(fn (ProfileQuestion $question) => $this->passesConditions($question, $answerByKey))
            ->values();
    }

    /**
     * @param  array<string, mixed>  $answersByKey
     */
    public function passesConditions(ProfileQuestion $question, array $answersByKey): bool
    {
        $rules = $question->conditional_rules_json;
        if (! $rules) {
            return true;
        }

        if (isset($rules['depends_on'])) {
            $parentKey = $rules['depends_on']['question'] ?? null;
            $expected = $rules['depends_on']['equals'] ?? null;
            if ($parentKey && array_key_exists($parentKey, $answersByKey)) {
                return (string) $answersByKey[$parentKey] === (string) $expected;
            }

            return false;
        }

        return true;
    }

    public function completionPercent(User $user): int
    {
        $total = ProfileQuestion::query()->where('is_active', true)->where('include_in_stage1', true)->count();
        if ($total === 0) {
            return 0;
        }

        $answered = ProfileAnswer::query()
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->whereIn('question_id', ProfileQuestion::query()->where('include_in_stage1', true)->select('id'))
            ->count();

        return (int) round(($answered / $total) * 100);
    }
}
