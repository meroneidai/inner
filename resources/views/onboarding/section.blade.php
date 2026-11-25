<x-app-layout>
    <x-slot name="header">
        <nav class="mb-2 flex flex-wrap items-center gap-2 text-sm text-charcoal/60">
            <a href="{{ route('dashboard') }}" class="hover:text-sage-dark">🏠 {{ __('الرئيسية') }}</a>
            <span>›</span>
            <a href="{{ route('onboarding.welcome') }}" class="hover:text-sage-dark">🫀 {{ __('الملف') }}</a>
            <span>›</span>
            <span class="text-charcoal">{{ $section->localizedName() }}</span>
        </nav>
        <h2 class="font-display text-3xl font-semibold">{{ $section->localizedName() }}</h2>
        <p class="ip-muted mt-1">{{ $section->localizedDescription() }} · {{ __('اضغط وسم/درجة فقط — بلا كتابة إلزامية') }}</p>
    </x-slot>
    <div class="ip-container max-w-3xl">
        <form method="POST" action="{{ route('onboarding.section.save', $section->slug) }}" class="space-y-4">
            @csrf
            @foreach($visible as $question)
                <x-profile-question-field :question="$question" :value="$answers[$question->id] ?? null" />
            @endforeach
            <div class="sticky bottom-4 z-10 flex flex-wrap gap-3 rounded-2xl bg-white/90 p-3 shadow-soft ring-1 ring-beige/70 backdrop-blur">
                <button name="action" value="continue" class="ip-btn">{{ __('حفظ ومتابعة') }}</button>
                <button name="action" value="skip" class="ip-btn-secondary">{{ __('تخطي القسم') }}</button>
                <button name="action" value="exit" class="ip-btn-secondary">🏠 {{ __('حفظ والخروج') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
