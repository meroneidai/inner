@extends('layouts.public')
@section('title', __('سياسة الخصوصية'))
@section('content')
<section class="ip-atmosphere border-b border-line py-10">
    <div class="ip-container">
        <x-innerpath.visual src="{{ asset('images/brand/doorway-light.jpg') }}" :alt="__('خصوصية وهدوء')" ratio="16/9" class="max-h-56" />
    </div>
</section>
<div class="ip-container py-14 lg:py-20">
    <div class="mx-auto max-w-reading">
        <h1 class="font-display text-h1 text-ink">{{ __('سياسة الخصوصية') }}</h1>
        <div class="ip-prose-calm mt-6 space-y-4">
            <p>{{ __('أحلامك وبياناتك الشخصية معلومات حساسة. لا نستخدم أحلامك الخاصة لتدريب نماذج دون موافقة صريحة. يمكنك تصدير بياناتك أو حذف حسابك في أي وقت.') }}</p>
        </div>
    </div>
</div>
@endsection
