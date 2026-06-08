<x-layouts.app title="Dashboard">
    @if (request('checkout') === 'success')
        <div class="alert alert-success mt-4 mb-0">Checkout completed. Your billing status will update as soon as Stripe confirms the subscription.</div>
    @endif

    <section class="row align-items-center g-4 mb-5">
        <div class="col-lg-7">
            <div class="eyebrow mb-3">User dashboard</div>
            <h1 class="section-title fw-bold mb-3">Welcome, {{ $user->name ?: $user->email }}.</h1>
            <p class="lead-copy mb-0">Your Firebase login is connected to Laravel plan, access, billing status, and report records.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a class="btn btn-sugar" href="{{ route('extension.download') }}">Install Extension</a>
                <form method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-soft" type="submit">Logout</button>
                </form>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="surface-card p-4">
                <div class="text-muted small fw-bold text-uppercase mb-1">Signed in as</div>
                <div class="h4 fw-bold mb-1">{{ $user->email }}</div>
                <div class="text-muted">Firebase UID: {{ $user->firebase_uid ?: 'n/a' }}</div>
            </div>
        </div>
    </section>

    <section class="row g-4 mb-5">
        @foreach ([
            ['Current plan', ucfirst($user->plan), 'Plan managed by Stripe or admin'],
            ['Status', ucfirst(str_replace('_', ' ', $user->billingStatus())), 'Billing/access state'],
            ['Period end', optional($user->paidThrough())->toFormattedDateString() ?: 'n/a', 'Paid-through date from Stripe'],
            ['Free call used', $access['free_call_used'] ? 'Yes' : 'No', 'Spark trial tracking'],
            ['Free call minutes used', $access['call_minutes_used'].' / '.$access['free_call_allowance_minutes'], 'Backend-counted Spark trial minutes'],
            ['Remaining free minutes', $access['remaining_minutes'] ?? 'Unlimited', 'Free Spark call balance'],
        ] as [$label, $value, $copy])
            <div class="col-sm-6 col-xl-3">
                <div class="feature-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">{{ $label }}</div>
                    <div class="fs-3 fw-bold mb-2">{{ $value }}</div>
                    <p class="text-muted mb-0">{{ $copy }}</p>
                </div>
            </div>
        @endforeach
    </section>

    <section class="surface-card p-4 p-lg-5 mb-5">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="h3 fw-bold mb-2">Access Status</h2>
                <p class="text-muted mb-0">These values are calculated by Laravel, not trusted from the extension frontend.</p>
            </div>
            <span class="metric-pill">Remaining minutes: {{ $access['remaining_minutes'] ?? 'Unlimited' }}</span>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Spark call</div>
                    <div class="h4 fw-bold mb-0">{{ $access['can_use_spark_call'] ? 'Allowed' : 'Upgrade required' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Live insights</div>
                    <div class="h4 fw-bold mb-0">{{ $access['can_use_live_insights'] ? 'Unlocked' : 'Locked' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Reports</div>
                    <div class="h4 fw-bold mb-0">{{ $access['can_use_reports'] ? 'Unlocked' : 'Locked' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Charts</div>
                    <div class="h4 fw-bold mb-0">{{ $access['can_use_charts'] ? 'Unlocked' : 'Locked' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="text-muted small fw-bold text-uppercase mb-2">Badge reports</div>
                    <div class="h4 fw-bold mb-0">{{ $access['can_use_badge_reports'] ? 'Unlocked' : 'Locked' }}</div>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4">
        @foreach ([
            ['Weekly Reports', $user->reports, 'summary', $access['can_use_reports']],
            ['Charts', $user->reportCharts, 'chart_type', $access['can_use_charts']],
            ['Badge Reports', $user->badgeReports, 'badge_name', $access['can_use_badge_reports']],
        ] as [$heading, $items, $metaField, $isUnlocked])
            <div class="col-lg-4">
                <div class="surface-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">{{ $heading }}</h2>
                        <span class="metric-pill">{{ $items->count() }}</span>
                    </div>
                    @if ($isUnlocked)
                        @forelse ($items as $item)
                            <div class="feature-card mb-3">
                                <div class="fw-bold">{{ $item->title }}</div>
                                <div class="small text-muted">
                                    {{ $item->{$metaField} ?: optional($item->published_at)->toDateString() ?: 'Available' }}
                                </div>
                                @if (method_exists($item, 'isForgeSundayWeeklyBrief') && $item->isForgeSundayWeeklyBrief())
                                    <a class="small fw-bold" href="{{ route('dashboard.reports.show', $item) }}">Open report</a>
                                @elseif (method_exists($item, 'isForgeWeeklyTimeline') && $item->isForgeWeeklyTimeline())
                                    <a class="small fw-bold" href="{{ route('dashboard.charts.show', $item) }}">Open report</a>
                                @elseif (method_exists($item, 'isForgeWeeklyHeatmap') && $item->isForgeWeeklyHeatmap())
                                    <a class="small fw-bold" href="{{ route('dashboard.charts.show', $item) }}">Open report</a>
                                @elseif (method_exists($item, 'isForgeMonthlyBadge') && $item->isForgeMonthlyBadge())
                                    <a class="small fw-bold" href="{{ route('dashboard.badges.show', $item) }}">Open report</a>
                                @elseif ($item->file_path)
                                    <a class="small fw-bold" href="{{ asset('storage/'.$item->file_path) }}" target="_blank" rel="noopener">Open file</a>
                                @endif
                            </div>
                        @empty
                            <div class="placeholder-media" style="min-height: 10rem;">No {{ strtolower($heading) }} available yet</div>
                        @endforelse
                    @else
                        <div class="placeholder-media" style="min-height: 10rem;">Forge access required</div>
                    @endif
                </div>
            </div>
        @endforeach
    </section>
</x-layouts.app>
