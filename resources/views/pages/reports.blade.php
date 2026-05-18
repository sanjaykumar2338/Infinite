<x-layouts.app title="Reports | infinitesugar">
    <style>
        .reports-preview-frame {
            max-width: 100%;
            margin: 0;
            padding: clamp(.65rem, 1.8vw, 1rem);
            border: 1px solid rgba(95, 60, 24, .14);
            border-radius: .5rem;
            background: rgba(255, 253, 248, .78);
            box-shadow: 0 24px 64px rgba(95, 60, 24, .1);
            overflow: hidden;
        }

        .reports-preview-frame img {
            display: block;
            max-width: 100%;
            width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: .42rem;
            box-shadow: 0 18px 42px rgba(95, 60, 24, .11);
        }
    </style>

    <section class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <div class="eyebrow mb-3">Forge deliverables</div>
            <h1 class="section-title mb-3">Reports that make progress visible.</h1>
            <p class="lead-copy">Admin-managed uploads support weekly reports, Sunday charts, and monthly badge summaries now, with room for automation later.</p>
        </div>
        <div class="col-lg-6">
            <figure class="reports-preview-frame">
                <img
                    src="{{ asset('images/briefings-and-reports.jpg') }}"
                    alt="InfiniteSugar Forge report and chart preview"
                    loading="lazy"
                >
            </figure>
        </div>
    </section>

    <section class="row g-4">
        @foreach ([
            ['Weekly Reports', 'Summaries of behavioral signals, coaching opportunities, and progress notes delivered for Forge customers.', 'W'],
            ['Sunday Charts', 'Two visual KPI snapshots every Sunday at 9 PM for trend tracking and review.', '2'],
            ['Monthly Badge Reports', 'Milestone reports that turn progress into a clear retention moment.', 'B'],
        ] as [$title, $copy, $icon])
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="icon-pill mb-3">{{ $icon }}</div>
                    <h2 class="display-serif h4 mb-3">{{ $title }}</h2>
                    <p class="text-muted mb-0">{{ $copy }}</p>
                </div>
            </div>
        @endforeach
    </section>
</x-layouts.app>
