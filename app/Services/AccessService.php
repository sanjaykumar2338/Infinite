<?php

namespace App\Services;

use App\Models\User;

class AccessService
{
    public const FREE_TRIAL_MINUTES = 30;

    public function check(User $user): array
    {
        $remainingMinutes = max(0, self::FREE_TRIAL_MINUTES - $user->call_minutes_used);
        $billingStatus = $user->billingStatus();
        $paidThrough = $user->paidThrough();
        $paidAccess = $this->hasPaidAccess($user);
        $fullAccess = $user->hasTestingAccess() || ($user->plan === 'forge' && $paidAccess);
        $paidSparkAccess = $user->plan === 'spark' && $paidAccess;

        return [
            'plan' => $user->plan,
            'status' => $billingStatus,
            'subscription_status' => $billingStatus,
            'current_period_end' => $paidThrough,
            'current_period_ends_at' => $paidThrough,
            'trial_ends_at' => $user->trial_ends_at,
            'can_use_live_insights' => $fullAccess || $paidSparkAccess,
            'can_use_reports' => $fullAccess,
            'can_use_charts' => $fullAccess,
            'can_use_badge_reports' => $fullAccess,
            'can_use_spark_call' => $fullAccess || $paidSparkAccess || $remainingMinutes > 0,
            'free_call_allowance_minutes' => self::FREE_TRIAL_MINUTES,
            'free_call_used' => $user->free_call_used,
            'call_minutes_used' => $user->call_minutes_used,
            'remaining_minutes' => $user->hasTestingAccess() || $paidSparkAccess || $fullAccess ? null : $remainingMinutes,
        ];
    }

    public function canUseSparkCall(User $user): bool
    {
        return $this->check($user)['can_use_spark_call'];
    }

    private function hasPaidAccess(User $user): bool
    {
        $billingStatus = $user->billingStatus();
        $paidThrough = $user->paidThrough();

        if ($billingStatus === 'active') {
            return true;
        }

        return in_array($billingStatus, ['cancelled', 'past_due'], true)
            && $paidThrough
            && $paidThrough->isFuture();
    }
}
