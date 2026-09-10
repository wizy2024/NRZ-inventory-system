<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class AuditPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage audits');
    }

    public function view(User $user, Audit $audit): bool
    {
        return $user->can('manage audits');
    }

    public function create(User $user): bool
    {
        return $user->can('manage audits');
    }

    public function update(User $user, Audit $audit): bool
    {
        return $user->can('manage audits');
    }

    public function delete(User $user, Audit $audit): bool
    {
        return $user->can('manage audits');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage audits');
    }
}
