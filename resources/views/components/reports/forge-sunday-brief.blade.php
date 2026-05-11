@props([
    'payload',
    'report',
])

<style>
    .forge-brief {
        max-width: 900px;
        margin: 0 auto;
    }

    .forge-brief-shell {
        border: 1px solid rgba(95, 60, 24, .12);
        border-radius: .5rem;
        background: #fffdf8;
        box-shadow: 0 18px 42px rgba(95, 60, 24, .06);
        overflow: hidden;
    }

    .forge-brief-header {
        padding: 2rem;
        border-bottom: 1px solid rgba(95, 60, 24, .1);
        background: linear-gradient(180deg, rgba(248, 242, 228, .65), rgba(255, 253, 248, .96));
    }

    .forge-brief-title {
        margin: 0;
        color: #17110c;
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 600;
        line-height: 1;
    }

    .forge-brief-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem 1.25rem;
        margin-top: 1rem;
        color: #6f6252;
        font-family: Inter, Arial, sans-serif;
        font-size: .95rem;
    }

    .forge-brief-body {
        padding: 2rem;
    }

    .forge-brief-section + .forge-brief-section {
        margin-top: 1.6rem;
        padding-top: 1.6rem;
        border-top: 1px solid rgba(95, 60, 24, .08);
    }

    .forge-brief-heading {
        margin: 0 0 .65rem;
        color: #5f3c18;
        font-family: Inter, Arial, sans-serif;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .forge-brief-copy,
    .forge-brief-list li {
        color: #17110c;
        font-family: Inter, Arial, sans-serif;
        font-size: 1rem;
        line-height: 1.7;
    }

    .forge-brief-verdict {
        margin: 0;
        color: #17110c;
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(1.5rem, 2.6vw, 2rem);
        font-weight: 600;
        line-height: 1.2;
    }

    .forge-brief-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .forge-brief-card {
        padding: 1rem 1.1rem;
        border: 1px solid rgba(95, 60, 24, .1);
        border-radius: .5rem;
        background: rgba(248, 242, 228, .35);
    }

    .forge-brief-list {
        margin: 0;
        padding-left: 1.15rem;
    }

    @media (max-width: 767.98px) {
        .forge-brief-header,
        .forge-brief-body {
            padding: 1.25rem;
        }

        .forge-brief-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="forge-brief">
    <div class="forge-brief-shell">
        <header class="forge-brief-header">
            <h1 class="forge-brief-title">Sunday Night Executive Report</h1>
            <div class="forge-brief-meta">
                <span>{{ data_get($payload, 'meta.prepared_time') }}</span>
                <span>Prepared by {{ data_get($payload, 'meta.system') }}</span>
                @if ($report->period_end)
                    <span>Week ending {{ $report->period_end->toFormattedDateString() }}</span>
                @endif
            </div>
        </header>

        <div class="forge-brief-body">
            <section class="forge-brief-section">
                <h2 class="forge-brief-heading">Deal Momentum Verdict</h2>
                <p class="forge-brief-verdict">{{ data_get($payload, 'executive_verdict.headline') }}</p>
            </section>

            <section class="forge-brief-section">
                <div class="forge-brief-grid">
                    <div class="forge-brief-card">
                        <h2 class="forge-brief-heading">Identity Evolution</h2>
                        <div class="forge-brief-copy">{{ data_get($payload, 'identity_evolution.text') }}</div>
                    </div>
                    <div class="forge-brief-card">
                        <h2 class="forge-brief-heading">Before / After Shift</h2>
                        <div class="forge-brief-copy">{{ data_get($payload, 'before_after_shift.text') }}</div>
                    </div>
                </div>
            </section>

            <section class="forge-brief-section">
                <h2 class="forge-brief-heading">Executive Summary</h2>
                <div class="forge-brief-copy">{{ data_get($payload, 'executive_summary.text') }}</div>
            </section>

            <section class="forge-brief-section">
                <h2 class="forge-brief-heading">Key Insights</h2>
                <ul class="forge-brief-list">
                    <li>{{ data_get($payload, 'key_insights.firing') }}</li>
                    <li>{{ data_get($payload, 'key_insights.string') }}</li>
                </ul>
            </section>

            <section class="forge-brief-section">
                <h2 class="forge-brief-heading">Business Translation Layer</h2>
                <div class="forge-brief-grid">
                    <div class="forge-brief-card">
                        <h3 class="forge-brief-heading">Objection Handling</h3>
                        <div class="forge-brief-copy">{{ data_get($payload, 'business_translation_layer.objection_handling') }}</div>
                    </div>
                    <div class="forge-brief-card">
                        <h3 class="forge-brief-heading">Positioning</h3>
                        <div class="forge-brief-copy">{{ data_get($payload, 'business_translation_layer.positioning') }}</div>
                    </div>
                </div>
                <div class="forge-brief-card" style="margin-top: 1rem;">
                    <h3 class="forge-brief-heading">Conversion Signal</h3>
                    <div class="forge-brief-copy">{{ data_get($payload, 'business_translation_layer.conversion_signal') }}</div>
                </div>
            </section>

            <section class="forge-brief-section">
                <h2 class="forge-brief-heading">Habit Reinforcement</h2>
                <div class="forge-brief-copy">{{ data_get($payload, 'habit_reinforcement.text') }}</div>
            </section>

            <section class="forge-brief-section">
                <h2 class="forge-brief-heading">Next Week Focus</h2>
                <div class="forge-brief-copy">{{ data_get($payload, 'next_week_focus.text') }}</div>
            </section>
        </div>
    </div>
</div>
