<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeBillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Throwable;

class BillingController extends Controller
{
    public function checkout(Request $request, StripeBillingService $billing, string $plan): JsonResponse
    {
        abort_unless(in_array($plan, ['spark', 'forge'], true), 404);

        try {
            $session = $billing->createCheckoutSession($request->user(), $plan);
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe checkout API failed.', [
                'plan' => $plan,
                'user_id' => $request->user()->id,
                'stripe_message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to start Stripe Checkout right now. Please verify the billing price configuration or try again shortly.',
            ], 502);
        } catch (Throwable $exception) {
            Log::error('Checkout API session creation failed.', [
                'plan' => $plan,
                'user_id' => $request->user()->id,
                'message' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to start checkout right now. Please try again shortly.',
            ], 502);
        }

        return response()->json([
            'checkout_session_id' => $session->id,
            'url' => $session->url,
        ]);
    }
}
