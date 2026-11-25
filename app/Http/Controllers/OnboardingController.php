<?php

namespace App\Http\Controllers;

use App\Models\ProfileAnswer;
use App\Models\ProfileQuestion;
use App\Models\ProfileReviewSession;
use App\Models\ProfileSection;
use App\Services\Profile\HumanInnerProfileService;
use App\Services\Profile\ProfileAnswerService;
use App\Services\Profile\ProfileConsentService;
use App\Services\Profile\ProfileQuestionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function welcome(Request $request): View|RedirectResponse
    {
        if ($request->user()->onboarding_completed_at && $request->user()->profile?->profile_status === 'stage1_complete') {
            return redirect()->route('inner-world.index');
        }

        return view('onboarding.welcome');
    }

    public function show(Request $request, ProfileQuestionService $questions): View|RedirectResponse
    {
        return redirect()->route('onboarding.welcome');
    }

    public function stage1(Request $request, ProfileQuestionService $questions): View
    {
        $user = $request->user();
        $items = $questions->stage1Questions();
        $answers = ProfileAnswer::query()
            ->where('user_id', $user->id)
            ->pluck('answer_value', 'question_id');

        $session = ProfileReviewSession::query()->firstOrCreate(
            [
                'user_id' => $user->id,
                'review_type' => 'onboarding',
                'completed_at' => null,
            ],
            ['started_at' => now()]
        );

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['profile_status' => 'in_progress', 'onboarding_stage' => 1]
        );

        return view('onboarding.stage1', [
            'questions' => $items,
            'answers' => $answers,
            'session' => $session,
            'progress' => $questions->completionPercent($user),
        ]);
    }

    public function saveStage1(
        Request $request,
        ProfileAnswerService $answerService,
        HumanInnerProfileService $profileService,
        ProfileConsentService $consents,
    ): RedirectResponse {
        $user = $request->user();
        $data = $request->validate([
            'answers' => ['nullable', 'array'],
            'action' => ['required', 'in:continue,exit,complete'],
        ]);

        foreach ($data['answers'] ?? [] as $questionId => $value) {
            if ($value === null || $value === '' || (is_array($value) && count($value) === 0)) {
                continue;
            }

            $question = ProfileQuestion::query()->find($questionId);
            if (! $question) {
                continue;
            }

            $answerService->save($user, $question, is_array($value) ? array_values($value) : $value, draft: $data['action'] === 'exit');
            $this->syncLegacyProfileFields($user, $question->question_key, $value);
        }

        $consents->ensureDefaults($user);
        $profileService->refreshCompletion($user);

        if ($data['action'] === 'exit') {
            return redirect()->route('dashboard')->with('status', __('تم حفظ تقدمك. يمكنك المتابعة لاحقاً.'));
        }

        if ($data['action'] === 'complete') {
            $user->update(['onboarding_completed_at' => now()]);
            $user->profile?->update([
                'profile_status' => 'stage1_complete',
                'onboarding_stage' => 2,
            ]);

            ProfileReviewSession::query()
                ->where('user_id', $user->id)
                ->where('review_type', 'onboarding')
                ->whereNull('completed_at')
                ->update(['completed_at' => now()]);

            return redirect()->route('onboarding.deeper')->with('status', __('أحسنت. يمكنك تعميق ملفك أو الانتقال للوحة التحكم.'));
        }

        return redirect()->route('onboarding.deeper');
    }

    public function deeper(Request $request, ProfileQuestionService $questions): View
    {
        $sections = $questions->activeSections('deeper');

        return view('onboarding.deeper', [
            'sections' => $sections,
            'progress' => $questions->completionPercent($request->user()),
        ]);
    }

    public function section(Request $request, string $slug, ProfileQuestionService $questions): View
    {
        $section = ProfileSection::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();
        $visible = $questions->visibleQuestionsForUser($request->user(), $section);
        $answers = ProfileAnswer::query()
            ->where('user_id', $request->user()->id)
            ->pluck('answer_value', 'question_id');

        $request->user()->profile?->update(['current_section_slug' => $slug]);

        return view('onboarding.section', compact('section', 'visible', 'answers'));
    }

    public function saveSection(
        Request $request,
        string $slug,
        ProfileAnswerService $answerService,
        HumanInnerProfileService $profileService,
    ): RedirectResponse {
        $section = ProfileSection::query()->where('slug', $slug)->firstOrFail();
        $data = $request->validate([
            'answers' => ['nullable', 'array'],
            'action' => ['required', 'in:continue,exit,skip'],
        ]);

        if ($data['action'] !== 'skip') {
            foreach ($data['answers'] ?? [] as $questionId => $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                $question = ProfileQuestion::query()->find($questionId);
                if ($question && $question->section_id === $section->id) {
                    $answerService->save($request->user(), $question, is_array($value) ? array_values($value) : $value);
                    $this->syncLegacyProfileFields($request->user(), $question->question_key, $value);
                }
            }
        }

        $profileService->refreshCompletion($request->user());

        if ($data['action'] === 'exit') {
            return redirect()->route('inner-world.index')->with('status', __('تم الحفظ.'));
        }

        $next = ProfileSection::query()
            ->where('is_active', true)
            ->where('display_order', '>', $section->display_order)
            ->orderBy('display_order')
            ->first();

        return $next
            ? redirect()->route('onboarding.section', $next->slug)
            : redirect()->route('inner-world.index')->with('status', __('اكتملت الأقسام المتاحة.'));
    }

    public function skip(Request $request, ProfileConsentService $consents): RedirectResponse
    {
        $user = $request->user();
        $consents->ensureDefaults($user);
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['profile_status' => 'skipped']
        );
        $user->update(['onboarding_completed_at' => now()]);

        return redirect()->route('dashboard')->with('status', __('يمكنك تسجيل الأحلام الآن وتعميق ملفك لاحقاً.'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Legacy single-form onboarding kept as redirect for old links.
        return redirect()->route('onboarding.welcome');
    }

    private function syncLegacyProfileFields($user, string $key, mixed $value): void
    {
        $map = [
            'A01' => 'preferred_name',
            'A02' => 'age_range',
            'A03' => 'preferred_language',
            'A04' => 'country_of_origin',
            'A05' => 'country_of_residence',
            'B01' => 'emotional_state',
            'H01' => 'life_circumstances',
        ];

        if (! isset($map[$key])) {
            return;
        }

        $payload = [$map[$key] => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value];
        $user->profile()->updateOrCreate(['user_id' => $user->id], $payload);
    }
}
