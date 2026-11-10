<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsBanner;
use App\Models\Asdreams\AsdreamsBrand;
use App\Models\Asdreams\AsdreamsClient;
use App\Models\Asdreams\AsdreamsContactEnquiry;
use App\Models\Asdreams\AsdreamsGalleryCategory;
use App\Models\Asdreams\AsdreamsGalleryImage;
use App\Models\Asdreams\AsdreamsMovingLogo;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\Asdreams\AsdreamsOfferEnquiry;
use App\Models\Asdreams\AsdreamsShootBooking;
use App\Models\Asdreams\AsdreamsTeamMember;
use Illuminate\View\View;

class AdminAsdreamsDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'banners_count' => AsdreamsBanner::count(),
            'banners_active' => AsdreamsBanner::where('is_active', true)->count(),
            'brands_count' => AsdreamsBrand::count(),
            'brands_active' => AsdreamsBrand::where('is_active', true)->count(),
            'offers_count' => AsdreamsOffer::count(),
            'offers_published' => AsdreamsOffer::where('status', 'published')->count(),
            'gallery_categories_count' => AsdreamsGalleryCategory::count(),
            'gallery_images_count' => AsdreamsGalleryImage::count(),
            'team_count' => AsdreamsTeamMember::count(),
            'clients_count' => AsdreamsClient::count(),
            'clients_active' => AsdreamsClient::where('is_active', true)->count(),
            'moving_logos_count' => AsdreamsMovingLogo::count(),
            'moving_logos_active' => AsdreamsMovingLogo::where('is_active', true)->count(),
            'offer_enquiries_new' => AsdreamsOfferEnquiry::where('status', 'new')->count(),
            'offer_enquiries_total' => AsdreamsOfferEnquiry::count(),
            'contact_enquiries_new' => AsdreamsContactEnquiry::where('status', 'new')->count(),
            'contact_enquiries_total' => AsdreamsContactEnquiry::count(),
            'shoot_bookings_new' => AsdreamsShootBooking::where('status', 'new')->count(),
            'shoot_bookings_total' => AsdreamsShootBooking::count(),
        ];

        $recentOfferEnquiries = AsdreamsOfferEnquiry::with('offer')->latest()->take(5)->get();
        $recentContactEnquiries = AsdreamsContactEnquiry::latest()->take(5)->get();
        $recentShootBookings = AsdreamsShootBooking::latest()->take(5)->get();

        return view('admin.asdreams.dashboard', compact(
            'stats',
            'recentOfferEnquiries',
            'recentContactEnquiries',
            'recentShootBookings'
        ));
    }
}
