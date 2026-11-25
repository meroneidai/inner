<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-h3 text-ink">{{ __('تسجيل الدخول') }}</h1>
        <p class="ip-muted mt-1">{{ __('مرحباً بعودتك إلى مساحتك الخاصة.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-text-input
                id="email"
                type="email"
                name="email"
                :label="__('البريد الإلكتروني')"
                :value="old('email')"
                required
                autofocus
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
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="rounded-control border-line text-primary shadow-none focus:ring-primary/30" name="remember">
                <span class="text-sm text-ink-secondary">{{ __('تذكرني') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-ink-secondary hover:text-ink" href="{{ route('password.request') }}">
                    {{ __('نسيت كلمة المرور؟') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full">
            {{ __('دخول') }}
        </x-primary-button>

        <p class="text-center text-sm text-ink-secondary">
            {{ __('ليس لديك حساب؟') }}
            <a href="{{ route('register') }}" class="font-medium text-primary-dark hover:underline">{{ __('إنشاء حساب') }}</a>
        </p>
    </form>
</x-guest-layout>
