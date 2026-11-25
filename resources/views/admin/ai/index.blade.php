<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('إعدادات الذكاء الاصطناعي (OpenRouter)') }}</h2></x-slot>
    <div class="ip-container space-y-6">
        <form method="POST" action="{{ route('admin.ai.update') }}" class="ip-card space-y-4 p-6">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="ip-label">{{ __('المزود الافتراضي') }}</label>
                    <select name="provider" class="ip-input">
                        <option value="openrouter" @selected(($settings['provider'] ?? '') === 'openrouter')>OpenRouter</option>
                        <option value="openai" @selected(($settings['provider'] ?? '') === 'openai')>OpenAI (مباشر)</option>
                    </select>
                </div>
                <div>
                    <label class="ip-label">{{ __('نموذج التحليل (ChatGPT عبر OpenRouter)') }}</label>
                    <select name="analysis_model" class="ip-input">
                        @foreach($models as $model)
                            <option value="{{ $model }}" @selected(($settings['analysis_model'] ?? '') === $model)>{{ $model }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="ip-label">{{ __('نموذج المحادثة') }}</label>
                    <input name="chat_model" class="ip-input" value="{{ $settings['chat_model'] ?? 'openai/gpt-4o-mini' }}">
                </div>
                <div>
                    <label class="ip-label">Temperature</label>
                    <input type="number" step="0.1" min="0" max="2" name="temperature" class="ip-input" value="{{ $settings['temperature'] ?? 0.4 }}">
                </div>
                <div>
                    <label class="ip-label">Max tokens</label>
                    <input type="number" name="max_tokens" class="ip-input" value="{{ $settings['max_tokens'] ?? 4096 }}">
                </div>
                <div>
                    <label class="ip-label">OpenRouter Base URL</label>
                    <input name="openrouter_base_url" class="ip-input" value="{{ $settings['openrouter_base_url'] ?? 'https://openrouter.ai/api/v1' }}">
                </div>
                <div class="md:col-span-2">
                    <label class="ip-label">OPENROUTER_API_KEY {{ __('(اتركه فارغاً للإبقاء على القيمة الحالية / .env)') }}</label>
                    <input type="password" name="openrouter_api_key" class="ip-input" placeholder="{{ $config['api_key_set'] ? '•••••••• (set)' : 'sk-or-...' }}">
                </div>
                <div>
                    <label class="ip-label">Site URL</label>
                    <input name="openrouter_site_url" class="ip-input" value="{{ $settings['openrouter_site_url'] ?? config('app.url') }}">
                </div>
                <div>
                    <label class="ip-label">App name</label>
                    <input name="openrouter_app_name" class="ip-input" value="{{ $settings['openrouter_app_name'] ?? config('app.name') }}">
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="auto_analyze" value="1" @checked(($settings['auto_analyze'] ?? '1') === '1') class="rounded text-sage">
                {{ __('تحليل تلقائي عند حفظ الحلم (مرة أولى فقط، مع التخزين في قاعدة البيانات)') }}
            </label>
            <button class="ip-btn">{{ __('حفظ الإعدادات الافتراضية') }}</button>
            <p class="text-xs text-charcoal/60">{{ __('القيم في لوحة الإدارة تتجاوز .env عند التعيين. المفتاح الأساسي يُضبط أيضاً عبر OPENROUTER_API_KEY في .env') }}</p>
        </form>

        <div class="ip-card p-5 text-sm">
            <p>{{ __('المزود الفعّال') }}: {{ $config['provider'] }}</p>
            <p>{{ __('النموذج') }}: {{ $config['model'] }}</p>
            <p>API Key: {{ $config['api_key_set'] ? __('مُعد') : __('غير مُعد') }}</p>
        </div>

        <div class="ip-card p-5">
            <h3 class="font-semibold">{{ __('تحليلات فاشلة') }}</h3>
            @forelse($failedJobs as $job)
                <p class="mt-2 text-xs text-terracotta">#{{ $job->id }} — {{ \Illuminate\Support\Str::limit($job->error_message, 120) }}</p>
            @empty
                <p class="ip-muted mt-2">{{ __('لا يوجد') }}</p>
            @endforelse
        </div>

        <div class="ip-card p-5">
            <h3 class="font-semibold">{{ __('سجل الاستخدام') }}</h3>
            @foreach($usage as $row)
                <p class="mt-1 text-xs">{{ $row->created_at }} · {{ $row->provider }} · {{ $row->operation }} · {{ $row->total_tokens }} tokens · {{ $row->status }}</p>
            @endforeach
            {{ $usage->links() }}
        </div>
    </div>
</x-app-layout>
