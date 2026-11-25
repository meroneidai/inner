<x-app-layout>
    <x-slot name="header"><h2 class="text-2xl font-semibold">{{ __('تعديل الحلم') }}</h2></x-slot>
    <div class="ip-container max-w-3xl">
        <form method="POST" action="{{ route('dreams.update', $dream) }}" class="ip-card space-y-4 p-6">
            @csrf @method('PUT')
            <x-floating-input name="title" :label="__('العنوان')" :value="old('title', $dream->title)" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-floating-input type="date" name="dream_date" :label="__('التاريخ')" :value="old('dream_date', $dream->dream_date?->toDateString())" required />
                <x-floating-input type="time" name="dream_time" :label="__('الوقت')" :value="old('dream_time', $dream->dream_time)" />
            </div>
            <x-floating-input type="textarea" name="corrected_text" :label="__('النص')" :rows="8" :value="old('corrected_text', $dream->corrected_text ?: $dream->original_text)" required />
            <div class="grid gap-4 sm:grid-cols-3">
                <x-floating-input type="number" name="sleep_quality" :label="__('جودة النوم')" :value="old('sleep_quality', $dream->sleep_quality)" min="1" max="10" />
                <x-floating-input type="number" name="emotional_intensity" :label="__('شدة المشاعر')" :value="old('emotional_intensity', $dream->emotional_intensity)" min="1" max="10" />
                <x-floating-input name="mood_upon_waking" :label="__('المزاج')" :value="old('mood_upon_waking', $dream->mood_upon_waking)" />
            </div>
            <x-floating-input type="textarea" name="personal_notes" :label="__('ملاحظات')" :rows="3" :value="old('personal_notes', $dream->personal_notes)" />
            <button class="ip-btn">{{ __('حفظ') }}</button>
        </form>
    </div>
</x-app-layout>
