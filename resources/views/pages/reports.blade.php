<x-layouts.app title="Reports | infinitesugar">
    <section class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <div class="eyebrow mb-3">Forge deliverables</div>
            <h1 class="section-title mb-3">Reports that make progress visible.</h1>
            <p class="lead-copy">Admin-managed uploads support weekly reports, Sunday charts, and monthly badge summaries now, with room for automation later.</p>
        </div>
        <div class="col-lg-6">
            <div class="placeholder-media">Forge report preview</div>
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
