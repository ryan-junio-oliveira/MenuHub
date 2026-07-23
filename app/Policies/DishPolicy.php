<?php

namespace App\Policies;

use App\Models\Dish;
use App\Models\User;

class DishPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function view(User $user, Dish $dish): bool
    {
        return $user->restaurant_id === $dish->restaurant_id;
    }

    public function create(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function update(User $user, Dish $dish): bool
    {
        return $user->restaurant_id === $dish->restaurant_id;
    }

    public function delete(User $user, Dish $dish): bool
    {
        return $user->restaurant_id === $dish->restaurant_id;
    }
}
