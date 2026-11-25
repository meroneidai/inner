<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-ink-muted">{{ __('مرحباً من جديد') }}</p>
            <h1 class="font-display text-h1 text-ink">
                {{ $preferredName }}
            </h1>
            <p class="ip-muted mt-2 max-w-xl">{{ __('مساحة بسيطة لفهم أحلامك والعناية بنفسك — خطوة واحدة في كل مرة.') }}</p>
        </div>
    </x-slot>

    <div class="ip-container space-y-6">
        <section class="relative overflow-hidden rounded-section border border-line">
            <img src="{{ asset('images/brand/hero-path.jpg') }}" alt="" class="absolute inset-0 size-full object-cover opacity-35" loading="lazy" decoding="async">
            <div class="absolute inset-0 bg-gradient-to-l from-canvas via-canvas/92 to-canvas/75"></div>
            <div class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-end sm:justify-between sm:p-8">
                <div class="max-w-xl">
                    <p class="text-sm font-medium text-ink-muted">{{ __('اليوم') }}</p>
                    <h2 class="mt-2 font-display text-h3 text-ink">
                        @if($todayDream)
                            {{ __('حلمك اليوم محفوظ — تابع التحليل أو أضف ملاحظة.') }}
                        @else
                            {{ __('سجّل حلمك قبل أن يتلاشى.') }}
                        @endif
                    </h2>
                    <p class="mt-2 text-sm leading-relaxed text-ink-secondary">{{ __('التحليل يبدأ تلقائياً بعد توفر النص.') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if($todayDream)
                        <a href="{{ route('dreams.show', $todayDream) }}" class="ip-btn">{{ __('عرض حلم اليوم') }}</a>
                    @else
                        <a href="{{ route('dreams.create') }}" class="ip-btn">{{ __('سجّل حلماً') }}</a>
                    @endif
                    <a href="{{ route('conversations.create', ['mode' => 'voice']) }}" class="ip-btn-secondary">{{ __('حوار') }}</a>
                </div>
            </div>
        </section>

        @if($careMessages->isNotEmpty())
            <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($careMessages->take(3) as $care)
                    <article class="ip-care-card ip-care-{{ $care->tone }}">
                        <div class="flex items-start gap-3">
                            <span class="ip-icon-box" aria-hidden="true">
                                <x-lucide-heart class="size-4" />
                            </span>
                            <div>
                                <h3 class="font-display text-h4 text-ink">{{ $care->title }}</h3>
                                <p class="mt-1 text-sm leading-7 text-ink-secondary text-pretty">{{ $care->body }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>
        @endif

        @if($profileCompletion < 40)
            <div class="ip-card flex flex-wrap items-center justify-between gap-3 p-5">
                <div class="flex items-start gap-3">
                    <span class="ip-icon-box"><x-lucide-compass class="size-4" /></span>
                    <div>
                        <p class="font-semibold text-ink">{{ __('حسّن دقة التحليل') }}</p>
                        <p class="ip-muted mt-1">{{ __('أسئلة اختيارية قصيرة — بدون كتابة مرهقة.') }} ({{ $profileCompletion }}%)</p>
                    </div>
                </div>
                <a href="{{ route('onboarding.welcome') }}" class="ip-btn-secondary">{{ __('إكمال الملف') }}</a>
            </div>
        @endif

        <section class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="ip-stat">
                <span class="ip-icon-box"><x-lucide-book-open class="size-4" /></span>
                <p class="ip-muted">{{ __('الأحلام') }}</p>
                <p class="font-display text-3xl font-semibold tabular-nums text-ink">{{ $totalDreams }}</p>
            </div>
            <div class="ip-stat">
                <span class="ip-icon-box"><x-lucide-sprout class="size-4" /></span>
                <p class="ip-muted">{{ __('موضوعات النمو') }}</p>
                <p class="font-display text-3xl font-semibold tabular-nums text-ink">{{ $activeThemes }}</p>
            </div>
            <a href="{{ route('notifications.index') }}" class="ip-stat ip-card-hover block">
                <span class="ip-icon-box"><x-lucide-bell class="size-4" /></span>
                <p class="ip-muted">{{ __('إشعارات') }}</p>
                <p class="font-display text-3xl font-semibold tabular-nums text-ink">{{ auth()->user()->unreadNotifications()->count() }}</p>
            </a>
            <a href="{{ route('reports.weekly') }}" class="ip-stat ip-card-hover block">
                <span class="ip-icon-box"><x-lucide-calendar-days class="size-4" /></span>
                <p class="ip-muted">{{ __('التقارير') }}</p>
                <p class="mt-2 text-sm font-medium text-primary-dark">{{ __('أسبوعي / شهري') }}</p>
            </a>
        </section>

        <section class="grid gap-4 lg:grid-cols-5">
            <div class="ip-card p-6 lg:col-span-3">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="font-display text-h4 text-ink">{{ __('خطواتك الآن') }}</h3>
                    <a href="{{ route('growth.index') }}" class="text-sm font-medium text-primary-dark hover:underline">{{ __('الكل') }}</a>
                </div>
                <ul class="mt-4 space-y-2">
                    @forelse($pendingActions as $action)
                        <li class="flex items-center justify-between gap-3 rounded-card border border-line bg-canvas-soft/80 px-4 py-3">
                            <div class="min-w-0">
                                <span class="ip-chip">{{ $action->cadenceLabel() }}</span>
                                <p class="mt-2 truncate text-sm font-medium text-ink">{{ $action->title }}</p>
                            </div>
                            <form method="POST" action="{{ route('growth.actions.complete', $action) }}">
                                @csrf
                                <button class="ip-btn py-2 text-xs">{{ __('تم') }}</button>
                            </form>
                        </li>
                    @empty
                        <li class="ip-empty py-8">
                            <span class="ip-icon-box mx-auto"><x-lucide-leaf class="size-4" /></span>
                            <p class="ip-muted mt-3">{{ __('لا مهام معلّقة — استرح أو سجّل حلماً جديداً.') }}</p>
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="space-y-4 lg:col-span-2">
                <div class="ip-card p-6">
                    <h3 class="font-display text-h4 text-ink">{{ __('تحليلات أخيرة') }}</h3>
                    <ul class="mt-3 space-y-1 text-sm">
                        @forelse($recentAnalyses as $analysis)
                            <li>
                                <a class="block rounded-control px-2 py-2.5 text-ink-secondary transition hover:bg-canvas-soft hover:text-ink" href="{{ route('analyses.show', $analysis) }}">
                                    {{ \Illuminate\Support\Str::limit($analysis->summary, 70) }}
                                </a>
                            </li>
                        @empty
                            <li class="ip-muted px-2 py-2">{{ __('لا توجد تحليلات بعد') }}</li>
                        @endforelse
                    </ul>
                </div>
                <div class="ip-card p-6">
                    <h3 class="font-display text-h4 text-ink">{{ __('رموز متكررة') }}</h3>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse($symbolCounts as $name => $count)
                            <span class="ip-chip">{{ $name }} · {{ $count }}</span>
                        @empty
                            <span class="ip-muted">{{ __('ستظهر بعد التحليل') }}</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
