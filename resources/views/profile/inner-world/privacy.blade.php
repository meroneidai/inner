<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('خصوصية الملف الداخلي') }}</h2></x-slot>
    <div class="ip-container max-w-2xl space-y-4">
        <form method="POST" action="{{ route('inner-world.privacy.update') }}" class="ip-card space-y-4 p-6">
            @csrf
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="analytical_use" value="1" @checked($analytical) class="rounded text-sage">
                {{ __('السماح باستخدام إجابات غير حسّاسة في تحليل الأحلام') }}
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="ai_training" value="1" @checked($training) class="rounded text-sage">
                {{ __('موافقة منفصلة على التدريب المجهّل (اختياري)') }}
            </label>
            <button class="ip-btn">{{ __('حفظ') }}</button>
        </form>
        <a href="{{ route('inner-world.export') }}" class="ip-btn-secondary">{{ __('تصدير ملف العالم الداخلي') }}</a>
        <p class="text-xs text-charcoal/60">{{ config('innerpath.disclaimer') }}</p>
    </div>
</x-app-layout>
