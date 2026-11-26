@php
    $oldCode = preg_replace('/\D+/', '', (string) old('code', '')) ?? '';
    $oldCode = substr($oldCode, 0, 6);
@endphp

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
            {{ __('تم إرسال رمز تأكيد جديد إلى بريدك. الرمز السابق لم يعد صالحاً.') }}
        </div>
    @endif

    <x-auth-session-status class="mb-4" :status="session('status') === 'verification-code-sent' ? null : session('status')" />

    <form
        method="POST"
        action="{{ route('verification.verify') }}"
        class="space-y-5"
        x-data="otpCodeInput(6, @js($oldCode))"
        x-ref="form"
    >
        @csrf
        <input type="hidden" name="code" :value="code">

        <div>
            <p class="mb-3 text-center text-sm font-medium text-ink-secondary">{{ __('رمز التأكيد') }}</p>

            <div class="ip-otp" dir="ltr" @paste="onPaste($event)">
                @for ($i = 0; $i < 6; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="1"
                        autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                        class="ip-otp-digit"
                        x-ref="digit{{ $i }}"
                        x-model="digits[{{ $i }}]"
                        @input="onInput({{ $i }}, $event)"
                        @keydown="onKeydown({{ $i }}, $event)"
                        @focus="$event.target.select()"
                        aria-label="{{ __('الرقم :n', ['n' => $i + 1]) }}"
                        @if ($i === 0) autofocus @endif
                    >
                @endfor
            </div>

            <x-input-error :messages="$errors->get('code')" class="mt-3 text-center" />
        </div>

        <x-primary-button class="w-full" x-bind:disabled="!isComplete || submitting">
            {{ __('تفعيل الحساب والدخول') }}
        </x-primary-button>
    </form>

    <div
        class="mt-6 flex flex-col items-center gap-3 text-sm"
        x-data="{
            cooldown: {{ session('status') === 'verification-code-sent' ? 30 : 0 }},
            timer: null,
            init() {
                if (this.cooldown > 0) {
                    this.timer = setInterval(() => {
                        this.cooldown -= 1;
                        if (this.cooldown <= 0) {
                            clearInterval(this.timer);
                        }
                    }, 1000);
                }
            },
        }"
    >
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button
                type="submit"
                class="font-medium text-primary-dark hover:underline disabled:cursor-not-allowed disabled:opacity-50 disabled:no-underline"
                :disabled="cooldown > 0"
            >
                <span x-show="cooldown <= 0">{{ __('إعادة إرسال الرمز') }}</span>
                <span x-show="cooldown > 0" x-cloak x-text="'{{ __('إعادة الإرسال بعد') }} ' + cooldown + 's'"></span>
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
