@php
    $is = fn (string $pattern) => request()->routeIs($pattern);
@endphp
<aside class="ip-sidebar">
    <a href="{{ route('dashboard') }}" class="mb-6 px-2" aria-label="InnerPath">
        <x-innerpath.logo />
    </a>

    <nav class="flex flex-1 flex-col gap-0.5 overflow-y-auto">
        <a href="{{ route('dashboard') }}" class="ip-nav-item {{ $is('dashboard') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-home class="size-4 shrink-0" /> {{ __('الرئيسية') }}
        </a>
        <a href="{{ route('dreams.index') }}" class="ip-nav-item {{ ($is('dreams.*') && ! $is('dreams.create')) ? 'ip-nav-item-active' : '' }}">
            <x-lucide-book-open class="size-4 shrink-0" /> {{ __('اليوميات') }}
        </a>
        <a href="{{ route('dreams.create') }}" class="ip-nav-item {{ $is('dreams.create') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-plus class="size-4 shrink-0" /> {{ __('حلم جديد') }}
        </a>
        <a href="{{ route('reports.weekly') }}" class="ip-nav-item {{ $is('reports.*') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-calendar-days class="size-4 shrink-0" /> {{ __('التقارير') }}
        </a>
        <a href="{{ route('growth.index') }}" class="ip-nav-item {{ $is('growth.*') || $is('reflections.*') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-sprout class="size-4 shrink-0" /> {{ __('النمو') }}
        </a>
        <a href="{{ route('conversations.index') }}" class="ip-nav-item {{ $is('conversations.*') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-message-circle class="size-4 shrink-0" /> {{ __('حوار') }}
        </a>
        <a href="{{ route('inner-world.index') }}" class="ip-nav-item {{ $is('inner-world.*') || $is('onboarding.*') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-user-round class="size-4 shrink-0" /> {{ __('عالمي الداخلي') }}
        </a>
        <a href="{{ route('notifications.index') }}" class="ip-nav-item {{ $is('notifications.*') ? 'ip-nav-item-active' : '' }}">
            <x-lucide-bell class="size-4 shrink-0" /> {{ __('الإشعارات') }}
            @if(($unreadNotificationsCount ?? 0) > 0)
                <span class="ms-auto inline-flex min-w-5 items-center justify-center rounded-full bg-primary px-1.5 text-[10px] font-semibold text-white">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
            @endif
        </a>
        @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="ip-nav-item {{ $is('admin.*') ? 'ip-nav-item-active' : '' }}">
                <x-lucide-settings class="size-4 shrink-0" /> {{ __('إدارة') }}
            </a>
        @endif
    </nav>

    <div class="mt-4 space-y-0.5 border-t border-line pt-4">
        <a href="{{ route('settings.edit') }}" class="ip-nav-item">
            <x-lucide-sliders-horizontal class="size-4 shrink-0" /> {{ Auth::user()->name }}
        </a>
        <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="ip-nav-item">
            <x-lucide-languages class="size-4 shrink-0" /> {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="ip-nav-item w-full text-start">
                <x-lucide-log-out class="size-4 shrink-0" /> {{ __('خروج') }}
            </button>
        </form>
    </div>
</aside>
