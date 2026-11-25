<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('الخصوصية والبيانات') }}</h2></x-slot>
    <div class="ip-container max-w-2xl space-y-4">
        <div class="ip-card p-6">
            <p class="text-sm leading-7">{{ config('innerpath.disclaimer') }}</p>
            <a href="{{ route('settings.export') }}" class="ip-btn mt-4">{{ __('تصدير بياناتي') }}</a>
        </div>
        <form method="POST" action="{{ route('settings.account.destroy') }}" class="ip-card space-y-3 p-6" onsubmit="return confirm('{{ __('حذف الحساب نهائياً؟') }}')">
            @csrf @method('DELETE')
            <h3 class="font-semibold text-terracotta">{{ __('حذف الحساب') }}</h3>
            <x-floating-input type="password" name="password" :label="__('كلمة المرور')" required autocomplete="current-password" />
            <button class="ip-btn-accent">{{ __('حذف نهائي') }}</button>
        </form>
    </div>
</x-app-layout>
