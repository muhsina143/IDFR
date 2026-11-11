<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Http\JsonResponse;
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

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'hero_overlay_top_word' => 'nullable|string|max:50',
            'hero_overlay_bottom_word' => 'nullable|string|max:50',
            'hero_overlay_bottom_highlight' => 'nullable|string|max:50',
            'instagram_url' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|string|max:255',
        ]);

        foreach ($validated as $key => $value) {
            AsdreamsWebsiteSetting::set($key, $value);
        }

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'settings',
            'ASDreamStudio settings updated successfully.',
            route('admin.asdreams.settings.index')
        );
    }
}
