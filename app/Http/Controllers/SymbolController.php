<?php

namespace App\Http\Controllers;

use App\Services\Analytics\PatternDetectionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SymbolController extends Controller
{
    public function index(Request $request, PatternDetectionService $patterns): View
    {
        $result = $patterns->detect($request->user());

        return view('symbols.index', [
            'result' => $result,
        ]);
    }
}
