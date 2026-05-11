<?php

namespace App\Http\Controllers;

use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Services\AccessService;
use App\Support\ForgeMonthlyBadgeReport;
use App\Support\ForgeSundayWeeklyBrief;
use App\Support\ForgeWeeklyHeatmap;
use App\Support\ForgeWeeklyTimeline;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
    public function showReport(Request $request, Report $report, AccessService $access): View
    {
        $this->ensureOwnership($request, $report->user_id);
        abort_unless($report->isForgeSundayWeeklyBrief(), 404);
        abort_unless($access->check($request->user())['can_use_reports'] ?? false, 403);

        return view('pages.reports.forge-sunday-brief', [
            'report' => $report,
            'validation' => ForgeSundayWeeklyBrief::inspect($report->report_data),
        ]);
    }

    public function showChart(Request $request, ReportChart $reportChart, AccessService $access): View
    {
        $this->ensureOwnership($request, $reportChart->user_id);
        abort_unless($reportChart->isForgeWeeklyTimeline() || $reportChart->isForgeWeeklyHeatmap(), 404);
        abort_unless($access->check($request->user())['can_use_charts'] ?? false, 403);

        if ($reportChart->isForgeWeeklyHeatmap()) {
            return view('pages.reports.forge-weekly-heatmap', [
                'chart' => $reportChart,
                'validation' => ForgeWeeklyHeatmap::inspect($reportChart->data),
            ]);
        }

        return view('pages.reports.forge-weekly-timeline', [
            'chart' => $reportChart,
            'validation' => ForgeWeeklyTimeline::inspect($reportChart->data),
        ]);
    }

    public function showBadge(Request $request, BadgeReport $badgeReport, AccessService $access): View
    {
        $this->ensureOwnership($request, $badgeReport->user_id);
        abort_unless($badgeReport->isForgeMonthlyBadge(), 404);
        abort_unless($access->check($request->user())['can_use_badge_reports'] ?? false, 403);

        return view('pages.reports.forge-monthly-badge', [
            'badge' => $badgeReport,
            'validation' => ForgeMonthlyBadgeReport::inspect($badgeReport->report_data),
        ]);
    }

    private function ensureOwnership(Request $request, int $ownerId): void
    {
        abort_unless($ownerId === $request->user()->id || $request->user()->isAdmin(), 404);
    }
}
