<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentAction;
use App\Models\DevelopmentProgress;
use App\Models\DevelopmentTheme;
use App\Models\ReflectionAnswer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GrowthController extends Controller
{
    public function index(Request $request): View
    {
        $themes = $request->user()->developmentThemes()->with(['actions', 'progressNotes'])->latest()->get();
        $actions = DevelopmentAction::query()->where('user_id', $request->user()->id)->latest()->limit(20)->get();

        return view('growth.index', compact('themes', 'actions'));
    }

    public function completeAction(Request $request, DevelopmentAction $action): RedirectResponse
    {
        abort_unless($action->user_id === $request->user()->id, 403);

        $action->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('status', __('تم إنجاز التمرين.'));
    }

    public function skipAction(Request $request, DevelopmentAction $action): RedirectResponse
    {
        abort_unless($action->user_id === $request->user()->id, 403);
        $action->update(['status' => 'skipped']);

        return back()->with('status', __('تم تخطي التمرين.'));
    }

    public function addProgress(Request $request, DevelopmentTheme $theme): RedirectResponse
    {
        abort_unless($theme->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:5000'],
            'progress_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        DevelopmentProgress::query()->create([
            'development_theme_id' => $theme->id,
            'user_id' => $request->user()->id,
            'note' => $data['note'],
            'progress_score' => $data['progress_score'] ?? null,
        ]);

        return back()->with('status', __('تم حفظ ملاحظة التقدم.'));
    }

    public function reflections(Request $request): View
    {
        $answers = ReflectionAnswer::query()->where('user_id', $request->user()->id)->latest()->paginate(20);
        $pendingQuestions = $request->user()->analyses()
            ->where('status', 'completed')
            ->latest()
            ->limit(5)
            ->get()
            ->flatMap(fn ($a) => collect($a->payload['reflection_questions'] ?? [])->map(fn ($q) => [
                'question' => is_string($q) ? $q : json_encode($q, JSON_UNESCAPED_UNICODE),
                'analysis_id' => $a->id,
            ]));

        return view('growth.reflections', compact('answers', 'pendingQuestions'));
    }

    public function storeReflection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string'],
            'answer' => ['required', 'string'],
            'dream_analysis_id' => ['nullable', 'integer'],
        ]);

        ReflectionAnswer::query()->create([
            'user_id' => $request->user()->id,
            'dream_analysis_id' => $data['dream_analysis_id'] ?? null,
            'question' => $data['question'],
            'answer' => $data['answer'],
        ]);

        return back()->with('status', __('تم حفظ التأمل.'));
    }
}
