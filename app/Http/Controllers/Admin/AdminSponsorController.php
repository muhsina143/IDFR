<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSponsorController extends Controller
{
    /**
     * Display a listing of sponsors.
     */
    public function index(Request $request): View
    {
        $query = Sponsor::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('tier', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        if ($request->has('is_title') && $request->input('is_title') !== '') {
            $query->where('is_title', $request->boolean('is_title'));
        }

        $sort = $request->input('sort', 'order');
        if ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            $query->orderByDesc('is_title')->orderBy('display_order', 'asc')->orderBy('id', 'asc');
        }

        $sponsors = $query->paginate(15)->withQueryString();

        return view('admin.sponsors.index', compact('sponsors'));
    }

    /**
     * Show the form for creating a new sponsor.
     */
    public function create(): View
    {
        $nextOrder = (Sponsor::max('display_order') ?? 0) + 1;

        return view('admin.sponsors.create', compact('nextOrder'));
    }

    /**
     * Store a newly created sponsor in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'tier' => ['required', 'string', 'max:100'],
            'is_title' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:5120'],
            'logo_style' => ['nullable', 'string', 'max:50'],
            'logo_mark' => ['nullable', 'string', 'max:20'],
            'logo_word' => ['nullable', 'string', 'max:100'],
            'logo_sub' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'sponsor_img_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/sponsors';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $imagePath = $dir.'/'.$fileName;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = 'sponsor_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/sponsors/logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $logoPath = $dir.'/'.$fileName;
        }

        $isTitle = $request->boolean('is_title');
        if ($isTitle) {
            Sponsor::where('is_title', true)->update(['is_title' => false]);
        }

        $sponsor = Sponsor::create([
            'name' => $validated['name'],
            'tier' => $validated['tier'],
            'is_title' => $isTitle,
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'logo_path' => $logoPath,
            'logo_style' => $validated['logo_style'] ?? 'crown',
            'logo_mark' => $validated['logo_mark'] ?? substr($validated['name'], 0, 1),
            'logo_word' => $validated['logo_word'] ?? strtoupper($validated['name']),
            'logo_sub' => $validated['logo_sub'] ?? strtoupper($validated['tier']),
            'website_url' => $validated['website_url'] ?? null,
            'display_order' => $validated['display_order'] ?? ((Sponsor::max('display_order') ?? 0) + 1),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        AdminActivity::log(
            'sponsor_created',
            auth()->user(),
            auth()->user()->email,
            "Created sponsor: {$sponsor->name} ({$sponsor->tier})",
            $request
        );

        return redirect()->route('admin.sponsors.index')
            ->with('success', "Sponsor '{$sponsor->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified sponsor.
     */
    public function edit(Sponsor $sponsor): View
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    /**
     * Update the specified sponsor in storage.
     */
    public function update(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'tier' => ['required', 'string', 'max:100'],
            'is_title' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:5120'],
            'logo_style' => ['nullable', 'string', 'max:50'],
            'logo_mark' => ['nullable', 'string', 'max:20'],
            'logo_word' => ['nullable', 'string', 'max:100'],
            'logo_sub' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($sponsor->image_path && str_starts_with($sponsor->image_path, 'uploads/') && File::exists(public_path($sponsor->image_path))) {
                @unlink(public_path($sponsor->image_path));
            }
            $file = $request->file('image');
            $fileName = 'sponsor_img_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/sponsors';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $sponsor->image_path = $dir.'/'.$fileName;
        }

        if ($request->hasFile('logo')) {
            if ($sponsor->logo_path && str_starts_with($sponsor->logo_path, 'uploads/') && File::exists(public_path($sponsor->logo_path))) {
                @unlink(public_path($sponsor->logo_path));
            }
            $file = $request->file('logo');
            $fileName = 'sponsor_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/sponsors/logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $sponsor->logo_path = $dir.'/'.$fileName;
        }

        $isTitle = $request->boolean('is_title');
        if ($isTitle && ! $sponsor->is_title) {
            Sponsor::where('id', '!=', $sponsor->id)->where('is_title', true)->update(['is_title' => false]);
        }

        $sponsor->name = $validated['name'];
        $sponsor->tier = $validated['tier'];
        $sponsor->is_title = $isTitle;
        $sponsor->description = $validated['description'] ?? null;
        $sponsor->logo_style = $validated['logo_style'] ?? $sponsor->logo_style;
        $sponsor->logo_mark = $validated['logo_mark'] ?? $sponsor->logo_mark;
        $sponsor->logo_word = $validated['logo_word'] ?? $sponsor->logo_word;
        $sponsor->logo_sub = $validated['logo_sub'] ?? $sponsor->logo_sub;
        $sponsor->website_url = $validated['website_url'] ?? null;
        $sponsor->display_order = $validated['display_order'] ?? $sponsor->display_order;
        $sponsor->is_active = $request->has('is_active') ? $request->boolean('is_active') : $sponsor->is_active;
        $sponsor->save();

        AdminActivity::log(
            'sponsor_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated sponsor: {$sponsor->name}",
            $request
        );

        return redirect()->route('admin.sponsors.index')
            ->with('success', "Sponsor '{$sponsor->name}' updated successfully.");
    }

    /**
     * Remove the specified sponsor from storage.
     */
    public function destroy(Request $request, Sponsor $sponsor): RedirectResponse
    {
        $name = $sponsor->name;

        if ($sponsor->image_path && str_starts_with($sponsor->image_path, 'uploads/') && File::exists(public_path($sponsor->image_path))) {
            @unlink(public_path($sponsor->image_path));
        }

        if ($sponsor->logo_path && str_starts_with($sponsor->logo_path, 'uploads/') && File::exists(public_path($sponsor->logo_path))) {
            @unlink(public_path($sponsor->logo_path));
        }

        $sponsor->delete();

        AdminActivity::log(
            'sponsor_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted sponsor: {$name}",
            $request
        );

        return redirect()->route('admin.sponsors.index')
            ->with('success', "Sponsor '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Sponsor $sponsor): JsonResponse|RedirectResponse
    {
        $sponsor->is_active = ! $sponsor->is_active;
        $sponsor->save();

        AdminActivity::log(
            'sponsor_status_toggled',
            auth()->user(),
            auth()->user()->email,
            "Toggled active status for sponsor: {$sponsor->name} to ".($sponsor->is_active ? 'Active' : 'Inactive'),
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $sponsor->is_active,
                'message' => 'Sponsor status updated to '.($sponsor->is_active ? 'Active' : 'Inactive').'.',
            ]);
        }

        return back()->with('success', 'Sponsor status updated to '.($sponsor->is_active ? 'Active' : 'Inactive').'.');
    }

    /**
     * Reorder sponsors display order.
     */
    public function reorder(Request $request): JsonResponse|RedirectResponse
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            if (isset($item['id'], $item['display_order'])) {
                Sponsor::where('id', $item['id'])->update(['display_order' => (int) $item['display_order']]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Sponsors reordered successfully.']);
        }

        return back()->with('success', 'Sponsors reordered successfully.');
    }
}
