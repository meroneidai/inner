@extends('layouts.public')

@section('head')
    <x-innerpath.seo
        :title="$article->seo_title ?: ($article->title.' — InnerPath')"
        :description="$article->meta_description ?: $article->excerpt"
        :image="asset($article->og_image_path ?: $article->featured_image_path ?: 'images/brand/journal.jpg')"
        type="article"
        :json-ld="[
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->excerpt,
            'datePublished' => optional($article->published_at)->toAtomString(),
            'dateModified' => optional($article->updated_content_at ?? $article->updated_at)->toAtomString(),
            'author' => [
                '@type' => 'Person',
                'name' => $article->author?->name ?: 'InnerPath Editorial',
            ],
            'mainEntityOfPage' => $article->url(),
        ]"
    />
@endsection

@section('content')
<article>
    <section class="ip-page-hero min-h-[46dvh]">
        <div class="ip-page-hero-media" aria-hidden="true">
            <img src="{{ asset($article->featured_image_path ?: 'images/brand/journal.jpg') }}" alt="" fetchpriority="high" decoding="async">
        </div>
        <div class="ip-page-hero-veil"></div>
        <div class="ip-container relative flex min-h-[46dvh] flex-col justify-end pb-12 pt-24">
            <nav class="mb-4 text-sm text-ink-muted">
                <a href="{{ route('explore.index') }}" class="hover:text-ink">{{ __('استكشف') }}</a>
                <span class="mx-2">/</span>
                <a href="{{ $pillar->url() }}" class="hover:text-ink">{{ $pillar->name }}</a>
            </nav>
            <h1 class="max-w-reading font-display text-h1 text-ink">{{ $article->title }}</h1>
            <p class="mt-3 text-sm text-ink-muted">
                @if($article->author)
                    {{ $article->author->name }} ·
                @endif
                {{ optional($article->published_at)->translatedFormat('d M Y') }}
                @if($article->reading_time_minutes)
                    · {{ $article->reading_time_minutes }} {{ __('دقيقة') }}
                @endif
            </p>
        </div>
    </section>

    <div class="ip-container py-12 lg:py-16">
        <div class="mx-auto max-w-reading">
            @if($article->requires_safety_notice)
                <div class="mb-8 rounded-card border border-warning/30 bg-beige/70 px-4 py-3 text-sm leading-relaxed text-ink">
                    {{ __('إذا كنت تمرّ بأزمة أو أفكار لإيذاء نفسك، اطلب مساعدة فورية من شخص تثق به أو من خدمات الطوارئ المحلية. InnerPath مساحة للتأمل وليست بديلاً عن الدعم المهني أو الطبي.') }}
                </div>
            @endif

            @if($article->excerpt)
                <p class="mb-8 text-lg leading-[1.8] text-ink-secondary">{{ $article->excerpt }}</p>
            @endif

            <div class="prose-article space-y-5 text-base leading-[1.85] text-ink-secondary whitespace-pre-line">
                {!! nl2br(e($article->body)) !!}
            </div>

            @if($article->faqs->isNotEmpty())
                <section class="mt-14">
                    <h2 class="font-display text-h3 text-ink">{{ __('أسئلة شائعة') }}</h2>
                    <div class="mt-5 space-y-3">
                        @foreach($article->faqs as $faq)
                            <details class="ip-card p-4">
                                <summary class="cursor-pointer font-medium text-ink">{{ $faq->question }}</summary>
                                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ $faq->answer }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($article->references->isNotEmpty())
                <section class="mt-14">
                    <h2 class="font-display text-h4 text-ink">{{ __('مراجع وقراءات') }}</h2>
                    <ul class="mt-4 space-y-2 text-sm text-ink-secondary">
                        @foreach($article->references as $ref)
                            <li>
                                @if($ref->url)
                                    <a href="{{ $ref->url }}" class="text-primary-dark hover:underline" rel="noopener noreferrer" target="_blank">{{ $ref->label }}</a>
                                @else
                                    {{ $ref->label }}
                                @endif
                                @if($ref->citation)
                                    <span class="text-ink-muted"> — {{ $ref->citation }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <section class="mt-14 rounded-card border border-line bg-canvas-soft p-6">
                <h2 class="font-display text-h4 text-ink">{{ $article->cta_label ?: __('واصل الاستكشاف في InnerPath') }}</h2>
                <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ __('حوّل التأمل إلى ممارسة شخصية هادئة داخل مساحتك الخاصة.') }}</p>
                <a href="{{ $article->cta_route ? route($article->cta_route) : route('register') }}" class="ip-btn mt-5">
                    {{ $article->cta_label ?: __('ابدأ رحلتك') }}
                </a>
            </section>

            @if($article->relatedArticles->isNotEmpty())
                <section class="mt-14">
                    <h2 class="font-display text-h4 text-ink">{{ __('مقالات ذات صلة') }}</h2>
                    <div class="mt-4 grid gap-3">
                        @foreach($article->relatedArticles as $related)
                            <a href="{{ $related->url() }}" class="ip-card ip-card-hover block p-4">
                                <p class="font-medium text-ink">{{ $related->title }}</p>
                                <p class="mt-1 text-sm text-ink-secondary">{{ $related->excerpt }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</article>
@endsection
