<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="ip-section-title">{{ __('تسجيل حلم جديد') }}</h1>
            <p class="ip-muted mt-1">{{ __('اكتب أو سجّل — التحليل يبدأ تلقائياً بعد توفر النص.') }}</p>
        </div>
    </x-slot>

    <div class="ip-container max-w-3xl space-y-5">
        <x-innerpath.visual
            src="{{ asset('images/brand/journal.jpg') }}"
            :alt="__('دفتر يوميات هادئ')"
            ratio="16/9"
            class="max-h-48"
        />
        <form method="POST" action="{{ route('dreams.store') }}" enctype="multipart/form-data" class="ip-card space-y-5 p-6 sm:p-8" x-data="voiceRecorder">
            @csrf
            <x-floating-input name="title" :label="__('العنوان')" :value="old('title')" />
            <div class="grid gap-4 sm:grid-cols-2">
                <x-floating-input type="date" name="dream_date" :label="__('تاريخ الحلم')" :value="old('dream_date', now()->toDateString())" required />
                <x-floating-input type="time" name="dream_time" :label="__('الوقت التقريبي')" :value="old('dream_time')" />
            </div>
            <x-floating-input type="textarea" name="original_text" :label="__('نص الحلم')" :rows="8" :value="old('original_text')" class="leading-[1.75]" />

            <div class="rounded-card border border-line bg-canvas-soft p-4">
                <p class="text-sm font-semibold text-ink">{{ __('تسجيل صوتي (اختياري)') }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <button type="button" class="ip-btn-secondary" @click="toggle()" :aria-pressed="recording.toString()">
                        <x-lucide-mic class="size-4" />
                        <span x-text="recording ? '{{ __('إيقاف') }}' : '{{ __('ابدأ التسجيل') }}'"></span>
                    </button>
                    <input id="audio-file-input" type="file" name="audio" accept="audio/*,video/webm" class="text-sm text-ink-secondary">
                </div>
                <template x-if="audioUrl">
                    <audio class="mt-3 w-full" controls :src="audioUrl"></audio>
                </template>
            </div>

            <div>
                <label class="ip-label">{{ __('المزاج عند الاستيقاظ') }}</label>
                <div class="flex flex-wrap gap-2" x-data="{ mood: @js(old('mood_upon_waking', '')) }">
                    <input type="hidden" name="mood_upon_waking" :value="mood">
                    @foreach(['هادئ', 'قلق', 'سعيد', 'حزين', 'متعب', 'مختلط'] as $m)
                        <button type="button" class="ip-choice" :class="mood === @js($m) ? 'ip-choice-active' : ''" @click="mood = @js($m)">
                            {{ $m }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="ip-label">{{ __('جودة النوم') }} 1–10</label>
                    <input type="range" min="1" max="10" name="sleep_quality" class="w-full accent-primary" value="{{ old('sleep_quality', 6) }}" oninput="this.nextElementSibling.textContent=this.value">
                    <p class="mt-1 text-sm font-semibold tabular-nums text-primary-dark">{{ old('sleep_quality', 6) }}</p>
                </div>
                <div>
                    <label class="ip-label">{{ __('شدة المشاعر') }} 1–10</label>
                    <input type="range" min="1" max="10" name="emotional_intensity" class="w-full accent-primary" value="{{ old('emotional_intensity', 5) }}" oninput="this.nextElementSibling.textContent=this.value">
                    <p class="mt-1 text-sm font-semibold tabular-nums text-primary-dark">{{ old('emotional_intensity', 5) }}</p>
                </div>
            </div>
            <x-floating-input type="textarea" name="personal_notes" :label="__('ملاحظات شخصية')" :rows="3" :value="old('personal_notes')" />
            <button class="ip-btn w-full sm:w-auto">{{ __('حفظ الحلم') }}</button>
        </form>
    </div>
</x-app-layout>
