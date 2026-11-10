<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsSettingController extends Controller
{
    public function index(): View
    {
        $settings = AsdreamsWebsiteSetting::pluck('value', 'key');

        return view('admin.asdreams.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_overlay_top_word' => 'nullable|string|max:50',
            'hero_overlay_bottom_word' => 'nullable|string|max:50',
            'hero_overlay_bottom_highlight' => 'nullable|string|max:50',
            'studio_rental_title' => 'nullable|string|max:100',
            'studio_rental_subtitle' => 'nullable|string|max:500',
            'studio_rental_price' => 'nullable|string|max:50',
            'studio_rental_per' => 'nullable|string|max:50',
            'instagram_url' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            AsdreamsWebsiteSetting::set($key, $value);
        }

        return redirect()->route('admin.asdreams.settings.index')
            ->with('success', 'ASDreamStudio settings updated successfully.');
    }
}
