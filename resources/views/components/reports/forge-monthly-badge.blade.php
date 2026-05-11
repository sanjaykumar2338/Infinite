@props([
    'payload',
    'badge',
])

<style>
    .forge-badge {
        max-width: 900px;
        margin: 0 auto;
    }

    .forge-badge-shell {
        border: 1px solid rgba(95, 60, 24, .12);
        border-radius: .5rem;
        background: #fffdf8;
        box-shadow: 0 18px 42px rgba(95, 60, 24, .06);
        overflow: hidden;
    }

    .forge-badge-header {
        padding: 2rem;
        border-bottom: 1px solid rgba(95, 60, 24, .1);
        background: linear-gradient(180deg, rgba(248, 242, 228, .65), rgba(255, 253, 248, .96));
    }

    .forge-badge-title,
    .forge-badge-headline {
        color: #17110c;
        font-family: "Playfair Display", Georgia, serif;
    }

    .forge-badge-title {
        margin: 0;
        font-size: clamp(2rem, 4vw, 2.9rem);
        font-weight: 600;
        line-height: 1;
    }

    .forge-badge-meta,
    .forge-badge-copy {
        color: #6f6252;
        font-family: Inter, Arial, sans-serif;
    }

    .forge-badge-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem 1.25rem;
        margin-top: 1rem;
        font-size: .95rem;
    }

    .forge-badge-body {
        padding: 2rem;
    }

    .forge-badge-section + .forge-badge-section {
        margin-top: 1.6rem;
        padding-top: 1.6rem;
        border-top: 1px solid rgba(95, 60, 24, .08);
    }

    .forge-badge-heading {
        margin: 0 0 .65rem;
        color: #5f3c18;
        font-family: Inter, Arial, sans-serif;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .forge-badge-headline {
        margin: 0;
        font-size: clamp(1.45rem, 2.6vw, 1.95rem);
        font-weight: 600;
        line-height: 1.2;
    }

    .forge-badge-copy {
        font-size: 1rem;
        line-height: 1.7;
    }

    .forge-badge-focus {
        padding: 1rem 1.1rem;
        border: 1px solid rgba(95, 60, 24, .1);
        border-radius: .5rem;
        background: rgba(248, 242, 228, .35);
    }

    @media (max-width: 767.98px) {
        .forge-badge-header,
        .forge-badge-body {
            padding: 1.25rem;
        }
    }
</style>

<div class="forge-badge">
    <div class="forge-badge-shell">
        <header class="forge-badge-header">
            <h1 class="forge-badge-title">{{ data_get($payload, 'badge_name', $badge->badge_name ?: $badge->title) }}</h1>
            <div class="forge-badge-meta">
                <span>{{ data_get($payload, 'meta.prepared_time') }}</span>
                <span>Prepared by {{ data_get($payload, 'meta.system') }}</span>
                @if ($badge->month)
                    <span>Month: {{ $badge->month->format('F Y') }}</span>
                @endif
            </div>
        </header>

        <div class="forge-badge-body">
            <section class="forge-badge-section">
                <h2 class="forge-badge-heading">Executive Summary</h2>
                <p class="forge-badge-headline">{{ data_get($payload, 'executive_summary.headline') }}</p>
            </section>

            <section class="forge-badge-section">
                <h2 class="forge-badge-heading">Strategic Interpretation</h2>
                <div class="forge-badge-copy">{{ data_get($payload, 'strategic_interpretation.text') }}</div>
            </section>

            <section class="forge-badge-section">
                <h2 class="forge-badge-heading">Identity Evolution</h2>
                <div class="forge-badge-copy">{{ data_get($payload, 'identity_evolution.text') }}</div>
            </section>

            <section class="forge-badge-section">
                <h2 class="forge-badge-heading">Next Month Focus</h2>
                <div class="forge-badge-focus">
                    <div class="forge-badge-copy">{{ data_get($payload, 'next_month_focus.text') }}</div>
                </div>
            </section>
        </div>
    </div>
</div>
