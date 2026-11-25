<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalysisPromptVersion;
use App\Services\AI\PromptManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptController extends Controller
{
    public function index(): View
    {
        $prompts = AnalysisPromptVersion::query()->latest()->paginate(20);
        $active = AnalysisPromptVersion::query()->where('is_active', true)->first();

        return view('admin.prompts.index', [
            'prompts' => $prompts,
            'active' => $active,
            'variables' => PromptManager::availableVariables(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'unique:analysis_prompt_versions,slug'],
            'version' => ['required', 'string', 'max:40'],
            'system_prompt' => ['required', 'string'],
            'user_prompt_template' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_active')) {
            AnalysisPromptVersion::query()->update(['is_active' => false]);
        }

        AnalysisPromptVersion::query()->create([
            ...$data,
            'is_active' => $request->boolean('is_active'),
            'metadata' => [
                'variables' => array_keys(PromptManager::availableVariables()),
                'updated_by' => $request->user()->id,
            ],
        ]);

        return back()->with('status', __('تم حفظ نسخة الموجه.'));
    }

    public function edit(AnalysisPromptVersion $prompt): View
    {
        return view('admin.prompts.edit', [
            'prompt' => $prompt,
            'variables' => PromptManager::availableVariables(),
        ]);
    }

    public function update(Request $request, AnalysisPromptVersion $prompt): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'version' => ['required', 'string', 'max:40'],
            'system_prompt' => ['required', 'string'],
            'user_prompt_template' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'save_as_new_version' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('save_as_new_version')) {
            if ($request->boolean('is_active')) {
                AnalysisPromptVersion::query()->update(['is_active' => false]);
            }

            AnalysisPromptVersion::query()->create([
                'name' => $data['name'],
                'slug' => $prompt->slug.'-'.now()->format('YmdHis'),
                'version' => $data['version'],
                'system_prompt' => $data['system_prompt'],
                'user_prompt_template' => $data['user_prompt_template'] ?? null,
                'is_active' => $request->boolean('is_active'),
                'metadata' => [
                    'parent_id' => $prompt->id,
                    'variables' => array_keys(PromptManager::availableVariables()),
                    'updated_by' => $request->user()->id,
                ],
            ]);

            return redirect()->route('admin.prompts.index')->with('status', __('تم إنشاء نسخة مطوّرة من الموجه.'));
        }

        if ($request->boolean('is_active')) {
            AnalysisPromptVersion::query()->where('id', '!=', $prompt->id)->update(['is_active' => false]);
        }

        $prompt->update([
            'name' => $data['name'],
            'version' => $data['version'],
            'system_prompt' => $data['system_prompt'],
            'user_prompt_template' => $data['user_prompt_template'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'metadata' => array_merge($prompt->metadata ?? [], [
                'variables' => array_keys(PromptManager::availableVariables()),
                'updated_by' => $request->user()->id,
                'updated_at_iso' => now()->toIso8601String(),
            ]),
        ]);

        return redirect()->route('admin.prompts.index')->with('status', __('تم تحديث الموجه.'));
    }

    public function activate(AnalysisPromptVersion $prompt): RedirectResponse
    {
        AnalysisPromptVersion::query()->update(['is_active' => false]);
        $prompt->update(['is_active' => true]);

        return back()->with('status', __('تم تفعيل الموجه.'));
    }
}
