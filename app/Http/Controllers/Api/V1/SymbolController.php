<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Analytics\PatternDetectionService;
use Illuminate\Http\Request;

class SymbolController extends Controller
{
    public function index(Request $request, PatternDetectionService $patterns)
    {
        return response()->json($patterns->detect($request->user()));
    }
}
