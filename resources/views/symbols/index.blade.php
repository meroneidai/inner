<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('مستكشف الرموز') }}</h2></x-slot>
    <div class="ip-container space-y-4">
        @if(($result['status'] ?? '') === 'insufficient_data')
            <div class="ip-card p-6">{{ __('بيانات غير كافية لاكتشاف الأنماط بعد.') }}</div>
        @endif
        @foreach($result['patterns'] ?? [] as $pattern)
            <div class="ip-card p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold">{{ $pattern['symbol'] }}</h3>
                    <span class="ip-chip">{{ __('درجة البروز') }}: {{ $pattern['pattern_score'] }}</span>
                </div>
                <p class="ip-muted mt-2">{{ $pattern['note'] }}</p>
                <p class="mt-2 text-sm">{{ __('الظهور') }}: {{ $pattern['occurrences'] }} · {{ $pattern['first_appearance'] }} → {{ $pattern['last_appearance'] }}</p>
            </div>
        @endforeach
    </div>
</x-app-layout>
