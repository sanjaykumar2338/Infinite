<x-layouts.admin title="Website Content">
    <div class="admin-card p-4 mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div>
                <h2 class="h4 fw-bold mb-1">{{ $pageLabels[$selectedPage] }} Content</h2>
                <p class="text-muted mb-0">Choose another page from the Website Content submenu in the sidebar.</p>
            </div>
            <span class="admin-pill pill-forge">{{ $sections->flatten(1)->count() }} sections</span>
        </div>
    </div>

    @foreach ([$selectedPage => $pageLabels[$selectedPage]] as $pageKey => $pageLabel)
        @php($items = $sections->get($pageKey, collect()))
        <div class="admin-card overflow-hidden mb-4">
            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between p-4 border-bottom">
                <div>
                    <div class="small text-muted fw-bold text-uppercase">{{ $pageKey }}</div>
                    <h2 class="h5 fw-bold mb-0">{{ $pageLabel }}</h2>
                </div>
                <span class="admin-pill pill-user">{{ $items->count() }} rows</span>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Content</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td style="min-width: 13rem;">
                                    <div class="fw-bold">{{ str_replace('_', ' ', $item->section_key) }}</div>
                                    <div class="small text-muted">Order {{ $item->sort_order }}</div>
                                </td>
                                <td style="min-width: 24rem;">
                                    <div class="fw-bold">{{ $item->title ?: 'Untitled section' }}</div>
                                    <div class="small text-muted text-truncate" style="max-width: 32rem;">{{ $item->subtitle ?: $item->body ?: 'No body copy yet' }}</div>
                                </td>
                                <td class="small" style="min-width: 12rem;">
                                    @if ($item->image_path)
                                        <a href="{{ $item->image_url }}" target="_blank" rel="noopener">View image</a>
                                    @else
                                        <span class="text-muted">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="admin-pill pill-{{ $item->is_active ? 'active' : 'cancelled' }}">{{ $item->is_active ? 'Active' : 'Hidden' }}</span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-admin" href="{{ route('admin.website-content.edit', ['pageContent' => $item, 'page' => $selectedPage]) }}">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-muted">No editable sections have been registered for this page.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</x-layouts.admin>
