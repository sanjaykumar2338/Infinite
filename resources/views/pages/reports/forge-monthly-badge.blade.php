<x-layouts.app title="FORGE Monthly Badge">
    <section class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="eyebrow mb-3">Forge badge</div>
                <h1 class="section-title mb-2">FORGE Monthly Badge Report</h1>
                <p class="lead-copy mb-0">Monthly badge report for Forge users.</p>
            </div>
            <a class="btn btn-soft" href="{{ route('dashboard') }}">Back to Dashboard</a>
        </div>
    </section>

    @if ($validation['valid'])
        <x-reports.forge-monthly-badge :payload="$validation['payload']" :badge="$badge" />
    @else
        <x-reports.forge-report-unavailable title="Forge monthly badge unavailable" :missing="$validation['missing']" />
    @endif
</x-layouts.app>
