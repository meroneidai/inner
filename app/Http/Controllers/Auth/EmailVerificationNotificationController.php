<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Invalidate the previous code and email a fresh OTP.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Always issue a new hashed code — previous OTP becomes invalid.
        $request->user()->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('status', 'verification-code-sent');
    }
}
