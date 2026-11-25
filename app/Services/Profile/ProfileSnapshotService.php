<?php

namespace App\Services\Profile;

use App\Models\ProfileAnswer;
use App\Models\ProfileSnapshot;
use App\Models\User;

class ProfileSnapshotService
{
    public function createFromAnswers(User $user, ?array $structured = null, string $generatedBy = 'system'): ProfileSnapshot
    {
        $answers = ProfileAnswer::query()
            ->with('question.translations')
            ->where('user_id', $user->id)
            ->where('is_draft', false)
            ->get();

        $version = (int) ProfileSnapshot::query()->where('user_id', $user->id)->max('version_number') + 1;

        $flat = [];
        foreach ($answers as $answer) {
            $flat[$answer->question->question_key] = [
                'question' => $answer->question->text('en'),
                'value' => $answer->decodedValue(),
                'dimension' => $answer->question->profile_dimension,
                'evidence_type' => 'user_reported',
            ];
        }

        return ProfileSnapshot::query()->create([
            'user_id' => $user->id,
            'version_number' => $version,
            'profile_summary' => $structured['profile_summary'] ?? null,
            'structured_profile_json' => $structured ?: ['answers' => $flat],
            'generated_by' => $generatedBy,
            'source_answer_ids' => $answers->pluck('id')->all(),
        ]);
    }
}
