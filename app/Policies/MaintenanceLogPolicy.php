<?php

namespace App\Policies;

use App\Models\MaintenanceLog;
use App\Models\User;

class MaintenanceLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view maintenance');
    }

    public function view(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('view maintenance');
    }

    public function create(User $user): bool
    {
        return $user->can('manage maintenance');
    }

    public function update(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('manage maintenance');
    }

    public function delete(User $user, MaintenanceLog $maintenanceLog): bool
    {
        return $user->can('manage maintenance');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('manage maintenance');
    }
}
