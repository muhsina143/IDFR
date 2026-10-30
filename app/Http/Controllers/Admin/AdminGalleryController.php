<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\GalleryImage;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminGalleryController extends Controller
{
    /**
     * Display a listing of gallery images with season filter, search, and pagination.
     */
    public function index(Request $request): View
    {
        $query = GalleryImage::with('season');

        // Search by title or alt text
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        // Filter by Season ID
        if ($seasonId = $request->input('season_id')) {
            $query->where('season_id', $seasonId);
        }

        // Filter by Season Name
        if ($seasonName = $request->input('season')) {
            if ($seasonName !== 'all') {
                $query->where('season_name', $seasonName);
            }
        }

        // Filter by Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sorting
        $sort = $request->input('sort', 'order');
        if ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'featured') {
            $query->orderByDesc('is_featured')->orderBy('display_order');
        } else {
            $query->orderBy('display_order')->orderBy('id');
        }

        $images = $query->paginate(16)->withQueryString();
        $seasons = Season::orderBy('number', 'asc')->get();
        $totalCount = GalleryImage::count();
        $publishedCount = GalleryImage::where('status', 'published')->count();

        return view('admin.gallery.index', compact('images', 'seasons', 'totalCount', 'publishedCount'));
    }

    /**
     * Show the form for creating a new gallery photo.
     */
    public function create(): View
    {
        $seasons = Season::orderBy('number', 'asc')->get();

        return view('admin.gallery.create', compact('seasons'));
    }

    /**
     * Store a newly created gallery photo in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'season_name' => ['nullable', 'string', 'max:50'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'width' => ['nullable', 'integer'],
            'height' => ['nullable', 'integer'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $seasonName = $validated['season_name'] ?? 'Season 1';
        if (! empty($validated['season_id'])) {
            $season = Season::find($validated['season_id']);
            if ($season) {
                $seasonName = $season->title;
            }
        }

        $file = $request->file('image');
        $filename = 'gal_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/gallery'), $filename);
        $imagePath = 'uploads/gallery/'.$filename;

        // Auto-detect dimensions if not provided
        $fullPath = public_path($imagePath);
        $width = $validated['width'] ?? 1000;
        $height = $validated['height'] ?? 1500;
        if (file_exists($fullPath) && (empty($validated['width']) || empty($validated['height']))) {
            $size = @getimagesize($fullPath);
            if ($size) {
                $width = $size[0];
                $height = $size[1];
            }
        }

        $gallery = GalleryImage::create([
            'title' => $validated['title'],
            'alt_text' => $validated['alt_text'] ?? $validated['title'],
            'season_id' => $validated['season_id'] ?? null,
            'season_name' => $seasonName,
            'image_path' => $imagePath,
            'width' => $width,
            'height' => $height,
            'display_order' => $validated['display_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
        ]);

        AdminActivity::log(
            'gallery_created',
            "Uploaded runway image: '{$gallery->title}' for {$gallery->season_name}",
            ['image_id' => $gallery->id]
        );

        return redirect()->route('admin.gallery')
            ->with('success', "Runway image '{$gallery->title}' has been successfully uploaded and added to the gallery.");
    }

    /**
     * Show the form for editing an existing gallery photo.
     */
    public function edit(GalleryImage $gallery): View
    {
        $seasons = Season::orderBy('number', 'asc')->get();

        return view('admin.gallery.edit', compact('gallery', 'seasons'));
    }

    /**
     * Update the specified gallery photo in the database.
     */
    public function update(Request $request, GalleryImage $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'season_id' => ['nullable', 'exists:seasons,id'],
            'season_name' => ['nullable', 'string', 'max:50'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'is_featured' => ['nullable', 'boolean'],
            'width' => ['nullable', 'integer'],
            'height' => ['nullable', 'integer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        $seasonName = $validated['season_name'] ?? $gallery->season_name;
        if (! empty($validated['season_id'])) {
            $season = Season::find($validated['season_id']);
            if ($season) {
                $seasonName = $season->title;
            }
        }

        $imagePath = $gallery->image_path;
        $width = $validated['width'] ?? $gallery->width;
        $height = $validated['height'] ?? $gallery->height;

        if ($request->hasFile('image')) {
            // Delete old file if it was uploaded to uploads/gallery
            if (str_starts_with($gallery->image_path, 'uploads/gallery/')) {
                $oldFile = public_path($gallery->image_path);
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            $file = $request->file('image');
            $filename = 'gal_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/gallery'), $filename);
            $imagePath = 'uploads/gallery/'.$filename;

            $fullPath = public_path($imagePath);
            if (file_exists($fullPath)) {
                $size = @getimagesize($fullPath);
                if ($size) {
                    $width = $size[0];
                    $height = $size[1];
                }
            }
        }

        $gallery->update([
            'title' => $validated['title'],
            'alt_text' => $validated['alt_text'] ?? $gallery->alt_text,
            'season_id' => $validated['season_id'] ?? null,
            'season_name' => $seasonName,
            'image_path' => $imagePath,
            'width' => $width,
            'height' => $height,
            'display_order' => $validated['display_order'] ?? 0,
            'is_featured' => $request->boolean('is_featured'),
            'status' => $validated['status'],
        ]);

        AdminActivity::log(
            'gallery_updated',
            "Updated runway image: '{$gallery->title}'",
            ['image_id' => $gallery->id]
        );

        return redirect()->route('admin.gallery')
            ->with('success', "Runway image '{$gallery->title}' was successfully updated.");
    }

    /**
     * Remove the specified gallery photo from the database.
     */
    public function destroy(GalleryImage $gallery): RedirectResponse
    {
        $title = $gallery->title;

        // Delete file if uploaded
        if (str_starts_with($gallery->image_path, 'uploads/gallery/')) {
            $filePath = public_path($gallery->image_path);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $gallery->delete();

        AdminActivity::log(
            'gallery_deleted',
            "Deleted runway image: '{$title}'",
            ['image_id' => $gallery->id]
        );

        return redirect()->route('admin.gallery')
            ->with('success', "Runway image '{$title}' has been deleted from the gallery.");
    }
}
