<x-layouts.admin title="Reports">
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="admin-card stat-card">
                <div class="stat-label mb-2">Weekly Reports</div>
                <div class="display-6 fw-bold mb-2">{{ $reports->count() }}</div>
                <div class="text-muted">Latest uploaded report assets.</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-card stat-card">
                <div class="stat-label mb-2">Weekly Charts</div>
                <div class="display-6 fw-bold mb-2">{{ $charts->count() }}</div>
                <div class="text-muted">Chart assets for Forge customers.</div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-card stat-card">
                <div class="stat-label mb-2">Badge Reports</div>
                <div class="display-6 fw-bold mb-2">{{ $badges->count() }}</div>
                <div class="text-muted">Monthly milestone report assets.</div>
            </div>
        </div>
    </div>

    <div class="admin-card upload-panel mb-4">
        <div class="p-4 border-bottom">
            <h2 class="h4 fw-bold mb-1">Upload Report Asset</h2>
            <p class="text-muted mb-0">Attach weekly reports, Sunday charts, or monthly badge reports to a user.</p>
        </div>
        <div class="p-4">
            <form method="post" action="{{ route('admin.reports.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-2">
                    <label class="form-label fw-bold">Type</label>
                    <select class="form-select" name="type">
                        <option value="report">Weekly Report</option>
                        <option value="chart">Weekly Chart</option>
                        <option value="badge">Monthly Badge</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">User</label>
                    <select class="form-select" name="user_id" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Title</label>
                    <input class="form-control" name="title" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Report Format</label>
                    <select class="form-select" name="report_type">
                        <option value="standard">Standard report</option>
                        <option value="forge_sunday_weekly_brief">Forge Sunday 9PM brief</option>
                        <option value="forge_weekly_heatmap">Forge Weekly Heatmap</option>
                        <option value="forge_weekly_timeline">Forge Weekly Timeline</option>
                        <option value="forge_monthly_badge">Forge Monthly Badge</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Period Start</label>
                    <input class="form-control" name="period_start" type="date">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Period End</label>
                    <input class="form-control" name="period_end" type="date">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Chart Type</label>
                    <input class="form-control" name="chart_type" placeholder="weekly">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Badge Name</label>
                    <input class="form-control" name="badge_name">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">File</label>
                    <input class="form-control" name="file" type="file">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Summary</label>
                    <textarea class="form-control" name="summary" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Structured Forge Report JSON</label>
                    <textarea class="form-control font-monospace" name="report_json" rows="12" placeholder='{"meta":{"prepared_time":"Sunday 9:00 PM","system":"Infinite Sugar"}}'></textarea>
                    <div class="form-text">Paste the fixed JSON for Sunday briefs, weekly heatmaps, weekly timelines, or monthly badge reports.</div>
                </div>
                <div class="col-12">
                    <button class="btn btn-admin px-4">Upload Asset</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ([['Weekly Reports', 'report', $reports], ['Weekly Charts', 'chart', $charts], ['Monthly Badges', 'badge', $badges]] as [$heading, $type, $items])
        <div class="admin-card overflow-hidden mb-4">
            <div class="p-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">{{ $heading }}</h2>
                    <p class="text-muted mb-0">Manage uploaded {{ strtolower($heading) }}.</p>
                </div>
                <span class="admin-pill pill-{{ $type === 'badge' ? 'tester' : ($type === 'chart' ? 'spark' : 'forge') }}">{{ $items->count() }} total</span>
            </div>
            <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead><tr><th>Title</th><th>User</th><th>Period</th><th>File</th><th></th></tr></thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $item->title }}</div>
                                @if (! empty($item->summary))
                                    <div class="small text-muted text-truncate" style="max-width: 24rem;">{{ $item->summary }}</div>
                                @endif
                                @if (($item->report_type ?? 'standard') !== 'standard')
                                    <div class="small text-muted">{{ $item->report_type }}</div>
                                @endif
                            </td>
                            <td><span class="admin-pill pill-user">{{ $item->user->email }}</span></td>
                            <td>{{ optional($item->period_start ?? $item->month)->toDateString() }} {{ optional($item->period_end)->toDateString() }}</td>
                            <td><code>{{ $item->file_path ?: 'n/a' }}</code></td>
                            <td class="text-end">
                                @if ($type === 'report' && ($item->report_type ?? 'standard') === 'forge_sunday_weekly_brief')
                                    <a class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-2" href="{{ route('dashboard.reports.show', $item) }}">Preview</a>
                                @elseif ($type === 'chart' && in_array(($item->report_type ?? 'standard'), ['forge_weekly_timeline', 'forge_weekly_heatmap'], true))
                                    <a class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-2" href="{{ route('dashboard.charts.show', $item) }}">Preview</a>
                                @elseif ($type === 'badge' && ($item->report_type ?? 'standard') === 'forge_monthly_badge')
                                    <a class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-2" href="{{ route('dashboard.badges.show', $item) }}">Preview</a>
                                @endif
                                <form method="post" action="{{ route('admin.reports.destroy', [$type, $item->id]) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted p-4">Nothing uploaded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    @endforeach
</x-layouts.admin>
