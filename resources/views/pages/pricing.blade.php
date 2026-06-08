<x-layouts.app title="Pricing | infinitesugar">
    @php
        $contentSections = $pageContent ?? collect();
        $rawSection = fn (string $key) => $contentSections->get($key);
        $section = fn (string $key) => $rawSection($key)?->is_active ? $rawSection($key) : null;
        $isHidden = fn (string $key) => (bool) ($rawSection($key) && ! $rawSection($key)->is_active);
        $contentTitle = fn (string $key, string $fallback) => $section($key)?->title ?: $fallback;
        $contentSubtitle = fn (string $key, string $fallback) => $section($key)?->subtitle ?: $fallback;
        $contentBody = fn (string $key, string $fallback) => $section($key)?->body ?: $fallback;
        $contentButton = fn (string $key, string $fallback) => $section($key)?->button_text ?: $fallback;
        $paragraphs = fn (string $text) => array_values(array_filter(preg_split('/\R{2,}/', trim($text)) ?: [], fn ($line) => trim($line) !== ''));
        $lines = fn (string $text) => array_values(array_filter(preg_split('/\R/', trim($text)) ?: [], fn ($line) => trim($line) !== ''));
        $currentUser = auth()->user();
        $isCurrentPlan = fn (string $plan) => $currentUser
            && $currentUser->plan === $plan
            && ($currentUser->billingStatus() === 'active' || (bool) $currentUser->paidThrough()?->isFuture());
        $sparkCopy = $paragraphs($contentBody('spark_plan', "Includes 1 free live call · 30 minutes\n\nExperience real-time guidance before committing.\nMost people know within one call."));
        $forgeCopy = $paragraphs($contentBody('forge_plan', "For operators who need to know:\n\nWhen leverage forms.\nWhen hesitation is costly.\nWhen to move — and when to hold.\n\nIt doesn’t add complexity.\nIt removes second-guessing.\nDecisions get quieter.\nTiming gets sharper.\nResults compound."));
    @endphp

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

        .pricing-card-form {
            margin-top: auto;
        }

        .pricing-card-form .pricing-card-action,
        button.pricing-card-action {
            width: 100%;
            border-color: #c7af69;
        }

        .pricing-card-action:hover {
            background: #96763b;
            color: #fffdf8;
        }

        .pricing-card-action:disabled {
            cursor: not-allowed;
            opacity: .72;
            box-shadow: none;
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

        @unless ($isHidden('hero'))
            <h1 class="pricing-page-title">{!! nl2br(e($contentTitle('hero', "You’re not choosing features.\nYou’re choosing certainty."))) !!}</h1>
        @endunless

        <div class="pricing-grid">
            @unless ($isHidden('spark_plan'))
            <article class="pricing-card-large">
                <div class="pricing-card-head">
                    <h2 class="pricing-card-name">{{ $contentTitle('spark_plan', 'Spark') }}</h2>
                    <div class="pricing-card-price">{{ $contentSubtitle('spark_plan', '$79/month') }}</div>
                </div>

                @foreach ($sparkCopy as $copy)
                    <p class="pricing-card-copy">{!! nl2br(e($copy)) !!}</p>
                @endforeach

                @if ($isCurrentPlan('spark'))
                    <button class="pricing-card-action" type="button" disabled>Current Spark Plan</button>
                @else
                    <form class="pricing-card-form" method="post" action="{{ route('billing.checkout', 'spark') }}">
                        @csrf
                        <button class="pricing-card-action" type="submit">
                            @auth
                                {{ $contentButton('spark_plan', 'Start Spark — Checkout') }}
                            @else
                                Start Spark — Sign in to Checkout
                            @endauth
                        </button>
                    </form>
                @endif
            </article>
            @endunless

            @unless ($isHidden('forge_plan'))
            <article class="pricing-card-large featured">
                <div class="pricing-card-head">
                    <h2 class="pricing-card-name">{{ $contentTitle('forge_plan', 'Forge') }}</h2>
                    <div class="pricing-card-price">{{ $contentSubtitle('forge_plan', '$249/month') }}</div>
                </div>

                <p class="pricing-card-copy">{!! nl2br(e($forgeCopy[0] ?? 'For operators who need to know:')) !!}</p>
                <ul class="pricing-card-list">
                    @foreach ($lines($forgeCopy[1] ?? "When leverage forms.\nWhen hesitation is costly.\nWhen to move — and when to hold.") as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
                <p class="pricing-card-copy">{!! nl2br(e($forgeCopy[2] ?? "It doesn’t add complexity.\nIt removes second-guessing.\nDecisions get quieter.\nTiming gets sharper.\nResults compound.")) !!}</p>

                @if ($isCurrentPlan('forge'))
                    <button class="pricing-card-action" type="button" disabled>Current Forge Plan</button>
                @else
                    <form class="pricing-card-form" method="post" action="{{ route('billing.checkout', 'forge') }}">
                        @csrf
                        <button class="pricing-card-action" type="submit">
                            @auth
                                {{ $contentButton('forge_plan', 'Start Forge — Checkout') }}
                            @else
                                Start Forge — Sign in to Checkout
                            @endauth
                        </button>
                    </form>
                @endif
            </article>
            @endunless
        </div>

        @unless ($isHidden('note'))
        <div class="pricing-note">
            <h2>{{ $contentTitle('note', 'No surprise subscription after the free call.') }}</h2>
            <p>{!! nl2br(e($contentBody('note', 'Spark trial usage is tracked by Laravel. Paid access starts only after the user chooses to upgrade through Stripe Checkout.'))) !!}</p>
            <a class="btn btn-soft" href="{{ $section('note')?->button_url ?: route('spark') }}">{{ $contentButton('note', 'Read About Spark') }}</a>
        </div>
        @endunless
    </section>
</x-layouts.app>
