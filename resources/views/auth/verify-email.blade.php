<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-h3 text-ink">{{ __('تأكيد البريد') }}</h1>
        <p class="ip-muted mt-1">
            {{ __('أدخل الرمز المكوّن من 6 أرقام الذي أرسلناه إلى') }}
            <span class="font-medium text-ink">{{ auth()->user()->email }}</span>
        </p>
    </div>

    @if (session('status') === 'verification-code-sent')
        <div class="mb-4 rounded-control bg-sage-soft px-4 py-3 text-sm text-ink">
            {{ __('تم إرسال رمز تأكيد جديد إلى بريدك.') }}
        </div>
    @endif

    <x-auth-session-status class="mb-4" :status="session('status') === 'verification-code-sent' ? null : session('status')" />

    <form method="POST" action="{{ route('verification.verify') }}" class="space-y-4">
        @csrf

        <div>
            <x-text-input
                id="code"
                type="text"
                name="code"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="6"
                autocomplete="one-time-code"
                :label="__('رمز التأكيد')"
                :value="old('code')"
                required
                autofocus
                class="tracking-[0.35em] text-center text-lg"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            {{ __('تفعيل الحساب والدخول') }}
        </x-primary-button>
    </form>

    <div class="mt-6 flex flex-col items-center gap-3 text-sm">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="font-medium text-primary-dark hover:underline">
                {{ __('إعادة إرسال الرمز') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-ink-secondary hover:text-ink">
                {{ __('تسجيل الخروج') }}
            </button>
        </form>
    </div>
</x-guest-layout>
