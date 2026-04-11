<?php

namespace App\Policies;

use App\Models\Layup;
use App\Models\Supplier;
use App\Models\User;

class LayupPolicy
{
    public function create(User $user, Supplier $supplier): bool
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
}
