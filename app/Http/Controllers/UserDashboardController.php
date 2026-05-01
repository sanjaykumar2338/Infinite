<?php

namespace App\Http\Controllers;

use App\Services\AccessService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function __invoke(Request $request, AccessService $access): View
    {
        $user = $request->user()->load([
            'reports' => fn ($query) => $query->latest()->limit(10),
            'reportCharts' => fn ($query) => $query->latest()->limit(10),
            'badgeReports' => fn ($query) => $query->latest()->limit(10),
        ]);

        return view('pages.dashboard', [
            'user' => $user,
            'access' => $access->check($user),
        ]);
    }
}
