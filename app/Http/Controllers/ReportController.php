<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMonthlyReport;
use App\Jobs\GenerateWeeklyReport;
use App\Models\MonthlyReport;
use App\Models\WeeklyReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function weekly(Request $request): View
    {
        $reports = $request->user()->weeklyReports()->latest('week_start')->paginate(10);

        return view('reports.weekly', compact('reports'));
    }

    public function showWeekly(WeeklyReport $report): View
    {
        abort_unless($report->user_id === auth()->id(), 403);

        return view('reports.weekly-show', compact('report'));
    }

    public function generateWeekly(Request $request): RedirectResponse
    {
        GenerateWeeklyReport::dispatch($request->user());

        return back()->with('status', __('جاري إنشاء التقرير الأسبوعي.'));
    }

    public function monthly(Request $request): View
    {
        $reports = $request->user()->monthlyReports()->latest('year')->latest('month')->paginate(10);

        return view('reports.monthly', compact('reports'));
    }

    public function showMonthly(MonthlyReport $report): View
    {
        abort_unless($report->user_id === auth()->id(), 403);

        return view('reports.monthly-show', compact('report'));
    }

    public function generateMonthly(Request $request): RedirectResponse
    {
        GenerateMonthlyReport::dispatch($request->user());

        return back()->with('status', __('جاري إنشاء التقرير الشهري.'));
    }
}
