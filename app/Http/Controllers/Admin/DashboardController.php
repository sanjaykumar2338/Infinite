<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\StripeEvent;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'activeCount' => User::where('status', 'active')->count(),
            'sparkCount' => User::where('plan', 'spark')->count(),
            'forgeCount' => User::where('plan', 'forge')->count(),
            'reportCount' => Report::count() + ReportChart::count() + BadgeReport::count(),
            'webhookCount' => StripeEvent::count(),
            'events' => StripeEvent::latest()->limit(10)->get(),
        ]);
    }
}
