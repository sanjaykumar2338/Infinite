<x-layouts.app title="Spark | infinitesugar">
    @php
        $contentSections = $pageContent ?? collect();
        $rawSection = fn (string $key) => $contentSections->get($key);
        $section = fn (string $key) => $rawSection($key)?->is_active ? $rawSection($key) : null;
        $isHidden = fn (string $key) => (bool) ($rawSection($key) && ! $rawSection($key)->is_active);
        $contentTitle = fn (string $key, string $fallback) => $section($key)?->title ?: $fallback;
        $contentSubtitle = fn (string $key, string $fallback) => $section($key)?->subtitle ?: $fallback;
        $contentBody = fn (string $key, string $fallback) => $section($key)?->body ?: $fallback;
        $contentButton = fn (string $key, string $fallback) => $section($key)?->button_text ?: $fallback;
        $lines = fn (string $text) => array_values(array_filter(preg_split('/\R/', trim($text)) ?: [], fn ($line) => trim($line) !== ''));
        $currentUser = auth()->user();
        $isCurrentPlan = $currentUser
            && $currentUser->plan === 'spark'
            && ($currentUser->billingStatus() === 'active' || (bool) $currentUser->paidThrough()?->isFuture());
    @endphp

    @unless ($isHidden('hero'))
    <section class="row align-items-center g-5">
        @unless ($isHidden('usage_card'))
        <div class="col-lg-6">
            <div class="eyebrow mb-3">{{ $contentSubtitle('hero', 'Spark') }}</div>
            <h1 class="section-title mb-3">{{ $contentTitle('hero', 'Live behavioral guidance before the moment passes.') }}</h1>
            <p class="lead-copy mb-4">{!! nl2br(e($contentBody('hero', 'Spark gives every new user one private 30-minute trial call. After the trial, continued access requires a manual Stripe upgrade.'))) !!}</p>
            <div class="pricing-plan-price mb-4" style="font-size: clamp(2.25rem, 3.4vw, 2.75rem);">$79/month</div>
            <div class="d-flex flex-wrap gap-3">
                @if ($isCurrentPlan)
                    <button class="btn btn-sugar" type="button" disabled>Current Spark Plan</button>
                @else
                    <form method="post" action="{{ route('billing.checkout', 'spark') }}">
                        @csrf
                        <button class="btn btn-sugar" type="submit">{{ $contentButton('hero', 'Get Started') }}</button>
                    </form>
                @endif
                <a class="btn btn-soft" href="{{ route('pricing') }}">Compare Plans</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="surface-card p-4 p-lg-5">
                <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold text-uppercase small">{{ $contentTitle('usage_card', 'Spark free call') }}</span>
                    <span class="metric-pill">{{ $contentSubtitle('usage_card', '30 min') }}</span>
                </div>
                <div class="progress mb-4" style="height: .65rem; background-color: #eadfca;">
                    <div class="progress-bar" style="width: 64%; background-color: #a8873f;"></div>
                </div>
                <ul class="check-list">
                    @foreach ($lines($contentBody('usage_card', "Backend-owned minute tracking\nNo frontend plan or status trust\nNo automatic subscription after the free call")) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endunless
    </section>
    @endunless
</x-layouts.app>
