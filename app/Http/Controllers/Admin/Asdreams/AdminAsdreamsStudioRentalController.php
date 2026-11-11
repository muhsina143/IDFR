<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsStudioEquipment;
use App\Models\Asdreams\AsdreamsStudioFacility;
use App\Models\Asdreams\AsdreamsStudioGalleryImage;
use App\Models\Asdreams\AsdreamsStudioRentalSetting;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAsdreamsStudioRentalController extends Controller
{
    /**
     * Allowed tab identifiers within the centralized Studio Rental Management page.
     *
     * @var list<string>
     */
    private const TABS = ['facilities', 'equipment', 'gallery', 'settings'];

    /**
     * Display the centralized Studio Rental Management hub.
     */
    public function index(Request $request): View
    {
        $requestedTab = (string) $request->query('tab', 'facilities');
        $activeTab = in_array($requestedTab, self::TABS, true) ? $requestedTab : 'facilities';

        return view('admin.asdreams.studio-rental.index', self::buildHubData($activeTab));
    }

    /**
     * Update dynamic Studio Rental pricing and studio information settings.
     */
    public function updatePricingSettings(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'studio_area' => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'booking_info' => ['nullable', 'string', 'max:500'],
            'additional_note' => ['nullable', 'string', 'max:500'],
            'price_amount' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'currency' => ['required', 'string', Rule::in(array_keys(AsdreamsStudioRentalSetting::CURRENCIES))],
            'rental_unit' => ['required', 'string', Rule::in(array_keys(AsdreamsStudioRentalSetting::UNITS))],
            'pricing_label' => ['nullable', 'string', 'max:100'],
            'price_description' => ['nullable', 'string', 'max:500'],
            'price_enabled' => ['nullable', 'boolean'],
        ]);

        $validated['price_enabled'] = $request->boolean('price_enabled', false);

        AsdreamsStudioRentalSetting::saveConfig($validated);

        return self::respond(
            $request,
            'settings',
            'Studio rental pricing and information updated successfully.',
            route('admin.asdreams.studio-rental.index', ['tab' => 'settings'])
        );
    }

    /**
     * Build shared dataset required by all tabs on the centralized hub.
     *
     * @return array<string, mixed>
     */
    public static function buildHubData(string $activeTab = 'facilities'): array
    {
        $settings = AsdreamsWebsiteSetting::pluck('value', 'key');

        return [
            'activeTab' => $activeTab,
            'facilities' => AsdreamsStudioFacility::ordered()->get(),
            'facilitiesHeading' => AsdreamsWebsiteSetting::get('studio_facilities_heading', 'Facilities Provided'),
            'facilitiesDescription' => AsdreamsWebsiteSetting::get('studio_facilities_description', 'All areas are air-conditioned.'),
            'nextFacilityOrder' => ((int) AsdreamsStudioFacility::max('display_order')) + 1,
            'facilityIcons' => AsdreamsStudioFacility::availableIcons(),
            'equipments' => AsdreamsStudioEquipment::ordered()->get(),
            'equipmentHeading' => AsdreamsWebsiteSetting::get('studio_equipment_heading', 'Equipment Included'),
            'equipmentDescription' => AsdreamsWebsiteSetting::get('studio_equipment_description', 'Available with every studio rental booking.'),
            'nextEquipmentOrder' => ((int) AsdreamsStudioEquipment::max('display_order')) + 1,
            'equipmentIcons' => AsdreamsStudioEquipment::availableIcons(),
            'galleryImages' => AsdreamsStudioGalleryImage::ordered()->get(),
            'nextGalleryOrder' => ((int) AsdreamsStudioGalleryImage::max('display_order')) + 1,
            'settings' => $settings,
            'rentalConfig' => AsdreamsStudioRentalSetting::getConfig($settings),
            'currencies' => AsdreamsStudioRentalSetting::CURRENCIES,
            'units' => AsdreamsStudioRentalSetting::UNITS,
        ];
    }

    /**
     * Render a single tab partial HTML for seamless AJAX panel updates.
     */
    public static function renderTabPartial(string $tab): string
    {
        $resolvedTab = in_array($tab, self::TABS, true) ? $tab : 'facilities';

        return view(
            "admin.asdreams.studio-rental.partials._{$resolvedTab}_tab",
            self::buildHubData($resolvedTab)
        )->render();
    }

    /**
     * Return JSON tab refresh payload for AJAX callers, or redirect cleanly for standard form submissions.
     */
    public static function respond(
        Request $request,
        string $tab,
        string $message,
        ?string $fallbackRedirectUrl = null
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            $data = self::buildHubData($tab);

            return response()->json([
                'success' => true,
                'message' => $message,
                'tab' => $tab,
                'counts' => [
                    'facilities' => $data['facilities']->count(),
                    'equipment' => $data['equipments']->count(),
                    'gallery' => $data['galleryImages']->count(),
                ],
                'nextOrder' => [
                    'facilities' => $data['nextFacilityOrder'],
                    'equipment' => $data['nextEquipmentOrder'],
                    'gallery' => $data['nextGalleryOrder'],
                ],
                'rentalConfig' => $data['rentalConfig'],
                'tabHtml' => self::renderTabPartial($tab),
            ]);
        }

        if ($request->boolean('from_hub')) {
            return redirect()
                ->route('admin.asdreams.studio-rental.index', ['tab' => $tab])
                ->with('success', $message);
        }

        if ($fallbackRedirectUrl !== null) {
            return redirect($fallbackRedirectUrl)->with('success', $message);
        }

        return back()->with('success', $message);
    }
}
