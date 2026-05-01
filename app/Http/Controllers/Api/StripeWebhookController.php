<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripeBillingService;
use App\Services\StripeWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request, StripeBillingService $billing, StripeWebhookService $webhooks): JsonResponse
    {
        try {
            $event = $billing->constructWebhookEvent($request->getContent(), $request->header('Stripe-Signature'));
        } catch (SignatureVerificationException) {
            return response()->json(['message' => 'Invalid Stripe signature.'], 400);
        }

        return response()->json(['status' => $webhooks->process($event)]);
    }
}
