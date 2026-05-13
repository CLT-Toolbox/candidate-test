<?php

namespace App\Policies;

use App\Models\Layup;
use App\Models\User;

class LayupPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Layup $layup): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Layup $layup): bool
    {
        return true;
    }

    public function delete(User $user, Layup $layup): bool
    {
        return true;
    }

    public function duplicate(User $user, Layup $layup): bool
    {
        return true;
    }
}
