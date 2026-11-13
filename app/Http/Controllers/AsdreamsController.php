<?php

namespace App\Http\Controllers;

use App\Models\Asdreams\AsdreamsBanner;
use App\Models\Asdreams\AsdreamsBrand;
use App\Models\Asdreams\AsdreamsClient;
use App\Models\Asdreams\AsdreamsContactEnquiry;
use App\Models\Asdreams\AsdreamsContactSetting;
use App\Models\Asdreams\AsdreamsGalleryCategory;
use App\Models\Asdreams\AsdreamsMovingLogo;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\Asdreams\AsdreamsOfferEnquiry;
use App\Models\Asdreams\AsdreamsShootBooking;
use App\Models\Asdreams\AsdreamsStudioEquipment;
use App\Models\Asdreams\AsdreamsStudioFacility;
use App\Models\Asdreams\AsdreamsStudioGalleryImage;
use App\Models\Asdreams\AsdreamsStudioRentalSetting;
use App\Models\Asdreams\AsdreamsTeamMember;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsdreamsController extends Controller
{
    /**
     * Display the ASDreamStudio home page.
     */
    public function home(): View
    {
        $banners = AsdreamsBanner::active()->get();
        $brands = AsdreamsBrand::active()->get();
        $categories = AsdreamsGalleryCategory::active()
            ->with('publishedImages')
            ->get();
        $clients = AsdreamsClient::active()->get();
        $movingLogos = AsdreamsMovingLogo::active()->get();
        $studioFacilities = AsdreamsStudioFacility::active()->get();
        $studioEquipments = AsdreamsStudioEquipment::active()->get();
        $studioGalleryImages = AsdreamsStudioGalleryImage::active()->get();
        $settings = AsdreamsWebsiteSetting::pluck('value', 'key');
        $studioRental = AsdreamsStudioRentalSetting::getConfig($settings);

        return view('asdreams.home', compact(
            'banners',
            'brands',
            'categories',
            'clients',
            'movingLogos',
            'studioFacilities',
            'studioEquipments',
            'studioGalleryImages',
            'settings',
            'studioRental'
        ));
    }

    /**
     * Display the ASDreamStudio About page.
     */
    public function about(): View
    {
        return view('asdreams.about');
    }

    /**
     * Display the ASDreamStudio Gallery page.
     */
    public function gallery(): View
    {
        $categories = AsdreamsGalleryCategory::active()
            ->with('publishedImages')
            ->get();

        return view('asdreams.gallery', compact('categories'));
    }

    /**
     * Display the ASDreamStudio Contact page (or individual brand contact page).
     */
    public function contact(Request $request): View
    {
        $brandParam = $request->query('brand');

        if ($brandParam) {
            $brandAliases = [
                'athuls' => 'athuls',
                'athuls-academy' => 'athuls',
                'modelling-academy' => 'athuls',
                'modeling' => 'athuls',
                'lakme' => 'lakme',
                'lakme-academy' => 'lakme',
            ];
            $activeBrand = $brandAliases[strtolower($brandParam)] ?? 'athuls';

            return view('asdreams.brand-contact', compact('activeBrand', 'brandParam'));
        }

        $contact = AsdreamsContactSetting::current();

        return view('asdreams.contact', compact('contact'));
    }

    /**
     * Display the ASDreamStudio Team Members page.
     */
    public function team(): View
    {
        $founders = AsdreamsTeamMember::active()->founders()->ordered()->get();
        $coFounders = AsdreamsTeamMember::active()->coFounders()->ordered()->get();
        $teamMembers = AsdreamsTeamMember::active()->teamMembers()->ordered()->get();

        return view('asdreams.team', compact('founders', 'coFounders', 'teamMembers'));
    }

    /**
     * Display the Offer Details page (default or requested via query parameter ?offer=slug).
     */
    public function offersIndex(Request $request): View
    {
        $offers = AsdreamsOffer::published()->with(['features', 'banner'])->get();

        $requestedSlug = $request->query('offer');
        $requestedBanner = $request->query('banner');
        $activeOffer = null;

        if ($requestedBanner) {
            $banner = AsdreamsBanner::with(['linkedOffer.features', 'offer.features'])->find($requestedBanner);
            if ($banner) {
                $activeOffer = $banner->linkedOffer ?? $banner->offer;
            }
        }

        if (! $activeOffer && $requestedSlug) {
            $activeOffer = $offers->firstWhere('slug', $requestedSlug) ?? $offers->firstWhere('id', $requestedSlug);
        }

        if (! $activeOffer && $offers->isNotEmpty()) {
            $activeOffer = $offers->firstWhere('slug', 'viewfinder') ?? $offers->first();
        }

        return view('asdreams.offer-details', compact('offers', 'activeOffer'));
    }

    /**
     * Display a specific Offer Details page by slug.
     */
    public function offerShow(string $slug): View
    {
        $offers = AsdreamsOffer::published()->with(['features', 'banner'])->get();
        $activeOffer = AsdreamsOffer::where('status', 'published')
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug)
                    ->orWhere('id', is_numeric($slug) ? $slug : 0);
            })
            ->with(['features', 'banner'])
            ->first();

        if (! $activeOffer && is_numeric($slug)) {
            $banner = AsdreamsBanner::with(['linkedOffer.features', 'offer.features'])->find($slug);
            if ($banner) {
                $activeOffer = $banner->linkedOffer ?? $banner->offer;
            }
        }

        if (! $activeOffer && $offers->isNotEmpty()) {
            $activeOffer = $offers->firstWhere('slug', 'viewfinder') ?? $offers->first();
        }

        return view('asdreams.offer-details', compact('offers', 'activeOffer'));
    }

    /**
     * Handle Offer Enquiry submission.
     */
    public function handleOfferEnquiry(\App\Http\Requests\Asdreams\AsdreamsOfferEnquiryRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $offer = null;
        if (! empty($validated['offer_id'])) {
            $offer = AsdreamsOffer::find((int) $validated['offer_id']);
        }
        if (! $offer && ! empty($validated['offer'])) {
            $offerValue = $validated['offer'];
            $offer = AsdreamsOffer::where('slug', $offerValue)
                ->orWhere('id', is_numeric($offerValue) ? (int) $offerValue : 0)
                ->first();
        }
        if (! $offer && ! empty($validated['banner_id'])) {
            $banner = AsdreamsBanner::with(['linkedOffer', 'offer'])->find((int) $validated['banner_id']);
            if ($banner) {
                $offer = $banner->linkedOffer ?? $banner->offer;
            }
        }

        $bannerId = ! empty($validated['banner_id']) ? (int) $validated['banner_id'] : null;
        if (! $bannerId && $offer) {
            $bannerId = $offer->banner_id ?: AsdreamsBanner::where('linked_offer_id', $offer->id)->value('id');
        }

        $enquiry = AsdreamsOfferEnquiry::create([
            'banner_id' => $bannerId,
            'offer_id' => $offer?->id,
            'offer_title' => $offer ? $offer->title : ($validated['offer'] ?? 'Offer Enquiry'),
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'preferred_date' => $validated['date'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        $message = 'Thank you! Your enquiry about "'.($offer?->title ?? 'our offer').'" has been received. Our team will get back to you within one working day.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'enquiry_id' => $enquiry->id,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Handle Contact Page enquiry submission.
     */
    public function handleContactEnquiry(\App\Http\Requests\Asdreams\AsdreamsContactRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $brand = $validated['brand'] ?? 'asdreams';

        $enquiry = AsdreamsContactEnquiry::create([
            'brand' => $brand,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'] ?? '',
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        $brandName = match (strtolower($brand)) {
            'athuls', 'athuls-academy' => "Athul's Academy",
            'lakme', 'lakme-academy' => 'Lakmé Academy',
            default => 'ASDreamStudio',
        };

        $message = "Thank you for reaching out to {$brandName}! Your message has been received and our team will get in touch shortly.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'enquiry_id' => $enquiry->id,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Handle shoot booking submission.
     */
    public function handleShootBooking(\App\Http\Requests\Asdreams\AsdreamsShootBookingRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $message = 'Thank you for choosing ASDreamStudio! Your shoot booking request has been received. Our team will contact you within 24 hours to confirm details.';

        // Prevent duplicate spam/double-click within 60 seconds
        $existing = AsdreamsShootBooking::where('phone', $validated['phone'])
            ->where('created_at', '>=', now()->subMinute())
            ->first();

        if ($existing) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'booking_id' => $existing->id,
                ]);
            }

            return back()->with('success', $message);
        }

        $shootDate = $validated['shoot_date'] ?? $validated['date'] ?? null;
        $subject = $validated['shoot_type'] ?? $validated['subject'] ?? 'Shoot Booking';
        $clientNotes = $validated['notes'] ?? $validated['message'] ?? null;

        $booking = AsdreamsShootBooking::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'booking_type' => 'shoot',
            'shoot_date' => $shootDate ?: now(),
            'start_date' => $shootDate ?: now(),
            'subject' => $subject,
            'message' => $clientNotes,
            'shoot_type' => $subject,
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking_id' => $booking->id,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Handle studio rental booking submission.
     */
    public function handleStudioBooking(\App\Http\Requests\Asdreams\AsdreamsStudioBookingRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $message = 'Your studio booking request has been submitted successfully! Our representative will contact you within 5 hours to confirm availability.';

        // Prevent duplicate spam/double-click within 60 seconds
        $existing = AsdreamsShootBooking::where('phone', $validated['phone'])
            ->where(function ($q) {
                $q->where('booking_type', 'studio')
                    ->orWhere('shoot_type', 'Studio Rental');
            })
            ->where('created_at', '>=', now()->subMinute())
            ->first();

        if ($existing) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'booking_id' => $existing->id,
                ]);
            }

            return back()->with('success', $message);
        }

        $startDate = $validated['start'];
        $endDate = $validated['end'];
        $purpose = trim($validated['purpose']);
        $days = (int) $validated['days'];

        $booking = AsdreamsShootBooking::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => $purpose,
            'days' => $days,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'shoot_date' => $startDate,
            'subject' => 'Studio Rental: '.$purpose,
            'message' => $validated['notes'] ?? null,
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking_id' => $booking->id,
            ]);
        }

        return back()->with('success', $message);
    }
}
