<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportChart;
use App\Services\AccessService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingFinalizeController extends Controller
{
    public function __invoke(Request $request, AccessService $access): JsonResponse
    {
        $user = $request->user();
        $accessState = $access->check($user);

        if (! ($accessState['can_use_reports'] ?? false)) {
            return response()->json([
                'message' => 'Forge access required for report sync.',
                'access' => $accessState,
            ], 403);
        }

        $data = $request->validate([
            'meeting_id' => ['required', 'string', 'max:255'],
            'session_id' => ['required', 'string', 'max:255'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date'],
            'summary' => ['required', 'array'],
            'summary.counts' => ['required', 'array'],
            'summary.counts.red' => ['required', 'integer', 'min:0'],
            'summary.counts.yellow' => ['required', 'integer', 'min:0'],
            'summary.counts.blue' => ['required', 'integer', 'min:0'],
            'summary.counts.green' => ['required', 'integer', 'min:0'],
            'summary.lastMessages' => ['nullable', 'array', 'max:20'],
            'summary.lastMessages.*.analysis' => ['nullable', 'string', 'max:1000'],
            'summary.lastMessages.*.timestamp' => ['nullable', 'date'],
            'summary.lastMessages.*.color' => ['nullable', 'string', 'in:red,yellow,blue,green'],
            'report_data' => ['nullable', 'array'],
        ]);

        $meetingId = $data['meeting_id'];
        $sessionId = $data['session_id'];
        $periodStart = $this->dateOnly($data['started_at'] ?? null);
        $periodEnd = $this->dateOnly($data['ended_at'] ?? null) ?? $periodStart;
        $counts = $data['summary']['counts'];
        $lastMessages = $data['summary']['lastMessages'] ?? [];
        $title = "Meeting {$meetingId}";

        $report = Report::updateOrCreate(
            [
                'user_id' => $user->id,
                'title' => "{$title} Summary",
            ],
            [
                'summary' => $this->summaryText($counts, $lastMessages),
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'published_at' => now(),
            ],
        );

        $chart = ReportChart::updateOrCreate(
            [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'title' => "{$title} Guidance Signals",
            ],
            [
                'chart_type' => 'meeting',
                'data' => [
                    'meeting_id' => $meetingId,
                    'session_id' => $sessionId,
                    'counts' => $counts,
                    'last_messages' => $lastMessages,
                    'report_data' => $data['report_data'] ?? null,
                ],
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'published_at' => now(),
            ],
        );

        return response()->json([
            'message' => 'Meeting report synced.',
            'report_id' => $report->id,
            'chart_id' => $chart->id,
        ]);
    }

    private function dateOnly(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value)->toDateString();
    }

    /**
     * @param  array{red: int, yellow: int, blue: int, green: int}  $counts
     * @param  array<int, array<string, mixed>>  $lastMessages
     */
    private function summaryText(array $counts, array $lastMessages): string
    {
        $latest = $lastMessages ? (string) ($lastMessages[array_key_last($lastMessages)]['analysis'] ?? '') : '';
        $summary = "Signals: red {$counts['red']}, yellow {$counts['yellow']}, blue {$counts['blue']}, green {$counts['green']}.";

        if ($latest !== '') {
            return "{$summary} Latest guidance: {$latest}";
        }

        return $summary;
    }
}
