@extends('layouts.public')
@section('title', __('الشروط'))
@section('content')
<section class="ip-atmosphere border-b border-line py-10">
    <div class="ip-container">
        <x-innerpath.visual src="{{ asset('images/brand/still-water.jpg') }}" :alt="__('هدوء وتأمل')" ratio="16/9" class="max-h-56" />
    </div>
</section>
<div class="ip-container py-14 lg:py-20">
    <div class="mx-auto max-w-reading">
        <h1 class="font-display text-h1 text-ink">{{ __('شروط الاستخدام') }}</h1>
        <div class="ip-prose-calm mt-6 space-y-4">
            <p>{{ __('InnerPath مساحة للتأمل الذاتي والنمو الشخصي. التحليلات تفسيرية وليست تشخيصاً طبياً أو نفسياً أو تنبؤاً بالمستقبل. استخدم المنصة بمسؤولية، واطلب مساعدة مختص عند الحاجة.') }}</p>
        </div>
    </div>
</div>
@endsection
