<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-2xl font-semibold">{{ $dream->title ?: __('تفاصيل الحلم') }}</h2>
                <p class="ip-muted">{{ $dream->dream_date?->translatedFormat('d M Y') }} · {{ $dream->processing_status }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dreams.edit', $dream) }}" class="ip-btn-secondary">{{ __('تعديل') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="ip-container grid gap-6 lg:grid-cols-3">
        <div class="space-y-4 lg:col-span-2">
            <div class="ip-card p-6">
                <h3 class="font-semibold">{{ __('النص') }}</h3>
                <p class="mt-3 whitespace-pre-wrap leading-8">{{ $dream->displayText() ?: __('لا يوجد نص بعد') }}</p>
            </div>

            @if($dream->latestTranscription && $dream->latestAnalysis?->status !== 'completed')
                <div class="ip-card p-6">
                    <h3 class="font-semibold">{{ __('تفريغ الصوت') }}</h3>
                    @if($dream->latestTranscription->confidence)
                        <p class="ip-muted mt-1">{{ __('الثقة التقريبية') }}: {{ $dream->latestTranscription->confidence }}</p>
                    @endif
                    <form method="POST" action="{{ route('dreams.transcription.confirm', $dream) }}" class="mt-3 space-y-3">
                        @csrf
                        <textarea name="edited_transcription" rows="6" class="ip-input">{{ old('edited_transcription', $dream->latestTranscription->edited_transcription ?: $dream->latestTranscription->original_transcription) }}</textarea>
                        <button class="ip-btn-secondary">{{ __('حفظ النص المؤكد') }}</button>
                        <p class="ip-muted">{{ __('بعد التأكيد سيبدأ التحليل تلقائياً إن لم يكن قد اكتمل.') }}</p>
                    </form>
                </div>
            @endif

            @if(in_array($dream->processing_status, ['analyzing', 'transcribing'], true) || $dream->latestAnalysis?->status === 'processing')
                <div
                    class="ip-card p-6 text-sm text-sage-dark"
                    x-data
                    x-init="setTimeout(() => window.location.reload(), 4000)"
                >
                    <p>{{ __('جاري معالجة الحلم وحفظ التحليل في قاعدة البيانات…') }}</p>
                    <p class="ip-muted mt-2 text-xs">{{ __('تُحدَّث الصفحة تلقائياً عند اكتمال التحليل.') }}</p>
                </div>
            @endif

            @if($dream->latestAnalysis?->status === 'completed')
                @php
                    $payload = $dream->latestAnalysis->payload ?? [];
                    $careTags = $payload['care_tags'] ?? [];
                    $encouragement = $payload['daily_encouragement'] ?? null;
                    $symbols = $payload['main_symbols'] ?? [];
                    $steps = $payload['step_by_step_growth'] ?? [];
                @endphp
                <div class="ip-card p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-h4 text-ink">{{ __('ملخص التحليل') }}</h3>
                        <a href="{{ route('analyses.show', $dream->latestAnalysis) }}" class="text-sm text-primary-dark hover:underline">{{ __('عرض كامل') }}</a>
                    </div>
                    <p class="mt-3 text-sm leading-[1.8] text-ink-secondary text-pretty">{{ $dream->latestAnalysis->summary }}</p>
                    @if($encouragement)
                        <p class="mt-4 rounded-card border border-line bg-beige/60 px-4 py-3 text-sm text-ink">{{ $encouragement }}</p>
                    @endif
                    @if(is_array($careTags) && count($careTags))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach($careTags as $tag)
                                @if(is_array($tag))
                                    <span class="ip-chip">{{ $tag['label'] ?? '' }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                @if(is_array($symbols) && count($symbols))
                    <div class="ip-card space-y-4 p-6">
                        <h3 class="font-display text-h4 text-ink">{{ __('رموز ودلالاتها لك') }}</h3>
                        @foreach($symbols as $symbol)
                            @continue(!is_array($symbol))
                            <div class="rounded-card border border-line bg-canvas-soft p-4">
                                <p class="font-semibold text-ink">{{ $symbol['symbol'] ?? '—' }}</p>
                                <p class="ip-muted mt-1">{{ $symbol['context'] ?? '' }}</p>
                                @php $assoc = $symbol['personal_associations'] ?? []; @endphp
                                @if(is_array($assoc) && count($assoc))
                                    <p class="mt-2 text-sm text-primary-dark">
                                        <span class="font-medium">{{ __('ارتباطاتك الشخصية') }}:</span>
                                        {{ collect($assoc)->map(fn ($a) => is_string($a) ? $a : json_encode($a, JSON_UNESCAPED_UNICODE))->implode(' · ') }}
                                    </p>
                                @else
                                    <p class="mt-2 text-xs text-ink-muted">{{ __('لا توجد ارتباطات شخصية مؤكدة بعد — أكمل ملفك لتدقيق الدلالة.') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(is_array($steps) && count($steps))
                    <div class="ip-card p-6">
                        <h3 class="font-display text-h4 text-ink">{{ __('خطوات لطيفة للتطور') }}</h3>
                        <ol class="mt-3 list-decimal space-y-2 pe-5 text-sm leading-7 text-ink-secondary">
                            @foreach($steps as $step)
                                @continue(!is_array($step) || blank($step['title'] ?? null))
                                <li>
                                    <span class="font-medium text-ink">{{ $step['title'] }}</span>
                                    @if(!blank($step['why'] ?? null))
                                        <span class="ip-muted"> — {{ $step['why'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                        <a href="{{ route('conversations.create', ['mode' => 'deep']) }}" class="ip-btn-secondary mt-4 inline-flex">{{ __('ناقش ذلك في حوار') }}</a>
                    </div>
                @endif
            @elseif($dream->latestAnalysis?->status === 'failed')
                <div class="ip-card border-danger/30 p-6 text-sm text-danger">
                    {{ __('تعذر إكمال التحليل. سيُعاد عند توفر الخدمة أو تحديث النص.') }}
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="ip-card space-y-2 p-5 text-sm">
                <p><span class="ip-muted">{{ __('المزاج') }}:</span> {{ $dream->mood_upon_waking ?: '—' }}</p>
                <p><span class="ip-muted">{{ __('شدة المشاعر') }}:</span> {{ $dream->emotional_intensity ?: '—' }}</p>
                <p><span class="ip-muted">{{ __('جودة النوم') }}:</span> {{ $dream->sleep_quality ?: '—' }}</p>
                <p><span class="ip-muted">{{ __('الطريقة') }}:</span> {{ $dream->entry_method }}</p>
            </div>
            <div class="ip-card p-5">
                <h3 class="font-semibold">{{ __('رموز مستخرجة') }}</h3>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse($dream->symbolOccurrences as $occ)
                        <span class="ip-chip">{{ $occ->symbol?->localizedName() }}</span>
                    @empty
                        <span class="ip-muted text-sm">{{ __('تظهر بعد اكتمال التحليل') }}</span>
                    @endforelse
                </div>
            </div>
            <form method="POST" action="{{ route('dreams.destroy', $dream) }}" onsubmit="return confirm('{{ __('حذف الحلم؟') }}')">
                @csrf @method('DELETE')
                <button class="text-sm text-danger">{{ __('حذف الحلم') }}</button>
            </form>
        </div>
    </div>
</x-app-layout>
