<?php

namespace App\Services;

use App\Models\User;

class AccessService
{
    public const FREE_TRIAL_MINUTES = 30;

    public function check(User $user): array
    {
        $plan = $user->normalizedPlan();
        $billingStatus = $user->normalizedBillingStatus();
        $remainingMinutes = max(0, self::FREE_TRIAL_MINUTES - $user->call_minutes_used);
        $paidThrough = $user->paidThrough();
        $paidAccess = $this->hasPaidAccess($user);
        $fullAccess = $user->hasTestingAccess() || ($plan === 'forge' && $paidAccess);
        $paidSparkAccess = $plan === 'spark' && $paidAccess;
        $canUseLiveGuidance = $fullAccess || $paidSparkAccess;
        $trialAvailable = ! $user->free_call_used && $remainingMinutes > 0;

        return [
            'authenticated' => true,
            'plan' => $plan,
            'status' => $billingStatus,
            'subscription_status' => $billingStatus,
            'billing_status' => $billingStatus,
            'current_period_end' => $paidThrough,
            'current_period_ends_at' => $paidThrough,
            'trial_ends_at' => $user->trial_ends_at,
            'trial_available' => $trialAvailable,
            'permissions' => [
                'live_guidance' => $canUseLiveGuidance,
                'reports' => $fullAccess,
            ],
            'can_use_live_insights' => $canUseLiveGuidance,
            'can_use_reports' => $fullAccess,
            'can_use_charts' => $fullAccess,
            'can_use_badge_reports' => $fullAccess,
            'can_use_spark_call' => $canUseLiveGuidance || $remainingMinutes > 0,
            'free_call_allowance_minutes' => self::FREE_TRIAL_MINUTES,
            'free_call_used' => $user->free_call_used,
            'call_minutes_used' => $user->call_minutes_used,
            'remaining_minutes' => $canUseLiveGuidance ? null : $remainingMinutes,
        ];
    }

    public function canUseSparkCall(User $user): bool
    {
        return $this->check($user)['can_use_spark_call'];
    }

    private function hasPaidAccess(User $user): bool
    {
        $billingStatus = $user->normalizedBillingStatus();
        $paidThrough = $user->paidThrough();

        if (in_array($billingStatus, ['active', 'trialing'], true)) {
            return true;
        }

        return in_array($billingStatus, ['cancelled', 'past_due'], true)
            && $paidThrough
            && $paidThrough->isFuture();
    }
}
