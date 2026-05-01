<x-layouts.admin title="Admin Dashboard">
    <div class="row g-3 mb-4">
        @foreach ([
            ['Users', $usersCount, 'Total Firebase-linked accounts'],
            ['Active Users', $activeCount, 'Currently paid or enabled'],
            ['Spark', $sparkCount, 'Spark plan members'],
            ['Forge', $forgeCount, 'Premium reporting members'],
            ['Report Assets', $reportCount, 'Reports, charts, badges'],
            ['Webhook Events', $webhookCount, 'Idempotent Stripe logs'],
        ] as [$label, $value, $copy])
            <div class="col-sm-6 col-xl-4">
                <div class="admin-card stat-card">
                    <div class="stat-label mb-2">{{ $label }}</div>
                    <div class="display-6 fw-bold mb-2">{{ $value }}</div>
                    <div class="text-muted">{{ $copy }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="admin-card overflow-hidden">
        <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
            <div>
                <h2 class="h5 fw-bold mb-1">Recent Stripe Events</h2>
                <p class="text-muted mb-0">Webhook history is stored once per Stripe event ID.</p>
            </div>
            <span class="admin-pill pill-spark">Stripe</span>
        </div>
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Event ID</th><th>Type</th><th>Processed</th><th>Received</th></tr></thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td><code>{{ $event->stripe_event_id }}</code></td>
                            <td><span class="admin-pill pill-free">{{ $event->type }}</span></td>
                            <td>{{ optional($event->processed_at)->toDateTimeString() }}</td>
                            <td>{{ $event->created_at->toDateTimeString() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted p-4">No webhook events yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
