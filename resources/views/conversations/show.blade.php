<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-3xl font-semibold">💬 {{ $conversation->title }}</h2>
        @if($conversation->dream)
            <p class="ip-muted mt-1">{{ __('مرتبط بحلم') }}: {{ \Illuminate\Support\Str::limit($conversation->dream->displayText(), 80) }}</p>
        @endif
    </x-slot>

    <div class="ip-container max-w-3xl space-y-4"
         x-data="{
            listening: false,
            recognition: null,
            speaking: false,
            start() {
                const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SR) { alert(@js(__('المتصفح لا يدعم الإدخال الصوتي'))); return; }
                this.recognition = new SR();
                this.recognition.lang = @js(app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US');
                this.recognition.onresult = (e) => {
                    this.$refs.message.value = (this.$refs.message.value ? this.$refs.message.value + ' ' : '') + e.results[0][0].transcript;
                };
                this.recognition.onend = () => this.listening = false;
                this.recognition.start();
                this.listening = true;
            },
            stop() { this.recognition?.stop(); this.listening = false; },
            speak(text) {
                if (!window.speechSynthesis) return;
                window.speechSynthesis.cancel();
                const u = new SpeechSynthesisUtterance(text);
                u.lang = @js(app()->getLocale() === 'ar' ? 'ar-SA' : 'en-US');
                u.onstart = () => this.speaking = true;
                u.onend = () => this.speaking = false;
                window.speechSynthesis.speak(u);
            }
         }">
        @foreach($conversation->messages as $message)
            <div class="ip-card p-5 {{ $message->role === 'user' ? 'bg-peach/15' : 'bg-mint/10' }}">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-xs font-medium text-charcoal/50">
                        {{ $message->role === 'user' ? __('أنت') : 'InnerPath' }}
                    </p>
                    @if($message->role === 'assistant')
                        <button type="button" class="ip-chip-joy" @click="speak(@js($message->content))">🔊 {{ __('استمع') }}</button>
                    @endif
                </div>
                <p class="mt-2 whitespace-pre-wrap text-sm leading-7 text-pretty">{{ $message->content }}</p>
            </div>
        @endforeach

        <form method="POST" action="{{ route('conversations.reply', $conversation) }}" class="ip-card space-y-3 p-4">
            @csrf
            <input type="hidden" name="mode" value="deep">
            <div class="flex items-center justify-between gap-2">
                <label class="ip-label mb-0">{{ __('رسالتك') }}</label>
                <button type="button" class="ip-chip-joy" @click="listening ? stop() : start()">
                    <span x-text="listening ? '{{ __('إيقاف') }}' : '{{ __('تحدّث') }}'"></span>
                </button>
            </div>
            <textarea x-ref="message" name="message" rows="3" class="ip-input" required placeholder="{{ __('اكتب أو تحدّث…') }}"></textarea>
            <button class="ip-btn">{{ __('إرسال') }}</button>
        </form>
    </div>
</x-app-layout>
