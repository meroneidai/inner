<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentAction;
use App\Models\DreamEntry;
use App\Services\Care\DailyCareService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DailyCareService $care): View
    {
        $user = $request->user();

        $todayDream = $user->dreams()
            ->whereDate('dream_date', now()->toDateString())
            ->latest()
            ->first();

        $recentAnalyses = $user->analyses()
            ->with('dream')
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        $symbolCounts = $user->dreams()
            ->with(['symbolOccurrences.symbol'])
            ->latest('dream_date')
            ->limit(50)
            ->get()
            ->flatMap(fn (DreamEntry $d) => $d->symbolOccurrences)
            ->groupBy(fn ($o) => $o->symbol?->localizedName())
            ->map->count()
            ->sortDesc()
            ->take(8);

        $emotionCounts = $user->dreams()
            ->with('emotions')
            ->latest('dream_date')
            ->limit(50)
            ->get()
            ->flatMap->emotions
            ->groupBy('emotion')
            ->map->count()
            ->sortDesc()
            ->take(8);

        $pendingActions = DevelopmentAction::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', [
            'todayDream' => $todayDream,
            'totalDreams' => $user->dreams()->count(),
            'recentAnalyses' => $recentAnalyses,
            'symbolCounts' => $symbolCounts,
            'emotionCounts' => $emotionCounts,
            'pendingActions' => $pendingActions,
            'activeThemes' => $user->developmentThemes()->where('status', 'active')->count(),
            'preferredName' => $user->profile?->preferred_name ?: $user->name,
            'careMessages' => $care->forToday($user),
            'profileCompletion' => (int) ($user->profile?->completion_percent ?? 0),
        ]);
    }
}
