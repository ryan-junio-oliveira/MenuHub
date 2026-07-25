<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTagController;
use App\Http\Controllers\DailyMenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\MenuDispatchController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\Root\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LegalController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/privacidade', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/termos', [LegalController::class, 'terms'])->name('legal.terms');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:root'])
        ->prefix('root')
        ->name('root.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'rootIndex'])->name('dashboard');
            Route::resource('restaurants', RestaurantController::class)->except(['create', 'store']);
            Route::put('/restaurants/{restaurant}/toggle-active', [RestaurantController::class, 'toggleActive'])->name('restaurants.toggle-active');
            Route::get('/orders', [OrderController::class, 'globalIndex'])->name('orders');
            Route::get('/users', [UserController::class, 'index'])->name('users');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

            Route::prefix('billing')->name('billing.')->group(function () {
                Route::get('/', [BillingController::class, 'index'])->name('index');
                Route::get('/plans', [BillingController::class, 'plans'])->name('plans');
                Route::get('/restaurant/{restaurant}', [BillingController::class, 'restaurantBilling'])->name('restaurant');
                Route::post('/restaurant/{restaurant}/generate', [BillingController::class, 'generateInvoice'])->name('generate-invoice');
                Route::put('/restaurant/{restaurant}/plan', [BillingController::class, 'updatePlan'])->name('update-plan');
                Route::put('/restaurant/{restaurant}/status', [BillingController::class, 'updateSubscriptionStatus'])->name('update-status');
                Route::put('/invoice/{invoice}/confirm', [BillingController::class, 'confirmPayment'])->name('confirm-payment');
                Route::put('/invoice/{invoice}/overdue', [BillingController::class, 'markOverdue'])->name('mark-overdue');
                Route::put('/invoice/{invoice}/cancel', [BillingController::class, 'cancelInvoice'])->name('cancel-invoice');
            });
        });

    Route::middleware(['tenant', 'subscription', 'restaurant.active', 'role:admin,user'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware(['tenant', 'subscription', 'restaurant.active', 'role:admin'])->group(function () {
        Route::get('/restaurant/edit', [RestaurantController::class, 'edit'])->name('restaurant.edit');
        Route::put('/restaurant', [RestaurantController::class, 'update'])->name('restaurant.update');

        Route::resource('dish-categories', DishCategoryController::class);
        Route::resource('dishes', DishController::class);
        Route::resource('daily-menus', DailyMenuController::class);
        Route::put('/daily-menus/{daily_menu}/publish', [DailyMenuController::class, 'publish'])->name('daily-menus.publish');
        Route::delete('/daily-menus/{daily_menu}', [DailyMenuController::class, 'destroy'])->name('daily-menus.destroy');

        Route::post('/daily-menus/{daily_menu}/dispatch', [MenuDispatchController::class, 'dispatch'])->name('daily-menus.dispatch');

        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::put('/customers/{customer}/anonymize', [CustomerController::class, 'anonymize'])->name('customers.anonymize');
        Route::resource('customers', CustomerController::class);
        Route::resource('customer-tags', CustomerTagController::class);

        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::match(['put', 'patch'], '/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/orders/kanban', [OrderController::class, 'kanban'])->name('orders.kanban');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::resource('deliveries', DeliveryController::class);
        Route::resource('payments', PaymentController::class)->only(['index', 'show']);
        Route::put('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.update-status');

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/dishes', [ReportController::class, 'dishes'])->name('dishes');
            Route::get('/combinations', [ReportController::class, 'combinations'])->name('combinations');
            Route::get('/hours', [ReportController::class, 'hours'])->name('hours');
            Route::get('/demand', [ReportController::class, 'demand'])->name('demand');
        });

        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/assinatura-expirada', function () {
        return view('subscription.expired');
    })->name('subscription.expired');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/restaurant/create', function () {
    return redirect()->route('home')->with('info', 'Contact the system administrator to register your restaurant.');
})->name('restaurant.create');

require __DIR__.'/auth.php';
