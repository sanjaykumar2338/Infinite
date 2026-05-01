<x-layouts.app title="Infinite Sugar">
    <section class="row align-items-center g-5">
        <div class="col-lg-7">
            <div class="eyebrow mb-4">Zoom behavior intelligence</div>
            <h1 class="hero-title fw-bold mb-4">Live coaching for better Zoom calls.</h1>
            <p class="lead-copy mb-4">
                Infinite Sugar turns real-time meeting signals into coaching prompts, Spark trial sessions, Forge reports, KPI charts, and badge-ready progress tracking.
            </p>
            <div class="d-flex flex-wrap gap-3 mb-4">
                <a class="btn btn-sugar" href="{{ route('pricing') }}">View Pricing</a>
                <a class="btn btn-soft" href="{{ route('signup') }}">Get Started</a>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="metric-pill">30-minute Spark trial</span>
                <span class="metric-pill">Forge reports every week</span>
                <span class="metric-pill">Stripe + Firebase ready</span>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="dashboard-visual">
                <div class="app-window">
                    <div class="app-window-top">
                        <span class="app-dot"></span><span class="app-dot"></span><span class="app-dot"></span>
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="text-white-50 small">Live Zoom Insight</div>
                                <div class="h5 mb-0">Energy rising in discovery</div>
                            </div>
                            <span class="badge rounded-pill text-bg-success">Live</span>
                        </div>
                        <div class="insight-line mb-3">
                            <div class="small text-white-50 mb-2">Coaching prompt</div>
                            <div class="fw-semibold">Ask one clarifying question before presenting the next offer.</div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="insight-line">
                                    <div class="text-white-50 small">Talk balance</div>
                                    <div class="fs-4 fw-bold">58%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="insight-line">
                                    <div class="text-white-50 small">Engagement</div>
                                    <div class="fs-4 fw-bold">High</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 mt-4">
        <div class="row align-items-end mb-4">
            <div class="col-lg-7">
                <div class="eyebrow mb-3">Extension SaaS</div>
                <h2 class="section-title fw-bold mb-3">Designed for coaching loops, not static notes.</h2>
            </div>
            <div class="col-lg-5">
                <p class="lead-copy mb-0">Each tier maps to a clear customer journey: try a Spark call, upgrade for continued access, then move into Forge for live insights and reporting.</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach ([
                ['Live Zoom insights', 'Real-time signals and prompts during high-stakes calls.', 'UP'],
                ['Spark 30-minute trial', 'A focused free trial that ends cleanly when minutes are used.', '30'],
                ['Forge weekly reports', 'Admin-managed weekly reports today, automation-ready later.', 'W'],
                ['Charts and KPI tracking', 'Two Sunday charts for visual progress snapshots.', 'KPI'],
                ['Monthly badge report', 'A polished monthly milestone report for retention moments.', '*'],
            ] as [$title, $copy, $icon])
                <div class="col-md-6 col-xl">
                    <div class="feature-card">
                        <div class="icon-pill mb-3">{{ $icon }}</div>
                        <h3 class="h5 fw-bold">{{ $title }}</h3>
                        <p class="text-muted mb-0">{{ $copy }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="row g-4 align-items-center py-4">
        <div class="col-lg-6">
            <img class="theme-media" src="{{ asset('assets/product-coaching-preview.svg') }}" alt="Infinite Sugar coaching reports and KPI preview">
        </div>
        <div class="col-lg-6">
            <div class="surface-card p-4 p-lg-5">
                <div class="eyebrow mb-3">Launch-ready foundation</div>
                <h2 class="h1 fw-bold mb-3">Billing, access, reports, and admin are connected.</h2>
                <p class="lead-copy mb-4">Firebase owns identity, Laravel owns business logic, and Stripe owns subscription events with idempotent webhook processing.</p>
                <a class="btn btn-sugar" href="{{ route('reports.showcase') }}">Explore Reports</a>
            </div>
        </div>
    </section>
</x-layouts.app>
