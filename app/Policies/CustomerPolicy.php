<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->restaurant_id === $customer->restaurant_id;
    }

    public function create(User $user): bool
    {
        return $user->restaurant_id !== null;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->restaurant_id === $customer->restaurant_id;
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->restaurant_id === $customer->restaurant_id;
    }

    public function anonymize(User $user, Customer $customer): bool
    {
        return $user->restaurant_id === $customer->restaurant_id;
    }
}
