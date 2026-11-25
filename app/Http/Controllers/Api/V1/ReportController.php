<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function weekly(Request $request)
    {
        return response()->json(
            $request->user()->weeklyReports()->latest('week_start')->paginate(20)
        );
    }

    public function monthly(Request $request)
    {
        return response()->json(
            $request->user()->monthlyReports()->latest('year')->latest('month')->paginate(20)
        );
    }
}
