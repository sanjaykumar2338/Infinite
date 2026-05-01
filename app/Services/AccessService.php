<?php

namespace App\Services;

use App\Models\User;

class AccessService
{
    public const FREE_TRIAL_MINUTES = 30;

    public function check(User $user): array
    {
        $remainingMinutes = max(0, self::FREE_TRIAL_MINUTES - $user->call_minutes_used);
        $fullAccess = $user->hasTestingAccess() || ($user->plan === 'forge' && $user->status === 'active');
        $paidSparkAccess = $user->plan === 'spark' && $user->status === 'active';

        return [
            'plan' => $user->plan,
            'status' => $user->status,
            'can_use_live_insights' => $fullAccess,
            'can_use_reports' => $fullAccess,
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
}
