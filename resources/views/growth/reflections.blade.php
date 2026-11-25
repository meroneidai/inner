<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('تمارين التأمل') }}</h2></x-slot>
    <div class="ip-container grid gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            @foreach($pendingQuestions as $item)
                <form method="POST" action="{{ route('reflections.store') }}" class="ip-card space-y-3 p-5">
                    @csrf
                    <input type="hidden" name="dream_analysis_id" value="{{ $item['analysis_id'] }}">
                    <input type="hidden" name="question" value="{{ $item['question'] }}">
                    <p class="text-sm font-medium">{{ $item['question'] }}</p>
                    <x-floating-input type="textarea" name="answer" :label="__('إجابتك')" :rows="3" required />
                    <button class="ip-btn-secondary">{{ __('حفظ الإجابة') }}</button>
                </form>
            @endforeach
        </div>
        <div class="space-y-3">
            @foreach($answers as $answer)
                <div class="ip-card p-4 text-sm">
                    <p class="font-medium">{{ $answer->question }}</p>
                    <p class="mt-2 text-charcoal/80">{{ $answer->answer }}</p>
                </div>
            @endforeach
            {{ $answers->links() }}
        </div>
    </div>
</x-app-layout>
