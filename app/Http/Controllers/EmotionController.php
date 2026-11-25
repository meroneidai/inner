<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class EmotionController extends Controller
{
    public function index(Request $request): View
    {
        $emotions = $request->user()->dreams()
            ->with('emotions')
            ->where('dream_date', '>=', now()->subDays(90)->toDateString())
            ->get()
            ->flatMap->emotions;

        $byEmotion = $emotions->groupBy('emotion')->map(function ($items, $emotion) {
            return [
                'emotion' => $emotion,
                'count' => $items->count(),
                'avg_intensity' => round($items->avg('intensity') ?? 0, 1),
            ];
        })->sortByDesc('count')->values();

        $timeline = $request->user()->dreams()
            ->with('emotions')
            ->where('dream_date', '>=', now()->subDays(30)->toDateString())
            ->orderBy('dream_date')
            ->get()
            ->map(fn ($dream) => [
                'date' => $dream->dream_date?->toDateString(),
                'intensity' => $dream->emotional_intensity,
                'emotions' => $dream->emotions->pluck('emotion'),
            ]);

        return view('emotions.index', compact('byEmotion', 'timeline'));
    }
}
