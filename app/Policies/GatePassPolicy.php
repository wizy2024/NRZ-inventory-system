<?php

namespace App\Policies;

use App\Models\GatePass;
use App\Models\User;

class GatePassPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage gate passes');
    }

    public function view(User $user, GatePass $gatePass): bool
    {
        return $user->can('manage gate passes');
    }

    public function create(User $user): bool
    {
        return $user->can('manage gate passes');
    }

    public function update(User $user, GatePass $gatePass): bool
    {
        return $user->can('manage gate passes');
    }

    public function delete(User $user, GatePass $gatePass): bool
    {
        return $user->can('manage gate passes');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage gate passes');
    }
}
