<?php

namespace App\Services;

use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeBillingService
{
    public function createCheckoutSession(User $user, string $plan): Session
    {
        $priceId = config("services.stripe.prices.$plan");

        if (! $priceId) {
            abort(422, "Stripe price ID for $plan is not configured.");
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::create([
            'mode' => 'subscription',
            'customer_email' => $user->email,
            'client_reference_id' => (string) $user->id,
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => config('services.stripe.success_url'),
            'cancel_url' => config('services.stripe.cancel_url'),
            'metadata' => [
                'user_id' => (string) $user->id,
                'firebase_uid' => (string) $user->firebase_uid,
                'plan' => $plan,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'plan' => $plan,
                ],
            ],
        ]);
    }

    /**
     * @throws SignatureVerificationException
     */
    public function constructWebhookEvent(string $payload, ?string $signature): Event
    {
        return Webhook::constructEvent(
            $payload,
            $signature ?? '',
            config('services.stripe.webhook_secret')
        );
    }

    public function planFromPrice(?string $priceId): ?string
    {
        return match ($priceId) {
            config('services.stripe.prices.spark') => 'spark',
            config('services.stripe.prices.forge') => 'forge',
            default => null,
        };
    }
}
