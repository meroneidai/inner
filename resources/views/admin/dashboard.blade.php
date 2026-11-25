<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('لوحة الإدارة') }}</h2></x-slot>
    <div class="ip-container space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="ip-card p-4"><p class="ip-muted">{{ __('المستخدمون') }}</p><p class="text-2xl font-semibold">{{ $usersCount }}</p></div>
            <div class="ip-card p-4"><p class="ip-muted">{{ __('الأحلام') }}</p><p class="text-2xl font-semibold">{{ $dreamsCount }}</p></div>
            <div class="ip-card p-4"><p class="ip-muted">{{ __('تحليلات فاشلة') }}</p><p class="text-2xl font-semibold">{{ $failedAnalyses }}</p></div>
            <div class="ip-card p-4"><p class="ip-muted">{{ __('التوكنات') }}</p><p class="text-2xl font-semibold">{{ $tokenUsage }}</p></div>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.users.index') }}" class="ip-btn-secondary">{{ __('المستخدمون') }}</a>
            <a href="{{ route('admin.prompts.index') }}" class="ip-btn-secondary">{{ __('الموجهات') }}</a>
            <a href="{{ route('admin.ai.index') }}" class="ip-btn-secondary">{{ __('إعدادات الذكاء الاصطناعي') }}</a>
            <a href="{{ route('admin.questions.index') }}" class="ip-btn-secondary">{{ __('بنك الأسئلة') }}</a>
            <a href="{{ route('admin.content.pillars.index') }}" class="ip-btn-secondary">{{ __('ركائز المحتوى') }}</a>
            <a href="{{ route('admin.content.articles.index') }}" class="ip-btn-secondary">{{ __('المقالات') }}</a>
        </div>
        <p class="text-xs text-charcoal/60">{{ __('المحتوى الخاص للأحلام غير معروض هنا افتراضياً.') }} · {{ __('Failed jobs') }}: {{ $queueFailed }}</p>
    </div>
</x-app-layout>
