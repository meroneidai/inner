<?php

namespace App\Policies;

use App\Models\ProfileAnswer;
use App\Models\User;

class ProfileAnswerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProfileAnswer $answer): bool
    {
        return $user->id === $answer->user_id;
    }

    public function update(User $user, ProfileAnswer $answer): bool
    {
        return $user->id === $answer->user_id;
    }

    public function delete(User $user, ProfileAnswer $answer): bool
    {
        return $user->id === $answer->user_id;
    }
}
