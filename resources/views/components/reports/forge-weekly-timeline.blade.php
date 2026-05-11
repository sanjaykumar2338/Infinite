@props([
    'payload',
    'chart',
])

@php
    $days = data_get($payload, 'timeline.days', []);
    $curve = data_get($payload, 'timeline.progression_curve', []);
    $insights = data_get($payload, 'pattern_insights', []);
    $width = 760;
    $height = 220;
    $paddingX = 30;
    $paddingY = 24;
    $count = max(count($curve), 1);
    $stepX = $count > 1 ? ($width - ($paddingX * 2)) / ($count - 1) : 0;
    $points = collect($curve)->values()->map(function ($value, $index) use ($height, $paddingX, $paddingY, $stepX) {
        $normalized = max(0, min(100, (float) $value));
        $x = $paddingX + ($index * $stepX);
        $y = $height - $paddingY - (($height - ($paddingY * 2)) * ($normalized / 100));

        return [
            'x' => round($x, 2),
            'y' => round($y, 2),
        ];
    });
    $polyline = $points->map(fn ($point) => "{$point['x']},{$point['y']}")->implode(' ');
@endphp

<style>
    .forge-timeline {
        max-width: 920px;
        margin: 0 auto;
    }

    .forge-timeline-shell {
        border: 1px solid rgba(95, 60, 24, .12);
        border-radius: .5rem;
        background: #fffdf8;
        box-shadow: 0 18px 42px rgba(95, 60, 24, .06);
        overflow: hidden;
    }

    .forge-timeline-header {
        padding: 2rem;
        border-bottom: 1px solid rgba(95, 60, 24, .1);
        background: linear-gradient(180deg, rgba(248, 242, 228, .65), rgba(255, 253, 248, .96));
    }

    .forge-timeline-title,
    .forge-timeline-subtitle,
    .forge-timeline-highlight {
        color: #17110c;
        font-family: "Playfair Display", Georgia, serif;
    }

    .forge-timeline-title {
        margin: 0;
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 600;
        line-height: 1;
    }

    .forge-timeline-subtitle {
        margin: .35rem 0 0;
        font-size: clamp(1.2rem, 2.6vw, 1.7rem);
        font-weight: 600;
        line-height: 1.1;
    }

    .forge-timeline-meta,
    .forge-timeline-copy,
    .forge-timeline-insights li,
    .forge-timeline-days span {
        color: #6f6252;
        font-family: Inter, Arial, sans-serif;
    }

    .forge-timeline-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem 1.25rem;
        margin-top: 1rem;
        font-size: .95rem;
    }

    .forge-timeline-body {
        padding: 2rem;
    }

    .forge-timeline-section + .forge-timeline-section {
        margin-top: 1.6rem;
        padding-top: 1.6rem;
        border-top: 1px solid rgba(95, 60, 24, .08);
    }

    .forge-timeline-heading {
        margin: 0 0 .65rem;
        color: #5f3c18;
        font-family: Inter, Arial, sans-serif;
        font-size: .8rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .forge-timeline-highlight {
        margin: 0;
        font-size: clamp(1.45rem, 2.6vw, 1.95rem);
        font-weight: 600;
        line-height: 1.2;
    }

    .forge-timeline-chart {
        padding: 1rem;
        border: 1px solid rgba(95, 60, 24, .08);
        border-radius: .5rem;
        background: rgba(248, 242, 228, .28);
    }

    .forge-timeline-chart svg {
        width: 100%;
        height: auto;
        display: block;
    }

    .forge-timeline-days {
        display: grid;
        grid-template-columns: repeat({{ max(count($days), 1) }}, minmax(0, 1fr));
        gap: .5rem;
        margin-top: .85rem;
        font-size: .9rem;
        font-weight: 700;
        text-align: center;
    }

    .forge-timeline-insights {
        margin: 0;
        padding-left: 1.15rem;
    }

    .forge-timeline-insights li {
        margin-bottom: .75rem;
        font-size: 1rem;
        line-height: 1.7;
    }

    .forge-timeline-takeaway {
        padding: 1rem 1.1rem;
        border: 1px solid rgba(95, 60, 24, .1);
        border-radius: .5rem;
        background: rgba(248, 242, 228, .35);
    }

    @media (max-width: 767.98px) {
        .forge-timeline-header,
        .forge-timeline-body {
            padding: 1.25rem;
        }

        .forge-timeline-days {
            font-size: .78rem;
        }
    }
</style>

<div class="forge-timeline">
    <div class="forge-timeline-shell">
        <header class="forge-timeline-header">
            <h1 class="forge-timeline-title">FORGE Presence Intelligence</h1>
            <p class="forge-timeline-subtitle">Weekly Strategic Timeline</p>
            <div class="forge-timeline-meta">
                <span>{{ data_get($payload, 'meta.prepared_time') }}</span>
                <span>Prepared by {{ data_get($payload, 'meta.system') }}</span>
                @if ($chart->period_end)
                    <span>Week ending {{ $chart->period_end->toFormattedDateString() }}</span>
                @endif
            </div>
        </header>

        <div class="forge-timeline-body">
            <section class="forge-timeline-section">
                <h2 class="forge-timeline-heading">Executive Summary</h2>
                <p class="forge-timeline-highlight">{{ data_get($payload, 'executive_summary.headline') }}</p>
            </section>

            <section class="forge-timeline-section">
                <h2 class="forge-timeline-heading">Progression Curve</h2>
                <div class="forge-timeline-chart">
                    <svg viewBox="0 0 {{ $width }} {{ $height }}" role="img" aria-label="Weekly progression curve">
                        @foreach ([25, 50, 75] as $line)
                            @php
                                $lineY = $height - $paddingY - (($height - ($paddingY * 2)) * ($line / 100));
                            @endphp
                            <line x1="{{ $paddingX }}" y1="{{ $lineY }}" x2="{{ $width - $paddingX }}" y2="{{ $lineY }}" stroke="rgba(95,60,24,.12)" stroke-dasharray="4 6" />
                        @endforeach
                        <polyline
                            fill="none"
                            stroke="#a8873f"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            points="{{ $polyline }}"
                        />
                        @foreach ($points as $point)
                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" fill="#fffdf8" stroke="#5f3c18" stroke-width="2" />
                        @endforeach
                    </svg>
                    <div class="forge-timeline-days">
                        @foreach ($days as $day)
                            <span>{{ $day }}</span>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="forge-timeline-section">
                <h2 class="forge-timeline-heading">Pattern Insights</h2>
                <ul class="forge-timeline-insights">
                    @foreach ($insights as $insight)
                        <li>{{ $insight }}</li>
                    @endforeach
                </ul>
            </section>

            <section class="forge-timeline-section">
                <h2 class="forge-timeline-heading">Directional Takeaway</h2>
                <div class="forge-timeline-takeaway">
                    <div class="forge-timeline-copy">{{ data_get($payload, 'directional_takeaway.text') }}</div>
                </div>
            </section>
        </div>
    </div>
</div>
