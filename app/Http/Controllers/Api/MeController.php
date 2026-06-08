<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->only([
                'id',
                'firebase_uid',
                'name',
                'email',
                'plan',
                'status',
                'subscription_status',
                'current_period_end',
                'current_period_ends_at',
                'trial_ends_at',
                'free_call_used',
                'call_minutes_used',
                'role',
            ]),
        ]);
    }
}
