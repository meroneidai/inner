<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-h1 text-ink">
            @if(($mode ?? 'text') === 'voice')
                {{ __('حوار صوتي داعم') }}
            @elseif(($mode ?? 'text') === 'deep')
                {{ __('حوار عميق بلطف') }}
            @else
                {{ __('حوار جديد') }}
            @endif
        </h2>
        <p class="ip-muted mt-2">{{ __('مساحة آمنة للنقاش — بدون تشخيص، وبخطوات عملية.') }}</p>
    </x-slot>

    <div class="ip-container max-w-2xl"
         x-data="{
            listening: false,
            recognition: null,
            start() {
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SR) { alert(@js(__('المتصفح لا يدعم الإدخال الصوتي'))); return; }
                this.recognition = new SR();
                this.recognition.lang = @js(app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US');
                this.recognition.interimResults = false;
                this.recognition.onresult = (e) => {
                    const text = e.results[0][0].transcript;
                    const el = this.$refs.message;
                    el.value = (el.value ? el.value + ' ' : '') + text;
                };
                this.recognition.onend = () => this.listening = false;
                this.recognition.start();
                this.listening = true;
            },
            stop() {
                this.recognition?.stop();
                this.listening = false;
            }
         }">
        <form method="POST" action="{{ route('conversations.store') }}" class="ip-card space-y-4 p-6">
            @csrf
            <input type="hidden" name="mode" value="{{ $mode ?? 'text' }}">
            <div class="ip-float ip-float--always">
                <select name="dream_entry_id" id="dream_entry_id" class="ip-input ip-float-control">
                    <option value="">—</option>
                    @foreach($dreams as $dream)
                        <option value="{{ $dream->id }}" @selected((string) ($selectedDreamId ?? '') === (string) $dream->id)>{{ $dream->dream_date?->toDateString() }} — {{ \Illuminate\Support\Str::limit($dream->displayText(), 40) }}</option>
                    @endforeach
                </select>
                <label class="ip-float-label" for="dream_entry_id">{{ __('ربط بحلم (اختياري)') }}</label>
            </div>
            <x-floating-input name="title" :label="__('العنوان')" :value="old('title')" />
            <div class="space-y-2">
                <div class="flex justify-end">
                    <button type="button" class="ip-chip" @click="listening ? stop() : start()">
                        <x-lucide-mic class="size-3.5" />
                        <span x-text="listening ? '{{ __('إيقاف') }}' : '{{ __('تحدّث') }}'"></span>
                    </button>
                </div>
                <div class="ip-float ip-float--textarea">
                    <textarea
                        x-ref="message"
                        id="conversation_message"
                        name="message"
                        rows="6"
                        class="ip-input ip-float-control leading-[1.75]"
                        required
                        placeholder=" "
                    >{{ old('message') }}</textarea>
                    <label class="ip-float-label" for="conversation_message">{{ __('رسالتك') }}</label>
                </div>
            </div>
            <button class="ip-btn">{{ __('بدء الحوار') }}</button>
        </form>
    </div>
</x-app-layout>
