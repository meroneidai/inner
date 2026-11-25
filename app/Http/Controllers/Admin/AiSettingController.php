<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiUsageLog;
use App\Models\DreamAnalysis;
use App\Services\AI\AiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiSettingController extends Controller
{
    public function index(): View
    {
        $settings = AiSettings::all();

        return view('admin.ai.index', [
            'settings' => $settings,
            'config' => [
                'provider' => $settings['provider'] ?? 'openrouter',
                'model' => $settings['analysis_model'] ?? 'openai/gpt-4o',
                'transcription' => config('ai.transcription'),
                'api_key_set' => filled($settings['openrouter_api_key'] ?? null) || filled(config('ai.openrouter.api_key')),
            ],
            'failedJobs' => DreamAnalysis::query()->where('status', 'failed')->latest()->limit(20)->get(),
            'usage' => AiUsageLog::query()->latest()->paginate(30),
            'models' => [
                'openai/gpt-4o',
                'openai/gpt-4o-mini',
                'openai/gpt-4.1',
                'openai/gpt-4.1-mini',
                'anthropic/claude-3.5-sonnet',
                'google/gemini-2.0-flash-001',
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'provider' => ['required', 'in:openrouter,openai'],
            'analysis_model' => ['required', 'string', 'max:120'],
            'chat_model' => ['nullable', 'string', 'max:120'],
            'temperature' => ['nullable', 'numeric', 'min:0', 'max:2'],
            'max_tokens' => ['nullable', 'integer', 'min:256', 'max:16000'],
            'openrouter_api_key' => ['nullable', 'string', 'max:500'],
            'openrouter_base_url' => ['nullable', 'url'],
            'openrouter_site_url' => ['nullable', 'url'],
            'openrouter_app_name' => ['nullable', 'string', 'max:120'],
            'auto_analyze' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'provider' => $data['provider'],
            'analysis_model' => $data['analysis_model'],
            'chat_model' => $data['chat_model'] ?? $data['analysis_model'],
            'temperature' => (string) ($data['temperature'] ?? 0.4),
            'max_tokens' => (string) ($data['max_tokens'] ?? 4096),
            'openrouter_base_url' => $data['openrouter_base_url'] ?? 'https://openrouter.ai/api/v1',
            'openrouter_site_url' => $data['openrouter_site_url'] ?? config('app.url'),
            'openrouter_app_name' => $data['openrouter_app_name'] ?? config('app.name'),
            'auto_analyze' => $request->boolean('auto_analyze') ? '1' : '0',
        ];

        if (filled($data['openrouter_api_key'] ?? null)) {
            $payload['openrouter_api_key'] = $data['openrouter_api_key'];
        }

        AiSettings::putMany($payload);

        return back()->with('status', __('تم حفظ إعدادات الذكاء الاصطناعي الافتراضية.'));
    }
}
