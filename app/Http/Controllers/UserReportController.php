<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\AccessService;
use App\Support\ForgeSundayWeeklyBrief;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function show(Request $request, Report $report, AccessService $access): View
    {
        abort_unless(
            $report->user_id === $request->user()->id || $request->user()->isAdmin(),
            404,
        );

        abort_unless($report->isForgeSundayWeeklyBrief(), 404);

        abort_unless($access->check($request->user())['can_use_reports'] ?? false, 403);

        return view('pages.reports.forge-sunday-brief', [
            'report' => $report,
            'validation' => ForgeSundayWeeklyBrief::inspect($report->report_data),
        ]);
    }
}
