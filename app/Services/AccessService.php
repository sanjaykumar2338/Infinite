<?php

namespace App\Services;

use App\Models\User;

class AccessService
{
    public const FREE_TRIAL_MINUTES = 30;

    public function check(User $user): array
    {
        $remainingMinutes = max(0, self::FREE_TRIAL_MINUTES - $user->call_minutes_used);
        $paidAccess = $this->hasPaidAccess($user);
        $fullAccess = $user->hasTestingAccess() || ($user->plan === 'forge' && $paidAccess);
        $paidSparkAccess = $user->plan === 'spark' && $paidAccess;

        return [
            'plan' => $user->plan,
            'status' => $user->status,
            'current_period_end' => $user->current_period_end,
            'can_use_live_insights' => $fullAccess || $paidSparkAccess,
            'can_use_reports' => $fullAccess,
            'can_use_charts' => $fullAccess,
            'can_use_badge_reports' => $fullAccess,
            'can_use_spark_call' => $fullAccess || $paidSparkAccess || $remainingMinutes > 0,
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
        if ($user->status === 'active') {
            return true;
        }

        return in_array($user->status, ['cancelled', 'past_due'], true)
            && $user->current_period_end
            && $user->current_period_end->isFuture();
    }
}
