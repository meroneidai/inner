<?php

namespace App\Http\Controllers;

use App\Models\AnalysisFeedback;
use App\Models\DreamAnalysis;
use App\Services\Growth\PersonalGrowthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    public function show(DreamAnalysis $analysis, PersonalGrowthService $growth): View
    {
        abort_unless($analysis->user_id === auth()->id(), 403);

        $analysis->load(['dream', 'feedback', 'culturalInterpretations', 'contextConnections']);
        $actions = $growth->actionsForAnalysis($analysis);
        $userFeedback = $analysis->feedback->firstWhere('user_id', auth()->id());

        return view('analyses.show', compact('analysis', 'actions', 'userFeedback'));
    }

    public function feedback(Request $request, DreamAnalysis $analysis): RedirectResponse
    {
        abort_unless($analysis->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'relevance' => ['required', 'in:relevant,partial,not_relevant,discuss'],
            'accuracy_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'will_try_action' => ['nullable', 'boolean'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        AnalysisFeedback::query()->updateOrCreate(
            [
                'dream_analysis_id' => $analysis->id,
                'user_id' => $request->user()->id,
            ],
            [
                'relevance' => $data['relevance'],
                'accuracy_rating' => $data['accuracy_rating'] ?? null,
                'will_try_action' => $request->boolean('will_try_action'),
                'comment' => $data['comment'] ?? null,
            ]
        );

        $status = match ($data['relevance']) {
            'discuss' => __('ممتاز — يمكنك فتح حوار لمناقشة التفسير.'),
            'not_relevant' => __('شكراً — ملاحظاتك تحسّن التحليل القادم.'),
            default => __('شكراً! إن وُجدت مهمة أدناه، ابدأ بخطوة اليوم.'),
        };

        if ($data['relevance'] === 'discuss') {
            return redirect()
                ->route('conversations.create', [
                    'mode' => 'deep',
                    'dream' => $analysis->dream_entry_id,
                ])
                ->with('status', $status);
        }

        return back()->with('status', $status);
    }
}
