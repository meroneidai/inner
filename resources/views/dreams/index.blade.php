<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="ip-section-title">{{ __('يوميات الأحلام') }}</h1>
                <p class="ip-muted mt-1">{{ __('كل أحلامك في مكان هادئ وواضح.') }}</p>
            </div>
            <a href="{{ route('dreams.create') }}" class="ip-btn">{{ __('حلم جديد') }}</a>
        </div>
    </x-slot>

    <div class="ip-container space-y-5">
        <form class="ip-panel grid gap-2 md:grid-cols-[1fr_auto_auto_auto]">
            <input name="q" value="{{ $filters['q'] ?? '' }}" class="ip-input" placeholder="{{ __('ابحث في النص أو المزاج…') }}">
            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="ip-input" aria-label="{{ __('من') }}">
            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="ip-input" aria-label="{{ __('إلى') }}">
            <button class="ip-btn">{{ __('تصفية') }}</button>
        </form>

        <div class="grid gap-3">
            @forelse($dreams as $dream)
                @php
                    $statusLabel = match ($dream->processing_status) {
                        'completed', 'analyzed' => __('مكتمل'),
                        'analyzing', 'processing' => __('قيد التحليل'),
                        'failed' => __('تعذّر التحليل'),
                        default => __('مسودة'),
                    };
                @endphp
                <a href="{{ route('dreams.show', $dream) }}" class="ip-card ip-card-hover block p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-display text-lg font-semibold text-ink">{{ $dream->title ?: __('حلم بدون عنوان') }}</h3>
                                <span class="ip-chip">{{ $statusLabel }}</span>
                            </div>
                            <p class="ip-muted mt-1">{{ $dream->dream_date?->translatedFormat('d M Y') }}</p>
                            <p class="mt-2 line-clamp-2 text-sm leading-7 text-ink-secondary">{{ \Illuminate\Support\Str::limit($dream->displayText(), 160) }}</p>
                        </div>
                        @if($dream->mood_upon_waking)
                            <span class="ip-chip shrink-0">{{ $dream->mood_upon_waking }}</span>
                        @endif
                    </div>
                </a>
            @empty
                <x-innerpath.empty-state
                    image="images/brand/journal.jpg"
                    :title="__('لا توجد أحلام بعد.')"
                    :body="__('ابدأ بأول حلم — التحليل يتولى الباقي.')"
                    :action-href="route('dreams.create')"
                    :action-label="__('ابدأ أول حلم')"
                />
            @endforelse
        </div>

        <div>{{ $dreams->links() }}</div>
    </div>
</x-app-layout>
