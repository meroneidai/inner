@extends('layouts.public')

@section('title', __('كيف تعمل') . ' — InnerPath')

@section('content')
<section class="ip-page-hero min-h-[48dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset('images/brand/journal.jpg') }}" alt="" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[48dvh] flex-col justify-end pb-14 pt-24">
        <div class="ip-fade-up max-w-reading">
            <p class="text-sm font-medium text-ink-muted">{{ __('كيف تعمل') }}</p>
            <h1 class="mt-2 font-display text-h1 text-ink">{{ __('مسار واضح، بدون تعقيد') }}</h1>
            <p class="mt-4 text-lg leading-relaxed text-ink-secondary">
                {{ __('من لحظة تذكّر الحلم إلى اكتشاف الأنماط — كل خطوة مصممة لتكون بسيطة ومريحة.') }}
            </p>
        </div>
    </div>
</section>

<section class="border-t border-line bg-canvas">
    <div class="ip-container py-14 lg:py-20">
        <div class="mb-10 grid gap-6 lg:grid-cols-2">
            <x-innerpath.visual
                src="{{ asset('images/brand/doorway-light.jpg') }}"
                :alt="__('باب مفتوح ونور دافئ')"
                ratio="4/3"
            />
            <x-innerpath.visual
                src="{{ asset('images/brand/light-path.jpg') }}"
                :alt="__('مسار ضوء هادئ')"
                ratio="4/3"
                class="hidden lg:block"
            />
        </div>

        <div class="space-y-5">
            @foreach([
                ['n' => '01', 'title' => __('سجّل أحلامك'), 'body' => __('اكتب ما تتذكره أو سجّل صوتاً قصيراً. لا حاجة لكمال النص — المهم أن تحفظ التجربة قبل أن تتلاشى.')],
                ['n' => '02', 'title' => __('استكشف المعنى'), 'body' => __('بعد توفر النص، يبدأ تحليل هادئ يربط الحلم بسياقك الشخصي ويقترح ملاحظات ومهاماً عملية صغيرة.')],
                ['n' => '03', 'title' => __('اربط تجاربك'), 'body' => __('أضف ذكريات ومشاعر وظروفاً حياتية عند رغبتك. الملف الداخلي يُبنى تدريجياً وبإرادتك فقط.')],
                ['n' => '04', 'title' => __('تابع النمو مع الوقت'), 'body' => __('تقارير أسبوعية وشهرية، وموضوعات نمو، وتأملات تساعدك على رؤية الاتجاه دون إرهاق.')],
            ] as $step)
                <article class="ip-card flex flex-col gap-4 p-6 transition duration-300 hover:border-line-strong sm:flex-row sm:items-start sm:gap-6 sm:p-8">
                    <span class="font-display text-h3 text-primary">{{ $step['n'] }}</span>
                    <div>
                        <h2 class="font-display text-h4 text-ink">{{ $step['title'] }}</h2>
                        <p class="mt-2 leading-relaxed text-ink-secondary">{{ $step['body'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('register') }}" class="ip-btn">{{ __('ابدأ رحلتك') }}</a>
        </div>
    </div>
</section>
@endsection
