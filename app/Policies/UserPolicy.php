<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage users');
    }

    public function view(User $user, User $subject): bool
    {
        return $user->can('manage users');
    }

    public function create(User $user): bool
    {
        return $user->can('manage users');
    }

    public function update(User $user, User $subject): bool
    {
        return $user->can('manage users');
    }

    public function delete(User $user, User $subject): bool
    {
        return $user->can('manage users');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage users');
    }
}
