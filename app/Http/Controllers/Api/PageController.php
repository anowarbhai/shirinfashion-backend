<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'meta_title', 'meta_description']);
        
        return response()->json($pages);
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
        
        return response()->json([
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'content' => $page->content,
            'widgets' => $page->widgets,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'is_active' => $page->is_active,
            'sort_order' => $page->sort_order,
        ]);
    }
}
