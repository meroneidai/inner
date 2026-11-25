<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'InnerPath'))</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="ip-shell">
        @include('layouts.partials.sidebar')
        <div class="ip-main">
            @include('layouts.partials.topbar')

            @isset($header)
                <header class="ip-container pt-7 pb-2 lg:pt-10">
                    {{ $header }}
                </header>
            @endisset

            @if (session('status'))
                <div class="ip-container pt-3">
                    <div class="ip-status" role="status">{{ session('status') }}</div>
                </div>
            @endif

            <main class="py-5 lg:py-7">
                {{ $slot }}
            </main>
        </div>
        @include('layouts.partials.bottom-nav')
    </div>
</body>
</html>
