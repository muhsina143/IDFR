<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsBrand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminAsdreamsBrandController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsdreamsBrand::orderBy('order', 'asc');

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tagline', 'like', "%{$search}%");
            });
        }

        $brands = $query->paginate(15)->withQueryString();

        return view('admin.asdreams.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.asdreams.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'tagline' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link_url' => 'nullable|string|max:255',
            'tags_raw' => 'nullable|string|max:500',
            'css_variant' => 'nullable|string|in:standard,wide,tall,lakme',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $logoPath = 'images/asdreams/asd-logo.png';
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'brand_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/brands');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $logoPath = 'uploads/asdreams/brands/'.$filename;
        }

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'brand_cover_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/brands');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $coverImagePath = 'uploads/asdreams/brands/'.$filename;
        }

        $tags = [];
        if (! empty($validated['tags_raw'])) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $validated['tags_raw']))));
        }

        AsdreamsBrand::create([
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'logo_path' => $logoPath,
            'cover_image' => $coverImagePath,
            'link_url' => $validated['link_url'] ?? null,
            'tags' => $tags,
            'css_variant' => $validated['css_variant'] ?? 'standard',
            'order' => $validated['order'] ?? (AsdreamsBrand::max('order') + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.asdreams.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(AsdreamsBrand $brand): View
    {
        return view('admin.asdreams.brands.edit', compact('brand'));
    }

    public function update(Request $request, AsdreamsBrand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'tagline' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link_url' => 'nullable|string|max:255',
            'tags_raw' => 'nullable|string|max:500',
            'css_variant' => 'nullable|string|in:standard,wide,tall,lakme',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'brand_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/brands');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $brand->logo_path = 'uploads/asdreams/brands/'.$filename;
        }

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'brand_cover_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/brands');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $brand->cover_image = 'uploads/asdreams/brands/'.$filename;
        }

        $tags = $brand->tags ?? [];
        if (isset($validated['tags_raw'])) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $validated['tags_raw']))));
        }

        $brand->update([
            'name' => $validated['name'],
            'tagline' => $validated['tagline'] ?? null,
            'description' => $validated['description'] ?? null,
            'link_url' => $validated['link_url'] ?? null,
            'tags' => $tags,
            'css_variant' => $validated['css_variant'] ?? 'standard',
            'order' => $validated['order'] ?? $brand->order,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.asdreams.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(AsdreamsBrand $brand): RedirectResponse
    {
        $brand->delete();

        return redirect()->route('admin.asdreams.brands.index')
            ->with('success', 'Brand removed.');
    }

    public function toggleStatus(AsdreamsBrand $brand): RedirectResponse
    {
        $brand->is_active = ! $brand->is_active;
        $brand->save();

        return back()->with('success', 'Brand status updated.');
    }
}
