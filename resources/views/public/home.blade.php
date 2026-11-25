@extends('layouts.public')

@section('title', 'InnerPath — ' . __('الكون الذي بداخلك'))

@section('head')
    <x-innerpath.seo
        :title="'InnerPath — ' . __('الكون الذي بداخلك')"
        :description="__('أبعد من الضجيج والذكريات — رحلة استثنائية إلى عوالمك الداخلية.')"
        :image="asset('images/brand/hero-path.jpg')"
    />
@endsection

@section('content')
{{-- Fullscreen video flow hero --}}
<x-innerpath.flow-hero />

{{-- Inspiration strip --}}
<section class="border-y border-line bg-surface">
    <div class="ip-container py-10 lg:py-12">
        <p class="ip-quote ip-fade-up-delay mx-auto max-w-reading text-center">
            {{ __('كل رحلة حقيقية تبدأ من الداخل.') }}
        </p>
    </div>
</section>

{{-- Personal space --}}
<section class="ip-atmosphere border-b border-line">
    <div class="ip-container grid items-center gap-10 py-16 lg:grid-cols-2 lg:gap-14 lg:py-24">
        <x-innerpath.visual
            src="{{ asset('images/brand/doorway-light.jpg') }}"
            :alt="__('ضوء هادئ يدخل من باب مفتوح — مساحة خاصة للتأمل')"
            ratio="4/3"
            class="ip-fade-up"
        >
            <x-slot:caption>{{ __('مساحة هادئة… لنفسك فقط.') }}</x-slot:caption>
        </x-innerpath.visual>

        <div>
            <p class="text-sm font-medium text-ink-muted">{{ __('مساحة شخصية') }}</p>
            <h2 class="mt-2 font-display text-h2 text-ink">{{ __('ليست منصة عامة — بل مساحة خاصة بك') }}</h2>
            <p class="ip-prose-calm mt-4">
                {{ __('كل حلم مرتبط بتجربة فردية: مشاعرك، ذكرياتك، ظروفك، وما يمرّ بك الآن. InnerPath يساعدك على رؤية هذه الروابط بهدوء، دون ضجيج أو أحكام.') }}
            </p>
        </div>
    </div>
</section>

{{-- How it works with journal visual --}}
<section class="border-b border-line bg-canvas-soft">
    <div class="ip-container py-16 lg:py-24">
        <div class="grid items-end gap-8 lg:grid-cols-[1fr_1.1fr] lg:gap-14">
            <div>
                <p class="text-sm font-medium text-ink-muted">{{ __('كيف تعمل') }}</p>
                <h2 class="mt-2 font-display text-h2 text-ink">{{ __('أربع خطوات بسيطة') }}</h2>
                <p class="mt-3 leading-relaxed text-ink-secondary">{{ __('من التسجيل الهادئ إلى اكتشاف الأنماط مع الوقت.') }}</p>
            </div>
            <x-innerpath.visual
                src="{{ asset('images/brand/journal.jpg') }}"
                :alt="__('دفتر يوميات هادئ على طاولة من الكتان في ضوء الصباح')"
                ratio="16/9"
            />
        </div>

        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['icon' => 'pen-line', 'title' => __('سجّل أحلامك'), 'body' => __('نصاً أو صوتاً — في اللحظة التي تتذكرها فيها.')],
                ['icon' => 'search', 'title' => __('استكشف معناها'), 'body' => __('منظورات تفسيرية متعددة مرتبطة بسياقك الشخصي.')],
                ['icon' => 'link-2', 'title' => __('اربط تجاربك'), 'body' => __('اربط الأحلام بالذكريات والمشاعر وظروف الحياة.')],
                ['icon' => 'chart-no-axes-column', 'title' => __('اكتشف الأنماط'), 'body' => __('مع الوقت تظهر الموضوعات المتكررة ومسار نموك.')],
            ] as $i => $step)
                <article class="ip-card p-6 transition duration-300 hover:-translate-y-0.5 hover:border-line-strong">
                    <div class="flex items-center gap-3">
                        <span class="ip-icon-box">
                            @switch($step['icon'])
                                @case('pen-line') <x-lucide-pen-line class="size-4" /> @break
                                @case('search') <x-lucide-search class="size-4" /> @break
                                @case('link-2') <x-lucide-link-2 class="size-4" /> @break
                                @default <x-lucide-chart-no-axes-column class="size-4" />
                            @endswitch
                        </span>
                        <span class="text-xs font-medium text-ink-muted">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="mt-5 font-display text-h4 text-ink">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ $step['body'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- Inner world --}}
<section class="border-b border-line">
    <div class="ip-container grid items-center gap-10 py-16 lg:grid-cols-2 lg:gap-14 lg:py-24">
        <div>
            <p class="text-sm font-medium text-ink-muted">{{ __('ملفك الداخلي') }}</p>
            <h2 class="mt-2 font-display text-h2 text-ink">{{ __('يتعرّف عليك تدريجياً — بإرادتك') }}</h2>
            <p class="ip-prose-calm mt-4">
                {{ __('من خلال إجابات اختيارية، تبني صورة عن خلفيتك وتفضيلاتك وذكرياتك وتجاربك. لا درجات نفسية مصطنعة — فقط سياق يساعد على فهم أعمق.') }}
            </p>
            <a href="{{ route('register') }}" class="ip-btn-text mt-6 px-0">{{ __('ابدأ بناء ملفك') }} <x-lucide-arrow-left class="size-4 rtl:rotate-180" /></a>
        </div>
        <x-innerpath.visual
            src="{{ asset('images/brand/inner-world.jpg') }}"
            :alt="__('أشكال عضوية هادئة بألوان الميرمية والرمال — تعبير عن العالم الداخلي')"
            ratio="4/3"
        />
    </div>
</section>

{{-- Reflections over water --}}
<section class="relative overflow-hidden border-b border-line">
    <div class="absolute inset-0" aria-hidden="true">
        <img src="{{ asset('images/brand/still-water.jpg') }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-canvas/88"></div>
    </div>
    <div class="ip-container relative py-16 lg:py-24">
        <div class="max-w-2xl">
            <p class="text-sm font-medium text-ink-muted">{{ __('أبعد من الحلم الواحد') }}</p>
            <h2 class="mt-2 font-display text-h2 text-ink">{{ __('تأملات أسبوعية وشهرية') }}</h2>
            <p class="mt-3 leading-relaxed text-ink-secondary">
                {{ __('مع تراكم يومياتك، تحصل على ملخصات هادئة تساعد على رؤية الاتجاه — لا ضجيج إحصائي، بل تأمل واضح.') }}
            </p>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-2">
            <article class="rounded-card border border-line bg-surface/90 p-7 backdrop-blur-sm">
                <span class="ip-icon-box"><x-lucide-calendar-days class="size-4" /></span>
                <h3 class="mt-5 font-display text-h4 text-ink">{{ __('تقرير أسبوعي') }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ __('نظرة على أحلام الأسبوع، المشاعر المتكررة، وخطوة عملية صغيرة.') }}</p>
            </article>
            <article class="rounded-card border border-line bg-surface/90 p-7 backdrop-blur-sm">
                <span class="ip-icon-box"><x-lucide-calendar-range class="size-4" /></span>
                <h3 class="mt-5 font-display text-h4 text-ink">{{ __('تأمل شهري') }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ __('صورة أوسع لمسار نموك والتغيرات التي لاحظتها على نفسك.') }}</p>
            </article>
        </div>
    </div>
</section>

{{-- Knowledge hub teaser --}}
<section class="border-b border-line bg-canvas-soft">
    <div class="ip-container py-16 lg:py-20">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="max-w-xl">
                <p class="text-sm font-medium text-ink-muted">{{ __('مركز المعرفة') }}</p>
                <h2 class="mt-2 font-display text-h2 text-ink">{{ __('استكشف الأسئلة التي تبقى معنا') }}</h2>
                <p class="mt-3 leading-relaxed text-ink-secondary">{{ __('فهم الذات، معنى الحياة، السلام الداخلي، الأحلام، والنمو — مقالات هادئة بعمق حقيقي.') }}</p>
            </div>
            <a href="{{ route('explore.index') }}" class="ip-btn">{{ __('افتح مركز المعرفة') }}</a>
        </div>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['href' => '/explore/discover-yourself', 'img' => 'inner-world.jpg', 'label' => __('فهم الذات')],
                ['href' => '/explore/understanding-dreams', 'img' => 'journal.jpg', 'label' => __('الأحلام')],
                ['href' => '/explore/inner-peace', 'img' => 'still-water.jpg', 'label' => __('السلام الداخلي')],
                ['href' => '/explore/meaning-of-life', 'img' => 'hero-path.jpg', 'label' => __('معنى الحياة')],
            ] as $card)
                <a href="{{ url($card['href']) }}" class="group overflow-hidden rounded-card border border-line bg-surface">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ asset('images/brand/'.$card['img']) }}" alt="" class="size-full object-cover transition duration-700 group-hover:scale-[1.03]" loading="lazy" decoding="async">
                    </div>
                    <p class="p-4 font-display text-h4 text-ink">{{ $card['label'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Privacy --}}
<section class="border-b border-line bg-surface">
    <div class="ip-container py-16 lg:py-20">
        <div class="mx-auto max-w-reading text-center">
            <span class="ip-icon-box mx-auto"><x-lucide-shield class="size-4" /></span>
            <h2 class="mt-5 font-display text-h2 text-ink">{{ __('مساحة خاصة وموثوقة') }}</h2>
            <p class="mt-4 text-base leading-[1.8] text-ink-secondary">
                {{ __('يومياتك ملكك. صُمّمت InnerPath لتكون مساحة آمنة للتأمل — دون ضغط اجتماعي أو مشاركة إجبارية.') }}
            </p>
            <a href="{{ route('privacy') }}" class="ip-btn-text mt-4">{{ __('اقرأ سياسة الخصوصية') }}</a>
        </div>
    </div>
</section>

{{-- Final CTA with light path --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0" aria-hidden="true">
        <img src="{{ asset('images/brand/light-path.jpg') }}" alt="" class="size-full object-cover object-center" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-canvas via-canvas/85 to-canvas/70"></div>
    </div>
    <div class="ip-container relative py-20 text-center lg:py-28">
        <h2 class="font-display text-h1 text-ink">{{ __('امنح نفسك مساحة للفهم') }}</h2>
        <p class="mx-auto mt-4 max-w-lg text-lg leading-relaxed text-ink-secondary">
            {{ __('ابدأ بحلم واحد، أو سؤال واحد، أو لحظة تأمل واحدة.') }}
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="ip-btn">{{ __('ابدأ رحلتك في InnerPath') }}</a>
            <a href="{{ route('login') }}" class="ip-btn-secondary">{{ __('لدي حساب') }}</a>
        </div>
    </div>
</section>
@endsection
