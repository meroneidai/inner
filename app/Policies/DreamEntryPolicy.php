<?php

namespace App\Policies;

use App\Models\DreamEntry;
use App\Models\User;

class DreamEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DreamEntry $dreamEntry): bool
    {
        return $user->id === $dreamEntry->user_id;
    }

    public function create(User $user): bool
    {
        return $user->is_active;
    }

    public function update(User $user, DreamEntry $dreamEntry): bool
    {
        return $user->id === $dreamEntry->user_id;
    }

    public function delete(User $user, DreamEntry $dreamEntry): bool
    {
        return $user->id === $dreamEntry->user_id;
    }
}
