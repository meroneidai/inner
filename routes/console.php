<?php

use App\Jobs\GenerateMonthlyReport;
use App\Jobs\GenerateWeeklyReport;
use App\Jobs\SendDailyCareNotifications;
use App\Jobs\SendDailyDreamReminders;
use App\Jobs\SendGrowthReminders;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Hourly ticks — jobs filter by preferred hour / prefs.
Schedule::job(new SendDailyCareNotifications)->hourly();
Schedule::job(new SendDailyDreamReminders)->hourly();
Schedule::job(new SendGrowthReminders)->dailyAt('09:30');

Schedule::call(function () {
    User::query()
        ->where('is_active', true)
        ->whereHas('preferences', fn ($q) => $q->where('weekly_report_enabled', true))
        ->each(fn (User $user) => GenerateWeeklyReport::dispatch($user));
})->weeklyOn(1, '06:00');

Schedule::call(function () {
    User::query()
        ->where('is_active', true)
        ->whereHas('preferences', fn ($q) => $q->where('monthly_report_enabled', true))
        ->each(fn (User $user) => GenerateMonthlyReport::dispatch($user));
})->monthlyOn(1, '06:30');
