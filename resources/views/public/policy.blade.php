@extends('layouts.public')

@section('head')
    <x-innerpath.seo
        :title="$title.' — InnerPath'"
        :description="$excerpt"
        :image="asset($image)"
    />
@endsection

@section('content')
<section class="ip-page-hero min-h-[42dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset($image) }}" alt="" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[42dvh] flex-col justify-end pb-12 pt-24">
        <p class="text-sm font-medium text-ink-muted">{{ __('الثقة والتحرير') }}</p>
        <h1 class="mt-2 font-display text-h1 text-ink">{{ $title }}</h1>
        <p class="mt-3 max-w-reading text-lg leading-relaxed text-ink-secondary">{{ $excerpt }}</p>
    </div>
</section>

<section class="border-t border-line">
    <div class="ip-container py-14 lg:py-16">
        <div class="ip-prose-calm mx-auto space-y-5 whitespace-pre-line">{{ $body }}</div>

        <div class="mx-auto mt-12 flex max-w-reading flex-wrap gap-3 border-t border-line pt-8">
            <a href="{{ route('editorial') }}" class="ip-chip">{{ __('السياسة التحريرية') }}</a>
            <a href="{{ route('ai-transparency') }}" class="ip-chip">{{ __('شفافية الذكاء الاصطناعي') }}</a>
            <a href="{{ route('sources') }}" class="ip-chip">{{ __('المصادر والمراجع') }}</a>
            <a href="{{ route('privacy') }}" class="ip-chip">{{ __('الخصوصية') }}</a>
        </div>
    </div>
</section>
@endsection
