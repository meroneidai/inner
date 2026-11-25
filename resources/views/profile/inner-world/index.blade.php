<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold">{{ __('عالمي الداخلي') }}</h2>
                <p class="ip-muted">{{ __('اكتمال الملف') }}: {{ $progress }}% · {{ __('إجابات') }}: {{ $answered }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('onboarding.deeper') }}" class="ip-btn-secondary">{{ __('تعميق الأقسام') }}</a>
                <form method="POST" action="{{ route('inner-world.analyze') }}">@csrf<button class="ip-btn">{{ __('تحليل الملف') }}</button></form>
            </div>
        </div>
    </x-slot>

    <div class="ip-container space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="ip-card p-5">
                <p class="ip-muted">{{ __('الحالة') }}</p>
                <p class="mt-2 font-semibold">{{ $user->profile?->profile_status ?: 'not_started' }}</p>
            </div>
            <div class="ip-card p-5">
                <p class="ip-muted">{{ __('آخر مراجعة') }}</p>
                <p class="mt-2 font-semibold">{{ optional($user->profile?->profile_last_reviewed_at)->diffForHumans() ?: '—' }}</p>
            </div>
            <div class="ip-card p-5">
                <p class="ip-muted">{{ __('أحدث لقطة') }}</p>
                <p class="mt-2 text-sm">{{ \Illuminate\Support\Str::limit($latestSnapshot?->profile_summary, 90) ?: __('لا توجد بعد') }}</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($sections as $section)
                <a href="{{ route('inner-world.section', $section->slug) }}" class="ip-card block p-5 hover:ring-sage/40">
                    <h3 class="font-semibold">{{ $section->localizedName() }}</h3>
                    <p class="ip-muted mt-2 line-clamp-2">{{ $section->localizedDescription() }}</p>
                    <p class="mt-3 text-xs">{{ $section->questions->count() }} {{ __('سؤال') }}</p>
                </a>
            @endforeach
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div class="ip-card space-y-3 p-5">
                <h3 class="font-semibold">{{ __('فرضيات بانتظار التأكيد') }}</h3>
                @forelse($pendingObservations as $obs)
                    <div class="rounded-xl bg-ivory p-3 text-sm">
                        <p>{{ $obs->observation }}</p>
                        <div class="mt-2 flex gap-2">
                            <form method="POST" action="{{ route('inner-world.observations.confirm', $obs) }}">@csrf<button class="ip-chip">{{ __('تأكيد') }}</button></form>
                            <form method="POST" action="{{ route('inner-world.observations.reject', $obs) }}">@csrf<button class="ip-chip">{{ __('رفض') }}</button></form>
                        </div>
                    </div>
                @empty
                    <p class="ip-muted">{{ __('لا توجد فرضيات حالياً.') }}</p>
                @endforelse
            </div>

            <div class="ip-card space-y-3 p-5">
                <h3 class="font-semibold">{{ __('أهدافك') }}</h3>
                @foreach($user->profileGoals as $goal)
                    <p class="text-sm">• {{ $goal->title }}</p>
                @endforeach
                <form method="POST" action="{{ route('inner-world.goals.store') }}" class="space-y-2">
                    @csrf
                    <input name="title" class="ip-input" placeholder="{{ __('هدف جديد') }}" required>
                    <input name="development_dimension" class="ip-input" placeholder="{{ __('البعد') }}">
                    <button class="ip-btn-secondary">{{ __('إضافة') }}</button>
                </form>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 text-sm">
            <a href="{{ route('inner-world.history') }}" class="text-sage-dark">{{ __('السجل') }}</a>
            <a href="{{ route('inner-world.privacy') }}" class="text-sage-dark">{{ __('الخصوصية') }}</a>
            <a href="{{ route('inner-world.export') }}" class="text-sage-dark">{{ __('تصدير الملف') }}</a>
        </div>
    </div>
</x-app-layout>
