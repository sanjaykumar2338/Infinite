<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Support\PageContentDefaults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebsiteContentController extends Controller
{
    public function index(Request $request): View
    {
        PageContentDefaults::ensureRecords();

        $pageLabels = PageContentDefaults::pageLabels();
        $selectedPage = $request->query('page', array_key_first($pageLabels));

        abort_unless(array_key_exists($selectedPage, $pageLabels), 404);

        $sections = PageContent::query()
            ->where('page_key', $selectedPage)
            ->orderBy('page_key')
            ->orderBy('sort_order')
            ->orderBy('section_key')
            ->get()
            ->groupBy('page_key');

        return view('admin.website-content.index', [
            'sections' => $sections,
            'pageLabels' => $pageLabels,
            'selectedPage' => $selectedPage,
        ]);
    }

    public function edit(PageContent $pageContent): View
    {
        return view('admin.website-content.edit', [
            'pageContent' => $pageContent,
            'pageLabels' => PageContentDefaults::pageLabels(),
        ]);
    }

    public function update(Request $request, PageContent $pageContent): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if ($request->boolean('remove_image')) {
            $this->deleteUploadedImage($pageContent->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            $this->deleteUploadedImage($pageContent->image_path);
            $data['image_path'] = $request->file('image')->store('page-content', 'public');
        }

        unset($data['image'], $data['remove_image']);

        $pageContent->update($data);

        return redirect()
            ->route('admin.website-content.index', ['page' => $pageContent->page_key])
            ->with('status', 'Website content updated.');
    }

    private function deleteUploadedImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
