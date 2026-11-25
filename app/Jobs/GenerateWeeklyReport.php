<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\WeeklyReportReadyNotification;
use App\Services\AI\WeeklyAnalysisService;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateWeeklyReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function handle(WeeklyAnalysisService $service, NotificationGateway $notifications): void
    {
        $report = $service->generate($this->user->loadMissing(['preferences', 'profile']));

        if (in_array($report->status, ['completed', 'insufficient_data'], true)) {
            $notifications->send($this->user, new WeeklyReportReadyNotification($report));
        }
    }
}
