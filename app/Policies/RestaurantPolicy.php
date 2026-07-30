<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        return $user->restaurant_id === $restaurant->id;
    }

    public function create(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->restaurant_id === $restaurant->id;
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $user->restaurant_id === $restaurant->id;
    }

    public function toggleActive(User $user, Restaurant $restaurant): bool
    {
        return $user->role === 'root';
    }
}
