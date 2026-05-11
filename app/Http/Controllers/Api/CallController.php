<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccessService;
use App\Services\CallUsageService;
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

    public function usage(Request $request, AccessService $access, CallUsageService $usage): JsonResponse
    {
        if (! $access->canUseSparkCall($request->user())) {
            return response()->json(['message' => 'Spark trial limit reached. Upgrade required.'], 402);
        }

        $result = $usage->touch($request->user());

        return response()->json($access->check($result['user']));
    }
}
