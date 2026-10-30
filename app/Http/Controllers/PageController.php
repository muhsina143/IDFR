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
use App\Models\Season;
use App\Models\Sponsor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the IDFR homepage with dynamic season, models, professionals, designers, and gallery.
     */
    public function home(Request $request): View
    {
        $seasonId = $request->query('season_id');
        $seasonNum = $request->query('season');

        if ($seasonId) {
            $currentSeason = Season::find($seasonId);
        } elseif ($seasonNum) {
            $currentSeason = Season::where('number', $seasonNum)->first();
        } else {
            $currentSeason = Season::where('status', 'published')
                ->orderByDesc('number')
                ->first() ?? Season::orderByDesc('number')->first();
        }

        if ($currentSeason) {
            $currentSeason->load(['activeImages' => function ($q) {
                $q->orderBy('display_order', 'asc')->orderBy('id', 'asc');
            }]);
        }

        $heroImages = $currentSeason ? $currentSeason->activeImages : collect();

        $seasons = Season::where('status', 'published')
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
            'statistics'
        ));
    }

    /**
     * Display the About IDFR page.
     */
    public function about(): View
    {
        $statistics = AboutStatistic::active()->ordered()->get();
        $currentSeason = Season::where('status', 'published')
            ->orderByDesc('number')
            ->first();

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

        return view('idfr.professionals', compact('professionals', 'designers'));
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
     * Display the Ticket Booking page.
     */
    public function tickets(): View
    {
        return view('idfr.tickets');
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
     * Handle ticket booking submission.
     */
    public function handleTicketBooking(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'ticket_type' => 'nullable|string',
            'quantity' => 'nullable|integer|min:1',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ticket reservation initiated successfully!',
            ]);
        }

        return back()->with('success', 'Ticket reservation initiated successfully!');
    }
}
