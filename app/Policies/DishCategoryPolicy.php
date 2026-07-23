<?php

namespace App\Policies;

use App\Models\DishCategory;
use App\Models\User;

class DishCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function view(User $user, DishCategory $dishCategory): bool
    {
        return $user->restaurant_id === $dishCategory->restaurant_id;
    }

    public function create(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function update(User $user, DishCategory $dishCategory): bool
    {
        return $user->restaurant_id === $dishCategory->restaurant_id;
    }

    public function delete(User $user, DishCategory $dishCategory): bool
    {
        return $user->restaurant_id === $dishCategory->restaurant_id;
    }
}
