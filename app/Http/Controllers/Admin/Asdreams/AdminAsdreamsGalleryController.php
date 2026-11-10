<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsGalleryCategory;
use App\Models\Asdreams\AsdreamsGalleryImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAsdreamsGalleryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = AsdreamsGalleryCategory::orderBy('order', 'asc')->get();

        $query = AsdreamsGalleryImage::with('category')->orderBy('order', 'asc');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $images = $query->paginate(20)->withQueryString();

        return view('admin.asdreams.gallery.index', compact('images', 'categories'));
    }

    public function createImage(): View
    {
        $categories = AsdreamsGalleryCategory::orderBy('order', 'asc')->get();

        return view('admin.asdreams.gallery.create-image', compact('categories'));
    }

    public function storeImage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:asdreams_gallery_categories,id',
            'title' => 'required|string|max:150',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:8192',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        $imagePath = 'images/asdreams/gallery/wedding-01.jpg';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'gallery_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/gallery');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $imagePath = 'uploads/asdreams/gallery/'.$filename;
        }

        AsdreamsGalleryImage::create([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'image_path' => $imagePath,
            'width' => $validated['width'] ?? 1000,
            'height' => $validated['height'] ?? 1500,
            'order' => $validated['order'] ?? (AsdreamsGalleryImage::max('order') + 1),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.asdreams.gallery.index')
            ->with('success', 'Gallery image uploaded successfully.');
    }

    public function editImage(AsdreamsGalleryImage $image): View
    {
        $categories = AsdreamsGalleryCategory::orderBy('order', 'asc')->get();

        return view('admin.asdreams.gallery.edit-image', compact('image', 'categories'));
    }

    public function updateImage(Request $request, AsdreamsGalleryImage $image): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:asdreams_gallery_categories,id',
            'title' => 'required|string|max:150',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:8192',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'gallery_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/gallery');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $image->image_path = 'uploads/asdreams/gallery/'.$filename;
        }

        $image->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'width' => $validated['width'] ?? $image->width,
            'height' => $validated['height'] ?? $image->height,
            'order' => $validated['order'] ?? $image->order,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.asdreams.gallery.index')
            ->with('success', 'Gallery image updated successfully.');
    }

    public function destroyImage(AsdreamsGalleryImage $image): RedirectResponse
    {
        $image->delete();

        return redirect()->route('admin.asdreams.gallery.index')
            ->with('success', 'Gallery image deleted.');
    }

    public function toggleImageStatus(AsdreamsGalleryImage $image): RedirectResponse
    {
        $image->is_published = ! $image->is_published;
        $image->save();

        return back()->with('success', 'Gallery image visibility updated.');
    }

    // Categories Management
    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:asdreams_gallery_categories,slug',
            'tab_label' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
        ]);

        $slug = ! empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        AsdreamsGalleryCategory::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'tab_label' => $validated['tab_label'] ?? $validated['name'],
            'order' => $validated['order'] ?? (AsdreamsGalleryCategory::max('order') + 1),
            'is_active' => true,
        ]);

        return back()->with('success', 'Gallery category created.');
    }

    public function destroyCategory(AsdreamsGalleryCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Gallery category and its images removed.');
    }
}
