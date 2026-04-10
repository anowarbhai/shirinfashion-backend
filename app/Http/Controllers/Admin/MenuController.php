<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function menuManage()
    {
        $menus = Menu::with('items')->get();
        $menuLocations = [
            'footer1' => 'Footer - Column 1',
            'footer2' => 'Footer - Column 2',
            'footer3' => 'Footer - Column 3',
            'footer4' => 'Footer - Column 4',
            'header' => 'Header Navigation',
            'mobile' => 'Mobile Menu',
        ];

        return view('admin.themes.menu-manage', compact('menus', 'menuLocations'));
    }

    public function menuSave(Request $request)
    {
        $data = $request->input('menus', []);

        foreach ($data as $menuData) {
            $menu = Menu::updateOrCreate(
                ['slug' => $menuData['slug']],
                [
                    'name' => $menuData['name'],
                    'location' => $menuData['location'],
                    'description' => $menuData['description'] ?? null,
                    'is_active' => true,
                ]
            );

            // Delete existing items
            MenuItem::where('menu_id', $menu->id)->delete();

            // Create new items
            if (! empty($menuData['items'])) {
                foreach ($menuData['items'] as $index => $itemData) {
                    $this->createMenuItem($menu->id, $itemData, $index);
                }
            }
        }

        Cache::forget('menu_items_api');

        return response()->json(['success' => true, 'message' => 'Menu saved successfully!']);
    }

    private function createMenuItem($menuId, $data, $order = 0, $parentId = null)
    {
        $item = MenuItem::create([
            'menu_id' => $menuId,
            'parent_id' => $parentId,
            'title' => $data['title'],
            'url' => $data['url'],
            'target' => $data['target'] ?? '_self',
            'icon' => $data['icon'] ?? null,
            'order' => $order,
            'is_active' => true,
        ]);

        if (! empty($data['children'])) {
            foreach ($data['children'] as $index => $childData) {
                $this->createMenuItem($menuId, $childData, $index, $item->id);
            }
        }

        return $item;
    }

    public function menuApi()
    {
        $menus = Menu::with('allItems')->where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $menus->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'slug' => $menu->slug,
                    'location' => $menu->location,
                    'items' => $this->buildMenuTree($menu->allItems),
                ];
            }),
        ]);
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
