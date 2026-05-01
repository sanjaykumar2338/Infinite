<x-layouts.app title="Spark Plan">
    <section class="row align-items-center g-5">
        <div class="col-lg-6">
            <div class="eyebrow mb-3">Trial-first coaching</div>
            <h1 class="section-title fw-bold mb-3">Spark gives every user a focused first call.</h1>
            <p class="lead-copy">Free users get 30 minutes. After the trial, the backend requires a manual upgrade through Stripe Checkout before Spark access continues.</p>
            <a class="btn btn-sugar" href="{{ route('pricing') }}">Compare Plans</a>
        </div>
        <div class="col-lg-6">
            <div class="surface-card p-4">
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Spark Trial</span>
                    <span class="metric-pill">30 min</span>
                </div>
                <div class="progress mb-4" style="height: .9rem;">
                    <div class="progress-bar" style="width: 64%; background: linear-gradient(90deg, #f66f9b, #f4b740);"></div>
                </div>
                <ul class="check-list">
                    <li>Backend-owned minute tracking</li>
                    <li>No frontend plan/status trust</li>
                    <li>Stripe upgrade path ready</li>
                </ul>
            </div>
        </div>
    </section>
</x-layouts.app>
