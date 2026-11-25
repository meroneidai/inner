@extends('layouts.public')

@section('head')
    <x-innerpath.seo
        :title="$pillar->seo_title ?: ($pillar->name.' — InnerPath')"
        :description="$pillar->meta_description ?: $pillar->excerpt"
        :image="asset($pillar->og_image_path ?: $pillar->featured_image_path ?: 'images/brand/still-water.jpg')"
        type="article"
    />
@endsection

@section('content')
<section class="ip-page-hero min-h-[48dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset($pillar->featured_image_path ?: 'images/brand/still-water.jpg') }}" alt="" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[48dvh] flex-col justify-end pb-14 pt-24">
        <nav class="mb-4 text-sm text-ink-muted">
            <a href="{{ route('explore.index') }}" class="hover:text-ink">{{ __('استكشف') }}</a>
            <span class="mx-2">/</span>
            <span class="text-ink">{{ $pillar->name }}</span>
        </nav>
        <h1 class="font-display text-h1 text-ink">{{ $pillar->name }}</h1>
        @if($pillar->tagline)
            <p class="mt-3 max-w-2xl text-lg leading-relaxed text-ink-secondary">{{ $pillar->tagline }}</p>
        @endif
    </div>
</section>

<section class="border-t border-line">
    <div class="ip-container py-14 lg:py-16">
        @if($pillar->intro)
            <div class="ip-prose-calm mx-auto mb-12 whitespace-pre-line">{{ $pillar->intro }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            @forelse($articles as $article)
                <a href="{{ $article->url() }}" class="ip-card ip-card-hover block overflow-hidden">
                    @if($article->featured_image_path)
                        <img src="{{ asset($article->featured_image_path) }}" alt="{{ $article->featured_image_alt }}" class="aspect-[16/9] w-full object-cover" loading="lazy">
                    @endif
                    <div class="p-5">
                        <h2 class="font-display text-lg text-ink">{{ $article->title }}</h2>
                        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-ink-secondary">{{ $article->excerpt }}</p>
                        @if($article->reading_time_minutes)
                            <p class="mt-3 text-xs text-ink-muted">{{ $article->reading_time_minutes }} {{ __('دقيقة قراءة') }}</p>
                        @endif
                    </div>
                </a>
            @empty
                <x-innerpath.empty-state
                    class="md:col-span-2"
                    :title="__('المقالات قادمة')"
                    :body="__('نجهّز محتوى عميقاً لهذا المسار.')"
                />
            @endforelse
        </div>

        <div class="mt-8">{{ $articles->links() }}</div>

        @if($faqs->isNotEmpty())
            <div class="mx-auto mt-16 max-w-reading">
                <h2 class="font-display text-h2 text-ink">{{ __('أسئلة شائعة') }}</h2>
                <div class="mt-6 space-y-4">
                    @foreach($faqs as $faq)
                        <details class="ip-card p-5">
                            <summary class="cursor-pointer font-medium text-ink">{{ $faq->question }}</summary>
                            <p class="mt-3 text-sm leading-relaxed text-ink-secondary">{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        @endif

        @if($pillar->cta_route)
            <div class="mt-14 text-center">
                <a href="{{ route($pillar->cta_route) }}" class="ip-btn">{{ $pillar->cta_label ?: __('ابدأ رحلتك') }}</a>
            </div>
        @endif
    </div>
</section>
@endsection
