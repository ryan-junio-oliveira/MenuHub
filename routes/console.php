<?php

use App\Console\Commands\SendDailyMenu;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(SendDailyMenu::class)
    ->dailyAt('09:30')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/schedule-menu.log'));
