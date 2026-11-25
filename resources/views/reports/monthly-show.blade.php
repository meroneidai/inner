<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('تقرير شهري') }} {{ $report->year }}/{{ $report->month }}</h2></x-slot>
    <div class="ip-container space-y-4">
        <div class="ip-card p-6"><p class="leading-8">{{ $report->summary }}</p></div>
        <pre class="ip-card overflow-auto p-4 text-xs">{{ json_encode($report->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
</x-app-layout>
