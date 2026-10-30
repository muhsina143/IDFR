<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\ModelProfile;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminModelController extends Controller
{
    /**
     * Display a listing of models with search, filters, and pagination.
     */
    public function index(Request $request): View
    {
        $query = ModelProfile::with('seasons');

        // Search by name, location, or description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by Gender / Category
        if ($gender = $request->input('gender')) {
            if ($gender === 'kids') {
                $query->where('is_kid', true);
            } else {
                $query->where('gender', ucfirst($gender));
            }
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

        $models = $query->paginate(12)->withQueryString();
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.models.index', compact('models', 'seasons'));
    }

    /**
     * Show the form for creating a new model profile.
     */
    public function create(): View
    {
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.models.create', compact('seasons'));
    }

    /**
     * Store a newly created model profile in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'string', 'in:Female,Male,Kids,Other'],
            'level' => ['required', 'string', 'in:New Face,Experienced,Kids'],
            'is_kid' => ['nullable', 'boolean'],
            'age' => ['nullable', 'integer', 'min:3', 'max:80'],
            'height' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'availability' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'types' => ['nullable', 'array'],
            'skills' => ['nullable', 'string'],
            'specialities' => ['nullable', 'string'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['exists:seasons,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = null;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'model_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/models'), $filename);
            $photoPath = 'uploads/models/'.$filename;
        }

        // Parse skills & specialities from comma-separated input
        $skills = ! empty($validated['skills']) ? array_filter(array_map('trim', explode(',', $validated['skills']))) : [];
        $specialities = ! empty($validated['specialities']) ? array_filter(array_map('trim', explode(',', $validated['specialities']))) : [];

        // Portfolio images upload
        $portfolioPaths = [];
        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/models'), $pFilename);
                $portfolioPaths[] = 'uploads/models/'.$pFilename;
            }
        }

        $model = ModelProfile::create([
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'level' => $validated['level'],
            'is_kid' => $request->boolean('is_kid') || $validated['gender'] === 'Kids',
            'age' => $validated['age'] ?? null,
            'height' => $validated['height'] ?? null,
            'location' => $validated['location'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'description' => $validated['description'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'display_order' => $validated['display_order'] ?? 0,
            'status' => $validated['status'],
            'profile_photo' => $photoPath,
            'types' => $validated['types'] ?? [],
            'skills' => $skills,
            'specialities' => $specialities,
            'portfolio_images' => $portfolioPaths,
        ]);

        if (! empty($validated['seasons'])) {
            $model->seasons()->sync($validated['seasons']);
        }

        AdminActivity::log(
            'model_created',
            auth()->user(),
            auth()->user()->email,
            "Created model profile: {$model->name}",
            $request
        );

        return redirect()->route('admin.models')
            ->with('success', "Model profile '{$model->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified model profile.
     */
    public function edit(ModelProfile $model): View
    {
        $seasons = Season::orderBy('number', 'desc')->get();

        return view('admin.models.edit', compact('model', 'seasons'));
    }

    /**
     * Update the specified model profile in the database.
     */
    public function update(Request $request, ModelProfile $model): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'string', 'in:Female,Male,Kids,Other'],
            'level' => ['required', 'string', 'in:New Face,Experienced,Kids'],
            'is_kid' => ['nullable', 'boolean'],
            'age' => ['nullable', 'integer', 'min:3', 'max:80'],
            'height' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:100'],
            'experience' => ['nullable', 'string', 'max:100'],
            'availability' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'types' => ['nullable', 'array'],
            'skills' => ['nullable', 'string'],
            'specialities' => ['nullable', 'string'],
            'seasons' => ['nullable', 'array'],
            'seasons.*' => ['exists:seasons,id'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $photoPath = $model->profile_photo;
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'model_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/models'), $filename);
            $photoPath = 'uploads/models/'.$filename;
        }

        $skills = ! empty($validated['skills']) ? array_filter(array_map('trim', explode(',', $validated['skills']))) : ($model->skills ?? []);
        $specialities = ! empty($validated['specialities']) ? array_filter(array_map('trim', explode(',', $validated['specialities']))) : ($model->specialities ?? []);

        $portfolioPaths = $model->portfolio_images ?? [];
        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/models'), $pFilename);
                $portfolioPaths[] = 'uploads/models/'.$pFilename;
            }
        }

        $model->update([
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'level' => $validated['level'],
            'is_kid' => $request->boolean('is_kid') || $validated['gender'] === 'Kids',
            'age' => $validated['age'] ?? null,
            'height' => $validated['height'] ?? null,
            'location' => $validated['location'] ?? null,
            'experience' => $validated['experience'] ?? null,
            'availability' => $validated['availability'] ?? null,
            'description' => $validated['description'] ?? null,
            'instagram_url' => $validated['instagram_url'] ?? null,
            'portfolio_url' => $validated['portfolio_url'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'display_order' => $validated['display_order'] ?? 0,
            'status' => $validated['status'],
            'profile_photo' => $photoPath,
            'types' => $validated['types'] ?? [],
            'skills' => $skills,
            'specialities' => $specialities,
            'portfolio_images' => $portfolioPaths,
        ]);

        $model->seasons()->sync($validated['seasons'] ?? []);

        AdminActivity::log(
            'model_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated model profile: {$model->name}",
            $request
        );

        return redirect()->route('admin.models')
            ->with('success', "Model profile '{$model->name}' updated successfully.");
    }

    /**
     * Remove the specified model profile from the database.
     */
    public function destroy(Request $request, ModelProfile $model): RedirectResponse
    {
        $name = $model->name;
        $model->seasons()->detach();
        $model->delete();

        AdminActivity::log(
            'model_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted model profile: {$name}",
            $request
        );

        return redirect()->route('admin.models')
            ->with('success', "Model profile '{$name}' has been deleted.");
    }
}
