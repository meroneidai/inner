<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function update(Request $request, string $locale): RedirectResponse
    {
        $available = config('innerpath.available_locales', ['ar', 'en']);

        if (! in_array($locale, $available, true)) {
            abort(404);
        }

        $request->session()->put('locale', $locale);

        if ($request->user()) {
            $request->user()->preferences()->updateOrCreate(
                ['user_id' => $request->user()->id],
                ['locale' => $locale]
            );
        }

        app()->setLocale($locale);

        return back();
    }
}
