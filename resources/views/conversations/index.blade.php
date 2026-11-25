<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="ip-section-title">{{ __('حوار InnerPath') }}</h1>
                <p class="ip-muted mt-1">{{ __('نص أو صوت — مساحة آمنة للتفكير معاً.') }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('conversations.create', ['mode' => 'voice']) }}" class="ip-btn-secondary">{{ __('صوتي') }}</a>
                <a href="{{ route('conversations.create') }}" class="ip-btn">{{ __('حوار جديد') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="ip-container space-y-3">
        @forelse($conversations as $conversation)
            <a href="{{ route('conversations.show', $conversation) }}" class="ip-card ip-card-hover block p-5">
                <p class="font-display text-lg font-semibold text-ink">{{ $conversation->title }}</p>
                <p class="ip-muted mt-1">{{ $conversation->created_at?->diffForHumans() }}</p>
            </a>
        @empty
            <x-innerpath.empty-state
                image="images/brand/doorway-light.jpg"
                :title="__('ابدأ حواراً هادئاً')"
                :body="__('مساحة آمنة لطرح أسئلة المتابعة.')"
                :action-href="route('conversations.create', ['mode' => 'deep'])"
                :action-label="__('حوار عميق')"
            />
        @endforelse
        {{ $conversations->links() }}
    </div>
</x-app-layout>
