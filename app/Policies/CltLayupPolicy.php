<?php

namespace App\Policies;

use App\Models\CltLayup;
use App\Models\User;

class CltLayupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CltLayup $cltLayup): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, CltLayup $cltLayup): bool
    {
        return true;
    }

    public function delete(User $user, CltLayup $cltLayup): bool
    {
        return true;
    }
}
