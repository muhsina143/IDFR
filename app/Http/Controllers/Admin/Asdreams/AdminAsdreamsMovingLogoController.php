<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Admin\Asdreams\Concerns\HandlesAsdreamsImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsMovingLogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsMovingLogoController extends Controller
{
    use HandlesAsdreamsImageUploads;

    /**
     * Display the paginated list and live strip preview of ASDreams moving client logos.
     */
    public function index(Request $request): View
    {
        $query = AsdreamsMovingLogo::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        match ($request->get('sort', 'order')) {
            'name' => $query->orderBy('name', 'asc'),
            'newest' => $query->orderBy('id', 'desc'),
            default => $query->orderBy('display_order', 'asc')->orderBy('id', 'asc'),
        };

        $logos = $query->paginate(15)->withQueryString();
        $activeLogosForPreview = AsdreamsMovingLogo::active()->get();

        return view('admin.asdreams.moving-logos.index', compact('logos', 'activeLogosForPreview'));
    }

    /**
     * Show the form for adding a new moving client logo.
     */
    public function create(): View
    {
        $nextOrder = ((int) AsdreamsMovingLogo::max('display_order')) + 1;

        return view('admin.asdreams.moving-logos.create', compact('nextOrder'));
    }

    /**
     * Store a newly created moving client logo in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateMovingLogo($request, isUpdate: false);

        $logoPath = $this->uploadAsdreamsFile($request->file('logo'), 'moving-logos', 'moving_logo');

        AsdreamsMovingLogo::create([
            'name' => $validated['name'],
            'logo_path' => $logoPath,
            'display_order' => $validated['display_order'] ?? (((int) AsdreamsMovingLogo::max('display_order')) + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.asdreams.moving-logos.index')
            ->with('success', 'Moving client logo added successfully.');
    }

    /**
     * Show the form for editing the specified moving client logo.
     */
    public function edit(AsdreamsMovingLogo $movingLogo): View
    {
        return view('admin.asdreams.moving-logos.edit', compact('movingLogo'));
    }

    /**
     * Update the specified moving client logo in storage.
     */
    public function update(Request $request, AsdreamsMovingLogo $movingLogo): RedirectResponse
    {
        $validated = $this->validateMovingLogo($request, isUpdate: true);

        if ($request->hasFile('logo')) {
            $movingLogo->logo_path = $this->uploadAsdreamsFile(
                $request->file('logo'),
                'moving-logos',
                'moving_logo',
                $movingLogo->logo_path
            );
        }

        $movingLogo->update([
            'name' => $validated['name'],
            'logo_path' => $movingLogo->logo_path,
            'display_order' => $validated['display_order'] ?? $movingLogo->display_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return redirect()->route('admin.asdreams.moving-logos.index')
            ->with('success', 'Moving client logo updated successfully.');
    }

    /**
     * Remove the specified moving client logo from storage.
     */
    public function destroy(AsdreamsMovingLogo $movingLogo): RedirectResponse
    {
        $this->deleteAsdreamsUploadedFile($movingLogo->logo_path);
        $movingLogo->delete();

        return redirect()->route('admin.asdreams.moving-logos.index')
            ->with('success', 'Moving client logo removed from strip.');
    }

    /**
     * Toggle active/inactive visibility for the specified moving client logo.
     */
    public function toggleStatus(AsdreamsMovingLogo $movingLogo): RedirectResponse
    {
        $movingLogo->is_active = ! $movingLogo->is_active;
        $movingLogo->save();

        return back()->with('success', 'Moving logo status updated.');
    }

    /**
     * Validate moving logo store/update request data.
     *
     * @return array<string, mixed>
     */
    private function validateMovingLogo(Request $request, bool $isUpdate): array
    {
        $logoRule = $isUpdate ? 'nullable' : 'required';

        return $request->validate([
            'name' => 'required|string|max:150',
            'logo' => "{$logoRule}|file|mimes:jpeg,png,jpg,webp,svg|max:5120",
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
