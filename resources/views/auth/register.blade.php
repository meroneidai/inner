<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-h3 text-ink">{{ __('ابدأ رحلتك') }}</h1>
        <p class="ip-muted mt-1">{{ __('أنشئ حساباً بهدوء — بلا تعقيد.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-text-input
                id="name"
                type="text"
                name="name"
                :label="__('الاسم')"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-text-input
                id="email"
                type="email"
                name="email"
                :label="__('البريد الإلكتروني')"
                :value="old('email')"
                required
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-text-input
                id="password"
                type="password"
                name="password"
                :label="__('كلمة المرور')"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                :label="__('تأكيد كلمة المرور')"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('إنشاء حساب') }}
        </x-primary-button>

        <p class="text-center text-sm text-ink-secondary">
            {{ __('لديك حساب؟') }}
            <a href="{{ route('login') }}" class="font-medium text-primary-dark hover:underline">{{ __('دخول') }}</a>
        </p>
    </form>
</x-guest-layout>
