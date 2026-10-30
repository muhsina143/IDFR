<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Designer;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminDesignerController extends Controller
{
    /**
     * Display a listing of designers with search, filters, and pagination.
     */
    public function index(Request $request): View
    {
        $query = Designer::with('seasons');

        // Search by name, brand_name, collection, or location
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand_name', 'like', "%{$search}%")
                    ->orWhere('collection_title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by Season
        if ($seasonId = $request->input('season_id')) {
            $query->whereHas('seasons', function ($q) use ($seasonId) {
                $q->where('seasons.id', $seasonId);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'order');
        if ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } elseif ($sort === 'featured') {
            $query->orderByDesc('is_featured')->orderBy('display_order');
        } else {
            $query->orderBy('display_order')->orderBy('id');
        }

        $designers = $query->paginate(12)->withQueryString();
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.designers.index', compact('designers', 'seasons'));
    }

    /**
     * Show the form for creating a new designer profile.
     */
    public function create(): View
    {
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.designers.create', compact('seasons'));
    }

    /**
     * Store a newly created designer in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'brand_name' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'short_intro' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'collection_title' => ['nullable', 'string', 'max:150'],
            'collection_description' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['exists:seasons,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'brand_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'designer_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/designers'), $filename);
            $photoPath = 'uploads/designers/'.$filename;
        }

        $logoPath = null;
        if ($request->hasFile('brand_logo')) {
            $file = $request->file('brand_logo');
            $filename = 'logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/designers'), $filename);
            $logoPath = 'uploads/designers/'.$filename;
        }

        $services = ! empty($validated['services']) ? array_filter(array_map('trim', explode(',', $validated['services']))) : [];

        $portfolio = [];
        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/designers'), $pFilename);
                $portfolio[] = ['image' => 'uploads/designers/'.$pFilename, 'caption' => 'Runway Look'];
            }
        }

        $designer = Designer::create([
            'name' => $validated['name'],
            'brand_name' => $validated['brand_name'] ?? null,
            'location' => $validated['location'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'short_intro' => $validated['short_intro'] ?? null,
            'biography' => $validated['biography'] ?? null,
            'collection_title' => $validated['collection_title'] ?? null,
            'collection_description' => $validated['collection_description'] ?? null,
            'services' => $services,
            'website_url' => $validated['website_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'display_order' => $validated['display_order'] ?? 0,
            'status' => $validated['status'],
            'profile_photo' => $photoPath,
            'brand_logo' => $logoPath,
            'portfolio_images' => $portfolio,
        ]);

        if (! empty($validated['seasons'])) {
            $designer->seasons()->sync($validated['seasons']);
        }

        AdminActivity::log(
            'designer_created',
            auth()->user(),
            auth()->user()->email,
            "Created designer profile: {$designer->name} ({$designer->brand_name})",
            $request
        );

        return redirect()->route('admin.designers')
            ->with('success', "Designer '{$designer->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified designer.
     */
    public function edit(Designer $designer): View
    {
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.designers.edit', compact('designer', 'seasons'));
    }

    /**
     * Update the specified designer in the database.
     */
    public function update(Request $request, Designer $designer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'brand_name' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'short_intro' => ['nullable', 'string', 'max:255'],
            'biography' => ['nullable', 'string'],
            'collection_title' => ['nullable', 'string', 'max:150'],
            'collection_description' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['exists:seasons,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'brand_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = $designer->profile_photo;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'designer_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/designers'), $filename);
            $photoPath = 'uploads/designers/'.$filename;
        }

        $logoPath = $designer->brand_logo;
        if ($request->hasFile('brand_logo')) {
            $file = $request->file('brand_logo');
            $filename = 'logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/designers'), $filename);
            $logoPath = 'uploads/designers/'.$filename;
        }

        $services = ! empty($validated['services']) ? array_filter(array_map('trim', explode(',', $validated['services']))) : ($designer->services ?? []);

        $portfolio = $designer->portfolio_images ?? [];
        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/designers'), $pFilename);
                $portfolio[] = ['image' => 'uploads/designers/'.$pFilename, 'caption' => 'Runway Look'];
            }
        }

        $designer->update([
            'name' => $validated['name'],
            'brand_name' => $validated['brand_name'] ?? null,
            'location' => $validated['location'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'short_intro' => $validated['short_intro'] ?? null,
            'biography' => $validated['biography'] ?? null,
            'collection_title' => $validated['collection_title'] ?? null,
            'collection_description' => $validated['collection_description'] ?? null,
            'services' => $services,
            'website_url' => $validated['website_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'display_order' => $validated['display_order'] ?? 0,
            'status' => $validated['status'],
            'profile_photo' => $photoPath,
            'brand_logo' => $logoPath,
            'portfolio_images' => $portfolio,
        ]);

        $designer->seasons()->sync($validated['seasons'] ?? []);

        AdminActivity::log(
            'designer_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated designer profile: {$designer->name}",
            $request
        );

        return redirect()->route('admin.designers')
            ->with('success', "Designer '{$designer->name}' updated successfully.");
    }

    /**
     * Remove the specified designer from the database.
     */
    public function destroy(Request $request, Designer $designer): RedirectResponse
    {
        $name = $designer->name;
        $designer->seasons()->detach();
        $designer->delete();

        AdminActivity::log(
            'designer_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted designer: {$name}",
            $request
        );

        return redirect()->route('admin.designers')
            ->with('success', "Designer '{$name}' has been deleted.");
    }
}
