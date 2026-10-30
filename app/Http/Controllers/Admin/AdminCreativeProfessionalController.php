<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\CreativeProfessional;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminCreativeProfessionalController extends Controller
{
    /**
     * Display a listing of creative professionals.
     */
    public function index(Request $request): View
    {
        $query = CreativeProfessional::with('seasons');

        // Search by name, title, bio, or location
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('professional_title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('biography', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($category = $request->input('category')) {
            $query->where('category', $category);
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

        $professionals = $query->paginate(15)->withQueryString();
        $categories = CreativeProfessional::categories();
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.professionals.index', compact('professionals', 'categories', 'seasons'));
    }

    /**
     * Show the form for creating a new creative professional.
     */
    public function create(): View
    {
        $categories = CreativeProfessional::categories();
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.professionals.create', compact('categories', 'seasons'));
    }

    /**
     * Store a newly created professional in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $categoriesKeys = implode(',', array_keys(CreativeProfessional::categories()));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', "in:{$categoriesKeys}"],
            'professional_title' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'biography' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['exists:seasons,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'pro_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/professionals'), $filename);
            $photoPath = 'uploads/professionals/'.$filename;
        }

        $services = ! empty($validated['services']) ? array_filter(array_map('trim', explode(',', $validated['services']))) : [];

        $portfolio = [];
        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/professionals'), $pFilename);
                $portfolio[] = ['image' => 'uploads/professionals/'.$pFilename, 'caption' => 'Portfolio Work'];
            }
        }

        $pro = CreativeProfessional::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'professional_title' => $validated['professional_title'] ?? CreativeProfessional::categories()[$validated['category']],
            'location' => $validated['location'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'biography' => $validated['biography'] ?? null,
            'services' => $services,
            'website_url' => $validated['website_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'display_order' => $validated['display_order'] ?? 0,
            'status' => $validated['status'],
            'profile_photo' => $photoPath,
            'portfolio_images' => $portfolio,
        ]);

        if (! empty($validated['seasons'])) {
            $pro->seasons()->sync($validated['seasons']);
        }

        AdminActivity::log(
            'professional_created',
            auth()->user(),
            auth()->user()->email,
            "Created creative professional: {$pro->name} ({$pro->category_label})",
            $request
        );

        return redirect()->route('admin.professionals')
            ->with('success', "Creative Professional '{$pro->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified professional.
     */
    public function edit(CreativeProfessional $professional): View
    {
        $categories = CreativeProfessional::categories();
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.professionals.edit', compact('professional', 'categories', 'seasons'));
    }

    /**
     * Update the specified professional in the database.
     */
    public function update(Request $request, CreativeProfessional $professional): RedirectResponse
    {
        $categoriesKeys = implode(',', array_keys(CreativeProfessional::categories()));

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'category' => ['required', 'string', "in:{$categoriesKeys}"],
            'professional_title' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'biography' => ['nullable', 'string'],
            'services' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['exists:seasons,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = $professional->profile_photo;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'pro_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/professionals'), $filename);
            $photoPath = 'uploads/professionals/'.$filename;
        }

        $services = ! empty($validated['services']) ? array_filter(array_map('trim', explode(',', $validated['services']))) : ($professional->services ?? []);

        $portfolio = $professional->portfolio_images ?? [];
        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/professionals'), $pFilename);
                $portfolio[] = ['image' => 'uploads/professionals/'.$pFilename, 'caption' => 'Portfolio Work'];
            }
        }

        $professional->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'professional_title' => $validated['professional_title'] ?? CreativeProfessional::categories()[$validated['category']],
            'location' => $validated['location'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'biography' => $validated['biography'] ?? null,
            'services' => $services,
            'website_url' => $validated['website_url'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'display_order' => $validated['display_order'] ?? 0,
            'status' => $validated['status'],
            'profile_photo' => $photoPath,
            'portfolio_images' => $portfolio,
        ]);

        $professional->seasons()->sync($validated['seasons'] ?? []);

        AdminActivity::log(
            'professional_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated creative professional: {$professional->name}",
            $request
        );

        return redirect()->route('admin.professionals')
            ->with('success', "Creative Professional '{$professional->name}' updated successfully.");
    }

    /**
     * Remove the specified professional from the database.
     */
    public function destroy(Request $request, CreativeProfessional $professional): RedirectResponse
    {
        $name = $professional->name;
        $professional->seasons()->detach();
        $professional->delete();

        AdminActivity::log(
            'professional_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted creative professional: {$name}",
            $request
        );

        return redirect()->route('admin.professionals')
            ->with('success', "Creative Professional '{$name}' has been deleted.");
    }
}
