<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
{
    /**
     * Получить массив разрешений пользователя.
     */
    protected function getPermissions(User $user): array
    {
        // Если permissions пустой или null
        if (empty($user->permissions)) {
            return [];
        }

        // Если уже массив
        if (is_array($user->permissions)) {
            return array_map('intval', $user->permissions);
        }

        // Если строка JSON
        if (is_string($user->permissions)) {
            $decoded = json_decode($user->permissions, true);
            return is_array($decoded) ? array_map('intval', $decoded) : [];
        }

        return [];
    }

    public function viewAny(User $user): bool
    {
        return in_array(25, $this->getPermissions($user));
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return in_array(25, $this->getPermissions($user));
    }

    public function create(User $user): bool
    {
        return in_array(25, $this->getPermissions($user));
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return in_array(25, $this->getPermissions($user));
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return in_array(25, $this->getPermissions($user));
    }
}
