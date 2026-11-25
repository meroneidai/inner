@extends('layouts.public')

@section('head')
    <x-innerpath.seo
        :title="__('استكشف') . ' — InnerPath'"
        :description="__('مركز معرفة هادئ لفهم الذات، معنى الحياة، السلام الداخلي، والأحلام.')"
        :image="asset('images/brand/inner-world.jpg')"
        :json-ld="[
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => 'InnerPath Explore',
            'description' => __('مركز معرفة لفهم الذات والأحلام'),
            'url' => route('explore.index'),
        ]"
    />
@endsection

@section('content')
<section class="ip-page-hero min-h-[56dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset('images/brand/inner-world.jpg') }}" alt="" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[56dvh] flex-col justify-end pb-14 pt-24">
        <div class="ip-fade-up max-w-2xl">
            <p class="text-sm font-medium text-ink-muted">{{ __('مركز المعرفة') }}</p>
            <h1 class="mt-2 font-display text-h1 text-ink">{{ __('أسئلة تبقى معنا… ومساحة لاستكشافها') }}</h1>
            <p class="mt-4 text-lg leading-relaxed text-ink-secondary">
                {{ __('افهم نفسك، تأمل معنى حياتك، استكشف أحلامك، وابنِ علاقة أوضح بعالمك الداخلي — بهدوء ووضوح.') }}
            </p>
        </div>
    </div>
</section>

<section class="ip-atmosphere border-t border-line">
    <div class="ip-container py-16 lg:py-20">
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($pillars as $pillar)
                <a href="{{ $pillar->url() }}" class="ip-card group overflow-hidden transition duration-300 hover:-translate-y-0.5 hover:border-line-strong">
                    <div class="aspect-[16/10] overflow-hidden">
                        <img
                            src="{{ asset($pillar->featured_image_path ?: 'images/brand/still-water.jpg') }}"
                            alt=""
                            class="size-full object-cover transition duration-700 group-hover:scale-[1.03]"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                    <div class="p-6">
                        <p class="text-xs font-medium text-ink-muted">{{ $pillar->articles_count }} {{ __('مقالات') }}</p>
                        <h2 class="mt-2 font-display text-h4 text-ink">{{ $pillar->name }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ $pillar->tagline ?: $pillar->excerpt }}</p>
                    </div>
                </a>
            @empty
                <x-innerpath.empty-state
                    class="sm:col-span-2 lg:col-span-3"
                    image="images/brand/inner-world.jpg"
                    :title="__('قريباً')"
                    :body="__('مركز المعرفة قيد الإعداد.')"
                />
            @endforelse
        </div>
    </div>
</section>

@if($featured->isNotEmpty())
<section class="border-t border-line bg-surface">
    <div class="ip-container py-16 lg:py-20">
        <h2 class="font-display text-h2 text-ink">{{ __('مقالات مختارة') }}</h2>
        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($featured as $article)
                <a href="{{ $article->url() }}" class="ip-card ip-card-hover block p-5">
                    <p class="text-xs text-ink-muted">{{ $article->pillar?->name }}</p>
                    <h3 class="mt-2 font-display text-lg text-ink">{{ $article->title }}</h3>
                    <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-ink-secondary">{{ $article->excerpt }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="border-t border-line">
    <div class="ip-container py-16 text-center lg:py-20">
        <h2 class="font-display text-h2 text-ink">{{ __('انتقل من القراءة إلى التجربة') }}</h2>
        <p class="mx-auto mt-3 max-w-lg text-ink-secondary">{{ __('ابدأ يومية أحلامك، وابنِ ملفك الداخلي بخطوات هادئة.') }}</p>
        <a href="{{ route('register') }}" class="ip-btn mt-8">{{ __('ابدأ رحلتك') }}</a>
    </div>
</section>
@endsection
