@extends('layouts.public')

@section('head')
    <x-innerpath.seo
        :title="$page['title'].' — InnerPath'"
        :description="$page['tagline']"
        :image="asset($page['image'])"
    />
@endsection

@section('content')
<section class="ip-page-hero min-h-[50dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset($page['image']) }}" alt="" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[50dvh] flex-col justify-end pb-14 pt-24">
        <p class="text-sm font-medium text-ink-muted">{{ __('الميزات') }}</p>
        <h1 class="mt-2 font-display text-h1 text-ink">{{ $page['title'] }}</h1>
        <p class="mt-3 max-w-2xl text-lg leading-relaxed text-ink-secondary">{{ $page['tagline'] }}</p>
    </div>
</section>

<section class="ip-atmosphere border-t border-line">
    <div class="ip-container grid items-start gap-10 py-16 lg:grid-cols-2 lg:py-20">
        <div>
            <p class="ip-prose-calm">{{ $page['body'] }}</p>
            <ul class="mt-8 space-y-3">
                @foreach($page['points'] as $point)
                    <li class="flex items-center gap-3 text-sm text-ink">
                        <x-lucide-check class="size-4 shrink-0 text-sage" />
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
            <a href="{{ route($page['cta_route']) }}" class="ip-btn mt-8">{{ $page['cta_label'] }}</a>
        </div>
        <x-innerpath.visual :src="asset($page['image'])" :alt="$page['title']" ratio="4/3" />
    </div>
</section>
@endsection
