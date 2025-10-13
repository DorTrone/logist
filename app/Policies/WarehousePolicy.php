<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array(25, $user->permissions ?? []);
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return in_array(25, $user->permissions ?? []);
    }

    public function create(User $user): bool
    {
        return in_array(25, $user->permissions ?? []);
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return in_array(25, $user->permissions ?? []);
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return in_array(25, $user->permissions ?? []);
    }
}