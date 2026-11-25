<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = config('innerpath.available_locales', ['ar', 'en']);
        $locale = config('app.locale', 'ar');

        if ($request->user()?->preferences?->locale) {
            $locale = $request->user()->preferences->locale;
        } elseif ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        }

        if (! in_array($locale, $available, true)) {
            $locale = config('innerpath.default_locale', 'ar');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
