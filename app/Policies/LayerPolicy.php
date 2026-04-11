<?php

namespace App\Policies;

use App\Models\Layer;
use App\Models\Layup;
use App\Models\User;

class LayerPolicy
{
    public function create(User $user, Layup $layup): bool
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
