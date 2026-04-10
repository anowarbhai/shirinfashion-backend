<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ThemeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function appearance()
    {
        $settings = ThemeSetting::getSettings();

        return view('admin.themes.appearance', compact('settings'));
    }

    public function appearanceUpdate(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'company_details' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_path' => 'nullable|string',
            'favicon_path' => 'nullable|string',
            'footer_logo_path' => 'nullable|string',
        ]);

        $settings = ThemeSetting::first();
        if (! $settings) {
            $settings = new ThemeSetting;
        }

        // Handle file upload or media library selection for logo
        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $settings->logo = $request->file('logo')->store('theme', 'public');
        } elseif ($request->filled('logo_path')) {
            // Use media library path
            $logoPath = $request->input('logo_path');
            // Remove 'storage/' prefix if it exists
            $logoPath = str_replace('storage/', '', $logoPath);
            $settings->logo = $logoPath;
        }

        // Handle file upload or media library selection for favicon
        if ($request->hasFile('favicon')) {
            if ($settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $settings->favicon = $request->file('favicon')->store('theme', 'public');
        } elseif ($request->filled('favicon_path')) {
            // Use media library path
            $faviconPath = $request->input('favicon_path');
            // Remove 'storage/' prefix if it exists
            $faviconPath = str_replace('storage/', '', $faviconPath);
            $settings->favicon = $faviconPath;
        }

        // Handle file upload or media library selection for footer_logo
        if ($request->hasFile('footer_logo')) {
            if ($settings->footer_logo) {
                Storage::disk('public')->delete($settings->footer_logo);
            }
            $settings->footer_logo = $request->file('footer_logo')->store('theme', 'public');
        } elseif ($request->filled('footer_logo_path')) {
            // Use media library path
            $footerLogoPath = $request->input('footer_logo_path');
            // Remove 'storage/' prefix if it exists
            $footerLogoPath = str_replace('storage/', '', $footerLogoPath);
            $settings->footer_logo = $footerLogoPath;
        }

        $settings->company_name = $validated['company_name'];
        $settings->tagline = $validated['tagline'] ?? null;
        $settings->company_details = $validated['company_details'] ?? null;
        $settings->email = $validated['email'] ?? null;
        $settings->phone = $validated['phone'] ?? null;
        $settings->address = $validated['address'] ?? null;
        $settings->facebook = $validated['facebook'] ?? null;
        $settings->instagram = $validated['instagram'] ?? null;
        $settings->twitter = $validated['twitter'] ?? null;
        $settings->youtube = $validated['youtube'] ?? null;
        $settings->linkedin = $validated['linkedin'] ?? null;
        $settings->tiktok = $validated['tiktok'] ?? null;
        $settings->save();

        // Clear theme cache so changes reflect immediately
        Cache::forget('theme_settings_api');

        return redirect()->route('admin.themes.appearance')->with('success', 'Appearance settings updated successfully!');
    }

    public function header()
    {
        $settings = ThemeSetting::getSettings();
        $headerStyles = ThemeSetting::getHeaderStyles();

        return view('admin.themes.header', compact('settings', 'headerStyles'));
    }

    public function headerUpdate(Request $request)
    {
        $validated = $request->validate([
            'header_style' => 'required|in:style1,style2,style3',
        ]);

        $settings = ThemeSetting::first();
        if (! $settings) {
            $settings = new ThemeSetting;
        }
        $settings->header_style = $validated['header_style'];
        $settings->save();

        // Clear theme cache so changes reflect immediately
        Cache::forget('theme_settings_api');

        return redirect()->route('admin.themes.header')->with('success', 'Header style updated successfully!');
    }

    public function footer()
    {
        $settings = ThemeSetting::getSettings();
        $footerStyles = ThemeSetting::getFooterStyles();

        return view('admin.themes.footer', compact('settings', 'footerStyles'));
    }

    public function footerUpdate(Request $request)
    {
        $validated = $request->validate([
            'footer_style' => 'required|in:style1,style2,style3',
        ]);

        $settings = ThemeSetting::first();
        if (! $settings) {
            $settings = new ThemeSetting;
        }
        $settings->footer_style = $validated['footer_style'];
        $settings->save();

        // Clear theme cache so changes reflect immediately
        Cache::forget('theme_settings_api');

        return redirect()->route('admin.themes.footer')->with('success', 'Footer style updated successfully!');
    }

    public function menu()
    {
        $settings = ThemeSetting::getSettings();
        $menus = ['main' => 'Main Menu', 'footer' => 'Footer Menu', 'top' => 'Top Bar Menu'];

        return view('admin.themes.menu', compact('settings', 'menus'));
    }

    public function menuUpdate(Request $request)
    {
        $validated = $request->validate([
            'primary_menu' => 'required|string',
        ]);

        $settings = ThemeSetting::first();
        if (! $settings) {
            $settings = new ThemeSetting;
        }
        $settings->primary_menu = $validated['primary_menu'];
        $settings->save();

        // Clear theme cache so changes reflect immediately
        Cache::forget('theme_settings_api');

        return redirect()->route('admin.themes.menu')->with('success', 'Menu settings updated successfully!');
    }
}
