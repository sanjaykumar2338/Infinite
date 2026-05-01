<x-layouts.app title="Dashboard">
    <section class="row align-items-center g-4 mb-5">
        <div class="col-lg-7">
            <div class="eyebrow mb-3">User dashboard</div>
            <h1 class="section-title fw-bold mb-3">Welcome, {{ $user->name ?: $user->email }}.</h1>
            <p class="lead-copy mb-0">Your Firebase login is connected to Laravel plan, access, billing status, and report records.</p>
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
            ['Status', ucfirst(str_replace('_', ' ', $user->status)), 'Billing/access state'],
            ['Free call used', $user->free_call_used ? 'Yes' : 'No', 'Spark trial tracking'],
            ['Call minutes', $user->call_minutes_used, 'Backend-counted minutes'],
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
        </div>
    </section>

    <section class="row g-4">
        @foreach ([
            ['Weekly Reports', $user->reports, 'summary'],
            ['Charts', $user->reportCharts, 'chart_type'],
            ['Badge Reports', $user->badgeReports, 'badge_name'],
        ] as [$heading, $items, $metaField])
            <div class="col-lg-4">
                <div class="surface-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">{{ $heading }}</h2>
                        <span class="metric-pill">{{ $items->count() }}</span>
                    </div>
                    @forelse ($items as $item)
                        <div class="feature-card mb-3">
                            <div class="fw-bold">{{ $item->title }}</div>
                            <div class="small text-muted">
                                {{ $item->{$metaField} ?: optional($item->published_at)->toDateString() ?: 'Available' }}
                            </div>
                            @if ($item->file_path)
                                <a class="small fw-bold" href="{{ asset('storage/'.$item->file_path) }}" target="_blank" rel="noopener">Open file</a>
                            @endif
                        </div>
                    @empty
                        <div class="placeholder-media" style="min-height: 10rem;">No {{ strtolower($heading) }} available yet</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </section>
</x-layouts.app>
