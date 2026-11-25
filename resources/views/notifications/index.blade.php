<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="ip-section-title">{{ __('الإشعارات') }}</h1>
                <p class="ip-muted mt-1">{{ __('عناية، تذكيرات، وتحليلات جاهزة.') }}</p>
            </div>
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button class="ip-btn-secondary">{{ __('تعليم الكل كمقروء') }}</button>
            </form>
        </div>
    </x-slot>

    <div class="ip-container max-w-3xl space-y-3">
        @forelse($notifications as $notification)
            @php $data = $notification->data; @endphp
            <article class="ip-card flex items-start gap-3 p-4 {{ $notification->read_at ? 'opacity-75' : 'border-primary/25 bg-primary-soft/30' }}">
                <span class="ip-icon-box" aria-hidden="true">
                    <x-lucide-bell class="size-4" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-semibold text-ink">{{ $data['title'] ?? __('إشعار') }}</h3>
                        <span class="text-xs text-ink-muted">{{ $notification->created_at?->diffForHumans() }}</span>
                    </div>
                    <p class="mt-1 text-sm leading-7 text-ink-secondary text-pretty">{{ $data['body'] ?? '' }}</p>
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="mt-3">
                        @csrf
                        <button class="ip-chip">{{ $notification->read_at ? __('فتح') : __('قراءة وفتح') }}</button>
                    </form>
                </div>
            </article>
        @empty
            <x-innerpath.empty-state
                image="images/brand/still-water.jpg"
                :title="__('مساحتك هادئة')"
                :body="__('لا إشعارات حالياً.')"
            />
        @endforelse

        <div>{{ $notifications->links() }}</div>
    </div>
</x-app-layout>
