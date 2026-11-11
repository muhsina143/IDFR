<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Admin\Asdreams\Concerns\HandlesAsdreamsImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsStudioGalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAsdreamsStudioGalleryController extends Controller
{
    use HandlesAsdreamsImageUploads;

    /**
     * Display the Studio Gallery image management grid.
     */
    public function index(Request $request): View
    {
        $query = AsdreamsStudioGalleryImage::ordered();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        $images = $query->paginate(24)->withQueryString();

        return view('admin.asdreams.studio-rental.gallery.index', compact('images'));
    }

    /**
     * Show the multi-image upload form with pre-save previews.
     */
    public function create(): View
    {
        $nextOrder = ((int) AsdreamsStudioGalleryImage::max('display_order')) + 1;

        return view('admin.asdreams.studio-rental.gallery.create', compact('nextOrder'));
    }

    /**
     * Store one or multiple uploaded Studio Gallery images.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // Normalize single 'image' field if posted instead of 'images[]'
        if ($request->hasFile('image') && ! $request->hasFile('images')) {
            $request->files->set('images', [$request->file('image')]);
        }

        $validated = $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:8192',
            'titles' => 'nullable|array',
            'titles.*' => 'nullable|string|max:150',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'nullable|string|max:500',
            'orders' => 'nullable|array',
            'orders.*' => 'nullable|integer|min:0',
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $uploadedFiles = $request->file('images', []);
        $baseOrder = (int) ($validated['display_order'] ?? (((int) AsdreamsStudioGalleryImage::max('display_order')) + 1));
        $isActive = $request->boolean('is_active', true);
        $count = 0;

        foreach ($uploadedFiles as $idx => $file) {
            $imagePath = $this->uploadAsdreamsFile($file, 'studio-gallery', 'studio_shot');

            $rawTitle = $validated['titles'][$idx]
                ?? (count($uploadedFiles) === 1 ? ($validated['title'] ?? null) : null);
            if (empty($rawTitle)) {
                $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $rawTitle = Str::headline(preg_replace('/[_-]+/', ' ', $baseName));
            }

            $description = $validated['descriptions'][$idx]
                ?? (count($uploadedFiles) === 1 ? ($validated['description'] ?? null) : null);

            $order = isset($validated['orders'][$idx])
                ? (int) $validated['orders'][$idx]
                : ($baseOrder + $idx);

            AsdreamsStudioGalleryImage::create([
                'title' => $rawTitle,
                'description' => $description,
                'image_path' => $imagePath,
                'display_order' => $order,
                'is_active' => $isActive,
            ]);
            $count++;
        }

        $label = $count === 1 ? 'Studio gallery image uploaded successfully.' : "{$count} studio gallery images uploaded successfully.";

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'gallery',
            $label,
            route('admin.asdreams.studio-gallery.index')
        );
    }

    /**
     * Show the form for editing an existing studio gallery image.
     */
    public function edit(AsdreamsStudioGalleryImage $studioGallery): View
    {
        return view('admin.asdreams.studio-rental.gallery.edit', compact('studioGallery'));
    }

    /**
     * Update the specified studio gallery image details and/or replace the image file.
     */
    public function update(Request $request, AsdreamsStudioGalleryImage $studioGallery): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $studioGallery->image_path = $this->uploadAsdreamsFile(
                $request->file('image'),
                'studio-gallery',
                'studio_shot',
                $studioGallery->image_path
            );
        }

        $studioGallery->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image_path' => $studioGallery->image_path,
            'display_order' => $validated['display_order'] ?? $studioGallery->display_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'gallery',
            'Studio gallery image updated successfully.',
            route('admin.asdreams.studio-gallery.index')
        );
    }

    /**
     * Remove the specified studio gallery image from storage.
     */
    public function destroy(Request $request, AsdreamsStudioGalleryImage $studioGallery): JsonResponse|RedirectResponse
    {
        $this->deleteAsdreamsUploadedFile($studioGallery->image_path);
        $studioGallery->delete();

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'gallery',
            'Studio gallery image deleted successfully.',
            route('admin.asdreams.studio-gallery.index')
        );
    }

    /**
     * Toggle active/inactive visibility for the specified studio gallery image.
     */
    public function toggleStatus(Request $request, AsdreamsStudioGalleryImage $studioGallery): JsonResponse|RedirectResponse
    {
        $studioGallery->is_active = ! $studioGallery->is_active;
        $studioGallery->save();

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'gallery',
            'Studio gallery image status updated.'
        );
    }
}
