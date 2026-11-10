<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsAboutContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminAsdreamsAboutController extends Controller
{
    public function index(): View
    {
        $about = AsdreamsAboutContent::current();

        return view('admin.asdreams.about.index', compact('about'));
    }

    public function update(Request $request): RedirectResponse
    {
        $about = AsdreamsAboutContent::current();

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video_cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video_url' => 'nullable|string|max:255',
            'years_of_experience' => 'required|integer|min:0|max:100',
            'how_we_work_title' => 'required|string|max:150',
            'how_we_work_text' => 'nullable|string|max:1000',
            'story_heading_main' => 'required|string|max:150',
            'story_heading_highlight' => 'nullable|string|max:150',
            'story_paragraph_1' => 'nullable|string|max:2000',
            'story_bullets_raw' => 'nullable|string|max:1000',
            'story_extended_text' => 'nullable|string|max:2000',
            'studio_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'studio_caption_title' => 'nullable|string|max:100',
            'studio_caption_subtitle' => 'nullable|string|max:100',
        ]);

        $destination = public_path('uploads/asdreams/about');
        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true, true);
        }

        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = 'about_banner_'.time().'.'.$file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $about->banner_image = 'uploads/asdreams/about/'.$filename;
        }

        if ($request->hasFile('video_cover_image')) {
            $file = $request->file('video_cover_image');
            $filename = 'about_video_'.time().'.'.$file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $about->video_cover_image = 'uploads/asdreams/about/'.$filename;
        }

        if ($request->hasFile('studio_image')) {
            $file = $request->file('studio_image');
            $filename = 'about_studio_'.time().'.'.$file->getClientOriginalExtension();
            $file->move($destination, $filename);
            $about->studio_image = 'uploads/asdreams/about/'.$filename;
        }

        $bullets = $about->story_bullets ?? [];
        if (isset($validated['story_bullets_raw'])) {
            $bullets = array_values(array_filter(array_map('trim', explode("\n", $validated['story_bullets_raw']))));
        }

        $about->update([
            'title' => $validated['title'],
            'video_url' => $validated['video_url'] ?? null,
            'years_of_experience' => $validated['years_of_experience'],
            'how_we_work_title' => $validated['how_we_work_title'],
            'how_we_work_text' => $validated['how_we_work_text'] ?? null,
            'story_heading_main' => $validated['story_heading_main'],
            'story_heading_highlight' => $validated['story_heading_highlight'] ?? null,
            'story_paragraph_1' => $validated['story_paragraph_1'] ?? null,
            'story_bullets' => $bullets,
            'story_extended_text' => $validated['story_extended_text'] ?? null,
            'studio_caption_title' => $validated['studio_caption_title'] ?? 'Studio 01',
            'studio_caption_subtitle' => $validated['studio_caption_subtitle'] ?? 'Kozhikode, Kerala',
        ]);

        return redirect()->route('admin.asdreams.about.index')
            ->with('success', 'About page content updated successfully.');
    }
}
