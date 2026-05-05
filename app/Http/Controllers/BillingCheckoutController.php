<?php

namespace App\Http\Controllers;

use App\Services\StripeBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Throwable;

class BillingCheckoutController extends Controller
{
    public function __invoke(Request $request, StripeBillingService $billing, string $plan): RedirectResponse
    {
        abort_unless(in_array($plan, ['spark', 'forge'], true), 404);

        if (! Auth::check()) {
            $request->session()->put('checkout_plan', $plan);

            return redirect()
                ->route('login')
                ->with('status', 'Sign in to continue to Stripe Checkout.');
        }

        try {
            $session = $billing->createCheckoutSession($request->user(), $plan);
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe checkout failed.', [
                'plan' => $plan,
                'user_id' => $request->user()->id,
                'stripe_message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('pricing')
                ->with('error', 'Unable to start Stripe Checkout right now. Please verify the billing price configuration or try again shortly.');
        } catch (Throwable $exception) {
            Log::error('Checkout session creation failed.', [
                'plan' => $plan,
                'user_id' => $request->user()->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('pricing')
                ->with('error', 'Unable to start checkout right now. Please try again shortly.');
        }

        return redirect()->away($session->url);
    }
}
