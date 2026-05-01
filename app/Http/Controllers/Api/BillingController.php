<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function checkout(Request $request, StripeBillingService $billing, string $plan): JsonResponse
    {
        abort_unless(in_array($plan, ['spark', 'forge'], true), 404);

        $session = $billing->createCheckoutSession($request->user(), $plan);

        return response()->json([
            'checkout_session_id' => $session->id,
            'url' => $session->url,
        ]);
    }
}
