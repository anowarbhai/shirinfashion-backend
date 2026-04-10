<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('file_name', 'like', "%{$request->search}%");
        }

        if ($request->month) {
            $query->whereYear('created_at', substr($request->month, 0, 4))
                ->whereMonth('created_at', substr($request->month, 5, 2));
        }

        $perPage = $request->per_page ?? 20;
        $media = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // If requested as JSON (for modal)
        if ($request->expectsJson() || $request->has('per_page')) {
            // Transform data to include full URLs
            $media->getCollection()->transform(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'file_name' => $item->file_name,
                    'file_path' => $item->file_path,
                    'url' => $item->url,
                    'alt_text' => $item->alt_text,
                    'created_at' => $item->created_at,
                ];
            });

            return response()->json($media);
        }

        $months = Media::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'label' => date('F Y', strtotime($item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT).'-01')),
                    'count' => $item->count,
                ];
            });

        return view('admin.media.index', compact('media', 'months'));
    }

    // Debug endpoint to check media data
    public function debug()
    {
        $media = Media::orderBy('created_at', 'desc')->limit(5)->get();

        return response()->json([
            'count' => Media::count(),
            'media' => $media->map(function ($m) {
                return [
                    'id' => $m->id,
                    'name' => $m->name,
                    'file_path' => $m->file_path,
                    'url' => $m->url,
                ];
            }),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $name = pathinfo($originalName, PATHINFO_FILENAME);

        $fileName = time().'_'.preg_replace('/[^a-zA-Z0-9]/', '_', $name).'.'.$file->getClientOriginalExtension();
        $path = 'uploads/'.date('Y/m');

        $file->storeAs($path, $fileName, 'public');
        $filePath = '/storage/'.$path.'/'.$fileName;

        $media = Media::create([
            'name' => $name,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'type' => 'image',
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'media' => $media,
            'url' => $media->url,
        ]);
    }

    public function uploadMultiple(Request $request)
    {
        $request->validate([
            'files.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);

        $uploaded = [];
        $files = $request->file('files');

        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $name = pathinfo($originalName, PATHINFO_FILENAME);

            $fileName = time().'_'.uniqid().'_'.preg_replace('/[^a-zA-Z0-9]/', '_', $name).'.'.$file->getClientOriginalExtension();
            $path = 'uploads/'.date('Y/m');

            $file->storeAs($path, $fileName, 'public');
            $filePath = '/storage/'.$path.'/'.$fileName;

            $media = Media::create([
                'name' => $name,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'type' => 'image',
                'uploaded_by' => auth()->id(),
            ]);

            $uploaded[] = $media;
        }

        return response()->json([
            'success' => true,
            'media' => $uploaded,
            'urls' => array_map(fn ($m) => $m->url, $uploaded),
        ]);
    }

    public function destroy(Media $media)
    {
        if (Storage::disk('public')->exists(str_replace('storage/', '', $media->file_path))) {
            Storage::disk('public')->delete(str_replace('storage/', '', $media->file_path));
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully',
        ]);
    }

    public function update(Request $request, Media $media)
    {
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
        ]);

        $media->update($validated);

        return response()->json([
            'success' => true,
            'media' => $media,
        ]);
    }
}
