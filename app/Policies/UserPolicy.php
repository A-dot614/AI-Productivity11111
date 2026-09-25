<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function updateRole(User $user, User $target): bool
    {
        return $user->isAdmin() && $target->id !== $user->id;
    }

    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin() && $target->id !== $user->id;
    }
}
