<?php

namespace App\Policies;

use App\Models\CltLayer;
use App\Models\User;

class CltLayerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CltLayer $cltLayer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CltLayer $cltLayer): bool
    {
        return true;
    }

    public function delete(User $user, CltLayer $cltLayer): bool
    {
        return true;
    }
}
