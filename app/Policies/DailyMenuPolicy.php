<?php

namespace App\Policies;

use App\Models\DailyMenu;
use App\Models\User;

class DailyMenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function view(User $user, DailyMenu $dailyMenu): bool
    {
        return $user->restaurant_id === $dailyMenu->restaurant_id;
    }

    public function create(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function update(User $user, DailyMenu $dailyMenu): bool
    {
        return $user->restaurant_id === $dailyMenu->restaurant_id;
    }

    public function delete(User $user, DailyMenu $dailyMenu): bool
    {
        return $user->restaurant_id === $dailyMenu->restaurant_id;
    }
}
