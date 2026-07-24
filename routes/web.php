<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DailyMenuController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DishCategoryController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:root'])
        ->prefix('root')
        ->name('root.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'rootIndex'])->name('dashboard');
            Route::resource('restaurants', RestaurantController::class)->except(['create', 'store']);
            Route::get('/users', function () {
                return view('root.users');
            })->name('users');
        });

    Route::middleware(['tenant', 'role:admin,user'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    Route::middleware(['tenant', 'role:admin'])->group(function () {
        Route::get('/restaurant/edit', [RestaurantController::class, 'edit'])->name('restaurant.edit');
        Route::put('/restaurant', [RestaurantController::class, 'update'])->name('restaurant.update');

        Route::resource('dish-categories', DishCategoryController::class);
        Route::resource('dishes', DishController::class);
        Route::resource('daily-menus', DailyMenuController::class);
        Route::put('/daily-menus/{daily_menu}/publish', [DailyMenuController::class, 'publish'])->name('daily-menus.publish');
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
        Route::resource('customers', CustomerController::class);
        Route::get('/orders/kanban', [OrderController::class, 'kanban'])->name('orders.kanban');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

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

    Route::middleware(['tenant', 'role:admin,user'])->group(function () {
        Route::get('/orders/kanban', [OrderController::class, 'kanban'])->name('orders.kanban');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/restaurant/create', function () {
    return redirect()->route('home')->with('info', 'Contact the system administrator to register your restaurant.');
})->name('restaurant.create');

require __DIR__.'/auth.php';
