@php
    $isFlowHome = request()->routeIs('home');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    @hasSection('head')
        @yield('head')
    @else
        <title>@yield('title', 'InnerPath')</title>
        <meta name="description" content="{{ __('مساحة هادئة لفهم الأحلام واكتشاف الذات.') }}">
        <meta property="og:image" content="{{ asset('images/brand/hero-path.jpg') }}">
    @endif
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body class="bg-canvas text-ink {{ $isFlowHome ? 'has-flow-hero' : '' }}">
    <header
        class="{{ $isFlowHome
            ? 'absolute inset-x-0 top-0 z-40 border-b border-transparent bg-transparent text-white'
            : 'sticky top-0 z-40 border-b border-line bg-surface/90 text-ink backdrop-blur-md' }}"
        x-data="{ open: false }"
    >
        <div class="ip-container flex h-16 items-center justify-between gap-4 px-5 sm:px-6 md:px-0">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="InnerPath">
                <x-innerpath.logo :tone="$isFlowHome ? 'dark' : 'light'" />
            </a>

            <nav class="hidden items-center gap-1 md:flex">
                @foreach([
                    ['explore.index', __('استكشف')],
                    ['how-it-works', __('كيف تعمل')],
                    ['about', __('عن المنصة')],
                    ['contact', __('تواصل')],
                ] as [$route, $label])
                    <a href="{{ route($route) }}" class="{{ $isFlowHome ? 'journey-nav-link' : 'ip-btn-ghost' }}">{{ $label }}</a>
                @endforeach
            </nav>

            <div class="hidden items-center gap-2 md:flex">
                <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="{{ $isFlowHome ? 'journey-nav-chip' : 'ip-chip' }}">{{ app()->getLocale() === 'ar' ? 'EN' : 'ع' }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="{{ $isFlowHome ? 'journey-cta-primary !min-h-9 !px-4 !text-xs' : 'ip-btn' }}">{{ __('مساحتي') }}</a>
                @else
                    <a href="{{ route('login') }}" class="{{ $isFlowHome ? 'journey-nav-link' : 'ip-btn-ghost' }}">{{ __('دخول') }}</a>
                    <a href="{{ route('register') }}" class="{{ $isFlowHome ? 'liquid-glass !inline-flex !min-h-9 !items-center !rounded-full !px-5 !py-2 !text-xs !font-medium !text-white' : 'ip-btn' }}">{{ __('ابدأ رحلتك') }}</a>
                @endauth
            </div>

            <button
                type="button"
                class="inline-flex size-10 items-center justify-center rounded-control md:hidden {{ $isFlowHome ? 'border border-white/25 text-white' : 'border border-line text-ink' }}"
                @click="open = !open"
                aria-label="{{ __('القائمة') }}"
            >
                <x-lucide-menu class="size-5" x-show="!open" />
                <x-lucide-x class="size-5" x-cloak x-show="open" />
            </button>
        </div>

        <div
            class="mx-4 mt-2 rounded-2xl px-5 py-4 md:hidden {{ $isFlowHome ? 'border border-white/10 bg-[#2C221C]/95 backdrop-blur-xl' : 'border-t border-line bg-surface' }}"
            x-cloak
            x-show="open"
            x-transition:enter="transition ease-out duration-[400ms]"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1)"
        >
            <div class="flex flex-col gap-1">
                <a href="{{ route('explore.index') }}" class="{{ $isFlowHome ? 'journey-nav-link px-3 py-2' : 'ip-nav-item' }}">{{ __('استكشف') }}</a>
                <a href="{{ route('how-it-works') }}" class="{{ $isFlowHome ? 'journey-nav-link px-3 py-2' : 'ip-nav-item' }}">{{ __('كيف تعمل') }}</a>
                <a href="{{ route('about') }}" class="{{ $isFlowHome ? 'journey-nav-link px-3 py-2' : 'ip-nav-item' }}">{{ __('عن المنصة') }}</a>
                <a href="{{ route('contact') }}" class="{{ $isFlowHome ? 'journey-nav-link px-3 py-2' : 'ip-nav-item' }}">{{ __('تواصل') }}</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="ip-btn mt-3">{{ __('مساحتي') }}</a>
                @else
                    <a href="{{ route('login') }}" class="ip-btn-secondary mt-3">{{ __('دخول') }}</a>
                    <a href="{{ route('register') }}" class="ip-btn mt-2">{{ __('ابدأ رحلتك') }}</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="mt-20 border-t border-line bg-surface">
        <div class="ip-container flex flex-col gap-6 py-10 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <x-innerpath.logo />
                <p class="ip-muted mt-3 max-w-sm">{{ __('مساحة خاصة للتأمل والنمو — بهدوء ووضوح.') }}</p>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-ink-secondary">
                <a href="{{ route('explore.index') }}" class="hover:text-ink">{{ __('استكشف') }}</a>
                <a href="{{ route('editorial') }}" class="hover:text-ink">{{ __('التحرير') }}</a>
                <a href="{{ route('ai-transparency') }}" class="hover:text-ink">{{ __('الذكاء الاصطناعي') }}</a>
                <a href="{{ route('privacy') }}" class="hover:text-ink">{{ __('الخصوصية') }}</a>
                <a href="{{ route('terms') }}" class="hover:text-ink">{{ __('الشروط') }}</a>
                <a href="{{ route('contact') }}" class="hover:text-ink">{{ __('تواصل') }}</a>
            </div>
        </div>
        <div class="border-t border-line">
            <div class="ip-container py-4 text-xs text-ink-muted">© {{ date('Y') }} InnerPath</div>
        </div>
    </footer>
</body>
</html>
