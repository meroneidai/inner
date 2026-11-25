<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold">{{ $section->localizedName() }}</h2>
        <a href="{{ route('inner-world.index') }}" class="text-sm text-sage-dark">← {{ __('عودة') }}</a>
    </x-slot>
    <div class="ip-container max-w-3xl space-y-4">
        <form method="POST" action="{{ route('inner-world.section.update', $section->slug) }}" class="space-y-4">
            @csrf
            @foreach($visible as $question)
                @php $ans = $answers[$question->id] ?? null; @endphp
                <x-profile-question-field :question="$question" :value="$ans?->decodedValue()" />
            @endforeach
            <button class="ip-btn">{{ __('حفظ القسم') }}</button>
        </form>

        <div class="space-y-2">
            <h3 class="text-sm font-semibold">{{ __('حذف إجابات') }}</h3>
            @foreach($visible as $question)
                @if(isset($answers[$question->id]))
                    <form method="POST" action="{{ route('inner-world.answers.delete', $question) }}" class="inline">
                        @csrf @method('DELETE')
                        <button class="text-xs text-terracotta">{{ __('حذف') }}: {{ $question->question_key }}</button>
                    </form>
                @endif
            @endforeach
        </div>
    </div>
</x-app-layout>
