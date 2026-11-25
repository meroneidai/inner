<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\DailyDreamReminderNotification;
use App\Services\Care\DailyCareService;
use App\Services\Notifications\NotificationGateway;
use App\Notifications\DailyCareNotification;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    protected $signature = 'innerpath:notify-test {email? : User email} {--type=care : care|reminder}';

    protected $description = 'Send a test InnerPath notification (database + mail if enabled)';

    public function handle(NotificationGateway $gateway, DailyCareService $care): int
    {
        $email = $this->argument('email') ?: 'user@innerpath.test';
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error('User not found: '.$email);

            return self::FAILURE;
        }

        if ($this->option('type') === 'reminder') {
            $gateway->send($user, new DailyDreamReminderNotification, now: true);
            $this->info('Sent dream reminder to '.$email);
        } else {
            $messages = $care->forToday($user);
            $gateway->send($user, new DailyCareNotification($messages), now: true);
            $this->info('Sent daily care to '.$email.' ('.$messages->count().' messages)');
        }

        return self::SUCCESS;
    }
}
