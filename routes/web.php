<?php

use App\Http\Controllers\Admin\AdminAboutStatisticController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminClientController;
use App\Http\Controllers\Admin\AdminCreativeProfessionalController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDesignerController;
use App\Http\Controllers\Admin\AdminEnquiryController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Admin\AdminModelController;
use App\Http\Controllers\Admin\AdminMovingLogoController;
use App\Http\Controllers\Admin\AdminPartnerController;
use App\Http\Controllers\Admin\AdminProfileEnquiryController;
use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\Admin\AdminSeasonController;
use App\Http\Controllers\Admin\AdminSeasonImageController;
use App\Http\Controllers\Admin\AdminSponsorController;
use App\Http\Controllers\AsdreamsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileEnquiryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - IDFR & AS Dream Studio
|--------------------------------------------------------------------------
|
| All routes mapped to dedicated controllers. Named routes ensure clean,
| reliable URL generation throughout layouts and navigation components.
|
*/

// ==========================================
// User Authentication & Email OTP Routes
// ==========================================
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

// ==========================================
// Profile Booking Enquiry & Email OTP Routes
// ==========================================
Route::post('/enquiry/send-otp', [ProfileEnquiryController::class, 'sendOtp'])->name('enquiry.send-otp');
Route::post('/enquiry/verify-otp', [ProfileEnquiryController::class, 'verifyOtp'])->name('enquiry.verify-otp');
Route::post('/enquiry/submit', [ProfileEnquiryController::class, 'store'])->name('enquiry.submit');

// ==========================================
// 1. IDFR Brand Routes (Indian Designer Fashion Runway)
// ==========================================
Route::name('idfr.')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/models', [PageController::class, 'models'])->name('models');
    Route::get('/professionals', [PageController::class, 'professionals'])->name('professionals');
    Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [PageController::class, 'handleContact'])->name('contact.submit');
    Route::post('/contact/enquiry', [PageController::class, 'handleContact'])->name('contact.enquiry');
    Route::get('/tickets', [PageController::class, 'tickets'])->name('tickets');
    Route::post('/tickets', [PageController::class, 'handleTicketBooking'])->name('tickets.submit');
});

// ==========================================
// 2. Creative Professional & Talent Profiles (Protected for Authenticated Public Users)
// ==========================================
Route::middleware(['web', 'auth:web'])->prefix('profiles')->name('profile.')->group(function () {
    // Model
    Route::get('/model', [ProfileController::class, 'model'])->name('model');
    Route::post('/model', [ProfileController::class, 'saveModel'])->name('model.save');

    // Fashion Designer
    Route::get('/fashion-designer', [ProfileController::class, 'fashionDesigner'])->name('fashion-designer');
    Route::post('/fashion-designer', [ProfileController::class, 'saveDesigner'])->name('fashion-designer.save');

    // Creative Professionals
    Route::get('/makeup-artist', [ProfileController::class, 'makeupArtist'])->name('makeup-artist');
    Route::post('/makeup-artist', [ProfileController::class, 'saveMakeupArtist'])->name('makeup-artist.save');

    Route::get('/hairstylist', [ProfileController::class, 'hairstylist'])->name('hairstylist');
    Route::post('/hairstylist', [ProfileController::class, 'saveHairstylist'])->name('hairstylist.save');

    Route::get('/photographer', [ProfileController::class, 'photographer'])->name('photographer');
    Route::post('/photographer', [ProfileController::class, 'savePhotographer'])->name('photographer.save');

    Route::get('/videographer', [ProfileController::class, 'videographer'])->name('videographer');
    Route::post('/videographer', [ProfileController::class, 'saveVideographer'])->name('videographer.save');

    Route::get('/drone-pilot', [ProfileController::class, 'dronePilot'])->name('drone-pilot');
    Route::post('/drone-pilot', [ProfileController::class, 'saveDronePilot'])->name('drone-pilot.save');

    Route::get('/photo-video-editor', [ProfileController::class, 'photoVideoEditor'])->name('photo-video-editor');
    Route::post('/photo-video-editor', [ProfileController::class, 'savePhotoVideoEditor'])->name('photo-video-editor.save');

    Route::get('/jewellery-designer', [ProfileController::class, 'jewelleryDesigner'])->name('jewellery-designer');
    Route::post('/jewellery-designer', [ProfileController::class, 'saveJewelleryDesigner'])->name('jewellery-designer.save');
});

Route::middleware(['web', 'auth:web'])->get('/account', function () {
    $user = Auth::guard('web')->user();

    return redirect($user->profileUrl());
})->name('account');

// ==========================================
// 3. AS Dream Studio Routes
// ==========================================
Route::prefix('asdreams')->name('asd.')->group(function () {
    Route::get('/', [AsdreamsController::class, 'home'])->name('home');
    Route::get('/about', [AsdreamsController::class, 'about'])->name('about');
    Route::get('/gallery', [AsdreamsController::class, 'gallery'])->name('gallery');
    Route::get('/contact', [AsdreamsController::class, 'contact'])->name('contact');
    Route::get('/team', [AsdreamsController::class, 'team'])->name('team');
    Route::post('/shoot-booking', [AsdreamsController::class, 'handleShootBooking'])->name('shoot-booking.submit');
});

// ==========================================
// 4. Executive Admin Portal
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest Admin Auth
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');

    // Protected Admin Routes
    Route::middleware(['web', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Runway Seasons Management
        Route::get('/seasons', [AdminSeasonController::class, 'index'])->name('seasons');
        Route::post('/seasons', [AdminSeasonController::class, 'store'])->name('seasons.store');
        Route::put('/seasons/{season}', [AdminSeasonController::class, 'update'])->name('seasons.update');
        Route::delete('/seasons/{season}', [AdminSeasonController::class, 'destroy'])->name('seasons.destroy');

        // Season Hero Images Management (Multi-Image Manager)
        Route::get('/season-images', [AdminSeasonImageController::class, 'index'])->name('season-images.index');
        Route::get('/seasons/{season}/images', [AdminSeasonImageController::class, 'seasonImages'])->name('seasons.images');
        Route::post('/seasons/{season}/images/upload', [AdminSeasonImageController::class, 'upload'])->name('seasons.images.upload');
        Route::put('/season-images/{image}', [AdminSeasonImageController::class, 'update'])->name('season-images.update');
        Route::delete('/season-images/{image}', [AdminSeasonImageController::class, 'destroy'])->name('season-images.destroy');
        Route::post('/season-images/{image}/set-first', [AdminSeasonImageController::class, 'setFirst'])->name('season-images.set-first');
        Route::post('/season-images/{image}/toggle-status', [AdminSeasonImageController::class, 'toggleStatus'])->name('season-images.toggle-status');
        Route::post('/seasons/{season}/images/reorder', [AdminSeasonImageController::class, 'reorder'])->name('seasons.images.reorder');

        // Gallery Management (Full CRUD)
        Route::get('/gallery', [AdminGalleryController::class, 'index'])->name('gallery');
        Route::get('/gallery/create', [AdminGalleryController::class, 'create'])->name('gallery.create');
        Route::post('/gallery', [AdminGalleryController::class, 'store'])->name('gallery.store');
        Route::get('/gallery/{gallery}/edit', [AdminGalleryController::class, 'edit'])->name('gallery.edit');
        Route::put('/gallery/{gallery}', [AdminGalleryController::class, 'update'])->name('gallery.update');
        Route::delete('/gallery/{gallery}', [AdminGalleryController::class, 'destroy'])->name('gallery.destroy');

        // Models Management (Phase 3 Full CRUD)
        Route::get('/models', [AdminModelController::class, 'index'])->name('models');
        Route::get('/models/create', [AdminModelController::class, 'create'])->name('models.create');
        Route::post('/models', [AdminModelController::class, 'store'])->name('models.store');
        Route::get('/models/{model}/edit', [AdminModelController::class, 'edit'])->name('models.edit');
        Route::put('/models/{model}', [AdminModelController::class, 'update'])->name('models.update');
        Route::delete('/models/{model}', [AdminModelController::class, 'destroy'])->name('models.destroy');

        // Designers Management (Phase 3 Full CRUD)
        Route::get('/designers', [AdminDesignerController::class, 'index'])->name('designers');
        Route::get('/designers/create', [AdminDesignerController::class, 'create'])->name('designers.create');
        Route::post('/designers', [AdminDesignerController::class, 'store'])->name('designers.store');
        Route::get('/designers/{designer}/edit', [AdminDesignerController::class, 'edit'])->name('designers.edit');
        Route::put('/designers/{designer}', [AdminDesignerController::class, 'update'])->name('designers.update');
        Route::delete('/designers/{designer}', [AdminDesignerController::class, 'destroy'])->name('designers.destroy');

        // Creative Professionals Management (Phase 3 Full CRUD)
        Route::get('/professionals', [AdminCreativeProfessionalController::class, 'index'])->name('professionals');
        Route::get('/professionals/create', [AdminCreativeProfessionalController::class, 'create'])->name('professionals.create');
        Route::post('/professionals', [AdminCreativeProfessionalController::class, 'store'])->name('professionals.store');
        Route::get('/professionals/{professional}/edit', [AdminCreativeProfessionalController::class, 'edit'])->name('professionals.edit');
        Route::put('/professionals/{professional}', [AdminCreativeProfessionalController::class, 'update'])->name('professionals.update');
        Route::delete('/professionals/{professional}', [AdminCreativeProfessionalController::class, 'destroy'])->name('professionals.destroy');

        // Inbound Contact & Sponsorship Enquiries (Full Management)
        Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries');
        Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
        Route::put('/enquiries/{enquiry}', [AdminEnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('/enquiries/{enquiry}', [AdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');

        // Profile Booking Enquiries (Full Management)
        Route::get('/profile-enquiries', [AdminProfileEnquiryController::class, 'index'])->name('profile-enquiries.index');
        Route::get('/profile-enquiries/{enquiry}', [AdminProfileEnquiryController::class, 'show'])->name('profile-enquiries.show');
        Route::put('/profile-enquiries/{enquiry}', [AdminProfileEnquiryController::class, 'update'])->name('profile-enquiries.update');
        Route::delete('/profile-enquiries/{enquiry}', [AdminProfileEnquiryController::class, 'destroy'])->name('profile-enquiries.destroy');

        // Our Partners Management (Full CRUD)
        Route::get('/partners', [AdminPartnerController::class, 'index'])->name('partners.index');
        Route::get('/partners/create', [AdminPartnerController::class, 'create'])->name('partners.create');
        Route::post('/partners', [AdminPartnerController::class, 'store'])->name('partners.store');
        Route::get('/partners/{partner}/edit', [AdminPartnerController::class, 'edit'])->name('partners.edit');
        Route::put('/partners/{partner}', [AdminPartnerController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{partner}', [AdminPartnerController::class, 'destroy'])->name('partners.destroy');
        Route::post('/partners/{partner}/toggle-status', [AdminPartnerController::class, 'toggleStatus'])->name('partners.toggle-status');
        Route::post('/partners/reorder', [AdminPartnerController::class, 'reorder'])->name('partners.reorder');

        // Our Sponsors Management (Full CRUD)
        Route::get('/sponsors', [AdminSponsorController::class, 'index'])->name('sponsors.index');
        Route::get('/sponsors/create', [AdminSponsorController::class, 'create'])->name('sponsors.create');
        Route::post('/sponsors', [AdminSponsorController::class, 'store'])->name('sponsors.store');
        Route::get('/sponsors/{sponsor}/edit', [AdminSponsorController::class, 'edit'])->name('sponsors.edit');
        Route::put('/sponsors/{sponsor}', [AdminSponsorController::class, 'update'])->name('sponsors.update');
        Route::delete('/sponsors/{sponsor}', [AdminSponsorController::class, 'destroy'])->name('sponsors.destroy');
        Route::post('/sponsors/{sponsor}/toggle-status', [AdminSponsorController::class, 'toggleStatus'])->name('sponsors.toggle-status');
        Route::post('/sponsors/reorder', [AdminSponsorController::class, 'reorder'])->name('sponsors.reorder');

        // Our Clients & Testimonials Management (Full CRUD)
        Route::get('/clients', [AdminClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/create', [AdminClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [AdminClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}/edit', [AdminClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [AdminClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [AdminClientController::class, 'destroy'])->name('clients.destroy');
        Route::post('/clients/{client}/toggle-status', [AdminClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::post('/clients/{client}/set-featured', [AdminClientController::class, 'setFeatured'])->name('clients.set-featured');
        Route::post('/clients/reorder', [AdminClientController::class, 'reorder'])->name('clients.reorder');

        // Moving Client Logos Management (Full CRUD)
        Route::get('/moving-logos', [AdminMovingLogoController::class, 'index'])->name('moving-logos.index');
        Route::get('/moving-logos/create', [AdminMovingLogoController::class, 'create'])->name('moving-logos.create');
        Route::post('/moving-logos', [AdminMovingLogoController::class, 'store'])->name('moving-logos.store');
        Route::get('/moving-logos/{movingLogo}/edit', [AdminMovingLogoController::class, 'edit'])->name('moving-logos.edit');
        Route::put('/moving-logos/{movingLogo}', [AdminMovingLogoController::class, 'update'])->name('moving-logos.update');
        Route::delete('/moving-logos/{movingLogo}', [AdminMovingLogoController::class, 'destroy'])->name('moving-logos.destroy');
        Route::post('/moving-logos/{movingLogo}/toggle-status', [AdminMovingLogoController::class, 'toggleStatus'])->name('moving-logos.toggle-status');
        Route::post('/moving-logos/reorder', [AdminMovingLogoController::class, 'reorder'])->name('moving-logos.reorder');

        // About Page Statistics Management (Full CRUD)
        Route::get('/about-statistics', [AdminAboutStatisticController::class, 'index'])->name('about-statistics.index');
        Route::get('/about-statistics/create', [AdminAboutStatisticController::class, 'create'])->name('about-statistics.create');
        Route::post('/about-statistics', [AdminAboutStatisticController::class, 'store'])->name('about-statistics.store');
        Route::get('/about-statistics/{aboutStatistic}/edit', [AdminAboutStatisticController::class, 'edit'])->name('about-statistics.edit');
        Route::put('/about-statistics/{aboutStatistic}', [AdminAboutStatisticController::class, 'update'])->name('about-statistics.update');
        Route::delete('/about-statistics/{aboutStatistic}', [AdminAboutStatisticController::class, 'destroy'])->name('about-statistics.destroy');
        Route::post('/about-statistics/{aboutStatistic}/toggle-status', [AdminAboutStatisticController::class, 'toggleStatus'])->name('about-statistics.toggle-status');
        Route::post('/about-statistics/reorder', [AdminAboutStatisticController::class, 'reorder'])->name('about-statistics.reorder');

        // Registered Users & Profiles Management (Full Management)
        Route::get('/registrations', [AdminRegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/{user}', [AdminRegistrationController::class, 'show'])->name('registrations.show');
        Route::get('/registrations/{user}/edit', [AdminRegistrationController::class, 'edit'])->name('registrations.edit');
        Route::put('/registrations/{user}', [AdminRegistrationController::class, 'update'])->name('registrations.update');
        Route::post('/registrations/{user}/toggle-status', [AdminRegistrationController::class, 'toggleStatus'])->name('registrations.toggle-status');
        Route::delete('/registrations/{user}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');
        Route::get('/registrations-alias', fn () => redirect()->route('admin.registrations.index'))->name('registrations');

        // Other Module routes (Placeholders)
        Route::get('/settings', fn () => app(AdminDashboardController::class)->placeholder('settings'))->name('settings');
    });
});

// ==========================================
// 5. Aliases & Legacy HTML Redirections (All 21 Original Pages)
// ==========================================
// IDFR Core Pages
Route::redirect('/index.html', '/');
Route::redirect('/idfr_about.html', '/about');
Route::redirect('/idfr_models.html', '/models');
Route::redirect('/professionals.html', '/professionals');
Route::redirect('/idfr_gallery.html', '/gallery');
Route::redirect('/idfr_contact.html', '/contact');
Route::redirect('/ticket_booking.html', '/tickets');

// ASDreamStudio Pages
Route::redirect('/asdreams.html', '/asdreams');
Route::redirect('/asd_about.html', '/asdreams/about');
Route::redirect('/asd_gallery.html', '/asdreams/gallery');
Route::redirect('/asd_contact.html', '/asdreams/contact');
Route::redirect('/team_members.html', '/asdreams/team');
Route::redirect('/team', '/asdreams/team');

// Creative Profiles Pages
Route::redirect('/model_profile.html', '/profiles/model');
Route::redirect('/model-profile', '/profiles/model');
Route::redirect('/register-as-model', '/profiles/model');
Route::redirect('/makeup_artist_profile.html', '/profiles/makeup-artist');
Route::redirect('/hairstylist_profile.html', '/profiles/hairstylist');
Route::redirect('/photographer_profile.html', '/profiles/photographer');
Route::redirect('/videographer_profile.html', '/profiles/videographer');
Route::redirect('/fashion_designer_profile.html', '/profiles/fashion-designer');
Route::redirect('/drone_pilot_profile.html', '/profiles/drone-pilot');
Route::redirect('/photo_video_editor_profile.html', '/profiles/photo-video-editor');
Route::redirect('/jewellery_designer_profile.html', '/profiles/jewellery-designer');
