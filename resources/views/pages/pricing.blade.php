<x-layouts.app title="Pricing | infinitesugar">
    <style>
        .pricing-page {
            width: 100vw;
            margin-inline: calc(50% - 50vw);
            padding: clamp(3.5rem, 6vw, 5rem) max(1.25rem, calc((100vw - 1180px) / 2));
            background: #fff;
        }

        .pricing-page-title {
            max-width: 780px;
            margin: 0 auto clamp(2.2rem, 4vw, 3.2rem);
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(3.4rem, 5vw, 5rem);
            font-weight: 600;
            line-height: .98;
            text-align: center;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: clamp(1.25rem, 2.5vw, 2rem);
            align-items: stretch;
        }

        .pricing-card-large {
            min-height: 100%;
            display: flex;
            flex-direction: column;
            border: 12px solid #c7af69;
            border-radius: 1.1rem;
            background: #fff;
            padding: clamp(1.6rem, 3vw, 2.25rem);
        }

        .pricing-card-large.featured {
            background: #fbfaf6;
        }

        .pricing-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.25rem;
            margin-bottom: 1.45rem;
        }

        .pricing-card-name,
        .pricing-card-price {
            color: #a8873f;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2.7rem, 4vw, 3.8rem);
            font-weight: 600;
            line-height: 1;
        }

        .pricing-card-price {
            white-space: nowrap;
            text-align: right;
        }

        .pricing-card-copy,
        .pricing-card-list li {
            color: #171717;
            font-size: clamp(1rem, 1.35vw, 1.2rem);
            line-height: 1.45;
        }

        .pricing-card-copy {
            margin-bottom: 1.35rem;
        }

        .pricing-card-list {
            margin: 0 0 1.35rem;
            padding-left: 1.2rem;
        }

        .pricing-card-list li + li {
            margin-top: .35rem;
        }

        .pricing-card-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.2rem;
            margin-top: auto;
            border: 1px solid #c7af69;
            border-radius: 999px;
            background: #b59a63;
            color: #fffdf8;
            font-weight: 800;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 12px 22px rgba(95, 60, 24, .18);
            padding: .85rem 1.2rem;
        }

        .pricing-card-action:hover {
            background: #96763b;
            color: #fffdf8;
        }

        .pricing-note {
            max-width: 860px;
            margin: clamp(2.3rem, 4vw, 3.5rem) auto 0;
            padding: clamp(1.4rem, 3vw, 2rem);
            border: 1px solid rgba(95, 60, 24, .12);
            border-radius: .5rem;
            background: #f3ead6;
            text-align: center;
        }

        .pricing-note h2 {
            margin-bottom: .65rem;
            color: #111;
            font-family: "Playfair Display", Georgia, serif;
            font-size: clamp(2rem, 3vw, 2.8rem);
            font-weight: 600;
            line-height: 1.05;
        }

        .pricing-note p {
            max-width: 620px;
            margin: 0 auto 1.25rem;
            color: #6f6252;
            line-height: 1.65;
        }

        @media (max-width: 991.98px) {
            .pricing-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .pricing-page {
                padding-top: 3rem;
            }

            .pricing-page-title {
                text-align: left;
            }

            .pricing-card-large {
                border-width: 9px;
                padding: 1.25rem;
            }

            .pricing-card-head {
                display: block;
            }

            .pricing-card-price {
                margin-top: .45rem;
                text-align: left;
            }
        }
    </style>

    <section class="pricing-page">
        @if (request('checkout') === 'cancelled')
            <div class="alert alert-warning mb-4">Checkout was cancelled. Your current access has not changed.</div>
        @endif

        <h1 class="pricing-page-title">You&rsquo;re not choosing features.<br>You&rsquo;re choosing certainty.</h1>

        <div class="pricing-grid">
            <article class="pricing-card-large">
                <div class="pricing-card-head">
                    <h2 class="pricing-card-name">Spark</h2>
                    <div class="pricing-card-price">$79/month</div>
                </div>

                <p class="pricing-card-copy">Includes 1 free live call · 30 minutes</p>
                <p class="pricing-card-copy">Experience real-time guidance before committing.<br>Most people know within one call.</p>

                <a class="pricing-card-action" href="{{ route('billing.checkout', 'spark') }}">
                    @auth
                        Start Spark — Checkout
                    @else
                        Start Spark — Sign in to Checkout
                    @endauth
                </a>
            </article>

            <article class="pricing-card-large featured">
                <div class="pricing-card-head">
                    <h2 class="pricing-card-name">Forge</h2>
                    <div class="pricing-card-price">$249/month</div>
                </div>

                <p class="pricing-card-copy">For operators who need to know:</p>
                <ul class="pricing-card-list">
                    <li>When leverage forms.</li>
                    <li>When hesitation is costly.</li>
                    <li>When to move — and when to hold.</li>
                </ul>
                <p class="pricing-card-copy">It doesn&rsquo;t add complexity.<br>It removes second-guessing.<br>Decisions get quieter.<br>Timing gets sharper.<br>Results compound.</p>

                <a class="pricing-card-action" href="{{ route('billing.checkout', 'forge') }}">
                    @auth
                        Start Forge — Checkout
                    @else
                        Start Forge — Sign in to Checkout
                    @endauth
                </a>
            </article>
        </div>

        <div class="pricing-note">
            <h2>No surprise subscription after the free call.</h2>
            <p>Spark trial usage is tracked by Laravel. Paid access starts only after the user chooses to upgrade through Stripe Checkout.</p>
            <a class="btn btn-soft" href="{{ route('spark') }}">Read About Spark</a>
        </div>
    </section>
</x-layouts.app>
