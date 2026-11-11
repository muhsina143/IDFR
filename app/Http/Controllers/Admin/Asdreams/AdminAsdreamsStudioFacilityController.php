<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsStudioFacility;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsStudioFacilityController extends Controller
{
    /**
     * Display the Facilities Provided list and editable section heading/description.
     */
    public function index(Request $request): View
    {
        $query = AsdreamsStudioFacility::ordered();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        $facilities = $query->paginate(20)->withQueryString();
        $heading = AsdreamsWebsiteSetting::get('studio_facilities_heading', 'Facilities Provided');
        $description = AsdreamsWebsiteSetting::get('studio_facilities_description', 'All areas are air-conditioned.');

        return view('admin.asdreams.studio-rental.facilities.index', compact(
            'facilities',
            'heading',
            'description'
        ));
    }

    /**
     * Update the Facilities Provided section heading and description.
     */
    public function updateSettings(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'heading' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        AsdreamsWebsiteSetting::set('studio_facilities_heading', $validated['heading'], 'studio_rental');
        AsdreamsWebsiteSetting::set('studio_facilities_description', $validated['description'] ?? '', 'studio_rental');

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'facilities',
            'Facilities heading and description updated successfully.'
        );
    }

    /**
     * Show the form for creating a new studio facility.
     */
    public function create(): View
    {
        $nextOrder = ((int) AsdreamsStudioFacility::max('display_order')) + 1;
        $icons = AsdreamsStudioFacility::availableIcons();

        return view('admin.asdreams.studio-rental.facilities.create', compact('nextOrder', 'icons'));
    }

    /**
     * Store a newly created facility in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateFacility($request);

        AsdreamsStudioFacility::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'check',
            'display_order' => $validated['display_order'] ?? (((int) AsdreamsStudioFacility::max('display_order')) + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'facilities',
            'Studio facility added successfully.',
            route('admin.asdreams.studio-facilities.index')
        );
    }

    /**
     * Show the form for editing an existing studio facility.
     */
    public function edit(AsdreamsStudioFacility $facility): View
    {
        $icons = AsdreamsStudioFacility::availableIcons();

        return view('admin.asdreams.studio-rental.facilities.edit', compact('facility', 'icons'));
    }

    /**
     * Update the specified facility in storage.
     */
    public function update(Request $request, AsdreamsStudioFacility $facility): JsonResponse|RedirectResponse
    {
        $validated = $this->validateFacility($request);

        $facility->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'check',
            'display_order' => $validated['display_order'] ?? $facility->display_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'facilities',
            'Studio facility updated successfully.',
            route('admin.asdreams.studio-facilities.index')
        );
    }

    /**
     * Remove the specified facility from storage.
     */
    public function destroy(Request $request, AsdreamsStudioFacility $facility): JsonResponse|RedirectResponse
    {
        $facility->delete();

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'facilities',
            'Studio facility deleted successfully.',
            route('admin.asdreams.studio-facilities.index')
        );
    }

    /**
     * Toggle active/inactive visibility for the specified facility.
     */
    public function toggleStatus(Request $request, AsdreamsStudioFacility $facility): JsonResponse|RedirectResponse
    {
        $facility->is_active = ! $facility->is_active;
        $facility->save();

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'facilities',
            'Facility status updated.'
        );
    }

    /**
     * Validate facility store/update request payload.
     *
     * @return array<string, mixed>
     */
    private function validateFacility(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'icon' => 'nullable|string|in:'.implode(',', array_keys(AsdreamsStudioFacility::availableIcons())),
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
