<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsBanner;
use App\Models\Asdreams\AsdreamsOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAsdreamsBannerController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsdreamsBanner::with(['linkedOffer.features', 'offer.features'])->orderBy('order', 'asc');

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('highlight_title', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%");
            });
        }

        $banners = $query->paginate(15)->withQueryString();

        return view('admin.asdreams.banners.index', compact('banners'));
    }

    public function create(): View
    {
        $offers = AsdreamsOffer::published()->orderBy('order', 'asc')->get();

        return view('admin.asdreams.banners.create', compact('offers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Banner Fields
            'title' => 'required|string|max:150',
            'highlight_title' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'alt_text' => 'nullable|string|max:150',
            'button_text' => 'nullable|string|max:50',
            'linked_offer_id' => 'nullable|exists:asdreams_offers,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',

            // Offer Details Fields (Section 2)
            'offer_title' => 'nullable|string|max:150',
            'offer_category_label' => 'nullable|string|max:150',
            'offer_lead_text' => 'nullable|string|max:1000',
            'offer_full_description' => 'nullable|string',
            'offer_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_price' => 'nullable|string|max:100',
            'offer_validity' => 'nullable|string|max:150',
            'offer_notes' => 'nullable|string|max:1000',
            'offer_terms' => 'nullable|string',
            'offer_important_notes' => 'nullable|string',
            'offer_features' => 'nullable|array',
            'offer_features.*' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // Upload Banner Image
            $imagePath = 'images/asdreams/hero-01.jpg';
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'banner_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $destination = public_path('uploads/asdreams/banners');
                if (! File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $imagePath = 'uploads/asdreams/banners/'.$filename;
            }

            // Create Banner first
            $banner = AsdreamsBanner::create([
                'title' => $validated['title'],
                'highlight_title' => $validated['highlight_title'] ?? null,
                'label' => $validated['label'] ?? null,
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'alt_text' => $validated['alt_text'] ?? null,
                'button_text' => $validated['button_text'] ?? 'Offer Details',
                'order' => $validated['order'] ?? (AsdreamsBanner::max('order') + 1),
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Determine / Create Linked Offer
            $offer = null;
            if (! empty($validated['linked_offer_id'])) {
                $offer = AsdreamsOffer::find($validated['linked_offer_id']);
            }

            $offerTitle = ! empty($validated['offer_title']) ? $validated['offer_title'] : $validated['title'];
            $offerCategory = ! empty($validated['offer_category_label']) ? $validated['offer_category_label'] : ($validated['label'] ?? null);
            $offerLead = ! empty($validated['offer_lead_text']) ? $validated['offer_lead_text'] : ($validated['description'] ?? null);

            // Upload Offer Image if provided
            $offerImagePath = $imagePath;
            if ($request->hasFile('offer_image')) {
                $oFile = $request->file('offer_image');
                $oFilename = 'offer_'.time().'_'.uniqid().'.'.$oFile->getClientOriginalExtension();
                $oDest = public_path('uploads/asdreams/offers');
                if (! File::isDirectory($oDest)) {
                    File::makeDirectory($oDest, 0755, true, true);
                }
                $oFile->move($oDest, $oFilename);
                $offerImagePath = 'uploads/asdreams/offers/'.$oFilename;
            }

            if (! $offer) {
                // Generate a unique slug
                $slug = Str::slug($offerTitle);
                $baseSlug = $slug ?: 'offer';
                $counter = 1;
                while (AsdreamsOffer::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $offer = AsdreamsOffer::create([
                    'banner_id' => $banner->id,
                    'title' => $offerTitle,
                    'slug' => $slug,
                    'category_label' => $offerCategory,
                    'heading_main' => $validated['title'],
                    'heading_highlight' => $validated['highlight_title'] ?? null,
                    'lead_text' => $offerLead,
                    'full_description' => $validated['offer_full_description'] ?? null,
                    'image' => $offerImagePath,
                    'price' => $validated['offer_price'] ?? null,
                    'validity' => $validated['offer_validity'] ?? null,
                    'notes' => $validated['offer_notes'] ?? null,
                    'terms' => $validated['offer_terms'] ?? null,
                    'important_notes' => $validated['offer_important_notes'] ?? null,
                    'order' => $validated['order'] ?? $banner->order,
                    'status' => 'published',
                ]);
            } else {
                $offerUpdate = [
                    'banner_id' => $banner->id,
                ];
                if (! empty($validated['offer_title'])) {
                    $offerUpdate['title'] = $validated['offer_title'];
                }
                if (isset($validated['offer_category_label'])) {
                    $offerUpdate['category_label'] = $validated['offer_category_label'];
                }
                if (isset($validated['offer_lead_text'])) {
                    $offerUpdate['lead_text'] = $validated['offer_lead_text'];
                }
                if (isset($validated['offer_full_description'])) {
                    $offerUpdate['full_description'] = $validated['offer_full_description'];
                }
                if ($request->hasFile('offer_image')) {
                    $offerUpdate['image'] = $offerImagePath;
                }
                if (isset($validated['offer_price'])) {
                    $offerUpdate['price'] = $validated['offer_price'];
                }
                if (isset($validated['offer_validity'])) {
                    $offerUpdate['validity'] = $validated['offer_validity'];
                }
                if (isset($validated['offer_notes'])) {
                    $offerUpdate['notes'] = $validated['offer_notes'];
                }
                if (isset($validated['offer_terms'])) {
                    $offerUpdate['terms'] = $validated['offer_terms'];
                }
                if (isset($validated['offer_important_notes'])) {
                    $offerUpdate['important_notes'] = $validated['offer_important_notes'];
                }

                $offer->update($offerUpdate);
            }

            // Sync features
            if (! empty($validated['offer_features'])) {
                $offer->features()->delete();
                foreach ($validated['offer_features'] as $fIdx => $featText) {
                    if (trim($featText) !== '') {
                        $offer->features()->create([
                            'feature_text' => trim($featText),
                            'order' => $fIdx + 1,
                        ]);
                    }
                }
            }

            // Update banner with linked offer details
            $banner->update([
                'linked_offer_id' => $offer->id,
                'linked_offer_slug' => $offer->slug,
            ]);

            return redirect()->route('admin.asdreams.banners.index')
                ->with('success', 'Banner slide and its Offer Details created successfully.');
        });
    }

    public function edit(AsdreamsBanner $banner): View
    {
        $banner->load(['linkedOffer.features', 'offer.features']);
        $offers = AsdreamsOffer::published()->orderBy('order', 'asc')->get();

        return view('admin.asdreams.banners.edit', compact('banner', 'offers'));
    }

    public function update(Request $request, AsdreamsBanner $banner): RedirectResponse
    {
        $validated = $request->validate([
            // Banner Fields
            'title' => 'required|string|max:150',
            'highlight_title' => 'nullable|string|max:100',
            'label' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'alt_text' => 'nullable|string|max:150',
            'button_text' => 'nullable|string|max:50',
            'linked_offer_id' => 'nullable|exists:asdreams_offers,id',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',

            // Offer Details Fields (Section 2)
            'offer_title' => 'nullable|string|max:150',
            'offer_category_label' => 'nullable|string|max:150',
            'offer_lead_text' => 'nullable|string|max:1000',
            'offer_full_description' => 'nullable|string',
            'offer_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'offer_price' => 'nullable|string|max:100',
            'offer_validity' => 'nullable|string|max:150',
            'offer_notes' => 'nullable|string|max:1000',
            'offer_terms' => 'nullable|string',
            'offer_important_notes' => 'nullable|string',
            'offer_features' => 'nullable|array',
            'offer_features.*' => 'nullable|string|max:500',
        ]);

        return DB::transaction(function () use ($request, $validated, $banner) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = 'banner_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $destination = public_path('uploads/asdreams/banners');
                if (! File::isDirectory($destination)) {
                    File::makeDirectory($destination, 0755, true, true);
                }
                $file->move($destination, $filename);
                $banner->image = 'uploads/asdreams/banners/'.$filename;
            }

            // Find current or selected offer
            $offer = null;
            if (! empty($validated['linked_offer_id'])) {
                $offer = AsdreamsOffer::find($validated['linked_offer_id']);
            }
            if (! $offer) {
                $offer = $banner->linkedOffer ?? $banner->offer;
            }

            $offerTitle = ! empty($validated['offer_title']) ? $validated['offer_title'] : $validated['title'];
            $offerCategory = ! empty($validated['offer_category_label']) ? $validated['offer_category_label'] : ($validated['label'] ?? null);
            $offerLead = ! empty($validated['offer_lead_text']) ? $validated['offer_lead_text'] : ($validated['description'] ?? null);

            // Upload Offer Image if provided
            $offerImagePath = null;
            if ($request->hasFile('offer_image')) {
                $oFile = $request->file('offer_image');
                $oFilename = 'offer_'.time().'_'.uniqid().'.'.$oFile->getClientOriginalExtension();
                $oDest = public_path('uploads/asdreams/offers');
                if (! File::isDirectory($oDest)) {
                    File::makeDirectory($oDest, 0755, true, true);
                }
                $oFile->move($oDest, $oFilename);
                $offerImagePath = 'uploads/asdreams/offers/'.$oFilename;
            }

            if (! $offer) {
                // Auto create offer
                $slug = Str::slug($offerTitle);
                $baseSlug = $slug ?: 'offer';
                $counter = 1;
                while (AsdreamsOffer::where('slug', $slug)->exists()) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $offer = AsdreamsOffer::create([
                    'banner_id' => $banner->id,
                    'title' => $offerTitle,
                    'slug' => $slug,
                    'category_label' => $offerCategory,
                    'heading_main' => $validated['title'],
                    'heading_highlight' => $validated['highlight_title'] ?? null,
                    'lead_text' => $offerLead,
                    'full_description' => $validated['offer_full_description'] ?? null,
                    'image' => $offerImagePath ?: $banner->image,
                    'price' => $validated['offer_price'] ?? null,
                    'validity' => $validated['offer_validity'] ?? null,
                    'notes' => $validated['offer_notes'] ?? null,
                    'terms' => $validated['offer_terms'] ?? null,
                    'important_notes' => $validated['offer_important_notes'] ?? null,
                    'order' => $validated['order'] ?? $banner->order,
                    'status' => 'published',
                ]);
            } else {
                $offerUpdate = [
                    'banner_id' => $banner->id,
                    'title' => $offerTitle,
                    'category_label' => $offerCategory,
                    'heading_main' => $validated['title'],
                    'heading_highlight' => $validated['highlight_title'] ?? null,
                    'lead_text' => $offerLead,
                    'full_description' => $validated['offer_full_description'] ?? $offer->full_description,
                    'price' => $validated['offer_price'] ?? $offer->price,
                    'validity' => $validated['offer_validity'] ?? $offer->validity,
                    'notes' => $validated['offer_notes'] ?? $offer->notes,
                    'terms' => $validated['offer_terms'] ?? $offer->terms,
                    'important_notes' => $validated['offer_important_notes'] ?? $offer->important_notes,
                ];
                if ($offerImagePath) {
                    $offerUpdate['image'] = $offerImagePath;
                }
                $offer->update($offerUpdate);
            }

            // Sync features
            if (isset($validated['offer_features'])) {
                $offer->features()->delete();
                foreach ($validated['offer_features'] as $fIdx => $featText) {
                    if (trim($featText) !== '') {
                        $offer->features()->create([
                            'feature_text' => trim($featText),
                            'order' => $fIdx + 1,
                        ]);
                    }
                }
            }

            // Update banner
            $banner->update([
                'title' => $validated['title'],
                'highlight_title' => $validated['highlight_title'] ?? null,
                'label' => $validated['label'] ?? null,
                'description' => $validated['description'] ?? null,
                'alt_text' => $validated['alt_text'] ?? null,
                'button_text' => $validated['button_text'] ?? 'Offer Details',
                'linked_offer_id' => $offer->id,
                'linked_offer_slug' => $offer->slug,
                'order' => $validated['order'] ?? $banner->order,
                'is_active' => $request->boolean('is_active', true),
            ]);

            return redirect()->route('admin.asdreams.banners.index')
                ->with('success', 'Banner slide and linked Offer Details updated successfully.');
        });
    }

    public function destroy(AsdreamsBanner $banner): RedirectResponse
    {
        if ($banner->offer) {
            $banner->offer->update(['banner_id' => null]);
        }
        $banner->delete();

        return redirect()->route('admin.asdreams.banners.index')
            ->with('success', 'Banner slide removed.');
    }

    public function toggleStatus(AsdreamsBanner $banner): RedirectResponse
    {
        $banner->is_active = ! $banner->is_active;
        $banner->save();

        return back()->with('success', 'Banner status updated.');
    }
}
