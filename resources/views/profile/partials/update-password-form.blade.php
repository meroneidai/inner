<section>
    <header>
        <h2 class="font-display text-lg font-medium text-ink">
            {{ __('Update Password') }}
        </h2>

        <p class="ip-muted mt-1">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        <div>
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                :label="__('Current Password')"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                :label="__('New Password')"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                :label="__('Confirm Password')"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-ink-secondary"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
