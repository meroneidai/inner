<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user()->load(['profile', 'preferences']);

        return view('settings.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'preferred_name' => ['nullable', 'string', 'max:120'],
            'locale' => ['required', 'in:ar,en'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'daily_care_hour' => ['nullable', 'integer', 'min:0', 'max:23'],
            'daily_reminder_hour' => ['nullable', 'integer', 'min:0', 'max:23'],
            'cultural_background' => ['nullable', 'string', 'max:180'],
            'religious_spiritual_background' => ['nullable', 'string', 'max:180'],
            'emotional_state' => ['nullable', 'string', 'max:120'],
            'life_circumstances' => ['nullable', 'string', 'max:5000'],
        ]);

        $request->user()->preferences()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'locale' => $data['locale'],
                'timezone' => $data['timezone'] ?? 'UTC',
                'email_notifications' => $request->boolean('email_notifications'),
                'in_app_notifications' => $request->boolean('in_app_notifications'),
                'daily_care_enabled' => $request->boolean('daily_care_enabled'),
                'daily_reminder_enabled' => $request->boolean('daily_reminder_enabled'),
                'analysis_ready_enabled' => $request->boolean('analysis_ready_enabled'),
                'growth_reminder_enabled' => $request->boolean('growth_reminder_enabled'),
                'weekly_report_enabled' => $request->boolean('weekly_report_enabled'),
                'monthly_report_enabled' => $request->boolean('monthly_report_enabled'),
                'daily_care_hour' => $data['daily_care_hour'] ?? 8,
                'daily_reminder_hour' => $data['daily_reminder_hour'] ?? 20,
                'allow_ai_training_consent' => $request->boolean('allow_ai_training_consent'),
            ]
        );

        $request->user()->profile()->updateOrCreate(
            ['user_id' => $request->user()->id],
            collect($data)->only([
                'preferred_name',
                'cultural_background',
                'religious_spiritual_background',
                'emotional_state',
                'life_circumstances',
            ])->all()
        );

        $request->session()->put('locale', $data['locale']);

        return back()->with('status', __('تم حفظ الإعدادات.'));
    }

    public function privacy(Request $request): View
    {
        return view('settings.privacy', ['user' => $request->user()->load('preferences')]);
    }

    public function export(Request $request): StreamedResponse
    {
        $user = $request->user()->load([
            'profile',
            'preferences',
            'dreams.analyses',
            'dreams.transcriptions',
            'weeklyReports',
            'monthlyReports',
            'developmentThemes.actions',
        ]);

        $payload = $user->toArray();

        return response()->streamDownload(function () use ($payload): void {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, 'innerpath-export-'.$user->id.'.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        foreach ($user->dreams as $dream) {
            foreach ($dream->audioFiles as $audio) {
                Storage::disk($audio->disk)->delete($audio->path);
            }
        }

        auth()->logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', __('تم حذف الحساب وجميع البيانات المرتبطة.'));
    }
}
