@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'robots' => 'index,follow',
    'jsonLd' => null,
])

@php
    $pageTitle = $title ?: config('app.name', 'InnerPath');
    $pageDescription = $description ?: __('مساحة هادئة لفهم الأحلام واكتشاف الذات.');
    $pageCanonical = $canonical ?: url()->current();
    $pageImage = $image ? url($image) : asset('images/brand/hero-path.jpg');
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<link rel="canonical" href="{{ $pageCanonical }}">
<meta name="robots" content="{{ $robots }}">

<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="InnerPath">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $pageCanonical }}">
<meta property="og:image" content="{{ $pageImage }}">
<meta property="og:locale" content="{{ app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US' }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $pageImage }}">

@if($jsonLd)
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif
