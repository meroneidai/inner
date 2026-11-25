<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="ip-section-title">{{ __('التقارير الأسبوعية') }}</h1>
                <p class="ip-muted mt-1">{{ __('ملخص هادئ لأنماطك واكتشافاتك.') }}</p>
            </div>
            <form method="POST" action="{{ route('reports.weekly.generate') }}">@csrf<button class="ip-btn">{{ __('إنشاء تقرير هذا الأسبوع') }}</button></form>
        </div>
    </x-slot>
    <div class="ip-container space-y-4">
        <div class="flex gap-2">
            <span class="ip-chip border-primary/30 bg-primary-soft text-primary-dark">{{ __('أسبوعي') }}</span>
            <a href="{{ route('reports.monthly') }}" class="ip-chip">{{ __('شهري') }}</a>
        </div>
        @forelse($reports as $report)
            <a href="{{ route('reports.weekly.show', $report) }}" class="ip-card ip-card-hover block p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="font-display text-lg font-semibold text-ink">{{ $report->week_start?->toDateString() }} → {{ $report->week_end?->toDateString() }}</p>
                    <span class="ip-chip">{{ $report->status }} · {{ $report->dream_count }} {{ __('أحلام') }}</span>
                </div>
                <p class="mt-2 text-sm leading-7 text-ink-secondary">{{ \Illuminate\Support\Str::limit($report->summary, 140) }}</p>
            </a>
        @empty
            <x-innerpath.empty-state
                image="images/brand/still-water.jpg"
                :title="__('لا توجد تقارير بعد')"
                :body="__('أنشئ تقرير هذا الأسبوع حين تكون جاهزاً.')"
            />
        @endforelse
        {{ $reports->links() }}
    </div>
</x-app-layout>
