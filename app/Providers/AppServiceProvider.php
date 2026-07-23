<?php

namespace App\Providers;

use App\Events\OrderStatusChanged;
use App\Listeners\LogOrderActivity;
use App\Listeners\SendOrderStatusNotification;
use App\Listeners\UpdateCustomerStats;
use App\Models\Customer;
use App\Models\DailyMenu;
use App\Models\DailyMenuItem;
use App\Models\Delivery;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Scopes\TenantScope;
use App\Models\Setting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            OrderStatusChanged::class,
            [LogOrderActivity::class, 'handle'],
        );

        Event::listen(
            OrderStatusChanged::class,
            [SendOrderStatusNotification::class, 'handle'],
        );

        Event::listen(
            OrderStatusChanged::class,
            [UpdateCustomerStats::class, 'handle'],
        );

        Customer::addGlobalScope(new TenantScope);
        DailyMenu::addGlobalScope(new TenantScope);
        DailyMenuItem::addGlobalScope(new TenantScope);
        Delivery::addGlobalScope(new TenantScope);
        Dish::addGlobalScope(new TenantScope);
        DishCategory::addGlobalScope(new TenantScope);
        Order::addGlobalScope(new TenantScope);
        Payment::addGlobalScope(new TenantScope);
        Setting::addGlobalScope(new TenantScope);
    }
}
