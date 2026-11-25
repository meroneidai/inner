<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\DailyCareNotification;
use App\Services\Care\DailyCareService;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDailyCareNotifications implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(DailyCareService $care, NotificationGateway $gateway): void
    {
        $hour = (int) now()->format('G');

        User::query()
            ->where('is_active', true)
            ->whereHas('preferences', function ($q) use ($hour): void {
                $q->where('daily_care_enabled', true)
                    ->where('daily_care_hour', $hour);
            })
            ->with(['preferences', 'profile'])
            ->chunkById(100, function ($users) use ($care, $gateway): void {
                foreach ($users as $user) {
                    $messages = $care->forToday($user);
                    if ($messages->isEmpty()) {
                        continue;
                    }
                    $gateway->send($user, new DailyCareNotification($messages));
                }
            });
    }
}
