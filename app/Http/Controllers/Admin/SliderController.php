<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = Slider::orderBy('order', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.sliders.index', compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'button_color' => 'nullable|in:rose,blue,green,purple,orange,dark,white,outline',
            'button_2_text' => 'nullable|string|max:100',
            'button_2_link' => 'nullable|string|max:500',
            'button_2_color' => 'nullable|in:rose,blue,green,purple,orange,dark,white,outline',
            'text_align' => 'nullable|in:left,center,right',
            'content_position' => 'nullable|in:top,center,bottom',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
            $validated['image'] = $path;
        }

        // Set default values
        $validated['button_color'] = $validated['button_color'] ?? 'rose';
        $validated['button_2_color'] = $validated['button_2_color'] ?? 'outline';
        $validated['text_align'] = $validated['text_align'] ?? 'left';
        $validated['content_position'] = $validated['content_position'] ?? 'center';
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        Slider::create($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
            'button_color' => 'nullable|in:rose,blue,green,purple,orange,dark,white,outline',
            'button_2_text' => 'nullable|string|max:100',
            'button_2_link' => 'nullable|string|max:500',
            'button_2_color' => 'nullable|in:rose,blue,green,purple,orange,dark,white,outline',
            'text_align' => 'nullable|in:left,center,right',
            'content_position' => 'nullable|in:top,center,bottom',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $path = $request->file('image')->store('sliders', 'public');
            $validated['image'] = $path;
        }

        // Set default values
        $validated['button_color'] = $validated['button_color'] ?? 'rose';
        $validated['button_2_color'] = $validated['button_2_color'] ?? 'outline';
        $validated['text_align'] = $validated['text_align'] ?? 'left';
        $validated['content_position'] = $validated['content_position'] ?? 'center';
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active');

        $slider->update($validated);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider)
    {
        // Delete image
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider deleted successfully!');
    }

    /**
     * Toggle slider active status
     */
    public function toggleStatus(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Slider status updated successfully!');
    }
}
