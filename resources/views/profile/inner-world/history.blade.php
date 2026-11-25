<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('سجل الملف') }}</h2></x-slot>
    <div class="ip-container grid gap-6 lg:grid-cols-2">
        <div class="space-y-3">
            <h3 class="font-semibold">{{ __('تغييرات الإجابات') }}</h3>
            @foreach($answerHistory as $row)
                <div class="ip-card p-4 text-sm">
                    <p class="font-medium">{{ $row->question?->text() }}</p>
                    <p class="ip-muted">{{ $row->changed_at?->toDateTimeString() }} · {{ $row->change_source }}</p>
                    <p class="mt-2">{{ \Illuminate\Support\Str::limit((string) $row->previous_value, 80) }} → {{ \Illuminate\Support\Str::limit((string) $row->new_value, 80) }}</p>
                </div>
            @endforeach
        </div>
        <div class="space-y-3">
            <h3 class="font-semibold">{{ __('الملاحظات') }}</h3>
            @foreach($observations as $obs)
                <div class="ip-card p-4 text-sm">
                    <span class="ip-chip">{{ $obs->source_type }} / {{ $obs->status }}</span>
                    <p class="mt-2">{{ $obs->observation }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
