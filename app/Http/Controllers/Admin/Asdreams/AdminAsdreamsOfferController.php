<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAsdreamsOfferController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsdreamsOffer::withCount(['features', 'enquiries'])->orderBy('order', 'asc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category_label', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $offers = $query->paginate(15)->withQueryString();

        return view('admin.asdreams.offers.index', compact('offers'));
    }

    public function create(): View
    {
        return view('admin.asdreams.offers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:asdreams_offers,slug',
            'category_label' => 'nullable|string|max:150',
            'heading_main' => 'required|string|max:150',
            'heading_highlight' => 'nullable|string|max:100',
            'lead_text' => 'nullable|string|max:1000',
            'full_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'price' => 'nullable|string|max:100',
            'validity' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'enquiry_placeholder' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'status' => 'required|in:published,draft',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:500',
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while (AsdreamsOffer::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $imagePath = 'images/asdreams/hero-01.jpg';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'offer_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/offers');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $imagePath = 'uploads/asdreams/offers/'.$filename;
        }

        $offer = AsdreamsOffer::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_label' => $validated['category_label'] ?? null,
            'heading_main' => $validated['heading_main'],
            'heading_highlight' => $validated['heading_highlight'] ?? null,
            'lead_text' => $validated['lead_text'] ?? null,
            'full_description' => $validated['full_description'] ?? null,
            'image' => $imagePath,
            'price' => $validated['price'] ?? null,
            'validity' => $validated['validity'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'important_notes' => $validated['important_notes'] ?? null,
            'enquiry_placeholder' => $validated['enquiry_placeholder'] ?? null,
            'order' => $validated['order'] ?? (AsdreamsOffer::max('order') + 1),
            'status' => $validated['status'],
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        if (! empty($validated['features'])) {
            foreach ($validated['features'] as $fIdx => $featureText) {
                if (trim($featureText)) {
                    $offer->features()->create([
                        'feature_text' => trim($featureText),
                        'order' => $fIdx + 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.asdreams.offers.index')
            ->with('success', 'Offer created successfully.');
    }

    public function edit(AsdreamsOffer $offer): View
    {
        $offer->load('features');

        return view('admin.asdreams.offers.edit', compact('offer'));
    }

    public function update(Request $request, AsdreamsOffer $offer): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:asdreams_offers,slug,'.$offer->id,
            'category_label' => 'nullable|string|max:150',
            'heading_main' => 'required|string|max:150',
            'heading_highlight' => 'nullable|string|max:100',
            'lead_text' => 'nullable|string|max:1000',
            'full_description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'price' => 'nullable|string|max:100',
            'validity' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'nullable|string',
            'important_notes' => 'nullable|string',
            'enquiry_placeholder' => 'nullable|string|max:500',
            'order' => 'nullable|integer',
            'status' => 'required|in:published,draft',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:500',
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : $offer->slug;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'offer_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/offers');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $offer->image = 'uploads/asdreams/offers/'.$filename;
        }

        $offer->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'category_label' => $validated['category_label'] ?? null,
            'heading_main' => $validated['heading_main'],
            'heading_highlight' => $validated['heading_highlight'] ?? null,
            'lead_text' => $validated['lead_text'] ?? null,
            'full_description' => $validated['full_description'] ?? null,
            'price' => $validated['price'] ?? null,
            'validity' => $validated['validity'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'important_notes' => $validated['important_notes'] ?? null,
            'enquiry_placeholder' => $validated['enquiry_placeholder'] ?? null,
            'order' => $validated['order'] ?? $offer->order,
            'status' => $validated['status'],
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
        ]);

        // Sync features
        $offer->features()->delete();
        if (! empty($validated['features'])) {
            foreach ($validated['features'] as $fIdx => $featureText) {
                if (trim($featureText)) {
                    $offer->features()->create([
                        'feature_text' => trim($featureText),
                        'order' => $fIdx + 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.asdreams.offers.index')
            ->with('success', 'Offer updated successfully.');
    }

    public function destroy(AsdreamsOffer $offer): RedirectResponse
    {
        $offer->delete();

        return redirect()->route('admin.asdreams.offers.index')
            ->with('success', 'Offer removed.');
    }

    public function toggleStatus(AsdreamsOffer $offer): RedirectResponse
    {
        $offer->status = $offer->status === 'published' ? 'draft' : 'published';
        $offer->save();

        return back()->with('success', 'Offer status updated.');
    }
}
