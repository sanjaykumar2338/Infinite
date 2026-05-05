<?php

namespace App\Services;

use App\Models\User;
use Stripe\Checkout\Session;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeBillingService
{
    public function createCheckoutSession(User $user, string $plan): Session
    {
        $priceId = config("services.stripe.prices.$plan");

        if (! $priceId) {
            abort(422, "Stripe price ID for $plan is not configured.");
        }

        if (! config('services.stripe.secret')) {
            abort(422, 'Stripe secret key is not configured.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = [
            'mode' => 'subscription',
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
        ];

        if ($user->stripe_customer_id) {
            $payload['customer'] = $user->stripe_customer_id;
        } else {
            $payload['customer_email'] = $user->email;
        }

        return Session::create($payload);
    }

    /**
     * @throws SignatureVerificationException
     * @throws UnexpectedValueException
     */
    public function constructWebhookEvent(string $payload, ?string $signature): Event
    {
        if (! config('services.stripe.webhook_secret')) {
            throw new UnexpectedValueException('Stripe webhook secret is not configured.');
        }

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
