<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h1 class="ip-section-title">{{ __('ركائز المحتوى') }}</h1>
            <a href="{{ route('admin.content.articles.index') }}" class="ip-btn-secondary">{{ __('المقالات') }}</a>
        </div>
    </x-slot>
    <div class="ip-container space-y-3">
        @foreach($pillars as $pillar)
            <a href="{{ route('admin.content.pillars.edit', $pillar) }}" class="ip-card ip-card-hover flex items-center justify-between gap-3 p-4">
                <div>
                    <p class="font-medium text-ink">{{ $pillar->name }}</p>
                    <p class="text-xs text-ink-muted">{{ $pillar->slug }} · {{ $pillar->status }}</p>
                </div>
                <span class="ip-chip">#{{ $pillar->sort_order }}</span>
            </a>
        @endforeach
        {{ $pillars->links() }}
    </div>
</x-app-layout>
