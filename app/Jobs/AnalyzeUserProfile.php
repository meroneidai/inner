<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Profile\HumanInnerProfileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyzeUserProfile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public User $user) {}

    public function handle(HumanInnerProfileService $service): void
    {
        $service->analyze($this->user);
    }
}
