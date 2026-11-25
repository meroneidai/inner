<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display text-h1 text-ink">{{ __('الإعدادات') }}</h2>
        <p class="ip-muted mt-1">{{ __('الملف، الإشعارات، والبريد — كل شيء تحت سيطرتك.') }}</p>
    </x-slot>

    <div class="ip-container max-w-2xl space-y-4">
        <form method="POST" action="{{ route('settings.update') }}" class="ip-card space-y-5 p-6">
            @csrf @method('PATCH')

            <div>
                <h3 class="font-display text-h4 text-ink">{{ __('الملف') }}</h3>
                <div class="mt-3 space-y-3">
                    <x-floating-input name="preferred_name" :label="__('الاسم المفضل')" :value="old('preferred_name', $user->profile?->preferred_name)" />
                    <div class="ip-float ip-float--always">
                        <select name="locale" id="settings_locale" class="ip-input ip-float-control">
                            <option value="ar" @selected(($user->preferences?->locale ?? 'ar') === 'ar')>العربية</option>
                            <option value="en" @selected(($user->preferences?->locale ?? '') === 'en')>English</option>
                        </select>
                        <label class="ip-float-label" for="settings_locale">{{ __('اللغة') }}</label>
                    </div>
                    <x-floating-input name="timezone" :label="__('المنطقة الزمنية')" :value="old('timezone', $user->preferences?->timezone ?? 'UTC')" placeholder="UTC / Asia/Riyadh" />
                    <x-floating-input name="cultural_background" :label="__('الخلفية الثقافية')" :value="old('cultural_background', $user->profile?->cultural_background)" />
                    <x-floating-input name="religious_spiritual_background" :label="__('الخلفية الروحية')" :value="old('religious_spiritual_background', $user->profile?->religious_spiritual_background)" />
                    <x-floating-input type="textarea" name="life_circumstances" :label="__('ظروف حياتية')" :rows="3" :value="old('life_circumstances', $user->profile?->life_circumstances)" />
                </div>
            </div>

            <div class="border-t border-line pt-5">
                <h3 class="font-display text-h4 text-ink">{{ __('نظام الإشعارات') }}</h3>
                <p class="ip-muted mt-1">{{ __('داخل التطبيق + البريد الإلكتروني حسب اختيارك.') }}</p>
                <div class="mt-4 space-y-3 text-sm text-ink">
                    <label class="flex items-center gap-2"><input type="checkbox" name="in_app_notifications" value="1" @checked($user->preferences?->in_app_notifications ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('إشعارات داخل التطبيق') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="email_notifications" value="1" @checked($user->preferences?->email_notifications ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('إشعارات البريد') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="daily_care_enabled" value="1" @checked($user->preferences?->daily_care_enabled ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('رسالة عناية يومية') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="daily_reminder_enabled" value="1" @checked($user->preferences?->daily_reminder_enabled ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('تذكير تسجيل حلم اليوم') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="analysis_ready_enabled" value="1" @checked($user->preferences?->analysis_ready_enabled ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('إشعار اكتمال تحليل الحلم') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="growth_reminder_enabled" value="1" @checked($user->preferences?->growth_reminder_enabled ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('تذكير مهام النمو') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="weekly_report_enabled" value="1" @checked($user->preferences?->weekly_report_enabled ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('تقرير أسبوعي') }}</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="monthly_report_enabled" value="1" @checked($user->preferences?->monthly_report_enabled ?? true) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('تقرير شهري') }}</label>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <x-floating-input type="number" name="daily_care_hour" :label="__('ساعة رسالة العناية (0–23 UTC)')" :value="old('daily_care_hour', $user->preferences?->daily_care_hour ?? 8)" min="0" max="23" />
                    <x-floating-input type="number" name="daily_reminder_hour" :label="__('ساعة تذكير الحلم (0–23 UTC)')" :value="old('daily_reminder_hour', $user->preferences?->daily_reminder_hour ?? 20)" min="0" max="23" />
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-secondary"><input type="checkbox" name="allow_ai_training_consent" value="1" @checked($user->preferences?->allow_ai_training_consent) class="rounded-control border-line text-primary focus:ring-primary/30"> {{ __('موافقة على استخدام مجهّل للتدريب (اختياري)') }}</label>
            <button class="ip-btn">{{ __('حفظ') }}</button>
        </form>
        <a href="{{ route('settings.privacy') }}" class="text-sm text-primary-dark hover:underline">{{ __('الخصوصية وإدارة البيانات') }}</a>
    </div>
</x-app-layout>
