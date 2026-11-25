@props([
    'variant' => 'full', // full|mark
    'tone' => 'light', // light|dark|mono
])

@php
    $ink = match ($tone) {
        'dark' => '#F8F8F5',
        'mono' => 'currentColor',
        default => '#55596F',
    };
    $accent = match ($tone) {
        'dark' => '#9AAE9D',
        'mono' => 'currentColor',
        default => '#9AAE9D',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <svg class="size-8 shrink-0" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <circle cx="20" cy="20" r="18.25" stroke="{{ $ink }}" stroke-width="1.5" opacity="0.35"/>
        <path d="M14 26.5C14 20.5 17.2 16.8 20 13.5C22.8 16.8 26 20.5 26 26.5" stroke="{{ $accent }}" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M20 13.5V27.5" stroke="{{ $ink }}" stroke-width="1.5" stroke-linecap="round" opacity="0.55"/>
        <circle cx="20" cy="12.25" r="1.6" fill="{{ $accent }}"/>
    </svg>
    @if($variant === 'full')
        <span class="font-display text-[1.35rem] font-semibold tracking-tight" style="color: {{ $tone === 'dark' ? '#F8F8F5' : '#30332F' }}">InnerPath</span>
    @endif
</span>
