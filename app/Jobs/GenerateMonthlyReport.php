<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\MonthlyReportReadyNotification;
use App\Services\AI\MonthlyAnalysisService;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateMonthlyReport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public ?int $year = null,
        public ?int $month = null,
    ) {}

    public function handle(MonthlyAnalysisService $service, NotificationGateway $notifications): void
    {
        $report = $service->generate(
            $this->user->loadMissing(['preferences', 'profile']),
            $this->year,
            $this->month
        );

        if (in_array($report->status, ['completed', 'insufficient_data'], true)) {
            $notifications->send($this->user, new MonthlyReportReadyNotification($report));
        }
    }
}
