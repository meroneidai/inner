<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-semibold">{{ __('إعداد ملفك الشخصي') }}</h2>
        <p class="ip-muted">{{ __('كل الحقول اختيارية ويمكن تخطيها.') }}</p>
    </x-slot>
    <div class="ip-container max-w-2xl">
        <form method="POST" action="{{ route('onboarding.store') }}" class="ip-card space-y-4 p-6">
            @csrf
            <x-floating-input name="preferred_name" :label="__('الاسم المفضل')" :value="old('preferred_name')" />
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="ip-float ip-float--always">
                    <select name="locale" id="locale" class="ip-input ip-float-control">
                        <option value="ar" selected>العربية</option>
                        <option value="en">English</option>
                    </select>
                    <label class="ip-float-label" for="locale">{{ __('اللغة') }}</label>
                </div>
                <x-floating-input name="age_range" :label="__('الفئة العمرية')" :value="old('age_range')" placeholder="25-34" />
            </div>
            <x-floating-input name="cultural_background" :label="__('الخلفية الثقافية')" :value="old('cultural_background')" />
            <x-floating-input name="religious_spiritual_background" :label="__('الخلفية الدينية/الروحية (اختياري)')" :value="old('religious_spiritual_background')" />
            <x-floating-input name="emotional_state" :label="__('الحالة العاطفية الحالية')" :value="old('emotional_state')" />
            <x-floating-input type="number" name="sleep_quality" :label="__('جودة النوم')" :value="old('sleep_quality')" min="1" max="10" />
            <x-floating-input type="textarea" name="life_circumstances" :label="__('ظروف حياتية (اختياري)')" :rows="3" :value="old('life_circumstances')" />
            <div class="flex flex-wrap gap-3">
                <button class="ip-btn">{{ __('متابعة') }}</button>
                <button name="skip" value="1" class="ip-btn-secondary">{{ __('تخطي الآن') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
