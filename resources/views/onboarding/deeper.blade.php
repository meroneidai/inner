<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold">{{ __('تعميق الملف (اختياري)') }}</h2>
        <p class="ip-muted">{{ __('يمكنك تخطي أي قسم. التقدم الحالي') }}: {{ $progress }}%</p>
    </x-slot>
    <div class="ip-container grid gap-4 md:grid-cols-2">
        @foreach($sections as $section)
            <a href="{{ route('onboarding.section', $section->slug) }}" class="ip-card block p-5 hover:ring-sage/40">
                <h3 class="font-semibold">{{ $section->localizedName() }}</h3>
                <p class="ip-muted mt-2">{{ $section->localizedDescription() }}</p>
                @if($section->is_sensitive)
                    <span class="ip-chip mt-3">{{ __('حسّاس') }}</span>
                @endif
            </a>
        @endforeach
    </div>
    <div class="ip-container mt-6">
        <a href="{{ route('inner-world.index') }}" class="ip-btn-secondary">{{ __('الذهاب إلى عالمي الداخلي') }}</a>
        <a href="{{ route('dashboard') }}" class="ms-3 text-sm text-sage-dark">{{ __('لوحة التحكم') }}</a>
    </div>
</x-app-layout>
