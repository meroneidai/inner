@extends('layouts.public')
@section('title', __('تواصل'))
@section('content')
<section class="ip-page-hero min-h-[40dvh]">
    <div class="ip-page-hero-media" aria-hidden="true">
        <img src="{{ asset('images/brand/doorway-light.jpg') }}" alt="" fetchpriority="high" decoding="async">
    </div>
    <div class="ip-page-hero-veil"></div>
    <div class="ip-container relative flex min-h-[40dvh] flex-col justify-end pb-12 pt-24">
        <div class="ip-fade-up max-w-xl">
            <h1 class="font-display text-h1 text-ink">{{ __('تواصل معنا') }}</h1>
            <p class="ip-muted mt-2">{{ __('نرحب بأسئلتك وملاحظاتك بهدوء.') }}</p>
        </div>
    </div>
</section>

<section class="ip-atmosphere border-t border-line">
    <div class="ip-container py-14 lg:py-16">
        <div class="mx-auto max-w-xl">
            @if(session('status'))
                <div class="ip-status mb-6">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('contact.send') }}" class="ip-card space-y-4 p-6 sm:p-8">
                @csrf
                <x-floating-input name="name" :label="__('الاسم')" :value="old('name')" required />
                <x-floating-input type="email" name="email" :label="__('البريد')" :value="old('email')" required autocomplete="email" />
                <x-floating-input type="textarea" name="message" :label="__('الرسالة')" :rows="5" :value="old('message')" required />
                <button class="ip-btn">{{ __('إرسال') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection
