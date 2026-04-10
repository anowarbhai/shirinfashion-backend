<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'widgets' => 'nullable|array',
            'widgets.*.id' => 'required|string',
            'widgets.*.settings' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Generate slug from title if not provided
        $validated['slug'] = $request->input('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($validated['title']);

        // Make sure slug is unique
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Page::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug.'-'.$count;
            $count++;
        }

        if (! isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        if (! isset($validated['sort_order'])) {
            $validated['sort_order'] = 0;
        }

        if (! isset($validated['widgets'])) {
            $validated['widgets'] = [];
        }

        Page::create($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully');
    }

    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'widgets' => 'nullable|array',
            'widgets.*.id' => 'required|string',
            'widgets.*.settings' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Generate slug from title if not provided
        $validated['slug'] = $request->input('slug')
            ? Str::slug($request->input('slug'))
            : Str::slug($validated['title']);

        // Make sure slug is unique (except current page)
        $count = 1;
        $originalSlug = $validated['slug'];
        while (Page::where('slug', $validated['slug'])->where('id', '!=', $page->id)->exists()) {
            $validated['slug'] = $originalSlug.'-'.$count;
            $count++;
        }

        if (! isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        if (! isset($validated['widgets'])) {
            $validated['widgets'] = [];
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully');
    }

    public function checkSlug(Request $request)
    {
        $slug = Str::slug($request->input('slug', ''));
        $pageId = $request->input('page_id');

        $query = Page::where('slug', $slug);

        if ($pageId) {
            $query->where('id', '!=', $pageId);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'slug' => $slug,
        ]);
    }

    public function builder(Page $page)
    {
        return view('admin.pages.builder', compact('page'));
    }

    public function builderUpdate(Request $request, Page $page)
    {
        try {
            $widgetsInput = $request->input('widgets');

            if (is_string($widgetsInput)) {
                $widgets = json_decode($widgetsInput, true) ?? [];
            } else {
                $widgets = is_array($widgetsInput) ? $widgetsInput : [];
            }

            $validatedWidgets = [];
            if (is_array($widgets)) {
                foreach ($widgets as $widget) {
                    if (isset($widget['id']) && is_string($widget['id'])) {
                        $validatedWidgets[] = [
                            'id' => $widget['id'],
                            'settings' => $widget['settings'] ?? [],
                        ];
                    }
                }
            }

            $page->update(['widgets' => $validatedWidgets]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Widgets saved successfully']);
            }

            return redirect()->route('admin.pages.builder', $page)->with('success', 'Widgets saved successfully!');
        } catch (\Exception $e) {
            \Log::error('Page builder save error: '.$e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Error saving widgets: '.$e->getMessage());
        }
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully');
    }
}
