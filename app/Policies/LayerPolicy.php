<?php

namespace App\Policies;

use App\Models\Layer;
use App\Models\User;

class LayerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Layer $layer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Layer $layer): bool
    {
        return true;
    }

    public function delete(User $user, Layer $layer): bool
    {
        return true;
    }
}
