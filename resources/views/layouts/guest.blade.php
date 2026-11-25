<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'InnerPath') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas">
    <div class="grid min-h-dvh lg:grid-cols-2">
        <aside class="relative hidden overflow-hidden lg:block" aria-hidden="true">
            <img src="{{ asset('images/brand/hero-path.jpg') }}" alt="" class="absolute inset-0 size-full object-cover" fetchpriority="high" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-t from-[#30332F]/55 via-[#30332F]/20 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-10 text-white">
                <x-innerpath.logo tone="dark" />
                <p class="mt-5 max-w-sm font-display text-2xl leading-relaxed">{{ __('رحلتك لفهم نفسك تبدأ من الداخل.') }}</p>
            </div>
        </aside>

        <div class="flex flex-col items-center justify-center px-5 py-10">
            <a href="{{ route('home') }}" class="mb-8 lg:hidden" aria-label="InnerPath">
                <x-innerpath.logo class="scale-110" />
            </a>
            <div class="ip-card w-full max-w-md p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
