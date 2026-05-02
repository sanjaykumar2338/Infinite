<x-layouts.app title="Spark | infinitesugar">
    <section class="row align-items-center g-5">
        <div class="col-lg-6">
            <div class="eyebrow mb-3">Spark</div>
            <h1 class="section-title mb-3">Live behavioral guidance before the moment passes.</h1>
            <p class="lead-copy mb-4">Spark gives every new user one private 30-minute trial call. After the trial, continued access requires a manual Stripe upgrade.</p>
            <div class="pricing-plan-price mb-4" style="font-size: clamp(2.25rem, 3.4vw, 2.75rem);">$79/month</div>
            <div class="d-flex flex-wrap gap-3">
                <a class="btn btn-sugar" href="{{ route('signup') }}">Get Started</a>
                <a class="btn btn-soft" href="{{ route('pricing') }}">Compare Plans</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="surface-card p-4 p-lg-5">
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold text-uppercase small">Spark free call</span>
                    <span class="metric-pill">30 min</span>
                </div>
                <div class="progress mb-4" style="height: .65rem; background-color: #eadfca;">
                    <div class="progress-bar" style="width: 64%; background-color: #a8873f;"></div>
                </div>
                <ul class="check-list">
                    <li>Backend-owned minute tracking</li>
                    <li>No frontend plan or status trust</li>
                    <li>No automatic subscription after the free call</li>
                </ul>
            </div>
        </div>
    </section>
</x-layouts.app>
