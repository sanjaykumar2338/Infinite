<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallSession;
use App\Services\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function start(Request $request, AccessService $access): JsonResponse
    {
        $user = $request->user();

        if (! $access->canUseSparkCall($user)) {
            return response()->json(['message' => 'Spark trial limit reached. Upgrade required.'], 402);
        }

        $session = $user->callSessions()
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $session) {
            $session = $user->callSessions()->create([
                'started_at' => now(),
                'status' => 'active',
            ]);
        }

        return response()->json([
            'call_session_id' => $session->id,
            'started_at' => $session->started_at,
            'access' => $access->check($user->fresh()),
        ]);
    }

    public function usage(Request $request, AccessService $access): JsonResponse
    {
        $user = $request->user();

        $session = CallSession::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->firstOrFail();

        $elapsedMinutes = max(1, (int) ceil($session->started_at->diffInSeconds(now()) / 60));
        $delta = max(0, $elapsedMinutes - $session->minutes_counted);
        $newMinutes = $user->hasTestingAccess() || $user->status === 'active'
            ? $user->call_minutes_used + $delta
            : min(AccessService::FREE_TRIAL_MINUTES, $user->call_minutes_used + $delta);

        $session->update([
            'minutes_counted' => $elapsedMinutes,
            'ended_at' => $newMinutes >= AccessService::FREE_TRIAL_MINUTES && $user->status !== 'active' ? now() : null,
            'status' => $newMinutes >= AccessService::FREE_TRIAL_MINUTES && $user->status !== 'active' ? 'ended' : 'active',
        ]);

        $user->update([
            'call_minutes_used' => $newMinutes,
            'free_call_used' => $newMinutes >= AccessService::FREE_TRIAL_MINUTES || $user->free_call_used,
        ]);

        return response()->json($access->check($user->fresh()));
    }
}
