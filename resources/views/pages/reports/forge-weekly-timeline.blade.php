<x-layouts.app title="FORGE Presence Intelligence">
    <section class="mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="eyebrow mb-3">Forge chart</div>
                <h1 class="section-title mb-2">FORGE Weekly Strategic Timeline</h1>
                <p class="lead-copy mb-0">Weekly strategic timeline for Forge users.</p>
            </div>
            <a class="btn btn-soft" href="{{ route('dashboard') }}">Back to Dashboard</a>
        </div>
    </section>

    @if ($validation['valid'])
        <x-reports.forge-weekly-timeline :payload="$validation['payload']" :chart="$chart" />
    @else
        <x-reports.forge-report-unavailable title="Forge weekly timeline unavailable" :missing="$validation['missing']" />
    @endif
</x-layouts.app>
