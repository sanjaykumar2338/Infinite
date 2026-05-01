<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index', [
            'users' => User::orderBy('email')->get(['id', 'email']),
            'reports' => Report::with('user')->latest()->limit(50)->get(),
            'charts' => ReportChart::with('user')->latest()->limit(50)->get(),
            'badges' => BadgeReport::with('user')->latest()->limit(50)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:report,chart,badge'],
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
            'badge_name' => ['nullable', 'string', 'max:255'],
            'chart_type' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $path = $request->file('file')?->store('reports', 'public');

        match ($data['type']) {
            'report' => Report::create($this->reportData($data, $path)),
            'chart' => ReportChart::create($this->chartData($data, $path)),
            'badge' => BadgeReport::create($this->badgeData($data, $path)),
        };

        return back()->with('status', 'Report asset saved.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $model = match ($type) {
            'report' => Report::findOrFail($id),
            'chart' => ReportChart::findOrFail($id),
            'badge' => BadgeReport::findOrFail($id),
            default => abort(404),
        };

        if ($model->file_path) {
            Storage::disk('public')->delete($model->file_path);
        }

        $model->delete();

        return back()->with('status', 'Report asset deleted.');
    }

    private function reportData(array $data, ?string $path): array
    {
        return [
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'file_path' => $path,
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'published_at' => now(),
        ];
    }

    private function chartData(array $data, ?string $path): array
    {
        return [
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'chart_type' => $data['chart_type'] ?? 'weekly',
            'file_path' => $path,
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'published_at' => now(),
        ];
    }

    private function badgeData(array $data, ?string $path): array
    {
        return [
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'summary' => $data['summary'] ?? null,
            'badge_name' => $data['badge_name'] ?? null,
            'file_path' => $path,
            'month' => $data['period_start'] ?? null,
            'published_at' => now(),
        ];
    }
}
