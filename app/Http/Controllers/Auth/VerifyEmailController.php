<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\EmailVerificationCodeService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Confirm the email with a numeric code and enter the dashboard.
     */
    public function __invoke(Request $request, EmailVerificationCodeService $codes): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $user = $request->user();
        $wasVerified = $user->hasVerifiedEmail();

        $codes->verify($user, $request->string('code')->toString());
        $user->refresh();

        if (! $wasVerified && $user->hasVerifiedEmail()) {
            event(new Verified($user));
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard', absolute: false))
            ->with('status', __('تم تفعيل حسابك. مرحباً بك في مساحتك.'));
    }
}
