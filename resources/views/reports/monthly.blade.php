<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="ip-section-title">{{ __('التقارير الشهرية') }}</h1>
                <p class="ip-muted mt-1">{{ __('صورة أوسع لمسار نموك.') }}</p>
            </div>
            <form method="POST" action="{{ route('reports.monthly.generate') }}">@csrf<button class="ip-btn">{{ __('إنشاء تقرير هذا الشهر') }}</button></form>
        </div>
    </x-slot>
    <div class="ip-container space-y-4">
        <div class="flex gap-2">
            <a href="{{ route('reports.weekly') }}" class="ip-chip">{{ __('أسبوعي') }}</a>
            <span class="ip-chip border-primary/30 bg-primary-soft text-primary-dark">{{ __('شهري') }}</span>
        </div>
        @forelse($reports as $report)
            <a href="{{ route('reports.monthly.show', $report) }}" class="ip-card ip-card-hover block p-5">
                <p class="font-display text-lg font-semibold text-ink">{{ $report->year }}/{{ $report->month }}</p>
                <p class="ip-muted mt-1">{{ $report->status }} · {{ $report->dream_count }} {{ __('أحلام') }}</p>
            </a>
        @empty
            <x-innerpath.empty-state
                image="images/brand/still-water.jpg"
                :title="__('لا توجد تقارير شهرية بعد')"
                :body="__('ستظهر هنا بعد إنشاء أول تقرير.')"
            />
        @endforelse
        {{ $reports->links() }}
    </div>
</x-app-layout>
