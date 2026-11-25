<?php

namespace App\Services\Notifications;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationGateway
{
    /**
     * Send a notification respecting user channel preferences.
     * Database and mail are sent independently so SMTP failures never block in-app alerts.
     *
     * @param  array<int, string>  $channels  Candidates: mail, database
     */
    public function send(User $user, Notification $notification, array $channels = ['database', 'mail'], bool $now = false): void
    {
        $prefs = $user->preferences;
        $allowed = [];

        foreach ($channels as $channel) {
            if ($channel === 'database' && ($prefs?->in_app_notifications ?? true)) {
                $allowed[] = 'database';
            }

            if ($channel === 'mail' && ($prefs?->email_notifications ?? true) && filled($user->email)) {
                $allowed[] = 'mail';
            }
        }

        if ($allowed === []) {
            return;
        }

        foreach ($allowed as $channel) {
            try {
                $clone = clone $notification;
                $payload = $clone->viaChannels([$channel]);

                if ($now) {
                    $user->notifyNow($payload);
                } else {
                    $user->notify($payload);
                }
            } catch (Throwable $e) {
                Log::warning('InnerPath notification channel failed', [
                    'user_id' => $user->id,
                    'channel' => $channel,
                    'notification' => $notification::class,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
