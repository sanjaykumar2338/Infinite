<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firebase_uid',
        'name',
        'email',
        'password',
        'plan',
        'status',
        'subscription_status',
        'stripe_customer_id',
        'stripe_subscription_id',
        'trial_ends_at',
        'current_period_ends_at',
        'current_period_end',
        'free_call_used',
        'call_minutes_used',
        'extension_connected_at',
        'extension_last_seen_at',
        'extension_version',
        'extension_platform',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'current_period_ends_at' => 'datetime',
            'current_period_end' => 'datetime',
            'free_call_used' => 'boolean',
            'call_minutes_used' => 'integer',
            'extension_connected_at' => 'datetime',
            'extension_last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function reportCharts()
    {
        return $this->hasMany(ReportChart::class);
    }

    public function badgeReports()
    {
        return $this->hasMany(BadgeReport::class);
    }

    public function callSessions()
    {
        return $this->hasMany(CallSession::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function hasTestingAccess(): bool
    {
        return in_array($this->role, ['admin', 'tester'], true);
    }

    public function billingStatus(): string
    {
        return $this->normalizedBillingStatus();
    }

    public function paidThrough(): mixed
    {
        return $this->current_period_ends_at ?: $this->current_period_end;
    }

    public function normalizedPlan(): string
    {
        if ($this->role === 'admin') {
            return 'admin';
        }

        if ($this->role === 'tester') {
            return 'tester';
        }

        $plan = strtolower((string) $this->plan);

        return in_array($plan, ['free', 'spark', 'forge'], true) ? $plan : 'free';
    }

    public function normalizedBillingStatus(): string
    {
        if ($this->hasTestingAccess()) {
            return 'active';
        }

        $subscriptionStatus = $this->normalizeStatusValue($this->subscription_status);
        $legacyStatus = $this->normalizeStatusValue($this->status);

        return $subscriptionStatus !== 'inactive' ? $subscriptionStatus : $legacyStatus;
    }

    private function normalizeStatusValue(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'active' => 'active',
            'trialing' => 'trialing',
            'past_due', 'unpaid', 'incomplete' => 'past_due',
            'cancelled', 'canceled', 'incomplete_expired' => 'cancelled',
            default => 'inactive',
        };
    }
}
