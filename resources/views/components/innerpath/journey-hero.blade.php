@php
    $registerUrl = route('register');
    $isArabic = app()->getLocale() === 'ar';
    $worldTitles = $isArabic
        ? ['الصدر', 'الذكريات', 'الألم', 'اللاوعي', 'الوعي', 'الماسة', 'السلام']
        : ['Heart', 'Memories', 'Pain', 'Unconscious', 'Ocean', 'Diamond', 'Peace'];
@endphp

<section class="journey" data-journey-root data-world="chest" aria-label="{{ __('رحلة إلى الداخل') }}">
    <div class="journey-sticky" data-journey-sticky>
        <canvas class="journey-canvas" data-journey-canvas></canvas>
        <div class="journey-vignette" aria-hidden="true"></div>

        <div class="journey-fallback" data-journey-fallback hidden>
            <div class="journey-fallback-cosmos" aria-hidden="true"></div>
            <div class="journey-fallback-veil"></div>
        </div>

        <div class="journey-loader" data-journey-loader>
            <span></span>
        </div>

        <div class="journey-overlay" data-journey-overlay>
            <div class="journey-intro">
                <p class="journey-kicker">InnerPath</p>
                <h1 class="journey-title">
                    {{ $isArabic ? 'الكون الذي بداخلك' : 'The Universe Within You' }}
                </h1>
                <p class="journey-lede">
                    {{ $isArabic
                        ? 'عالم واحد متصل: من نبض الصدر إلى قلب كل طبقة — بلا فواصل، فقط تحوّل هادئ.'
                        : 'One continuous cosmos: from the heart’s pulse into each inner world — no cuts, only soft becoming.' }}
                </p>
                <div class="journey-actions">
                    <a href="{{ $registerUrl }}" class="journey-cta-primary">
                        {{ $isArabic ? 'ابدأ رحلتك' : 'Begin Your Journey' }}
                    </a>
                    <button type="button" class="journey-cta-secondary" data-journey-explore>
                        {{ $isArabic ? 'ادخل إلى الداخل' : 'Wander Within' }}
                    </button>
                </div>
            </div>

            <div class="journey-world-copy" aria-live="polite">
                <p class="journey-world-name" data-journey-world>
                    {{ $isArabic ? 'عتبة الصدر' : 'Threshold of the Heart' }}
                </p>
                <p class="journey-world-line" data-journey-line>
                    {{ $isArabic ? 'من نبضك يبدأ النفق إلى العوالم.' : 'From your pulse, the tunnel opens inward.' }}
                </p>
            </div>

            <nav class="journey-dots" aria-label="{{ $isArabic ? 'عوالم الرحلة' : 'Journey worlds' }}">
                @foreach ($worldTitles as $index => $title)
                    <button
                        type="button"
                        class="journey-dot {{ $index === 0 ? 'is-active' : '' }}"
                        data-journey-dot="{{ $index }}"
                        aria-label="{{ $title }}"
                        @if ($index === 0) aria-current="true" @endif
                    ></button>
                @endforeach
            </nav>

            <button
                type="button"
                class="journey-audio"
                data-journey-audio
                data-state="off"
                data-label-on="{{ $isArabic ? 'كتم الصوت' : 'Mute sound' }}"
                data-label-off="{{ $isArabic ? 'تشغيل الصوت' : 'Unmute sound' }}"
                aria-pressed="false"
                aria-label="{{ $isArabic ? 'تشغيل الصوت' : 'Unmute sound' }}"
                title="{{ $isArabic ? 'تشغيل الصوت' : 'Unmute sound' }}"
            >
                <span class="journey-audio-icon" aria-hidden="true"></span>
            </button>

            <p class="journey-hint" data-journey-hint>
                {{ $isArabic ? 'مرّر للتجوّل داخل الكون ↓' : 'Scroll to wander within ↓' }}
            </p>

            <div class="journey-progress" aria-hidden="true">
                <span data-journey-progress></span>
            </div>
        </div>
    </div>

    <div class="journey-track" data-journey-track aria-hidden="true"></div>
</section>
