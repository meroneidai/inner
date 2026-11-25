<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <h1 class="ip-section-title">{{ __('المقالات') }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.content.pillars.index') }}" class="ip-btn-secondary">{{ __('الركائز') }}</a>
                <a href="{{ route('admin.content.articles.create') }}" class="ip-btn">{{ __('مقال جديد') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="ip-container space-y-3">
        @foreach($articles as $article)
            <a href="{{ route('admin.content.articles.edit', $article) }}" class="ip-card ip-card-hover block p-4">
                <p class="font-medium text-ink">{{ $article->title }}</p>
                <p class="mt-1 text-xs text-ink-muted">{{ $article->pillar?->name }} · {{ $article->editorial_status }}</p>
            </a>
        @endforeach
        {{ $articles->links() }}
    </div>
</x-app-layout>
