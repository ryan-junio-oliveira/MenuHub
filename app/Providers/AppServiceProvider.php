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
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Scopes\TenantScope;
use App\Models\Setting;
use App\Services\Contracts\GeocodingInterface;
use App\Services\Contracts\PaymentGatewayInterface;
use App\Services\Contracts\ThermalPrinterInterface;
use App\Services\Contracts\WhatsAppInterface;
use App\Services\GeocodingService;
use App\Services\PaymentGatewayService;
use App\Services\ThermalPrinterService;
use App\Services\WhatsAppService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ThermalPrinterInterface::class, ThermalPrinterService::class);
        $this->app->bind(WhatsAppInterface::class, WhatsAppService::class);
        $this->app->bind(PaymentGatewayInterface::class, PaymentGatewayService::class);
        $this->app->bind(GeocodingInterface::class, GeocodingService::class);

        $this->app->extend('translation.loader', function ($loader, $app) {
            return new class ($loader) extends \Illuminate\Translation\FileLoader
            {
                public function __construct(
                    private readonly \Illuminate\Translation\FileLoader $inner,
                ) {
                }

                public function load($locale, $group, $namespace = null): array
                {
                    try {
                        return $this->inner->load($locale, $group, $namespace);
                    } catch (QueryException) {
                        return [];
                    }
                }

                public function addNamespace($namespace, $hint): void
                {
                    $this->inner->addNamespace($namespace, $hint);
                }

                public function addPath($path): void
                {
                    $this->inner->addPath($path);
                }

                public function addJsonPath($path): void
                {
                    $this->inner->addJsonPath($path);
                }

                public function namespaces(): array
                {
                    return $this->inner->namespaces();
                }
            };
        });
    }

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
        OrderItem::addGlobalScope(new TenantScope);
        Payment::addGlobalScope(new TenantScope);
        Setting::addGlobalScope(new TenantScope);
    }
}
