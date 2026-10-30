<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Client;
use App\Models\MovingLogo;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMovingLogoController extends Controller
{
    /**
     * Display a listing of moving logos with an animated preview strip.
     */
    public function index(Request $request): View
    {
        $query = MovingLogo::with(['client', 'sponsor']);

        if ($search = $request->input('search')) {
            $query->where('company_name', 'like', "%{$search}%");
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $sort = $request->input('sort', 'order');
        if ($sort === 'name') {
            $query->orderBy('company_name', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
        }

        $logos = $query->paginate(20)->withQueryString();
        $activeLogosForPreview = MovingLogo::where('is_active', true)->orderBy('display_order')->get();

        return view('admin.moving-logos.index', compact('logos', 'activeLogosForPreview'));
    }

    /**
     * Show the form for creating a new moving logo.
     */
    public function create(): View
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $sponsors = Sponsor::where('is_active', true)->orderBy('name')->get();
        $nextOrder = (MovingLogo::max('display_order') ?? 0) + 1;

        return view('admin.moving-logos.create', compact('clients', 'sponsors', 'nextOrder'));
    }

    /**
     * Store a newly created moving logo in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('company_name') && $request->filled('name')) {
            $request->merge(['company_name' => $request->input('name')]);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:5120'],
            'logo_style' => ['nullable', 'string', 'max:50'],
            'logo_mark' => ['nullable', 'string', 'max:20'],
            'logo_word' => ['nullable', 'string', 'max:100'],
            'logo_sub' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = 'strip_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/moving-logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $logoPath = $dir.'/'.$fileName;
        }

        $logo = MovingLogo::create([
            'client_id' => $validated['client_id'] ?? null,
            'sponsor_id' => $validated['sponsor_id'] ?? null,
            'company_name' => $validated['company_name'],
            'logo_path' => $logoPath,
            'logo_style' => $validated['logo_style'] ?? 'circle',
            'logo_mark' => $validated['logo_mark'] ?? substr($validated['company_name'], 0, 1),
            'logo_word' => $validated['logo_word'] ?? strtoupper($validated['company_name']),
            'logo_sub' => $validated['logo_sub'] ?? '',
            'website_url' => $validated['website_url'] ?? null,
            'display_order' => $validated['display_order'] ?? ((MovingLogo::max('display_order') ?? 0) + 1),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        AdminActivity::log(
            'moving_logo_created',
            auth()->user(),
            auth()->user()->email,
            "Added logo to moving strip: {$logo->company_name}",
            $request
        );

        return redirect()->route('admin.moving-logos.index')
            ->with('success', "Logo for '{$logo->company_name}' added to moving strip.");
    }

    /**
     * Show the form for editing the specified moving logo.
     */
    public function edit(MovingLogo $movingLogo): View
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get();
        $sponsors = Sponsor::where('is_active', true)->orderBy('name')->get();

        return view('admin.moving-logos.edit', compact('movingLogo', 'clients', 'sponsors'));
    }

    /**
     * Update the specified moving logo in storage.
     */
    public function update(Request $request, MovingLogo $movingLogo): RedirectResponse
    {
        if (! $request->filled('company_name') && $request->filled('name')) {
            $request->merge(['company_name' => $request->input('name')]);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'client_id' => ['nullable', 'exists:clients,id'],
            'sponsor_id' => ['nullable', 'exists:sponsors,id'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:5120'],
            'logo_style' => ['nullable', 'string', 'max:50'],
            'logo_mark' => ['nullable', 'string', 'max:20'],
            'logo_word' => ['nullable', 'string', 'max:100'],
            'logo_sub' => ['nullable', 'string', 'max:100'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('logo')) {
            if ($movingLogo->logo_path && str_starts_with($movingLogo->logo_path, 'uploads/') && File::exists(public_path($movingLogo->logo_path))) {
                @unlink(public_path($movingLogo->logo_path));
            }
            $file = $request->file('logo');
            $fileName = 'strip_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/moving-logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $movingLogo->logo_path = $dir.'/'.$fileName;
        }

        $movingLogo->client_id = $validated['client_id'] ?? null;
        $movingLogo->sponsor_id = $validated['sponsor_id'] ?? null;
        $movingLogo->company_name = $validated['company_name'];
        $movingLogo->logo_style = $validated['logo_style'] ?? $movingLogo->logo_style;
        $movingLogo->logo_mark = $validated['logo_mark'] ?? $movingLogo->logo_mark;
        $movingLogo->logo_word = $validated['logo_word'] ?? $movingLogo->logo_word;
        $movingLogo->logo_sub = $validated['logo_sub'] ?? $movingLogo->logo_sub;
        $movingLogo->website_url = $validated['website_url'] ?? null;
        $movingLogo->display_order = $validated['display_order'] ?? $movingLogo->display_order;
        $movingLogo->is_active = $request->has('is_active') ? $request->boolean('is_active') : $movingLogo->is_active;
        $movingLogo->save();

        AdminActivity::log(
            'moving_logo_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated moving logo: {$movingLogo->company_name}",
            $request
        );

        return redirect()->route('admin.moving-logos.index')
            ->with('success', "Moving logo '{$movingLogo->company_name}' updated successfully.");
    }

    /**
     * Remove the specified moving logo from storage.
     */
    public function destroy(Request $request, MovingLogo $movingLogo): RedirectResponse
    {
        $name = $movingLogo->company_name;

        if ($movingLogo->logo_path && str_starts_with($movingLogo->logo_path, 'uploads/') && File::exists(public_path($movingLogo->logo_path))) {
            @unlink(public_path($movingLogo->logo_path));
        }

        $movingLogo->delete();

        AdminActivity::log(
            'moving_logo_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted moving logo: {$name}",
            $request
        );

        return redirect()->route('admin.moving-logos.index')
            ->with('success', "Logo '{$name}' removed from moving strip.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, MovingLogo $movingLogo): JsonResponse|RedirectResponse
    {
        $movingLogo->is_active = ! $movingLogo->is_active;
        $movingLogo->save();

        AdminActivity::log(
            'moving_logo_status_toggled',
            auth()->user(),
            auth()->user()->email,
            "Toggled active status for moving logo: {$movingLogo->company_name} to ".($movingLogo->is_active ? 'Active' : 'Inactive'),
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $movingLogo->is_active,
                'message' => 'Logo status updated to '.($movingLogo->is_active ? 'Active' : 'Inactive').'.',
            ]);
        }

        return back()->with('success', 'Logo status updated to '.($movingLogo->is_active ? 'Active' : 'Inactive').'.');
    }

    /**
     * Reorder moving logos display order.
     */
    public function reorder(Request $request): JsonResponse|RedirectResponse
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            if (isset($item['id'], $item['display_order'])) {
                MovingLogo::where('id', $item['id'])->update(['display_order' => (int) $item['display_order']]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Moving logos reordered successfully.']);
        }

        return back()->with('success', 'Moving logos reordered successfully.');
    }
}
