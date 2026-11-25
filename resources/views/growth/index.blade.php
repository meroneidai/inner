<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="ip-section-title">{{ __('النمو الشخصي') }}</h1>
                <p class="ip-muted mt-1">{{ __('خطوات صغيرة واضحة — يومية وأسبوعية.') }}</p>
            </div>
            <a href="{{ route('reflections.index') }}" class="ip-btn-secondary">{{ __('تمارين التأمل') }}</a>
        </div>
    </x-slot>

    <div class="ip-container grid gap-6 lg:grid-cols-2">
        <section class="space-y-3">
            <h2 class="font-display text-h4 text-ink">{{ __('الموضوعات') }}</h2>
            @forelse($themes as $theme)
                <article class="ip-card ip-card-hover p-5">
                    <h3 class="font-medium text-ink">{{ $theme->title }}</h3>
                    <p class="ip-muted mt-1">{{ $theme->description }}</p>
                    <form method="POST" action="{{ route('growth.progress', $theme) }}" class="mt-4 space-y-2">
                        @csrf
                        <textarea name="note" class="ip-input" rows="2" placeholder="{{ __('ملاحظة تقدم قصيرة') }}" required></textarea>
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="range" name="progress_score" min="0" max="100" value="50" class="min-w-[10rem] flex-1 accent-primary" oninput="this.nextElementSibling.textContent=this.value+'%'">
                            <span class="text-sm font-semibold tabular-nums text-primary-dark">50%</span>
                            <button class="ip-btn-secondary py-2">{{ __('حفظ') }}</button>
                        </div>
                    </form>
                </article>
            @empty
                <x-innerpath.empty-state
                    image="images/brand/light-path.jpg"
                    :title="__('ابدأ مسار نموك')"
                    :body="__('ستظهر موضوعات النمو بعد التحليلات.')"
                />
            @endforelse
        </section>

        <section class="space-y-3">
            <h2 class="font-display text-h4 text-ink">{{ __('خطة العمل') }}</h2>
            @forelse($actions as $action)
                <article class="ip-card flex items-start justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <span class="ip-chip">{{ $action->cadenceLabel() }}</span>
                        <p class="mt-2 text-sm font-medium leading-7 text-ink">{{ $action->title }}</p>
                        @if($action->status === 'completed')
                            <p class="mt-1 text-xs text-success">{{ __('منجزة') }}</p>
                        @endif
                    </div>
                    @if($action->status === 'pending')
                        <div class="flex shrink-0 flex-col gap-2">
                            <form method="POST" action="{{ route('growth.actions.complete', $action) }}">@csrf<button class="ip-btn py-1.5 text-xs">{{ __('إنجاز') }}</button></form>
                            <form method="POST" action="{{ route('growth.actions.skip', $action) }}">@csrf<button class="text-xs text-danger">{{ __('تخطي') }}</button></form>
                        </div>
                    @endif
                </article>
            @empty
                <x-innerpath.empty-state
                    image="images/brand/still-water.jpg"
                    :title="__('مساحة هادئة')"
                    :body="__('لا مهام حالياً — استرح أو سجّل حلماً.')"
                />
            @endforelse
        </section>
    </div>
</x-app-layout>
