<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold">{{ __('الملف الأولي') }}</h2>
        <p class="ip-muted">{{ __('التقدم') }}: {{ $progress }}%</p>
        <div class="mt-3 h-2 overflow-hidden rounded-full bg-beige">
            <div class="h-full bg-sage" style="width: {{ $progress }}%"></div>
        </div>
    </x-slot>

    <div class="ip-container max-w-3xl">
        <form method="POST" action="{{ route('onboarding.stage1.save') }}" class="space-y-4">
            @csrf
            @foreach($questions as $question)
                <x-profile-question-field :question="$question" :value="$answers[$question->id] ?? null" />
            @endforeach

            <div class="flex flex-wrap gap-3 pt-2">
                <button name="action" value="complete" class="ip-btn">{{ __('حفظ ومتابعة') }}</button>
                <button name="action" value="exit" class="ip-btn-secondary">{{ __('حفظ والخروج') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
