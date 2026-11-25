<?php

namespace App\Jobs;

use App\Models\DreamEntry;
use App\Notifications\DreamAnalysisReadyNotification;
use App\Services\AI\DreamAnalysisService;
use App\Services\Care\DailyCareService;
use App\Services\Growth\PersonalGrowthService;
use App\Services\Notifications\NotificationGateway;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeDream implements ShouldBeUnique, ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public DreamEntry $dream) {}

    public function uniqueId(): string
    {
        return 'analyze-dream-'.$this->dream->id;
    }

    public function backoff(): array
    {
        return [30, 60, 120];
    }

    public function handle(
        DreamAnalysisService $analysisService,
        PersonalGrowthService $growthService,
        DailyCareService $care,
        NotificationGateway $notifications,
    ): void {
        $analysis = $analysisService->analyze($this->dream->fresh());

        if ($analysis && $analysis->status === 'completed') {
            $user = $this->dream->user()->with(['preferences', 'profile'])->first() ?? $this->dream->user;
            $growthService->suggestFromAnalysis($user, $analysis);
            $care->syncFromAnalysis($user, $analysis);
            $care->forToday($user, refresh: true);

            if ($user->preferences?->analysis_ready_enabled ?? true) {
                $notifications->send($user, new DreamAnalysisReadyNotification($analysis));
            }
        }
    }
}
