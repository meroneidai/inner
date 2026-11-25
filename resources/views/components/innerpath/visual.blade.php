@props([
    'src',
    'alt' => '',
    'ratio' => '4/3', // 16/9 | 4/3 | 3/4 | auto
    'priority' => false,
])

@php
    $ratioClass = match ($ratio) {
        '16/9' => 'aspect-[16/9]',
        '3/4' => 'aspect-[3/4]',
        'auto' => '',
        default => 'aspect-[4/3]',
    };
@endphp

<figure {{ $attributes->class(['ip-visual group relative overflow-hidden rounded-section', $ratioClass]) }}>
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        @if($priority) fetchpriority="high" @else loading="lazy" @endif
        decoding="async"
        class="ip-visual-img size-full object-cover transition duration-700 ease-out group-hover:scale-[1.02]"
        width="1200"
        height="900"
    >
    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#30332F]/25 via-transparent to-transparent opacity-60"></div>
    @isset($caption)
        <figcaption class="absolute inset-x-0 bottom-0 p-5 sm:p-7">
            <div class="max-w-md text-sm leading-relaxed text-white/95 drop-shadow-sm">
                {{ $caption }}
            </div>
        </figcaption>
    @endisset
</figure>
