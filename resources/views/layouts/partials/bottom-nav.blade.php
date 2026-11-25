@php
    $is = fn (string $pattern) => request()->routeIs($pattern);
@endphp
<nav class="ip-bottom-nav" aria-label="{{ __('التنقل السريع') }}">
    <div class="flex items-stretch px-1 pt-1">
        <a href="{{ route('dashboard') }}" class="ip-bottom-item {{ $is('dashboard') ? 'ip-bottom-item-active' : '' }}">
            <x-lucide-home class="size-5" />
            <span>{{ __('الرئيسية') }}</span>
        </a>
        <a href="{{ route('dreams.index') }}" class="ip-bottom-item {{ $is('dreams.index') || $is('dreams.show') || $is('dreams.edit') ? 'ip-bottom-item-active' : '' }}">
            <x-lucide-book-open class="size-5" />
            <span>{{ __('يوميات') }}</span>
        </a>
        <a href="{{ route('dreams.create') }}" class="ip-bottom-item {{ $is('dreams.create') ? 'ip-bottom-item-active' : '' }}">
            <span class="-mt-3 inline-flex size-11 items-center justify-center rounded-full bg-primary text-white shadow-soft">
                <x-lucide-plus class="size-5" />
            </span>
            <span>{{ __('جديد') }}</span>
        </a>
        <a href="{{ route('growth.index') }}" class="ip-bottom-item {{ $is('growth.*') ? 'ip-bottom-item-active' : '' }}">
            <x-lucide-sprout class="size-5" />
            <span>{{ __('نمو') }}</span>
        </a>
        <a href="{{ route('conversations.index') }}" class="ip-bottom-item {{ $is('conversations.*') ? 'ip-bottom-item-active' : '' }}">
            <x-lucide-message-circle class="size-5" />
            <span>{{ __('حوار') }}</span>
        </a>
    </div>
</nav>
