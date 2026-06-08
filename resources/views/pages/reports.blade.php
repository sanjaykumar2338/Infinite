<x-layouts.app title="Reports | infinitesugar">
    @php
        $contentSections = $pageContent ?? collect();
        $rawSection = fn (string $key) => $contentSections->get($key);
        $section = fn (string $key) => $rawSection($key)?->is_active ? $rawSection($key) : null;
        $isHidden = fn (string $key) => (bool) ($rawSection($key) && ! $rawSection($key)->is_active);
        $contentTitle = fn (string $key, string $fallback) => $section($key)?->title ?: $fallback;
        $contentSubtitle = fn (string $key, string $fallback) => $section($key)?->subtitle ?: $fallback;
        $contentBody = fn (string $key, string $fallback) => $section($key)?->body ?: $fallback;
        $contentImage = fn (string $key, string $fallback) => $section($key)?->image_url ?: asset($fallback);
    @endphp

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

    @unless ($isHidden('hero'))
    <section class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <div class="eyebrow mb-3">{{ $contentSubtitle('hero', 'Forge deliverables') }}</div>
            <h1 class="section-title mb-3">{{ $contentTitle('hero', 'Reports that make progress visible.') }}</h1>
            <p class="lead-copy">{!! nl2br(e($contentBody('hero', 'Admin-managed uploads support weekly reports, Sunday charts, and monthly badge summaries now, with room for automation later.'))) !!}</p>
        </div>
        <div class="col-lg-6">
            <figure class="reports-preview-frame">
                <img
                    src="{{ $contentImage('hero', 'images/briefings-and-reports.jpg') }}"
                    alt="InfiniteSugar Forge report and chart preview"
                    loading="lazy"
                >
            </figure>
        </div>
    </section>
    @endunless

    <section class="row g-4">
        @foreach ([
            ['weekly_reports', 'Weekly Reports', 'Summaries of behavioral signals, coaching opportunities, and progress notes delivered for Forge customers.', 'W'],
            ['sunday_charts', 'Sunday Charts', 'Two visual KPI snapshots every Sunday at 9 PM for trend tracking and review.', '2'],
            ['monthly_badges', 'Monthly Badge Reports', 'Milestone reports that turn progress into a clear retention moment.', 'B'],
        ] as [$key, $title, $copy, $icon])
            @unless ($isHidden($key))
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-pill mb-3">{{ $contentSubtitle($key, $icon) }}</div>
                        <h2 class="display-serif h4 mb-3">{{ $contentTitle($key, $title) }}</h2>
                        <p class="text-muted mb-0">{!! nl2br(e($contentBody($key, $copy))) !!}</p>
                    </div>
                </div>
            @endunless
        @endforeach
    </section>
</x-layouts.app>
