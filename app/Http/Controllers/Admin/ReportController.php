<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BadgeReport;
use App\Models\Report;
use App\Models\ReportChart;
use App\Models\User;
use App\Support\ForgeMonthlyBadgeReport;
use App\Support\ForgeSundayWeeklyBrief;
use App\Support\ForgeWeeklyHeatmap;
use App\Support\ForgeWeeklyTimeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
            'report_type' => ['nullable', 'string', 'in:standard,'.implode(',', [
                ForgeSundayWeeklyBrief::REPORT_TYPE,
                ForgeWeeklyHeatmap::REPORT_TYPE,
                ForgeWeeklyTimeline::REPORT_TYPE,
                ForgeMonthlyBadgeReport::REPORT_TYPE,
            ])],
            'report_json' => ['nullable', 'string'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
            'badge_name' => ['nullable', 'string', 'max:255'],
            'chart_type' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $path = $request->file('file')?->store('reports', 'public');
        $payload = $this->structuredPayload($data);

        match ($data['type']) {
            'report' => Report::create($this->reportData($data, $path, $payload)),
            'chart' => ReportChart::create($this->chartData($data, $path, $payload)),
            'badge' => BadgeReport::create($this->badgeData($data, $path, $payload)),
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

    private function reportData(array $data, ?string $path, ?array $reportPayload): array
    {
        return [
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'report_type' => $data['report_type'] ?? 'standard',
            'summary' => $data['summary'] ?? data_get($reportPayload, 'executive_summary.text'),
            'report_data' => $reportPayload,
            'file_path' => $path,
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'published_at' => now(),
        ];
    }

    private function chartData(array $data, ?string $path, ?array $payload): array
    {
        return [
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'report_type' => $data['report_type'] ?? 'standard',
            'chart_type' => $data['chart_type'] ?? ($this->isTimeline($data) ? 'timeline' : ($this->isHeatmap($data) ? 'heatmap' : 'weekly')),
            'data' => $payload,
            'file_path' => $path,
            'period_start' => $data['period_start'] ?? null,
            'period_end' => $data['period_end'] ?? null,
            'published_at' => now(),
        ];
    }

    private function badgeData(array $data, ?string $path, ?array $payload): array
    {
        return [
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'report_type' => $data['report_type'] ?? 'standard',
            'summary' => $data['summary'] ?? data_get($payload, 'executive_summary.headline'),
            'report_data' => $payload,
            'badge_name' => $data['badge_name'] ?? data_get($payload, 'badge_name'),
            'file_path' => $path,
            'month' => $data['period_start'] ?? null,
            'published_at' => now(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function structuredPayload(array $data): ?array
    {
        if (($data['report_type'] ?? 'standard') === 'standard') {
            return null;
        }

        $decoded = json_decode((string) ($data['report_json'] ?? ''), true);

        if (! is_array($decoded)) {
            throw ValidationException::withMessages([
                'report_json' => 'Structured Forge report JSON must be valid JSON.',
            ]);
        }

        $validation = match (true) {
            $this->isSunday($data) => ForgeSundayWeeklyBrief::inspect($decoded),
            $this->isHeatmap($data) => ForgeWeeklyHeatmap::inspect($decoded),
            $this->isTimeline($data) => ForgeWeeklyTimeline::inspect($decoded),
            $this->isMonthlyBadge($data) => ForgeMonthlyBadgeReport::inspect($decoded),
            default => throw ValidationException::withMessages([
                'report_type' => 'That report type is not supported for the selected asset category.',
            ]),
        };

        if (! $validation['valid']) {
            throw ValidationException::withMessages([
                'report_json' => 'Structured Forge report JSON is missing required fields: '.implode(', ', $validation['missing']),
            ]);
        }

        return $validation['payload'] ?? $decoded;
    }

    private function isSunday(array $data): bool
    {
        return ($data['type'] ?? null) === 'report'
            && ($data['report_type'] ?? 'standard') === ForgeSundayWeeklyBrief::REPORT_TYPE;
    }

    private function isTimeline(array $data): bool
    {
        return ($data['type'] ?? null) === 'chart'
            && ($data['report_type'] ?? 'standard') === ForgeWeeklyTimeline::REPORT_TYPE;
    }

    private function isHeatmap(array $data): bool
    {
        return ($data['type'] ?? null) === 'chart'
            && ($data['report_type'] ?? 'standard') === ForgeWeeklyHeatmap::REPORT_TYPE;
    }

    private function isMonthlyBadge(array $data): bool
    {
        return ($data['type'] ?? null) === 'badge'
            && ($data['report_type'] ?? 'standard') === ForgeMonthlyBadgeReport::REPORT_TYPE;
    }
}
