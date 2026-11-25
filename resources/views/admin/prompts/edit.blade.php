<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold">{{ __('تعديل الموجه') }} — {{ $prompt->name }}</h2>
    </x-slot>
    <div class="ip-container max-w-4xl space-y-4">
        <div class="ip-card p-4 text-sm">
            <p class="font-semibold mb-2">{{ __('المتغيرات') }}</p>
            <div class="flex flex-wrap gap-2">
                @foreach($variables as $var => $desc)
                    <span class="ip-chip" title="{{ $desc }}">{{ $var }}</span>
                @endforeach
            </div>
        </div>
        <form method="POST" action="{{ route('admin.prompts.update', $prompt) }}" class="ip-card space-y-4 p-6">
            @csrf @method('PUT')
            <div><label class="ip-label">{{ __('الاسم') }}</label><input name="name" class="ip-input" value="{{ $prompt->name }}" required></div>
            <div><label class="ip-label">{{ __('الإصدار') }}</label><input name="version" class="ip-input" value="{{ $prompt->version }}" required></div>
            <div>
                <label class="ip-label">{{ __('موجه النظام (System)') }}</label>
                <textarea name="system_prompt" rows="16" class="ip-input font-mono text-sm" required>{{ $prompt->system_prompt }}</textarea>
            </div>
            <div>
                <label class="ip-label">{{ __('قالب المستخدم (User) مع متغيرات الشخصية') }}</label>
                <textarea name="user_prompt_template" rows="14" class="ip-input font-mono text-sm">{{ $prompt->user_prompt_template }}</textarea>
            </div>
            <label class="text-sm"><input type="checkbox" name="is_active" value="1" @checked($prompt->is_active)> {{ __('تفعيل هذا الموجه') }}</label>
            <label class="text-sm"><input type="checkbox" name="save_as_new_version" value="1"> {{ __('حفظ كتطوير/نسخة جديدة (يُبقي النسخة القديمة)') }}</label>
            <div class="flex gap-3">
                <button class="ip-btn">{{ __('حفظ التعديلات') }}</button>
                <a href="{{ route('admin.prompts.index') }}" class="ip-btn-secondary">{{ __('رجوع') }}</a>
            </div>
        </form>
    </div>
</x-app-layout>
