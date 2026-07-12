<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request, AccessService $access): JsonResponse
    {
        $user = $request->user();
        $accessData = $access->check($user);

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'firebase_uid' => $user->firebase_uid,
                'name' => $user->name,
                'email' => $user->email,
                'plan' => $accessData['plan'],
                'status' => $accessData['status'],
                'subscription_status' => $accessData['subscription_status'],
                'billing_status' => $accessData['billing_status'],
                'current_period_end' => $accessData['current_period_end'],
                'current_period_ends_at' => $accessData['current_period_ends_at'],
                'trial_ends_at' => $accessData['trial_ends_at'],
                'free_call_used' => $accessData['free_call_used'],
                'call_minutes_used' => $accessData['call_minutes_used'],
                'role' => $user->role,
            ],
            'plan' => $accessData['plan'],
            'subscription_status' => $accessData['subscription_status'],
            'billing_status' => $accessData['billing_status'],
            'trial_available' => $accessData['trial_available'],
            'permissions' => $accessData['permissions'],
        ]);
    }
}
