<?php

namespace App\Jobs;

use App\Models\DevelopmentAction;
use App\Models\User;
use App\Notifications\GrowthActionReminderNotification;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGrowthReminders implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationGateway $gateway): void
    {
        User::query()
            ->where('is_active', true)
            ->whereHas('preferences', fn ($q) => $q->where('growth_reminder_enabled', true))
            ->whereHas('developmentThemes.actions', fn ($q) => $q->where('status', 'pending'))
            ->with(['preferences', 'profile'])
            ->chunkById(100, function ($users) use ($gateway): void {
                foreach ($users as $user) {
                    $actions = DevelopmentAction::query()
                        ->where('user_id', $user->id)
                        ->where('status', 'pending')
                        ->latest()
                        ->limit(5)
                        ->get();

                    if ($actions->isEmpty()) {
                        continue;
                    }

                    $gateway->send($user, new GrowthActionReminderNotification($actions));
                }
            });
    }
}
