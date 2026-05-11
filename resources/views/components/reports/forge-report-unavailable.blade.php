@props([
    'title',
    'missing',
])

<section class="surface-card p-4 p-lg-5">
    <h2 class="h4 fw-bold mb-3">{{ $title }}</h2>
    <p class="text-muted mb-3">This report is missing required fields and cannot be rendered safely right now.</p>
    <div class="small text-muted">Missing fields: {{ implode(', ', $missing) }}</div>
</section>
