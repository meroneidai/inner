<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfileAnswer;
use App\Models\ProfileQuestion;
use App\Models\ProfileQuestionTranslation;
use App\Models\ProfileSection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(): View
    {
        $sections = ProfileSection::query()->withCount('questions')->orderBy('display_order')->get();
        $questions = ProfileQuestion::query()->with(['section', 'translations'])->orderBy('display_order')->paginate(40);

        $users = User::query()->count();
        $completed = User::query()->whereNotNull('onboarding_completed_at')->count();

        return view('admin.questions.index', [
            'sections' => $sections,
            'questions' => $questions,
            'onboardingRate' => $users ? round(($completed / $users) * 100, 1) : 0,
            'answerCount' => ProfileAnswer::query()->where('is_draft', false)->count(),
        ]);
    }

    public function edit(ProfileQuestion $question): View
    {
        $question->load(['translations', 'section']);
        $sections = ProfileSection::query()->orderBy('display_order')->get();

        return view('admin.questions.edit', compact('question', 'sections'));
    }

    public function update(Request $request, ProfileQuestion $question): RedirectResponse
    {
        $data = $request->validate([
            'section_id' => ['required', 'exists:profile_sections,id'],
            'answer_type' => ['required', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'is_sensitive' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'include_in_stage1' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'ai_context_relevance' => ['nullable', 'string'],
            'question_text_ar' => ['required', 'string'],
            'question_text_en' => ['required', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
        ]);

        $question->update([
            'section_id' => $data['section_id'],
            'answer_type' => $data['answer_type'],
            'is_required' => $request->boolean('is_required'),
            'is_sensitive' => $request->boolean('is_sensitive'),
            'is_active' => $request->boolean('is_active'),
            'include_in_stage1' => $request->boolean('include_in_stage1'),
            'display_order' => $data['display_order'] ?? $question->display_order,
            'ai_context_relevance' => $data['ai_context_relevance'] ?? 'medium',
        ]);

        ProfileQuestionTranslation::query()->updateOrCreate(
            ['question_id' => $question->id, 'locale' => 'ar'],
            ['question_text' => $data['question_text_ar'], 'description' => $data['description_ar'] ?? null]
        );
        ProfileQuestionTranslation::query()->updateOrCreate(
            ['question_id' => $question->id, 'locale' => 'en'],
            ['question_text' => $data['question_text_en'], 'description' => $data['description_en'] ?? null]
        );

        return redirect()->route('admin.questions.index')->with('status', __('تم تحديث السؤال.'));
    }

    public function toggle(ProfileQuestion $question): RedirectResponse
    {
        $question->update(['is_active' => ! $question->is_active]);

        return back()->with('status', __('تم تبديل حالة السؤال.'));
    }
}
