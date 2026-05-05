<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccessService;
use App\Services\CallUsageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class AnalyzeController extends Controller
{
    public function __invoke(Request $request, AccessService $access, CallUsageService $usage): JsonResponse
    {
        $data = $request->validate([
            'image' => ['required', 'string', 'starts_with:data:image/'],
            'mode' => ['nullable', 'integer', 'min:0', 'max:10'],
        ]);

        $user = $request->user();
        $accessState = $access->check($user);
        $hasLiveGuidance = (bool) ($accessState['can_use_live_insights'] ?? false);
        $hasLimitedTrial = $user->plan === 'free' && (bool) ($accessState['can_use_spark_call'] ?? false);

        if (! $hasLiveGuidance && ! $hasLimitedTrial) {
            return response()->json([
                'message' => 'Upgrade required for live guidance.',
                'access' => $accessState,
            ], 403);
        }

        try {
            $usageState = $usage->touch($user);
            $accessState = $access->check($usageState['user']);

            return response()->json($this->mockAnalysisResponse($data, $accessState, [
                'call_session_id' => $usageState['session']->id,
                'call_minutes_used' => $usageState['user']->call_minutes_used,
                'free_call_used' => $usageState['user']->free_call_used,
            ]));
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to analyze the image right now.',
            ], 500);
        }
    }

    /**
     * Keeps the extension contract stable until real analysis is wired in.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $accessState
     * @param  array<string, mixed>  $usageState
     * @return array<string, mixed>
     */
    private function mockAnalysisResponse(array $data, array $accessState, array $usageState): array
    {
        $mode = (int) ($data['mode'] ?? 0);
        $signal = match ($mode) {
            1 => 'rapport',
            2 => 'attention',
            default => 'presence',
        };
        $suggestion = match ($signal) {
            'rapport' => 'Hold steady eye contact and mirror the client pace.',
            'attention' => 'Pause briefly, then re-anchor the next point.',
            default => 'Stay centered and keep your next prompt concise.',
        };

        return [
            'type' => 'mock',
            'signal' => $signal,
            'confidence' => 0.72,
            'suggestion' => $suggestion,
            'timestamp' => now()->toISOString(),
            'messages' => [
                'red' => [],
                'green' => [$suggestion],
                'yellow' => [],
                'blue' => [],
            ],
            'nose_position' => null,
            'features' => [
                'live_guidance' => true,
                'advanced_reports' => (bool) ($accessState['can_use_reports'] ?? false),
            ],
            'access' => $accessState,
            'usage' => $usageState,
        ];
    }
}
