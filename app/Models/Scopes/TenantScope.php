<?php

namespace App\Models\Scopes;

use App\Models\DailyMenuItem;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check() || !Auth::user()->restaurant_id) {
            return;
        }

        $restaurantId = Auth::user()->restaurant_id;

        if ($model instanceof DailyMenuItem) {
            $builder->whereHas('dailyMenu', function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId);
            });
        } elseif ($model instanceof OrderItem) {
            $builder->whereHas('order', function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId);
            });
        } else {
            $builder->where('restaurant_id', $restaurantId);
        }
    }
}