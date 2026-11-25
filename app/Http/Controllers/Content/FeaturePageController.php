<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class FeaturePageController extends Controller
{
    public function __invoke(string $feature): RedirectResponse
    {
        return redirect()->route('explore.feature', $feature);
    }
}
