@php
    $isArabic = app()->getLocale() === 'ar';
    $registerUrl = route('register');
    $exploreUrl = route('explore.index');
    $words = $isArabic
        ? ['الكون', 'السلام', 'النور', 'الجوهر', 'الوضوح', 'الحرية']
        : ['universe', 'stillness', 'light', 'essence', 'clarity', 'freedom'];
@endphp

<section
    class="ip-flow-hero"
    data-flow-hero
    aria-label="{{ __('الكون الذي بداخلك') }}"
    x-data="innerPathFlowHero(@js($words))"
    x-init="init()"
>
    <video
        class="ip-flow-hero-video"
        autoplay
        muted
        loop
        playsinline
        preload="metadata"
        poster="{{ asset('images/journey/hero-poster.jpg') }}"
        x-ref="video"
    >
        <source src="https://d8j0ntlcm91z4.cloudfront.net/user_38xzZboKViGWJOttwIXH07lWA1P/hf_20260703_053131_1ec3dd1c-d627-44fb-ab20-6e1fce41b0d5.mp4" type="video/mp4">
    </video>

    <div class="ip-flow-hero-overlay" aria-hidden="true"></div>
    <div class="ip-flow-hero-veil" aria-hidden="true"></div>

    {{-- Soft floating petals (motion triggers flower sounds) --}}
    <div class="ip-flow-petals" aria-hidden="true">
        @for ($i = 0; $i < 8; $i++)
            <span
                class="ip-flow-petal"
                style="--i: {{ $i }}"
                @animationiteration="onPetalMotion()"
            ></span>
        @endfor
    </div>

    <div class="ip-flow-hero-body">
        <div class="ip-flow-hero-copy">
            <p class="ip-flow-kicker">InnerPath</p>

            <h1 class="ip-flow-title">
                @if ($isArabic)
                    <span class="block">{{ __('اكتشف') }}</span>
                    <span class="block">
                        <span class="ip-flow-word" x-text="word"></span>
                        <span class="ip-flow-dim">{{ __('داخلك.') }}</span>
                    </span>
                    <span class="block ip-flow-dim">{{ __('بلا ضجيج. بوضوح.') }}</span>
                @else
                    <span class="block">Discover the</span>
                    <span class="block">
                        <span class="ip-flow-word" x-text="word"></span>
                        <span class="ip-flow-dim">within.</span>
                    </span>
                    <span class="block ip-flow-dim">Beyond the noise.</span>
                @endif
            </h1>

            <p class="ip-flow-lede">
                {{ $isArabic
                    ? 'InnerPath يجمع تأملك وأحلامك ونموك في مسار واحد هادئ — أقل تشتيتاً، وأكثر حضوراً.'
                    : 'InnerPath unifies your dreams, reflection, and growth into one calm path — less grinding gaps, more real progress.' }}
            </p>

            <div class="ip-flow-actions">
                <a href="{{ $registerUrl }}" class="ip-flow-cta-solid">
                    {{ $isArabic ? 'ابدأ رحلتك' : 'Begin your journey' }}
                </a>
                <a href="{{ $exploreUrl }}" class="ip-flow-cta-glass liquid-glass">
                    {{ $isArabic ? 'استكشف العوالم' : 'See the inner worlds' }}
                </a>
            </div>
        </div>
    </div>

    <div class="ip-flow-corner">
        <span>{{ $isArabic ? 'شاهد الزهور تتنفس' : 'Watch the flowers breathe' }}</span>
        <i></i>
        <button
            type="button"
            class="ip-flow-sound"
            @click="toggleSound()"
            :aria-pressed="soundOn.toString()"
            :aria-label="soundOn ? '{{ $isArabic ? 'كتم الصوت' : 'Mute sound' }}' : '{{ $isArabic ? 'تشغيل الصوت' : 'Play sound' }}'"
            :title="soundOn ? '{{ $isArabic ? 'كتم الصوت' : 'Mute sound' }}' : '{{ $isArabic ? 'تشغيل الصوت' : 'Play sound' }}'"
        >
            <x-lucide-volume-2 class="size-5" x-show="soundOn" x-cloak />
            <x-lucide-volume-x class="size-5" x-show="!soundOn" />
        </button>
    </div>
</section>
