<x-layouts.admin title="Edit Website Content">
    <form class="admin-card p-4" method="post" action="{{ route('admin.website-content.update', $pageContent) }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="d-flex flex-wrap gap-3 align-items-start justify-content-between mb-4">
            <div>
                <div class="small text-muted fw-bold text-uppercase">{{ $pageLabels[$pageContent->page_key] ?? $pageContent->page_key }}</div>
                <h2 class="h4 fw-bold mb-1">{{ str_replace('_', ' ', $pageContent->section_key) }}</h2>
                <p class="text-muted mb-0">Empty fields fall back to the current hardcoded page copy when this section is inactive or missing.</p>
            </div>
            <a class="btn btn-admin-soft" href="{{ route('admin.website-content.index', ['page' => $pageContent->page_key]) }}">Back</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <div class="fw-bold mb-1">Please review the highlighted fields.</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-8">
                <label class="form-label fw-bold">Title</label>
                <input class="form-control form-control-lg" name="title" value="{{ old('title', $pageContent->title) }}">
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-bold">Sort Order</label>
                <input class="form-control form-control-lg" name="sort_order" type="number" min="0" value="{{ old('sort_order', $pageContent->sort_order) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Subtitle / Label</label>
                <input class="form-control" name="subtitle" value="{{ old('subtitle', $pageContent->subtitle) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold">Body</label>
                <textarea class="form-control" name="body" rows="10">{{ old('body', $pageContent->body) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Button Text</label>
                <input class="form-control" name="button_text" value="{{ old('button_text', $pageContent->button_text) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Button URL</label>
                <input class="form-control" name="button_url" value="{{ old('button_url', $pageContent->button_url) }}" placeholder="/pricing">
            </div>
            <div class="col-lg-7">
                <label class="form-label fw-bold">Section Image</label>
                <input class="form-control" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg,image/*">
                <div class="form-text">Optional. Replaces the current image for sections that render one.</div>
            </div>
            <div class="col-lg-5">
                @if ($pageContent->image_path)
                    <div class="small text-muted fw-bold text-uppercase mb-2">Current image</div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <img src="{{ $pageContent->image_url }}" alt="" style="width: 8rem; height: 5rem; object-fit: cover; border-radius: .75rem; border: 1px solid #e5d9c2;">
                        <label class="form-check">
                            <input class="form-check-input" name="remove_image" type="checkbox" value="1">
                            <span class="form-check-label">Remove image</span>
                        </label>
                    </div>
                @else
                    <div class="text-muted small mt-4">No image is set for this section.</div>
                @endif
            </div>
            <div class="col-12">
                <label class="form-check">
                    <input class="form-check-input" name="is_active" type="checkbox" value="1" @checked(old('is_active', $pageContent->is_active))>
                    <span class="form-check-label fw-bold">Active on website</span>
                </label>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end mt-4">
            <a class="btn btn-admin-soft" href="{{ route('admin.website-content.index', ['page' => $pageContent->page_key]) }}">Cancel</a>
            <button class="btn btn-admin px-4">Save Content</button>
        </div>
    </form>
</x-layouts.admin>
