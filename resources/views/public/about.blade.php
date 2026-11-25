@extends('layouts.public')

@section('title', __('عن InnerPath'))

@section('content')
<section class="ip-page-hero min-h-[52dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset('images/brand/still-water.jpg') }}" alt="" class="object-[center_40%]" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[52dvh] flex-col justify-end pb-14 pt-24">
        <div class="ip-fade-up max-w-reading">
            <p class="text-sm font-medium text-ink-muted">{{ __('عن المنصة') }}</p>
            <h1 class="mt-2 font-display text-h1 text-ink">{{ __('مساحة هادئة لاكتشاف الذات') }}</h1>
        </div>
    </div>
</section>

<section class="ip-atmosphere border-t border-line">
    <div class="ip-container grid items-start gap-12 py-16 lg:grid-cols-[1.1fr_0.9fr] lg:py-20">
        <div class="ip-prose-calm space-y-5">
            <p>{{ __('InnerPath منصة ليوميات الأحلام والتأمل والنمو الشخصي. نؤمن أن الأحلام نافذة لطيفة على تجربتك الإنسانية — لا تشخيص طبي، ولا تنبؤ بالمستقبل، ولا دردشة عامة.') }}</p>
            <p>{{ __('صُممت التجربة لتقليل الجهد الذهني: واجهة هادئة، لغة واضحة، وخطوات صغيرة يمكنك العودة إليها يومياً دون ضغط.') }}</p>
            <p>{{ __('هدفك معنا ليس «الإجابة الكاملة»، بل فهم أعمق لنفسك — تدريجياً، وبوتيرة تناسبك.') }}</p>
            <div class="flex flex-wrap gap-3 pt-2">
                <a href="{{ route('register') }}" class="ip-btn">{{ __('ابدأ رحلتك') }}</a>
                <a href="{{ route('how-it-works') }}" class="ip-btn-secondary">{{ __('كيف تعمل') }}</a>
            </div>
        </div>
        <x-innerpath.visual
            src="{{ asset('images/brand/inner-world.jpg') }}"
            :alt="__('تعبير بصري هادئ عن العالم الداخلي')"
            ratio="4/3"
        />
    </div>
</section>
@endsection
