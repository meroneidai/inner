<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-h1 text-ink">{{ __('عالمك الداخلي') }}</h2>
        <p class="ip-muted mt-1">{{ __('اختياري — اختيارات سريعة، بلا كتابة مرهقة.') }}</p>
    </x-slot>

    <div class="ip-container max-w-3xl space-y-6">
        <div class="relative overflow-hidden rounded-section border border-line">
            <img src="{{ asset('images/brand/doorway-light.jpg') }}" alt="" class="absolute inset-0 size-full object-cover opacity-40" loading="lazy" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-t from-surface via-surface/85 to-surface/55"></div>
            <div class="relative space-y-4 p-6 sm:p-8">
                <p class="text-pretty leading-8 text-ink-secondary">{{ __('يمكنك استخدام المنصة فوراً. التحليل يعمل على نص الحلم، ويصبح أدق كلما أضفت سياقاً شخصياً عبر وسوم ودرجات واضحة.') }}</p>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-card border border-line bg-surface/90 p-4 text-center backdrop-blur-sm">
                        <span class="ip-icon-box mx-auto"><x-lucide-tags class="size-4" /></span>
                        <p class="mt-3 text-sm font-medium text-ink">{{ __('وسوم جاهزة') }}</p>
                    </div>
                    <div class="rounded-card border border-line bg-surface/90 p-4 text-center backdrop-blur-sm">
                        <span class="ip-icon-box mx-auto"><x-lucide-sliders-horizontal class="size-4" /></span>
                        <p class="mt-3 text-sm font-medium text-ink">{{ __('درجات 1–5') }}</p>
                    </div>
                    <div class="rounded-card border border-line bg-surface/90 p-4 text-center backdrop-blur-sm">
                        <span class="ip-icon-box mx-auto"><x-lucide-shield class="size-4" /></span>
                        <p class="mt-3 text-sm font-medium text-ink">{{ __('خصوصيتك أولاً') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('onboarding.stage1') }}" class="ip-btn">{{ __('ابدأ الملف القصير') }}</a>
            <a href="{{ route('dashboard') }}" class="ip-btn-secondary">{{ __('لاحقاً — لوحة التحكم') }}</a>
            <form method="POST" action="{{ route('onboarding.skip') }}">@csrf<button class="text-sm text-ink-muted hover:text-ink">{{ __('تخطي') }}</button></form>
        </div>
    </div>
</x-app-layout>
