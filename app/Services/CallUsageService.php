<?php

namespace App\Services;

use App\Models\CallSession;
use App\Models\User;

class CallUsageService
{
    public function __construct(private AccessService $access) {}

    /**
     * @return array{session: CallSession, user: User}
     */
    public function touch(User $user): array
    {
        $session = $user->callSessions()
            ->where('status', 'active')
            ->latest()
            ->first();

        if (! $session) {
            $session = $user->callSessions()->create([
                'started_at' => now(),
                'status' => 'active',
            ]);
        }

        $elapsedMinutes = max(1, (int) ceil($session->started_at->diffInSeconds(now()) / 60));
        $delta = max(0, $elapsedMinutes - $session->minutes_counted);
        $hasUnlimitedUsage = $this->access->check($user)['remaining_minutes'] === null;
        $newMinutes = $hasUnlimitedUsage
            ? $user->call_minutes_used + $delta
            : min(AccessService::FREE_TRIAL_MINUTES, $user->call_minutes_used + $delta);
        $trialEnded = ! $hasUnlimitedUsage && $newMinutes >= AccessService::FREE_TRIAL_MINUTES;

        $session->update([
            'minutes_counted' => $elapsedMinutes,
            'ended_at' => $trialEnded ? now() : null,
            'status' => $trialEnded ? 'ended' : 'active',
        ]);

        $user->update([
            'call_minutes_used' => $newMinutes,
            'free_call_used' => $trialEnded || $user->free_call_used,
        ]);

        return [
            'session' => $session->fresh(),
            'user' => $user->fresh(),
        ];
    }
}
