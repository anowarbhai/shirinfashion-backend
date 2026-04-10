<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use Illuminate\Support\Facades\Cache;

class MenuController extends BaseController
{
    public function index()
    {
        $cacheKey = 'menu_items_api';

        $menus = Cache::remember($cacheKey, 3600, function () {
            $menus = Menu::with('allItems')->where('is_active', true)->get();

            return $menus->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'location' => $menu->location,
                    'items' => $this->buildMenuTree($menu->allItems),
                ];
            });
        });

        return $this->success($menus);
    }

    private function buildMenuTree($items, $parentId = null)
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $node = [
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => $item->url,
                    'target' => $item->target,
                    'icon' => $item->icon,
                    'order' => $item->order,
                ];

                $children = $items->where('parent_id', $item->id);
                if ($children->count() > 0) {
                    $node['children'] = $this->buildMenuTree($items, $item->id);
                }

                $tree[] = $node;
            }
        }

        return $tree;
    }
}
