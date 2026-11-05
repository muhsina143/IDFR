<?php

namespace App\Http\Controllers;

use App\Models\AboutStatistic;
use App\Models\Client;
use App\Models\ContactEnquiry;
use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\GalleryImage;
use App\Models\ModelProfile;
use App\Models\MovingLogo;
use App\Models\Partner;
use App\Models\ProfessionalCategory;
use App\Models\Season;
use App\Models\SeasonModelRegistration;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the IDFR homepage with the currently active runway season, models, professionals, designers, and gallery.
     */
    public function home(): View
    {
        $currentSeason = Season::current();

        if ($currentSeason) {
            session([
                'selected_season_id' => $currentSeason->id,
                'selected_season_num' => $currentSeason->number,
            ]);

            $currentSeason->load(['activeImages' => function ($q) {
                $q->orderBy('display_order', 'asc')->orderBy('id', 'asc');
            }]);
            $currentSeason->loadCount(['designers', 'models']);
        }

        $heroImages = $currentSeason ? $currentSeason->activeImages : collect();

        $seasons = Season::where('status', 'published')
            ->withCount(['designers', 'models'])
            ->orderBy('number', 'asc')
            ->get();

        $models = ModelProfile::where('status', 'published')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $designers = Designer::where('status', 'published')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $otherPros = CreativeProfessional::where('status', 'published')
            ->where('category', '!=', 'fashion-designer')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $designerPros = $designers->map(function ($d) {
            $pro = new CreativeProfessional;
            $pro->id = $d->id;
            $pro->name = $d->name;
            $pro->slug = 'fashion-designer-'.Str::slug($d->name);
            $pro->category = 'fashion-designer';
            $pro->professional_title = $d->brand_name ? $d->brand_name.' · Fashion Designer' : 'Couture Fashion Designer';
            $pro->location = $d->location ?: 'Kerala, India';
            $pro->experience = $d->experience ?: 'Couture Designer';
            $pro->short_intro = $d->short_intro ?: ($d->brand_name ? $d->brand_name.' bespoke runway collection.' : 'Contemporary fashion designer.');
            $pro->biography = ($d->collection_title ? "Collection: {$d->collection_title}. " : '').($d->biography ?: $d->short_intro);
            $services = ! empty($d->services) && is_array($d->services) ? $d->services : [];
            if ($d->collection_title && ! in_array($d->collection_title, $services)) {
                array_unshift($services, $d->collection_title);
            }
            if (empty($services)) {
                $services = ['Couture', 'Bridal', 'Runway collections'];
            }
            $pro->services = $services;
            $pro->profile_photo = $d->profile_photo;
            $pro->portfolio_images = is_array($d->portfolio_images) ? $d->portfolio_images : [];
            $pro->status = $d->status;

            return $pro;
        });

        $professionals = $otherPros->concat($designerPros);

        $galleryImages = GalleryImage::where('status', 'published')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $partners = Partner::where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $sponsors = Sponsor::where('is_active', true)
            ->orderByDesc('is_title')
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $clients = Client::where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $featuredClient = Client::where('is_active', true)
            ->where('is_featured', true)
            ->first() ?? $clients->first();

        $movingLogos = MovingLogo::with(['client', 'sponsor'])
            ->where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $statistics = AboutStatistic::active()->ordered()->get();

        $categories = ProfessionalCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('idfr.home', compact(
            'currentSeason',
            'heroImages',
            'seasons',
            'models',
            'professionals',
            'designers',
            'galleryImages',
            'partners',
            'sponsors',
            'clients',
            'featuredClient',
            'movingLogos',
            'statistics',
            'categories'
        ));
    }

    /**
     * Display the About IDFR page.
     */
    public function about(): View
    {
        $statistics = AboutStatistic::active()->ordered()->get();
        $currentSeason = Season::current();

        return view('idfr.about', compact('statistics', 'currentSeason'));
    }

    /**
     * Display the Models directory page with real database records.
     */
    public function models(): View
    {
        $models = ModelProfile::where('status', 'published')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $seasons = Season::where('status', 'published')
            ->orderBy('number', 'asc')
            ->get();

        return view('idfr.models', compact('models', 'seasons'));
    }

    /**
     * Display the Creative Professionals page with real database records.
     */
    public function professionals(): View
    {
        $categories = ProfessionalCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $otherPros = CreativeProfessional::where('status', 'published')
            ->where('category', '!=', 'fashion-designer')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $designers = Designer::where('status', 'published')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $designerPros = $designers->map(function ($d) {
            $pro = new CreativeProfessional;
            $pro->id = $d->id;
            $pro->name = $d->name;
            $pro->slug = 'fashion-designer-'.Str::slug($d->name);
            $pro->category = 'fashion-designer';
            $pro->professional_title = $d->brand_name ? $d->brand_name.' · Fashion Designer' : 'Couture Fashion Designer';
            $pro->location = $d->location ?: 'Kerala, India';
            $pro->experience = $d->experience ?: 'Couture Designer';
            $pro->short_intro = $d->short_intro ?: ($d->brand_name ? $d->brand_name.' bespoke runway collection.' : 'Contemporary fashion designer.');
            $pro->biography = ($d->collection_title ? "Collection: {$d->collection_title}. " : '').($d->biography ?: $d->short_intro);
            $services = ! empty($d->services) && is_array($d->services) ? $d->services : [];
            if ($d->collection_title && ! in_array($d->collection_title, $services)) {
                array_unshift($services, $d->collection_title);
            }
            if (empty($services)) {
                $services = ['Couture', 'Bridal', 'Runway collections'];
            }
            $pro->services = $services;
            $pro->profile_photo = $d->profile_photo;
            $pro->portfolio_images = is_array($d->portfolio_images) ? $d->portfolio_images : [];
            $pro->status = $d->status;

            return $pro;
        });

        $professionals = $otherPros->concat($designerPros);

        return view('idfr.professionals', compact('professionals', 'designers', 'categories'));
    }

    /**
     * Display a dedicated category listing page for a creative professional category.
     */
    public function categoryListing(string $category): View|RedirectResponse
    {
        if ($category === 'party-event-mnagement') {
            return redirect()->route('idfr.professionals.category', ['category' => 'party-event-management']);
        }
        $categoryModel = ProfessionalCategory::where('slug', $category)
            ->where('is_active', true)
            ->firstOrFail();

        $categories = ProfessionalCategory::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        if ($category === 'fashion-designer') {
            $designers = Designer::where('status', 'published')
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();

            $professionals = $designers->map(function ($d) {
                $pro = new CreativeProfessional;
                $pro->id = $d->id;
                $pro->name = $d->name;
                $pro->slug = 'fashion-designer-'.Str::slug($d->name);
                $pro->category = 'fashion-designer';
                $pro->professional_title = $d->brand_name ? $d->brand_name.' · Fashion Designer' : 'Couture Fashion Designer';
                $pro->location = $d->location ?: 'Kerala, India';
                $pro->experience = $d->experience ?: 'Couture Designer';
                $pro->short_intro = $d->short_intro ?: ($d->brand_name ? $d->brand_name.' bespoke runway collection.' : 'Contemporary fashion designer.');
                $pro->biography = ($d->collection_title ? "Collection: {$d->collection_title}. " : '').($d->biography ?: $d->short_intro);
                $services = ! empty($d->services) && is_array($d->services) ? $d->services : [];
                if ($d->collection_title && ! in_array($d->collection_title, $services)) {
                    array_unshift($services, $d->collection_title);
                }
                if (empty($services)) {
                    $services = ['Couture', 'Bridal', 'Runway collections'];
                }
                $pro->services = $services;
                $pro->profile_photo = $d->profile_photo;
                $pro->portfolio_images = is_array($d->portfolio_images) ? $d->portfolio_images : [];
                $pro->status = $d->status;

                return $pro;
            });
        } else {
            $professionals = CreativeProfessional::where('status', 'published')
                ->where('category', $category)
                ->orderBy('display_order')
                ->orderBy('id')
                ->get();
        }

        return view('idfr.category-listing', compact('categoryModel', 'categories', 'professionals'));
    }

    /**
     * Display the Gallery page with real database records.
     */
    public function gallery(): View
    {
        $galleryImages = GalleryImage::where('status', 'published')
            ->orderBy('display_order')
            ->orderBy('id')
            ->get();

        $seasons = Season::where('status', 'published')
            ->orderBy('number', 'asc')
            ->get();

        return view('idfr.gallery', compact('galleryImages', 'seasons'));
    }

    /**
     * Display the Contact page.
     */
    public function contact(): View
    {
        return view('idfr.contact');
    }

    /**
     * Display the Season Model Registration page automatically locked to the single active runway season.
     */
    public function tickets(): View
    {
        $currentSeason = Season::current();

        if ($currentSeason) {
            $currentSeason->load('registrationPrice');
            session([
                'selected_season_id' => $currentSeason->id,
                'selected_season_num' => $currentSeason->number,
            ]);
        }

        $registrationFee = $currentSeason ? $currentSeason->getRegistrationFee() : 9999.00;
        $formattedFee = $currentSeason ? $currentSeason->formattedRegistrationFee() : '₹9,999';

        return view('idfr.tickets', compact('currentSeason', 'registrationFee', 'formattedFee'));
    }

    /**
     * Handle contact form submission and persist to database.
     */
    public function handleContact(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'phone' => ['required', 'string', 'min:8', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $enquiry = ContactEnquiry::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'unread',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $message = "Thank you, {$enquiry->name}. Your enquiry has been received — the IDFR team will get back to you on {$enquiry->phone} within one working day.";

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Handle model registration submission.
     */
    public function handleTicketBooking(Request $request): JsonResponse|RedirectResponse
    {
        // Backward-compatibility mapping if legacy form inputs are submitted
        if ($request->filled('name') && ! $request->filled('full_name')) {
            $request->merge(['full_name' => $request->input('name')]);
        }
        if ($request->filled('phone') && ! $request->filled('whatsapp_number')) {
            $request->merge(['whatsapp_number' => $request->input('phone')]);
        }
        if (! $request->filled('age')) {
            $request->merge(['age' => 21]);
        }
        if (! $request->filled('gender')) {
            $request->merge(['gender' => 'female']);
        }
        if (! $request->filled('category')) {
            $request->merge(['category' => 'adult']);
        }
        if (! $request->filled('citizenship')) {
            $request->merge(['citizenship' => 'Indian']);
        }

        $validated = $request->validate([
            'season_id' => ['required', 'exists:seasons,id'],
            'full_name' => ['required', 'string', 'min:2', 'max:150'],
            'whatsapp_number' => ['required', 'string', 'min:8', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'age' => ['required', 'integer', 'min:3', 'max:99'],
            'gender' => ['required', 'string', 'in:male,female,other,Male,Female,Other'],
            'category' => ['required', 'string', 'in:adult,kids,Adult,Kids'],
            'citizenship' => ['required', 'string', 'min:2', 'max:100'],
            'parent_name' => ['nullable', 'string', 'max:150'],
            'parent_contact' => ['nullable', 'string', 'max:50'],
            'parent_relationship' => ['nullable', 'string', 'max:50'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
        ]);

        $season = Season::findOrFail($validated['season_id']);

        // Enforce single active season booking validation
        $activeSeason = Season::current();
        if ($activeSeason && (int) $season->id !== (int) $activeSeason->id) {
            $errorMsg = "Model registration is only accepted for the currently active runway season ({$activeSeason->title}).";
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                    'errors' => [
                        'season_id' => [$errorMsg],
                    ],
                ], 422);
            }

            return back()->withErrors(['season_id' => $errorMsg])->withInput();
        }

        $registrationFee = $season->getRegistrationFee();
        $isMinor = (int) $validated['age'] < 18 || strtolower($validated['category']) === 'kids';

        // Check guardian requirement for minors
        if ($isMinor && (empty($validated['parent_name']) || empty($validated['parent_contact']))) {
            $errorMsg = 'Parent / Legal Guardian name and contact number are required for minors / kids registrations.';
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                    'errors' => [
                        'parent_name' => ['Parent/Guardian name is required for minors.'],
                        'parent_contact' => ['Parent/Guardian contact number is required for minors.'],
                    ],
                ], 422);
            }

            return back()->withErrors([
                'parent_name' => 'Parent/Guardian name is required for minors.',
                'parent_contact' => 'Parent/Guardian contact number is required for minors.',
            ])->withInput();
        }

        // Prevent accidental duplicate submissions within 2 minutes
        $existing = SeasonModelRegistration::where('season_id', $season->id)
            ->where('whatsapp_number', $validated['whatsapp_number'])
            ->where('created_at', '>=', now()->subMinutes(2))
            ->first();

        if ($existing) {
            $msg = "You have already submitted a registration for {$season->title}. Registration ID: {$existing->registration_number}. Our casting team will contact you shortly.";
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'duplicate' => true,
                    'message' => $msg,
                    'registration_number' => $existing->registration_number,
                ]);
            }

            return back()->with('info', $msg);
        }

        $regNumber = SeasonModelRegistration::generateRegistrationNumber($season->number);

        $registration = SeasonModelRegistration::create([
            'registration_number' => $regNumber,
            'season_id' => $season->id,
            'full_name' => $validated['full_name'],
            'whatsapp_number' => $validated['whatsapp_number'],
            'email' => $validated['email'] ?? null,
            'age' => (int) $validated['age'],
            'gender' => strtolower($validated['gender']),
            'category' => $isMinor ? 'kids' : 'adult',
            'citizenship' => $validated['citizenship'],
            'parent_name' => $validated['parent_name'] ?? null,
            'parent_contact' => $validated['parent_contact'] ?? null,
            'parent_relationship' => $validated['parent_relationship'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            'registration_fee' => $registrationFee,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
            'registration_status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $feeFormatted = '₹'.number_format($registrationFee, 0);
        $message = "Model registration successfully submitted for {$season->title}! Your Registration ID is {$regNumber}. Registration Fee: {$feeFormatted}. Our casting desk will connect with you on WhatsApp ({$registration->whatsapp_number}) within 24 hours.";

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'registration' => [
                    'id' => $registration->id,
                    'registration_number' => $registration->registration_number,
                    'full_name' => $registration->full_name,
                    'season' => $season->title,
                    'registration_fee' => $registration->formatted_fee,
                    'payment_status' => $registration->payment_status,
                ],
            ]);
        }

        return back()->with('success', $message);
    }
}
