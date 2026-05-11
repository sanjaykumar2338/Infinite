<x-layouts.app title="FORGE Sunday Night Executive Report">
    <section class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="eyebrow mb-3">Forge report</div>
                <h1 class="section-title mb-2">FORGE Sunday Night Executive Report</h1>
                <p class="lead-copy mb-0">Plain-language strategic interpretation for Forge users.</p>
            </div>
            <a class="btn btn-soft" href="{{ route('dashboard') }}">Back to Dashboard</a>
        </div>
    </section>

    @if ($validation['valid'])
        <x-reports.forge-sunday-brief :payload="$report->report_data" :report="$report" />
    @else
        <section class="surface-card p-4 p-lg-5">
            <h2 class="h4 fw-bold mb-3">Forge Sunday report unavailable</h2>
            <p class="text-muted mb-3">This report is missing required fields and cannot be rendered safely right now.</p>
            <div class="small text-muted">Missing fields: {{ implode(', ', $validation['missing']) }}</div>
        </section>
    @endif
</x-layouts.app>
