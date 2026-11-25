<div class="ip-topbar">
    <div class="flex h-14 items-center justify-between gap-3 px-4">
        <a href="{{ route('dashboard') }}" aria-label="InnerPath">
            <x-innerpath.logo variant="mark" class="[&>svg]:size-8" />
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('notifications.index') }}" class="relative inline-flex size-10 items-center justify-center rounded-control border border-line bg-surface text-ink-secondary" aria-label="{{ __('الإشعارات') }}">
                <x-lucide-bell class="size-4" />
                @if(($unreadNotificationsCount ?? 0) > 0)
                    <span class="absolute -top-1 -end-1 inline-flex size-4 items-center justify-center rounded-full bg-primary text-[9px] font-semibold text-white">{{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}</span>
                @endif
            </a>
            <a href="{{ route('dreams.create') }}" class="ip-btn py-2 text-xs">
                <x-lucide-plus class="size-3.5" /> {{ __('حلم') }}
            </a>
        </div>
    </div>
</div>
