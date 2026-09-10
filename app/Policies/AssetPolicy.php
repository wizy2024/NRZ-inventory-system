<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view assets');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->can('view assets');
    }

    public function create(User $user): bool
    {
        return $user->can('create assets');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->can('edit assets');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->can('delete assets');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete assets');
    }
}
