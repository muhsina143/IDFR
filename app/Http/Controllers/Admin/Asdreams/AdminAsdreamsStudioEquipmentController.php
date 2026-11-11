<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Admin\Asdreams\Concerns\HandlesAsdreamsImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsStudioEquipment;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsStudioEquipmentController extends Controller
{
    use HandlesAsdreamsImageUploads;

    /**
     * Display the Equipment Included list and editable heading/description.
     */
    public function index(Request $request): View
    {
        $query = AsdreamsStudioEquipment::ordered();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        $equipments = $query->paginate(20)->withQueryString();
        $heading = AsdreamsWebsiteSetting::get('studio_equipment_heading', 'Equipment Included');
        $description = AsdreamsWebsiteSetting::get('studio_equipment_description', 'Available with every studio rental booking.');

        return view('admin.asdreams.studio-rental.equipment.index', compact(
            'equipments',
            'heading',
            'description'
        ));
    }

    /**
     * Update the Equipment Included section heading and description.
     */
    public function updateSettings(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'heading' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ]);

        AsdreamsWebsiteSetting::set('studio_equipment_heading', $validated['heading'], 'studio_rental');
        AsdreamsWebsiteSetting::set('studio_equipment_description', $validated['description'] ?? '', 'studio_rental');

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'equipment',
            'Equipment heading and description updated successfully.'
        );
    }

    /**
     * Show the form for adding a new equipment item.
     */
    public function create(): View
    {
        $nextOrder = ((int) AsdreamsStudioEquipment::max('display_order')) + 1;
        $icons = AsdreamsStudioEquipment::availableIcons();

        return view('admin.asdreams.studio-rental.equipment.create', compact('nextOrder', 'icons'));
    }

    /**
     * Store a newly created equipment item in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $this->validateEquipment($request);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $this->uploadAsdreamsFile($request->file('image'), 'studio-equipment', 'equipment');
        }

        AsdreamsStudioEquipment::create([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'light',
            'image_path' => $imagePath,
            'display_order' => $validated['display_order'] ?? (((int) AsdreamsStudioEquipment::max('display_order')) + 1),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'equipment',
            'Studio equipment item added successfully.',
            route('admin.asdreams.studio-equipment.index')
        );
    }

    /**
     * Show the form for editing an existing equipment item.
     */
    public function edit(AsdreamsStudioEquipment $equipment): View
    {
        $icons = AsdreamsStudioEquipment::availableIcons();

        return view('admin.asdreams.studio-rental.equipment.edit', compact('equipment', 'icons'));
    }

    /**
     * Update the specified equipment item in storage.
     */
    public function update(Request $request, AsdreamsStudioEquipment $equipment): JsonResponse|RedirectResponse
    {
        $validated = $this->validateEquipment($request);

        if ($request->boolean('remove_image') && $equipment->image_path) {
            $this->deleteAsdreamsUploadedFile($equipment->image_path);
            $equipment->image_path = null;
        }

        if ($request->hasFile('image')) {
            $equipment->image_path = $this->uploadAsdreamsFile(
                $request->file('image'),
                'studio-equipment',
                'equipment',
                $equipment->image_path
            );
        }

        $equipment->update([
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?: 'light',
            'image_path' => $equipment->image_path,
            'display_order' => $validated['display_order'] ?? $equipment->display_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'equipment',
            'Studio equipment item updated successfully.',
            route('admin.asdreams.studio-equipment.index')
        );
    }

    /**
     * Remove the specified equipment item from storage.
     */
    public function destroy(Request $request, AsdreamsStudioEquipment $equipment): JsonResponse|RedirectResponse
    {
        $this->deleteAsdreamsUploadedFile($equipment->image_path);
        $equipment->delete();

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'equipment',
            'Studio equipment item deleted successfully.',
            route('admin.asdreams.studio-equipment.index')
        );
    }

    /**
     * Toggle active/inactive visibility for the specified equipment item.
     */
    public function toggleStatus(Request $request, AsdreamsStudioEquipment $equipment): JsonResponse|RedirectResponse
    {
        $equipment->is_active = ! $equipment->is_active;
        $equipment->save();

        return AdminAsdreamsStudioRentalController::respond(
            $request,
            'equipment',
            'Equipment status updated.'
        );
    }

    /**
     * Validate equipment store/update request payload.
     *
     * @return array<string, mixed>
     */
    private function validateEquipment(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'icon' => 'nullable|string|in:'.implode(',', array_keys(AsdreamsStudioEquipment::availableIcons())),
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'remove_image' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
