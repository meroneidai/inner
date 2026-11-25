<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('بنك الأسئلة') }}</h2></x-slot>
    <div class="ip-container space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="ip-card p-4"><p class="ip-muted">{{ __('معدل إكمال التهيئة') }}</p><p class="text-2xl font-semibold">{{ $onboardingRate }}%</p></div>
            <div class="ip-card p-4"><p class="ip-muted">{{ __('الإجابات') }}</p><p class="text-2xl font-semibold">{{ $answerCount }}</p></div>
            <div class="ip-card p-4"><p class="ip-muted">{{ __('الأقسام') }}</p><p class="text-2xl font-semibold">{{ $sections->count() }}</p></div>
        </div>
        <p class="text-xs text-charcoal/60">{{ __('التحليلات التجميعية لا تعرض محتوى الإجابات الخاصة.') }}</p>
        <div class="space-y-3">
            @foreach($questions as $question)
                <div class="ip-card flex flex-wrap items-center justify-between gap-3 p-4">
                    <div>
                        <p class="font-medium">{{ $question->question_key }} · {{ $question->text() }}</p>
                        <p class="ip-muted">{{ $question->section?->localizedName() }} · {{ $question->answer_type }} · {{ $question->is_active ? 'active' : 'inactive' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.questions.edit', $question) }}" class="ip-btn-secondary">{{ __('تعديل') }}</a>
                        <form method="POST" action="{{ route('admin.questions.toggle', $question) }}">@csrf<button class="ip-chip">{{ __('تبديل') }}</button></form>
                    </div>
                </div>
            @endforeach
            {{ $questions->links() }}
        </div>
    </div>
</x-app-layout>
