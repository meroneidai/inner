<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeUserProfile;
use App\Models\ProfileAnswer;
use App\Models\ProfileGoal;
use App\Models\ProfileObservation;
use App\Models\ProfileQuestion;
use App\Models\ProfileSection;
use App\Services\Profile\HumanInnerProfileService;
use App\Services\Profile\ProfileAnswerService;
use App\Services\Profile\ProfileConsentService;
use App\Services\Profile\ProfileObservationService;
use App\Services\Profile\ProfileQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InnerWorldController extends Controller
{
    public function index(Request $request, ProfileQuestionService $questions): View
    {
        $user = $request->user()->load(['profile', 'profileGoals', 'profileObservations', 'profileSnapshots']);
        $sections = $questions->activeSections();
        $answered = ProfileAnswer::query()->where('user_id', $user->id)->where('is_draft', false)->count();
        $pendingObservations = $user->profileObservations()->where('status', 'pending')->latest()->limit(10)->get();

        return view('profile.inner-world.index', [
            'user' => $user,
            'sections' => $sections,
            'answered' => $answered,
            'progress' => $questions->completionPercent($user),
            'pendingObservations' => $pendingObservations,
            'latestSnapshot' => $user->profileSnapshots()->latest('version_number')->first(),
        ]);
    }

    public function section(Request $request, string $slug, ProfileQuestionService $questions): View
    {
        $section = ProfileSection::query()->where('slug', $slug)->firstOrFail();
        $visible = $questions->visibleQuestionsForUser($request->user(), $section);
        $answers = ProfileAnswer::query()->where('user_id', $request->user()->id)->get()->keyBy('question_id');

        return view('profile.inner-world.section', compact('section', 'visible', 'answers'));
    }

    public function updateSection(Request $request, string $slug, ProfileAnswerService $answers, HumanInnerProfileService $profile): RedirectResponse
    {
        $section = ProfileSection::query()->where('slug', $slug)->firstOrFail();
        $data = $request->validate(['answers' => ['nullable', 'array']]);

        foreach ($data['answers'] ?? [] as $questionId => $value) {
            $question = ProfileQuestion::query()->find($questionId);
            if (! $question || $question->section_id !== $section->id) {
                continue;
            }
            if ($value === null || $value === '') {
                $answers->delete($request->user(), $question);
                continue;
            }
            $answers->save($request->user(), $question, is_array($value) ? array_values($value) : $value);
        }

        $profile->refreshCompletion($request->user());

        return back()->with('status', __('تم تحديث القسم.'));
    }

    public function history(Request $request): View
    {
        $histories = $request->user()->profileAnswers()
            ->with('question.translations')
            ->latest('updated_at')
            ->paginate(30);

        $answerHistory = \App\Models\ProfileAnswerHistory::query()
            ->with('question.translations')
            ->where('user_id', $request->user()->id)
            ->latest('changed_at')
            ->limit(50)
            ->get();

        $observations = $request->user()->profileObservations()->latest()->limit(30)->get();

        return view('profile.inner-world.history', compact('histories', 'answerHistory', 'observations'));
    }

    public function privacy(Request $request, ProfileConsentService $consents): View
    {
        return view('profile.inner-world.privacy', [
            'user' => $request->user()->load('profileConsents'),
            'analytical' => $consents->has($request->user(), ProfileConsentService::ANALYTICAL_USE),
            'training' => $consents->has($request->user(), ProfileConsentService::AI_TRAINING),
        ]);
    }

    public function updatePrivacy(Request $request, ProfileConsentService $consents): RedirectResponse
    {
        $data = $request->validate([
            'analytical_use' => ['nullable', 'boolean'],
            'ai_training' => ['nullable', 'boolean'],
        ]);

        $request->boolean('analytical_use')
            ? $consents->grant($request->user(), ProfileConsentService::ANALYTICAL_USE)
            : $consents->revoke($request->user(), ProfileConsentService::ANALYTICAL_USE);

        $request->boolean('ai_training')
            ? $consents->grant($request->user(), ProfileConsentService::AI_TRAINING)
            : $consents->revoke($request->user(), ProfileConsentService::AI_TRAINING);

        return back()->with('status', __('تم تحديث إعدادات الخصوصية.'));
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user()->load([
            'profile',
            'profileAnswers.question.translations',
            'profileConsents',
            'profileSnapshots',
            'profileObservations',
            'profileGoals',
        ]);

        return response()->streamDownload(function () use ($user): void {
            echo json_encode($user->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, 'innerpath-inner-world-'.$user->id.'.json', ['Content-Type' => 'application/json']);
    }

    public function analyze(Request $request): RedirectResponse
    {
        AnalyzeUserProfile::dispatch($request->user());

        return back()->with('status', __('جاري تحليل ملفك الداخلي في الخلفية.'));
    }

    public function confirmObservation(Request $request, ProfileObservation $observation, ProfileObservationService $service): RedirectResponse
    {
        abort_unless($observation->user_id === $request->user()->id, 403);
        $service->confirm($observation);

        return back()->with('status', __('تم تأكيد الملاحظة.'));
    }

    public function rejectObservation(Request $request, ProfileObservation $observation, ProfileObservationService $service): RedirectResponse
    {
        abort_unless($observation->user_id === $request->user()->id, 403);
        $service->reject($observation);

        return back()->with('status', __('تم رفض الفرضية.'));
    }

    public function storeGoal(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'development_dimension' => ['nullable', 'string', 'max:80'],
            'target_date' => ['nullable', 'date'],
            'importance' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        ProfileGoal::query()->create([
            'user_id' => $request->user()->id,
            ...$data,
        ]);

        return back()->with('status', __('تمت إضافة الهدف.'));
    }

    public function deleteAnswer(Request $request, ProfileQuestion $question, ProfileAnswerService $answers, HumanInnerProfileService $profile): RedirectResponse
    {
        $answers->delete($request->user(), $question);
        $profile->refreshCompletion($request->user());

        return back()->with('status', __('تم حذف الإجابة.'));
    }
}
