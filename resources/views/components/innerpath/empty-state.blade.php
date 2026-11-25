@props([
    'image' => 'images/brand/still-water.jpg',
    'title' => null,
    'body' => null,
    'actionHref' => null,
    'actionLabel' => null,
])

<div {{ $attributes->class(['ip-empty overflow-hidden !p-0']) }}>
    <div class="relative h-36 overflow-hidden sm:h-44">
        <img src="{{ asset($image) }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-surface via-surface/40 to-transparent"></div>
    </div>
    <div class="px-6 pb-10 pt-2 text-center">
        @if($title)
            <p class="font-display text-h4 text-ink">{{ $title }}</p>
        @endif
        @if($body)
            <p class="ip-muted mx-auto mt-2 max-w-md">{{ $body }}</p>
        @endif
        {{ $slot }}
        @if($actionHref && $actionLabel)
            <a href="{{ $actionHref }}" class="ip-btn mt-5">{{ $actionLabel }}</a>
        @endif
    </div>
</div>
