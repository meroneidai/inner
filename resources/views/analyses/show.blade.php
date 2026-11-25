<x-app-layout>
    <x-slot name="header">
        <nav class="mb-3 flex flex-wrap items-center gap-2 text-sm text-ink-muted">
            <a href="{{ route('dashboard') }}" class="hover:text-ink">{{ __('الرئيسية') }}</a>
            <span>/</span>
            <a href="{{ route('dreams.show', $analysis->dream) }}" class="hover:text-ink">{{ __('الحلم') }}</a>
            <span>/</span>
            <span class="text-ink">{{ __('التحليل') }}</span>
        </nav>
        <h1 class="ip-section-title">{{ __('تقرير تحليل الحلم') }}</h1>
        <p class="ip-muted mt-1">
            {{ __('ثقة تفسيرية تقديرية') }}:
            <span class="font-semibold tabular-nums text-primary-dark">{{ $analysis->analysis_confidence }}%</span>
            · {{ $analysis->dream?->dream_date?->translatedFormat('d M Y') }}
        </p>
    </x-slot>

    <div class="ip-container space-y-6">
        <section class="ip-card p-6 sm:p-8">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-ink-muted">{{ __('ماذا أفعل حيال ذلك؟') }}</p>
                    <h3 class="mt-1 font-display text-h3 text-ink">{{ __('مهمة عملية بعد هذا التحليل') }}</h3>
                    <p class="ip-muted mt-1 max-w-2xl">{{ __('اختر خطوة واحدة واضحة — يومية أو أسبوعية — وطبّقها بلطف.') }}</p>
                </div>
                <a href="{{ route('conversations.create', ['mode' => 'deep']) }}" class="ip-chip">{{ __('ناقش معي') }}</a>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-3">
                @forelse($actions as $action)
                    <article class="rounded-card border border-line bg-canvas-soft p-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="ip-chip">{{ $action->cadenceLabel() }}</span>
                            @if($action->status === 'completed')
                                <span class="text-xs text-success">{{ __('تم') }}</span>
                            @endif
                        </div>
                        <p class="mt-3 text-sm font-medium leading-7 text-ink text-pretty">{{ $action->title }}</p>
                        @if($action->status !== 'completed')
                            <form method="POST" action="{{ route('growth.actions.complete', $action) }}" class="mt-4">
                                @csrf
                                <button class="ip-btn w-full text-sm">{{ __('سأبدأ / أنجزت') }}</button>
                            </form>
                        @endif
                    </article>
                @empty
                    <p class="ip-muted">{{ __('ستظهر المهام بعد اكتمال التحليل.') }}</p>
                @endforelse
            </div>
        </section>

        <section class="ip-card p-6">
            <h3 class="font-display text-h4 text-ink">{{ __('هل كان التحليل مفيداً؟') }}</h3>
            <p class="ip-muted mt-1">{{ __('تقييمك يحسّن دقة التحليلات القادمة.') }}</p>

            @if($userFeedback)
                <p class="ip-status mt-4">
                    {{ __('سجّلنا تقييمك') }}
                    @if($userFeedback->accuracy_rating)
                        · {{ __('دقة') }}: {{ $userFeedback->accuracy_rating }}/5
                    @endif
                </p>
            @endif

            <form method="POST" action="{{ route('analyses.feedback', $analysis) }}" class="mt-4 space-y-5"
                  x-data="{ relevance: @js(old('relevance', $userFeedback?->relevance)), rating: @js(old('accuracy_rating', $userFeedback?->accuracy_rating)) }">
                @csrf
                <input type="hidden" name="relevance" :value="relevance">
                <input type="hidden" name="accuracy_rating" :value="rating">

                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach([
                        ['value' => 'relevant', 'label' => __('مفيد')],
                        ['value' => 'partial', 'label' => __('جزئياً')],
                        ['value' => 'not_relevant', 'label' => __('غير مفيد')],
                        ['value' => 'discuss', 'label' => __('أريد مناقشة')],
                    ] as $opt)
                        <button type="button"
                                @click="relevance = '{{ $opt['value'] }}'"
                                :class="relevance === '{{ $opt['value'] }}' ? 'ip-choice-active' : ''"
                                class="ip-choice justify-center py-3.5">
                            {{ $opt['label'] }}
                        </button>
                    @endforeach
                </div>

                <div>
                    <p class="ip-label">{{ __('دقة التحليل (1–5)') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach([1,2,3,4,5] as $n)
                            <button type="button"
                                    @click="rating = {{ $n }}"
                                    :class="rating == {{ $n }} ? 'ip-choice-active' : ''"
                                    class="ip-choice size-11 justify-center p-0">
                                {{ $n }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-ink-secondary">
                    <input type="checkbox" name="will_try_action" value="1" class="rounded-control border-line text-primary focus:ring-primary/30" @checked(old('will_try_action', $userFeedback?->will_try_action))>
                    {{ __('سأجرّب مهمة اليوم/الأسبوع أعلاه') }}
                </label>

                <textarea name="comment" rows="2" class="ip-input" placeholder="{{ __('تعليق اختياري') }}">{{ old('comment', $userFeedback?->comment) }}</textarea>
                <button class="ip-btn" :disabled="!relevance">{{ __('حفظ التقييم') }}</button>
            </form>
        </section>

        <section class="ip-card p-6">
            <h3 class="font-display text-h4 text-ink">{{ __('ملخص') }}</h3>
            <p class="mt-3 leading-[1.8] text-ink-secondary text-pretty">{{ $analysis->summary }}</p>
            @if(data_get($analysis->payload, 'daily_encouragement'))
                <p class="mt-4 rounded-card border border-line bg-beige/60 px-4 py-3 text-sm text-ink">{{ data_get($analysis->payload, 'daily_encouragement') }}</p>
            @endif
            @if(data_get($analysis->payload, 'overall_interpretation'))
                <p class="mt-4 text-sm leading-7 text-ink-secondary text-pretty">{{ data_get($analysis->payload, 'overall_interpretation') }}</p>
            @endif
            @php $careTags = data_get($analysis->payload, 'care_tags', []); @endphp
            @if(is_array($careTags) && count($careTags))
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($careTags as $tag)
                        @continue(!is_array($tag))
                        <span class="ip-chip">{{ $tag['label'] ?? '' }}</span>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="ip-card p-6">
                <h3 class="font-display text-h4 text-ink">{{ __('الرموز') }}</h3>
                @forelse(data_get($analysis->payload, 'main_symbols', []) as $symbol)
                    @continue(!is_array($symbol))
                    <div class="mt-4 rounded-card border border-line bg-canvas-soft p-4">
                        <p class="font-medium text-ink">{{ $symbol['symbol'] ?? '' }}</p>
                        <p class="ip-muted mt-1 text-sm">{{ $symbol['context'] ?? '' }}</p>
                        @php $assoc = $symbol['personal_associations'] ?? []; @endphp
                        @if(is_array($assoc) && count($assoc))
                            <p class="mt-2 text-sm text-primary-dark">
                                <span class="font-medium">{{ __('لك شخصياً') }}:</span>
                                {{ collect($assoc)->map(fn ($a) => is_string($a) ? $a : json_encode($a, JSON_UNESCAPED_UNICODE))->implode(' · ') }}
                            </p>
                        @endif
                        <ul class="mt-2 space-y-1 text-sm text-ink-secondary">
                            @foreach(array_slice($symbol['possible_interpretations'] ?? [], 0, 3) as $interp)
                                <li>• {{ is_string($interp) ? $interp : json_encode($interp, JSON_UNESCAPED_UNICODE) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="ip-muted mt-3">{{ __('لا رموز مستخرجة') }}</p>
                @endforelse
            </section>

            <section class="ip-card p-6">
                <h3 class="font-display text-h4 text-ink">{{ __('للتأمل') }}</h3>
                <p class="ip-muted mt-1">{{ __('فرضيات للتأمل — ليست أحكاماً.') }}</p>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach(data_get($analysis->payload, 'psychological_reflection.possible_themes', []) as $theme)
                        <li class="rounded-control border border-line bg-canvas-soft px-3 py-2 text-ink-secondary">{{ is_string($theme) ? $theme : json_encode($theme, JSON_UNESCAPED_UNICODE) }}</li>
                    @endforeach
                </ul>
                <h4 class="mt-5 font-medium text-ink">{{ __('أسئلة قصيرة') }}</h4>
                <ul class="mt-2 space-y-2 text-sm">
                    @foreach(array_slice(data_get($analysis->payload, 'reflection_questions', []), 0, 4) as $q)
                        <li class="rounded-control border border-line bg-primary-soft/50 px-3 py-2 text-ink-secondary">{{ is_string($q) ? $q : json_encode($q, JSON_UNESCAPED_UNICODE) }}</li>
                    @endforeach
                </ul>
            </section>
        </div>

        <section class="ip-card p-6">
            <h3 class="font-display text-h4 text-ink">{{ __('حدود التحليل') }}</h3>
            <ul class="mt-2 space-y-1 text-sm text-ink-secondary">
                @foreach(data_get($analysis->payload, 'limitations', []) as $limit)
                    <li>• {{ is_string($limit) ? $limit : json_encode($limit, JSON_UNESCAPED_UNICODE) }}</li>
                @endforeach
            </ul>
            <p class="mt-4 text-xs text-ink-muted">{{ config('innerpath.disclaimer') }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('dreams.show', $analysis->dream) }}" class="ip-btn-secondary">{{ __('العودة للحلم') }}</a>
                <a href="{{ route('growth.index') }}" class="ip-btn-secondary">{{ __('صفحة النمو') }}</a>
                <a href="{{ route('dashboard') }}" class="ip-btn-secondary">{{ __('الرئيسية') }}</a>
            </div>
        </section>
    </div>
</x-app-layout>
