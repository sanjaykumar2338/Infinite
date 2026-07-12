<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtensionHeartbeatController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'extension_version' => ['nullable', 'string', 'max:32'],
            'platform' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $now = now();

        $user->forceFill([
            'extension_connected_at' => $user->extension_connected_at ?: $now,
            'extension_last_seen_at' => $now,
            'extension_version' => $data['extension_version'] ?? $user->extension_version,
            'extension_platform' => $data['platform'] ?? $user->extension_platform,
        ])->save();

        return response()->json([
            'connected' => true,
            'extension_connected_at' => $user->extension_connected_at,
            'extension_last_seen_at' => $user->extension_last_seen_at,
            'extension_version' => $user->extension_version,
        ]);
    }
}
