<x-layouts.app title="Pricing">
    <section class="text-center mx-auto mb-5" style="max-width: 760px;">
        <div class="eyebrow mb-3">Simple plan ladder</div>
        <h1 class="section-title fw-bold mb-3">Start with Spark. Grow into Forge.</h1>
        <p class="lead-copy">Keep trial access clean, subscriptions managed through Stripe, and reporting ready for client delivery.</p>
    </section>

    <div class="row g-4 align-items-stretch">
        <div class="col-md-6">
            <div class="pricing-card">
                <span class="metric-pill mb-3">Free trial available</span>
                <h2 class="display-6 fw-bold">Spark</h2>
                <p class="text-muted mb-4">A focused entry tier for the first live coaching experience.</p>
                <ul class="check-list mb-4">
                    <li>30-minute free Spark call</li>
                    <li>Manual upgrade after trial limit</li>
                    <li>Paid Spark subscription through Stripe Checkout</li>
                    <li>Backend-protected usage tracking</li>
                </ul>
                <a class="btn btn-soft w-100" href="{{ route('signup') }}">Start Spark</a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="pricing-card featured">
                <span class="metric-pill mb-3">Best for ongoing coaching</span>
                <h2 class="display-6 fw-bold">Forge</h2>
                <p class="text-muted mb-4">The premium reporting tier for continued insight and retention.</p>
                <ul class="check-list mb-4">
                    <li>Live Zoom behavior insights</li>
                    <li>Weekly Forge reports</li>
                    <li>Two Sunday KPI charts</li>
                    <li>Monthly badge report</li>
                </ul>
                <a class="btn btn-sugar w-100" href="{{ route('signup') }}">Start Forge</a>
            </div>
        </div>
    </div>

    <section class="placeholder-media mt-5">Client pricing screenshots, demo video, or customer proof placeholder</section>
</x-layouts.app>
