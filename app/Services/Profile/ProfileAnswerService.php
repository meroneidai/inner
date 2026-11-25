<?php

namespace App\Services\Profile;

use App\Models\ProfileAnswer;
use App\Models\ProfileAnswerHistory;
use App\Models\ProfileQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProfileAnswerService
{
    public function save(User $user, ProfileQuestion $question, mixed $value, bool $draft = false, string $source = 'user'): ProfileAnswer
    {
        $encoded = is_scalar($value) || $value === null
            ? ($value === null ? null : (string) $value)
            : json_encode($value, JSON_UNESCAPED_UNICODE);

        return DB::transaction(function () use ($user, $question, $encoded, $draft, $source) {
            $existing = ProfileAnswer::query()
                ->where('user_id', $user->id)
                ->where('question_id', $question->id)
                ->first();

            if ($existing && (string) $existing->answer_value !== (string) $encoded) {
                ProfileAnswerHistory::query()->create([
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                    'previous_value' => $existing->answer_value,
                    'new_value' => $encoded,
                    'change_source' => $source,
                    'changed_at' => now(),
                ]);
            }

            return ProfileAnswer::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'question_id' => $question->id,
                ],
                [
                    'question_version' => $question->version,
                    'answer_value' => $encoded,
                    'is_draft' => $draft,
                    'answered_at' => $draft ? $existing?->answered_at : now(),
                ]
            );
        });
    }

    public function delete(User $user, ProfileQuestion $question): void
    {
        $answer = ProfileAnswer::query()
            ->where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        if (! $answer) {
            return;
        }

        ProfileAnswerHistory::query()->create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'previous_value' => $answer->answer_value,
            'new_value' => null,
            'change_source' => 'user_delete',
            'changed_at' => now(),
        ]);

        $answer->delete();
    }

    public function setExcludedFromAi(User $user, ProfileQuestion $question, bool $excluded): void
    {
        ProfileAnswer::query()
            ->where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->update(['excluded_from_ai' => $excluded]);
    }
}
