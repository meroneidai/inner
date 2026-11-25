<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('إدارة موجه تحليل الأحلام') }}</h2></x-slot>
    <div class="ip-container space-y-6">
        <div class="ip-card p-5 text-sm">
            <h3 class="font-semibold">{{ __('المتغيرات المتاحة في القالب') }}</h3>
            <p class="ip-muted mt-1">{{ __('استخدم هذه المتغيرات لفهم الشخصية والسياق دون إرسال الملف كاملاً.') }}</p>
            <div class="mt-3 grid gap-2 md:grid-cols-2">
                @foreach($variables as $var => $desc)
                    <div class="rounded-lg bg-ivory px-3 py-2"><code class="text-sage-dark">{{ $var }}</code> — {{ $desc }}</div>
                @endforeach
            </div>
        </div>

        @if($active)
            <div class="ip-card p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="font-semibold">{{ __('الموجه النشط') }}: {{ $active->name }} ({{ $active->version }})</p>
                        <p class="ip-muted">{{ \Illuminate\Support\Str::limit($active->system_prompt, 160) }}</p>
                    </div>
                    <a href="{{ route('admin.prompts.edit', $active) }}" class="ip-btn">{{ __('تعديل / تطوير') }}</a>
                </div>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-2">
            <form method="POST" action="{{ route('admin.prompts.store') }}" class="ip-card space-y-3 p-5">
                @csrf
                <h3 class="font-semibold">{{ __('نسخة جديدة') }}</h3>
                <input name="name" class="ip-input" placeholder="name" required>
                <input name="slug" class="ip-input" placeholder="slug" required>
                <input name="version" class="ip-input" placeholder="1.1.0" required>
                <textarea name="system_prompt" rows="10" class="ip-input" required placeholder="system prompt"></textarea>
                <textarea name="user_prompt_template" rows="8" class="ip-input" placeholder="user template with {{dream_text}} ..."></textarea>
                <label class="text-sm"><input type="checkbox" name="is_active" value="1"> {{ __('تفعيل فوراً') }}</label>
                <button class="ip-btn">{{ __('حفظ') }}</button>
            </form>
            <div class="space-y-3">
                @foreach($prompts as $prompt)
                    <div class="ip-card p-4">
                        <p class="font-medium">{{ $prompt->name }} ({{ $prompt->version }}) @if($prompt->is_active)<span class="ip-chip">active</span>@endif</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <a href="{{ route('admin.prompts.edit', $prompt) }}" class="ip-btn-secondary">{{ __('تعديل') }}</a>
                            @unless($prompt->is_active)
                                <form method="POST" action="{{ route('admin.prompts.activate', $prompt) }}">@csrf<button class="ip-chip">{{ __('تفعيل') }}</button></form>
                            @endunless
                        </div>
                    </div>
                @endforeach
                {{ $prompts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
