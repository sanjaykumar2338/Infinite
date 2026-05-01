<x-layouts.app title="Forge Plan">
    <section class="row align-items-center g-5">
        <div class="col-lg-6">
            <div class="eyebrow mb-3">Premium insights tier</div>
            <h1 class="section-title fw-bold mb-3">Forge turns calls into a reporting engine.</h1>
            <p class="lead-copy">Active Forge users get live insights, weekly reports, two weekly charts every Sunday at 9 PM, and a monthly badge report.</p>
            <a class="btn btn-sugar" href="{{ route('reports.showcase') }}">See Reports</a>
        </div>
        <div class="col-lg-6">
            <div class="row g-3">
                @foreach ([['Live insights', 'Real-time prompts'], ['Weekly reports', 'Admin-managed uploads'], ['KPI charts', 'Sunday delivery'], ['Badge report', 'Monthly milestone']] as [$title, $copy])
                    <div class="col-sm-6">
                        <div class="feature-card">
                            <h2 class="h5 fw-bold">{{ $title }}</h2>
                            <p class="text-muted mb-0">{{ $copy }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
