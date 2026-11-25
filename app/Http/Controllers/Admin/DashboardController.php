<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\DreamAnalysis;
use App\Models\DreamEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'usersCount' => User::query()->count(),
            'dreamsCount' => DreamEntry::query()->count(),
            'failedAnalyses' => DreamAnalysis::query()->where('status', 'failed')->count(),
            'tokenUsage' => AiUsageLog::query()->sum('total_tokens'),
            'recentUsage' => AiUsageLog::query()->latest()->limit(15)->get(),
            'queueFailed' => DB::table('failed_jobs')->count(),
        ]);
    }
}
