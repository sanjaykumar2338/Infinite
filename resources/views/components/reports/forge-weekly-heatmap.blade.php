@props([
    'payload',
    'chart',
])

@php
    $days = data_get($payload, 'heatmap.days', []);
    $rows = data_get($payload, 'heatmap.rows', []);
    $toneClass = fn ($value) => match ($value) {
        'soft' => 'soft',
        'strong' => 'strong',
        'earned' => 'earned',
        default => 'steady',
    };
@endphp

<style>
    .forge-heatmap {
        max-width: 920px;
        margin: 0 auto;
    }

    .forge-heatmap-shell {
        overflow: hidden;
        border: 1px solid rgba(95, 60, 24, .14);
        border-radius: .5rem;
        background: #fffdf8;
        box-shadow: 0 18px 42px rgba(95, 60, 24, .06);
    }

    .forge-heatmap-header {
        padding: 2rem;
        border-bottom: 1px solid rgba(95, 60, 24, .1);
        background: linear-gradient(180deg, rgba(248, 242, 228, .72), rgba(255, 253, 248, .96));
    }

    .forge-heatmap-title,
    .forge-heatmap-verdict {
        color: #17110c;
        font-family: "Playfair Display", Georgia, serif;
    }

    .forge-heatmap-title {
        margin: 0;
        font-size: clamp(2rem, 4vw, 2.9rem);
        font-weight: 600;
        line-height: 1;
    }

    .forge-heatmap-meta,
    .forge-heatmap-copy,
    .forge-heatmap-label,
    .forge-heatmap-day {
        color: #6f6252;
        font-family: Inter, Arial, sans-serif;
    }

    .forge-heatmap-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem 1.25rem;
        margin-top: 1rem;
        font-size: .95rem;
    }

    .forge-heatmap-body {
        padding: 2rem;
    }

    .forge-heatmap-section + .forge-heatmap-section {
        margin-top: 1.6rem;
        padding-top: 1.6rem;
        border-top: 1px solid rgba(95, 60, 24, .08);
    }

    .forge-heatmap-heading {
        margin: 0 0 .65rem;
        color: #5f3c18;
        font-family: Inter, Arial, sans-serif;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .forge-heatmap-verdict {
        margin: 0;
        font-size: clamp(1.45rem, 2.6vw, 1.95rem);
        font-weight: 600;
        line-height: 1.2;
    }

    .forge-heatmap-grid {
        display: grid;
        gap: .75rem;
        padding: 1rem;
        border: 1px solid rgba(95, 60, 24, .08);
        border-radius: .5rem;
        background: rgba(248, 242, 228, .28);
    }

    .forge-heatmap-row,
    .forge-heatmap-days {
        display: grid;
        grid-template-columns: minmax(10rem, 1.15fr) repeat({{ max(count($days), 1) }}, minmax(2.4rem, 1fr));
        gap: .5rem;
        align-items: center;
    }

    .forge-heatmap-day,
    .forge-heatmap-label {
        font-size: .88rem;
        font-weight: 800;
    }

    .forge-heatmap-day {
        text-align: center;
    }

    .forge-heatmap-cell {
        min-height: 2.85rem;
        border: 1px solid rgba(95, 60, 24, .08);
        border-radius: .35rem;
        background: #efe6d4;
    }

    .forge-heatmap-cell.soft {
        background: #efe6d4;
    }

    .forge-heatmap-cell.steady {
        background: #dac694;
    }

    .forge-heatmap-cell.strong {
        background: #b89a5d;
    }

    .forge-heatmap-cell.earned {
        background: #56624f;
    }

    .forge-heatmap-copy {
        font-size: 1rem;
        line-height: 1.7;
    }

    .forge-heatmap-note {
        padding: 1rem 1.1rem;
        border: 1px solid rgba(95, 60, 24, .1);
        border-radius: .5rem;
        background: rgba(248, 242, 228, .35);
    }

    @media (max-width: 767.98px) {
        .forge-heatmap-header,
        .forge-heatmap-body {
            padding: 1.25rem;
        }

        .forge-heatmap-grid {
            overflow-x: auto;
        }

        .forge-heatmap-row,
        .forge-heatmap-days {
            min-width: 42rem;
        }
    }
</style>

<div class="forge-heatmap">
    <div class="forge-heatmap-shell">
        <header class="forge-heatmap-header">
            <h1 class="forge-heatmap-title">Weekly Heatmap</h1>
            <div class="forge-heatmap-meta">
                <span>{{ data_get($payload, 'meta.prepared_time') }}</span>
                <span>Prepared by {{ data_get($payload, 'meta.system') }}</span>
                @if ($chart->period_end)
                    <span>Week ending {{ $chart->period_end->toFormattedDateString() }}</span>
                @endif
            </div>
        </header>

        <div class="forge-heatmap-body">
            <section class="forge-heatmap-section">
                <h2 class="forge-heatmap-heading">Executive Read</h2>
                <p class="forge-heatmap-verdict">{{ data_get($payload, 'executive_summary.headline') }}</p>
            </section>

            <section class="forge-heatmap-section">
                <h2 class="forge-heatmap-heading">Behavioral Presence Map</h2>
                <div class="forge-heatmap-grid">
                    <div class="forge-heatmap-days">
                        <span></span>
                        @foreach ($days as $day)
                            <span class="forge-heatmap-day">{{ $day }}</span>
                        @endforeach
                    </div>
                    @foreach ($rows as $row)
                        <div class="forge-heatmap-row">
                            <span class="forge-heatmap-label">{{ $row['label'] }}</span>
                            @foreach ($row['values'] as $value)
                                <span class="forge-heatmap-cell {{ $toneClass($value) }}" aria-label="{{ $row['label'] }} {{ $value }}"></span>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="forge-heatmap-section">
                <h2 class="forge-heatmap-heading">Strategic Interpretation</h2>
                <div class="forge-heatmap-copy">{{ data_get($payload, 'strategic_interpretation.text') }}</div>
            </section>

            <section class="forge-heatmap-section">
                <h2 class="forge-heatmap-heading">Reinforcement</h2>
                <div class="forge-heatmap-note">
                    <div class="forge-heatmap-copy">{{ data_get($payload, 'reinforcement.text') }}</div>
                </div>
            </section>
        </div>
    </div>
</div>
