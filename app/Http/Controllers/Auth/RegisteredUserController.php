<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Profile\ProfileConsentService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $user->profile()->create([
            'profile_status' => 'not_started',
            'preferred_language' => 'ar',
        ]);
        $user->preferences()->create([
            'locale' => 'ar',
            'email_notifications' => true,
            'in_app_notifications' => true,
            'daily_care_enabled' => true,
            'daily_reminder_enabled' => true,
            'analysis_ready_enabled' => true,
            'growth_reminder_enabled' => true,
            'weekly_report_enabled' => true,
            'monthly_report_enabled' => true,
            'daily_care_hour' => 8,
            'daily_reminder_hour' => 20,
        ]);
        app(ProfileConsentService::class)->ensureDefaults($user);

        // Easy signup: mark onboarding skippable; user can enrich profile later for better analysis.
        $user->update(['onboarding_completed_at' => now()]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()
            ->route('verification.notice')
            ->with('status', __('أدخل رمز التأكيد المرسل إلى بريدك لتفعيل حسابك.'));
    }
}
