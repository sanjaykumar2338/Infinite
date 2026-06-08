<?php

namespace App\Services;

use App\Models\StripeEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stripe\Event;

class StripeWebhookService
{
    public function __construct(private StripeBillingService $billing) {}

    public function process(Event $event): string
    {
        return DB::transaction(function () use ($event) {
            $stored = StripeEvent::query()
                ->where('stripe_event_id', $event->id)
                ->lockForUpdate()
                ->first();

            if ($stored) {
                return 'duplicate';
            }

            $stored = StripeEvent::create([
                'stripe_event_id' => $event->id,
                'type' => $event->type,
                'payload' => $event->toArray(),
            ]);

            match ($event->type) {
                'checkout.session.completed' => $this->checkoutCompleted($event->data->object),
                'invoice.payment_succeeded' => $this->invoicePaymentSucceeded($event->data->object),
                'invoice.payment_failed' => $this->invoicePaymentFailed($event->data->object),
                'customer.subscription.created' => $this->subscriptionUpdated($event->data->object),
                'customer.subscription.updated' => $this->subscriptionUpdated($event->data->object),
                'customer.subscription.deleted' => $this->subscriptionDeleted($event->data->object),
                default => null,
            };

            $stored->update(['processed_at' => now()]);

            return 'processed';
        });
    }

    private function checkoutCompleted(object $session): void
    {
        $user = $this->findUser(
            $session->metadata->user_id ?? $session->client_reference_id ?? null,
            $session->customer ?? null,
            $session->customer_details->email ?? $session->customer_email ?? null
        );

        if (! $user) {
            return;
        }

        $plan = $session->metadata->plan
            ?? $this->planFromSession($session)
            ?? $user->plan;

        $user->update([
            'plan' => in_array($plan, ['spark', 'forge'], true) ? $plan : $user->plan,
            'status' => 'active',
            'subscription_status' => 'active',
            'stripe_customer_id' => $session->customer ?? $user->stripe_customer_id,
            'stripe_subscription_id' => $session->subscription ?? $user->stripe_subscription_id,
        ]);
    }

    private function invoicePaymentSucceeded(object $invoice): void
    {
        $user = $this->findUser(null, $invoice->customer ?? null, null, $invoice->subscription ?? null);

        if (! $user) {
            return;
        }

        $periodEnd = $this->periodEndFromInvoice($invoice) ?? $user->paidThrough();

        $user->update([
            'status' => 'active',
            'subscription_status' => 'active',
            'stripe_customer_id' => $invoice->customer ?? $user->stripe_customer_id,
            'stripe_subscription_id' => $invoice->subscription ?? $user->stripe_subscription_id,
            'current_period_end' => $periodEnd,
            'current_period_ends_at' => $periodEnd,
        ]);
    }

    private function invoicePaymentFailed(object $invoice): void
    {
        $user = $this->findUser(null, $invoice->customer ?? null, null, $invoice->subscription ?? null);

        if (! $user) {
            return;
        }

        $periodEnd = $this->periodEndFromInvoice($invoice) ?? $user->paidThrough();

        $user->update([
            'status' => 'past_due',
            'subscription_status' => 'past_due',
            'stripe_customer_id' => $invoice->customer ?? $user->stripe_customer_id,
            'stripe_subscription_id' => $invoice->subscription ?? $user->stripe_subscription_id,
            'current_period_end' => $periodEnd,
            'current_period_ends_at' => $periodEnd,
        ]);
    }

    private function subscriptionUpdated(object $subscription): void
    {
        $user = $this->findUser($subscription->metadata->user_id ?? null, $subscription->customer ?? null, null, $subscription->id ?? null);

        if (! $user) {
            return;
        }

        $plan = $subscription->metadata->plan ?? $this->billing->planFromPrice($subscription->items->data[0]->price->id ?? null) ?? $user->plan;
        $status = $this->normalizeSubscriptionStatus($subscription->status ?? null, $user->billingStatus());
        $periodEnd = $this->fromTimestamp($subscription->current_period_end ?? null) ?? $user->paidThrough();
        $trialEnd = $this->fromTimestamp($subscription->trial_end ?? null);

        $user->update([
            'plan' => in_array($plan, ['spark', 'forge'], true) ? $plan : $user->plan,
            'status' => $status,
            'subscription_status' => $status,
            'stripe_customer_id' => $subscription->customer ?? $user->stripe_customer_id,
            'stripe_subscription_id' => $subscription->id ?? $user->stripe_subscription_id,
            'trial_ends_at' => $trialEnd ?? $user->trial_ends_at,
            'current_period_end' => $periodEnd,
            'current_period_ends_at' => $periodEnd,
        ]);
    }

    private function subscriptionDeleted(object $subscription): void
    {
        $user = $this->findUser($subscription->metadata->user_id ?? null, $subscription->customer ?? null, null, $subscription->id ?? null);

        if (! $user) {
            return;
        }

        $periodEnd = $this->fromTimestamp($subscription->current_period_end ?? null);

        $user->update([
            'status' => 'cancelled',
            'subscription_status' => 'cancelled',
            'current_period_end' => $periodEnd,
            'current_period_ends_at' => $periodEnd,
            'stripe_customer_id' => $subscription->customer ?? $user->stripe_customer_id,
            'stripe_subscription_id' => $subscription->id ?? $user->stripe_subscription_id,
            'plan' => $periodEnd && $periodEnd->isFuture() ? $user->plan : 'free',
        ]);
    }

    private function findUser(?string $id = null, ?string $customer = null, ?string $email = null, ?string $subscription = null): ?User
    {
        if (! $id && ! $customer && ! $email && ! $subscription) {
            return null;
        }

        return User::query()
            ->when($id, fn ($query) => $query->orWhere('id', $id))
            ->when($customer, fn ($query) => $query->orWhere('stripe_customer_id', $customer))
            ->when($subscription, fn ($query) => $query->orWhere('stripe_subscription_id', $subscription))
            ->when($email, fn ($query) => $query->orWhere('email', $email))
            ->first();
    }

    private function fromTimestamp(null|int|string $timestamp): ?Carbon
    {
        return $timestamp ? Carbon::createFromTimestamp((int) $timestamp) : null;
    }

    private function periodEndFromInvoice(object $invoice): ?Carbon
    {
        return $this->fromTimestamp($invoice->lines->data[0]->period->end ?? null);
    }

    private function planFromSession(object $session): ?string
    {
        return $this->billing->planFromPrice($session->line_items->data[0]->price->id ?? null);
    }

    private function normalizeSubscriptionStatus(?string $stripeStatus, string $fallback): string
    {
        return match ($stripeStatus) {
            'active', 'trialing' => 'active',
            'past_due', 'unpaid', 'incomplete' => 'past_due',
            'canceled', 'cancelled', 'incomplete_expired' => 'cancelled',
            default => $fallback,
        };
    }
}
