<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DailyDreamReminderNotification;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDailyDreamReminders implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationGateway $gateway): void
    {
        $hour = (int) now()->format('G');
        $today = now()->toDateString();

        User::query()
            ->where('is_active', true)
            ->whereHas('preferences', function ($q) use ($hour): void {
                $q->where('daily_reminder_enabled', true)
                    ->where('daily_reminder_hour', $hour);
            })
            ->whereDoesntHave('dreams', fn ($q) => $q->whereDate('dream_date', $today))
            ->with(['preferences', 'profile'])
            ->chunkById(100, function ($users) use ($gateway): void {
                foreach ($users as $user) {
                    $gateway->send($user, new DailyDreamReminderNotification);
                }
            });
    }
}
