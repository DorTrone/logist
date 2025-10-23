<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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

    public function packagesPanel(User $user): bool
    {
        return in_array(1, $this->getPermissions($user));
    }

    public function visitorsPanel(User $user): bool
    {
        return in_array(3, $this->getPermissions($user));
    }

    public function adminPanel(User $user): bool
    {
        return in_array(4, $this->getPermissions($user));
    }

    public function errors(User $user): bool
    {
        return in_array(5, $this->getPermissions($user));
    }

    public function tokens(User $user): bool
    {
        return in_array(23, $this->getPermissions($user));
    }

    public function packages(User $user): bool
    {
        return in_array(6, $this->getPermissions($user));
    }

    public function transports(User $user): bool
    {
        return in_array(7, $this->getPermissions($user));
    }

    public function customers(User $user): bool
    {
        return in_array(8, $this->getPermissions($user));
    }

    public function verifications(User $user): bool
    {
        return in_array(9, $this->getPermissions($user));
    }

    public function contacts(User $user): bool
    {
        return in_array(10, $this->getPermissions($user));
    }

    public function banners(User $user): bool
    {
        return in_array(24, $this->getPermissions($user));
    }

    public function notifications(User $user): bool
    {
        return in_array(11, $this->getPermissions($user));
    }

    public function pushNotifications(User $user): bool
    {
        return in_array(12, $this->getPermissions($user));
    }

    public function tasks(User $user): bool
    {
        return in_array(16, $this->getPermissions($user));
    }

    public function users(User $user): bool
    {
        return in_array(17, $this->getPermissions($user));
    }

    public function configs(User $user): bool
    {
        return in_array(18, $this->getPermissions($user));
    }

    public function ipAddresses(User $user): bool
    {
        return in_array(19, $this->getPermissions($user));
    }

    public function userAgents(User $user): bool
    {
        return in_array(20, $this->getPermissions($user));
    }

    public function authAttempts(User $user): bool
    {
        return in_array(21, $this->getPermissions($user));
    }

    public function visitors(User $user): bool
    {
        return in_array(22, $this->getPermissions($user));
    }

    public function warehouses(User $user): bool
    {
        return in_array(25, $this->getPermissions($user));
    }
}
