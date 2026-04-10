<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('name')->paginate(20);
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'slug' => 'required|string|max:255|unique:attributes,slug',
            'values_text' => 'nullable|string',
        ]);

        $attribute = Attribute::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        if (!empty($validated['values_text'])) {
            $values = explode("\n", $validated['values_text']);
            foreach ($values as $value) {
                $value = trim($value);
                if ($value) {
                    $attribute->values()->create(['value' => $value]);
                }
            }
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully');
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'slug' => 'required|string|max:255|unique:attributes,slug,' . $attribute->id,
            'values_text' => 'nullable|string',
        ]);

        $attribute->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
        ]);

        if (isset($validated['values_text'])) {
            $attribute->values()->delete();
            $values = explode("\n", $validated['values_text']);
            foreach ($values as $value) {
                $value = trim($value);
                if ($value) {
                    $attribute->values()->create(['value' => $value]);
                }
            }
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully');
    }
}
