<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('تقرير أسبوعي') }}</h2></x-slot>
    <div class="ip-container space-y-4">
        <div class="ip-card p-6">
            <p class="ip-muted">{{ $report->week_start?->toDateString() }} — {{ $report->week_end?->toDateString() }}</p>
            <p class="mt-3 leading-8">{{ $report->summary }}</p>
        </div>
        <pre class="ip-card overflow-auto p-4 text-xs">{{ json_encode($report->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</x-app-layout>
