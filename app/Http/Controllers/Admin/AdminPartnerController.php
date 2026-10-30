<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminPartnerController extends Controller
{
    /**
     * Display a listing of partners.
     */
    public function index(Request $request): View
    {
        $query = Partner::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $sort = $request->input('sort', 'order');
        if ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
        }

        $partners = $query->paginate(15)->withQueryString();

        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create(): View
    {
        $nextOrder = (Partner::max('display_order') ?? 0) + 1;

        return view('admin.partners.create', compact('nextOrder'));
    }

    /**
     * Store a newly created partner in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:100'],
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
            $fileName = 'partner_card_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/partners';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $imagePath = $dir.'/'.$fileName;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = 'partner_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/partners/logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $logoPath = $dir.'/'.$fileName;
        }

        $partner = Partner::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'logo_path' => $logoPath,
            'logo_style' => $validated['logo_style'] ?? 'circle',
            'logo_mark' => $validated['logo_mark'] ?? substr($validated['name'], 0, 1),
            'logo_word' => $validated['logo_word'] ?? strtoupper($validated['name']),
            'logo_sub' => $validated['logo_sub'] ?? strtoupper($validated['type']),
            'website_url' => $validated['website_url'] ?? null,
            'display_order' => $validated['display_order'] ?? ((Partner::max('display_order') ?? 0) + 1),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        AdminActivity::log(
            'partner_created',
            auth()->user(),
            auth()->user()->email,
            "Created partner: {$partner->name}",
            $request
        );

        return redirect()->route('admin.partners.index')
            ->with('success', "Partner '{$partner->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner): View
    {
        return view('admin.partners.edit', compact('partner'));
    }

    /**
     * Update the specified partner in storage.
     */
    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:100'],
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
            if ($partner->image_path && str_starts_with($partner->image_path, 'uploads/') && File::exists(public_path($partner->image_path))) {
                @unlink(public_path($partner->image_path));
            }
            $file = $request->file('image');
            $fileName = 'partner_card_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/partners';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $partner->image_path = $dir.'/'.$fileName;
        }

        if ($request->hasFile('logo')) {
            if ($partner->logo_path && str_starts_with($partner->logo_path, 'uploads/') && File::exists(public_path($partner->logo_path))) {
                @unlink(public_path($partner->logo_path));
            }
            $file = $request->file('logo');
            $fileName = 'partner_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/partners/logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $partner->logo_path = $dir.'/'.$fileName;
        }

        $partner->name = $validated['name'];
        $partner->type = $validated['type'];
        $partner->description = $validated['description'] ?? null;
        $partner->logo_style = $validated['logo_style'] ?? $partner->logo_style;
        $partner->logo_mark = $validated['logo_mark'] ?? $partner->logo_mark;
        $partner->logo_word = $validated['logo_word'] ?? $partner->logo_word;
        $partner->logo_sub = $validated['logo_sub'] ?? $partner->logo_sub;
        $partner->website_url = $validated['website_url'] ?? null;
        $partner->display_order = $validated['display_order'] ?? $partner->display_order;
        $partner->is_active = $request->has('is_active') ? $request->boolean('is_active') : $partner->is_active;
        $partner->save();

        AdminActivity::log(
            'partner_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated partner: {$partner->name}",
            $request
        );

        return redirect()->route('admin.partners.index')
            ->with('success', "Partner '{$partner->name}' updated successfully.");
    }

    /**
     * Remove the specified partner from storage.
     */
    public function destroy(Request $request, Partner $partner): RedirectResponse
    {
        $name = $partner->name;

        if ($partner->image_path && str_starts_with($partner->image_path, 'uploads/') && File::exists(public_path($partner->image_path))) {
            @unlink(public_path($partner->image_path));
        }

        if ($partner->logo_path && str_starts_with($partner->logo_path, 'uploads/') && File::exists(public_path($partner->logo_path))) {
            @unlink(public_path($partner->logo_path));
        }

        $partner->delete();

        AdminActivity::log(
            'partner_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted partner: {$name}",
            $request
        );

        return redirect()->route('admin.partners.index')
            ->with('success', "Partner '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Partner $partner): JsonResponse|RedirectResponse
    {
        $partner->is_active = ! $partner->is_active;
        $partner->save();

        AdminActivity::log(
            'partner_status_toggled',
            auth()->user(),
            auth()->user()->email,
            "Toggled active status for partner: {$partner->name} to ".($partner->is_active ? 'Active' : 'Inactive'),
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $partner->is_active,
                'message' => 'Partner status updated to '.($partner->is_active ? 'Active' : 'Inactive').'.',
            ]);
        }

        return back()->with('success', 'Partner status updated to '.($partner->is_active ? 'Active' : 'Inactive').'.');
    }

    /**
     * Reorder partners display order.
     */
    public function reorder(Request $request): JsonResponse|RedirectResponse
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            if (isset($item['id'], $item['display_order'])) {
                Partner::where('id', $item['id'])->update(['display_order' => (int) $item['display_order']]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Partners reordered successfully.']);
        }

        return back()->with('success', 'Partners reordered successfully.');
    }
}
