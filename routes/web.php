<?php

use App\Http\Controllers\Admin\AdminAboutStatisticController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminClientController;
use App\Http\Controllers\Admin\AdminCreativeProfessionalController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDesignerController;
use App\Http\Controllers\Admin\AdminDynamicFormController;
use App\Http\Controllers\Admin\AdminEnquiryController;
use App\Http\Controllers\Admin\AdminGalleryController;
use App\Http\Controllers\Admin\AdminModelController;
use App\Http\Controllers\Admin\AdminMovingLogoController;
use App\Http\Controllers\Admin\AdminPartnerController;
use App\Http\Controllers\Admin\AdminProfileEnquiryController;
use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\Admin\AdminSeasonController;
use App\Http\Controllers\Admin\AdminSeasonImageController;
use App\Http\Controllers\Admin\AdminSeasonRegistrationController;
use App\Http\Controllers\Admin\AdminSponsorController;
use App\Http\Controllers\Admin\AdminSponsorshipEnquiryController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsBannerController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsBrandController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsClientController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsContactController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsDashboardController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsEnquiryController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsGalleryController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsMovingLogoController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsOfferController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsSettingController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsShootBookingController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsStudioBookingController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsStudioEquipmentController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsStudioFacilityController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsStudioGalleryController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsStudioRentalController;
use App\Http\Controllers\Admin\Asdreams\AdminAsdreamsTeamController;
use App\Http\Controllers\AsdreamsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileEnquiryController;
use App\Http\Controllers\SponsorshipEnquiryController;
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
Route::get('/login', fn () => redirect()->route('idfr.home', ['auth' => 'login']))->name('login.view');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Password Reset Routes
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ==========================================
// Profile Booking Enquiry & Email OTP Routes
// ==========================================
Route::post('/enquiry/send-otp', [ProfileEnquiryController::class, 'sendOtp'])->name('enquiry.send-otp');
Route::post('/enquiry/verify-otp', [ProfileEnquiryController::class, 'verifyOtp'])->name('enquiry.verify-otp');
Route::post('/enquiry/submit', [ProfileEnquiryController::class, 'store'])->name('enquiry.submit');

// Sponsorship Inbound Enquiry Submission
Route::post('/sponsorship/enquiry', [SponsorshipEnquiryController::class, 'store'])->name('sponsorship.enquiry');

// ==========================================
// Main Landing Page (ASDreamStudio Homepage)
// ==========================================
Route::get('/', [AsdreamsController::class, 'home'])->name('landing');

// ==========================================
// 1. IDFR Brand Routes (Indian Designer Fashion Runway)
// ==========================================
Route::prefix('idfr')->name('idfr.')->group(function () {
    Route::get('/', [PageController::class, 'home'])->name('home');
    Route::get('/about', [PageController::class, 'about'])->name('about');
    Route::get('/models', [PageController::class, 'models'])->name('models');
    Route::get('/professionals', [PageController::class, 'professionals'])->name('professionals');
    Route::get('/professionals/{category}', [PageController::class, 'categoryListing'])->name('professionals.category');
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

    // Dynamic Category Profile & Save
    Route::get('/{category}', [ProfileController::class, 'showCreativeProfile'])->name('category');
    Route::post('/{category}', [ProfileController::class, 'handleSaveCreative'])->name('category.save');
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

    // Dynamic Offers
    Route::get('/offers', [AsdreamsController::class, 'offersIndex'])->name('offers.index');
    Route::get('/offers/{slug}', [AsdreamsController::class, 'offerShow'])->name('offers.show');
    Route::post('/offers/enquire', [AsdreamsController::class, 'handleOfferEnquiry'])->name('offers.enquire');

    // Contact Enquiry
    Route::post('/contact/enquire', [AsdreamsController::class, 'handleContactEnquiry'])->name('contact.enquire');

    // Bookings
    Route::post('/shoot-booking', [AsdreamsController::class, 'handleShootBooking'])->name('shoot-booking.submit');
    Route::post('/studio-booking', [AsdreamsController::class, 'handleStudioBooking'])->name('studio-booking.submit');
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
        Route::post('/seasons/{season}/activate', [AdminSeasonController::class, 'activate'])->name('seasons.activate');

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
        Route::get('/models/{model}', [AdminModelController::class, 'show'])->name('models.show');
        Route::get('/models/{model}/edit', [AdminModelController::class, 'edit'])->name('models.edit');
        Route::put('/models/{model}', [AdminModelController::class, 'update'])->name('models.update');
        Route::delete('/models/{model}', [AdminModelController::class, 'destroy'])->name('models.destroy');
        Route::post('/models/{model}/approval-status', [AdminModelController::class, 'updateApprovalStatus'])->name('models.approval-status');

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
        Route::get('/professionals/{professional}', [AdminCreativeProfessionalController::class, 'show'])->name('professionals.show');
        Route::get('/professionals/{professional}/edit', [AdminCreativeProfessionalController::class, 'edit'])->name('professionals.edit');
        Route::put('/professionals/{professional}', [AdminCreativeProfessionalController::class, 'update'])->name('professionals.update');
        Route::delete('/professionals/{professional}', [AdminCreativeProfessionalController::class, 'destroy'])->name('professionals.destroy');
        Route::post('/professionals/{professional}/approval-status', [AdminDynamicFormController::class, 'toggleApproval'])->name('professionals.approval-status');

        // Dynamic Form Builder & Category Field Management
        Route::get('/dynamic-forms', [AdminDynamicFormController::class, 'index'])->name('dynamic-forms.index');
        Route::get('/dynamic-forms/categories/create', [AdminDynamicFormController::class, 'createCategory'])->name('dynamic-forms.categories.create');
        Route::post('/dynamic-forms/categories', [AdminDynamicFormController::class, 'storeCategory'])->name('dynamic-forms.categories.store');
        Route::get('/dynamic-forms/categories/{category}/edit', [AdminDynamicFormController::class, 'editCategory'])->name('dynamic-forms.categories.edit');
        Route::put('/dynamic-forms/categories/{category}', [AdminDynamicFormController::class, 'updateCategory'])->name('dynamic-forms.categories.update');
        Route::delete('/dynamic-forms/categories/{category}', [AdminDynamicFormController::class, 'destroyCategory'])->name('dynamic-forms.categories.destroy');
        Route::post('/dynamic-forms/categories/{category}/toggle-status', [AdminDynamicFormController::class, 'toggleCategory'])->name('dynamic-forms.categories.toggle-status');
        Route::get('/dynamic-forms/categories/{category}/fields', [AdminDynamicFormController::class, 'categoryFields'])->name('dynamic-forms.categories.fields');
        Route::put('/dynamic-forms/categories/{category}/fields', [AdminDynamicFormController::class, 'updateCategoryFields'])->name('dynamic-forms.categories.fields.update');
        Route::post('/dynamic-forms/categories/{category}/fields/attach', [AdminDynamicFormController::class, 'attachFieldToCategory'])->name('dynamic-forms.categories.fields.attach');
        Route::delete('/dynamic-forms/categories/{category}/fields/{field}', [AdminDynamicFormController::class, 'detachFieldFromCategory'])->name('dynamic-forms.categories.fields.detach');
        Route::get('/dynamic-forms/preview/{category:slug}', [AdminDynamicFormController::class, 'preview'])->name('dynamic-forms.preview');

        // Form Fields Library
        Route::get('/dynamic-forms/fields', [AdminDynamicFormController::class, 'fields'])->name('dynamic-forms.fields.index');
        Route::get('/dynamic-forms/fields/create', [AdminDynamicFormController::class, 'createField'])->name('dynamic-forms.fields.create');
        Route::post('/dynamic-forms/fields', [AdminDynamicFormController::class, 'storeField'])->name('dynamic-forms.fields.store');
        Route::get('/dynamic-forms/fields/{field}/edit', [AdminDynamicFormController::class, 'editField'])->name('dynamic-forms.fields.edit');
        Route::put('/dynamic-forms/fields/{field}', [AdminDynamicFormController::class, 'updateField'])->name('dynamic-forms.fields.update');
        Route::delete('/dynamic-forms/fields/{field}', [AdminDynamicFormController::class, 'destroyField'])->name('dynamic-forms.fields.destroy');
        Route::post('/dynamic-forms/fields/{field}/toggle-status', [AdminDynamicFormController::class, 'toggleField'])->name('dynamic-forms.fields.toggle-status');

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

        // Sponsorship Enquiries (Full Management)
        Route::get('/sponsorship-enquiries', [AdminSponsorshipEnquiryController::class, 'index'])->name('sponsorship-enquiries.index');
        Route::get('/sponsorship-enquiries/{enquiry}', [AdminSponsorshipEnquiryController::class, 'show'])->name('sponsorship-enquiries.show');
        Route::put('/sponsorship-enquiries/{enquiry}', [AdminSponsorshipEnquiryController::class, 'update'])->name('sponsorship-enquiries.update');
        Route::delete('/sponsorship-enquiries/{enquiry}', [AdminSponsorshipEnquiryController::class, 'destroy'])->name('sponsorship-enquiries.destroy');

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
        Route::post('/registrations/{user}/approval-status', [AdminRegistrationController::class, 'updateApprovalStatus'])->name('registrations.approval-status');
        Route::post('/registrations/{user}/toggle-status', [AdminRegistrationController::class, 'toggleStatus'])->name('registrations.toggle-status');
        Route::delete('/registrations/{user}', [AdminRegistrationController::class, 'destroy'])->name('registrations.destroy');
        Route::get('/registrations-alias', fn () => redirect()->route('admin.registrations.index'))->name('registrations');

        // Season Model Registrations Management
        Route::get('/season-registrations', [AdminSeasonRegistrationController::class, 'index'])->name('season-registrations.index');
        Route::get('/season-registrations/export', [AdminSeasonRegistrationController::class, 'export'])->name('season-registrations.export');
        Route::get('/season-registrations/{registration}', [AdminSeasonRegistrationController::class, 'show'])->name('season-registrations.show');
        Route::get('/season-registrations/{registration}/edit', [AdminSeasonRegistrationController::class, 'edit'])->name('season-registrations.edit');
        Route::put('/season-registrations/{registration}', [AdminSeasonRegistrationController::class, 'update'])->name('season-registrations.update');
        Route::delete('/season-registrations/{registration}', [AdminSeasonRegistrationController::class, 'destroy'])->name('season-registrations.destroy');
        Route::post('/season-registrations/{registration}/payments', [AdminSeasonRegistrationController::class, 'storePayment'])->name('season-registrations.payments.store');
        Route::delete('/season-registrations/payments/{payment}', [AdminSeasonRegistrationController::class, 'destroyPayment'])->name('season-registrations.payments.destroy');

        // Season Registration Prices Management
        Route::get('/season-prices', [AdminSeasonRegistrationController::class, 'pricesIndex'])->name('season-prices.index');
        Route::post('/season-prices', [AdminSeasonRegistrationController::class, 'pricesStore'])->name('season-prices.store');
        Route::put('/season-prices/{price}', [AdminSeasonRegistrationController::class, 'pricesUpdate'])->name('season-prices.update');
        Route::delete('/season-prices/{price}', [AdminSeasonRegistrationController::class, 'pricesDestroy'])->name('season-prices.destroy');

        // Dedicated Payment Management
        Route::get('/season-payments', [AdminSeasonRegistrationController::class, 'paymentsIndex'])->name('season-payments.index');

        // Other Module routes (Placeholders)
        Route::get('/settings', fn () => app(AdminDashboardController::class)->placeholder('settings'))->name('settings');

        // ==========================================
        // ASDreamStudio Dedicated Management Module
        // ==========================================
        Route::prefix('asdreams')->name('asdreams.')->group(function () {
            // 1. Dashboard
            Route::get('/', [AdminAsdreamsDashboardController::class, 'index'])->name('dashboard');

            // 2. Homepage Banner / Slider
            Route::resource('banners', AdminAsdreamsBannerController::class)->except(['show']);
            Route::post('banners/{banner}/toggle-status', [AdminAsdreamsBannerController::class, 'toggleStatus'])->name('banners.toggle-status');

            // 3. Homepage Brands
            Route::resource('brands', AdminAsdreamsBrandController::class)->except(['show']);
            Route::post('brands/{brand}/toggle-status', [AdminAsdreamsBrandController::class, 'toggleStatus'])->name('brands.toggle-status');

            // 4. Offer Management & Offer Details
            Route::resource('offers', AdminAsdreamsOfferController::class)->except(['show']);
            Route::post('offers/{offer}/toggle-status', [AdminAsdreamsOfferController::class, 'toggleStatus'])->name('offers.toggle-status');

            // 6. Gallery Management
            Route::get('gallery', [AdminAsdreamsGalleryController::class, 'index'])->name('gallery.index');
            Route::get('gallery/create', [AdminAsdreamsGalleryController::class, 'createImage'])->name('gallery.create');
            Route::post('gallery', [AdminAsdreamsGalleryController::class, 'storeImage'])->name('gallery.store');
            Route::get('gallery/{image}/edit', [AdminAsdreamsGalleryController::class, 'editImage'])->name('gallery.edit');
            Route::put('gallery/{image}', [AdminAsdreamsGalleryController::class, 'updateImage'])->name('gallery.update');
            Route::delete('gallery/{image}', [AdminAsdreamsGalleryController::class, 'destroyImage'])->name('gallery.destroy');
            Route::post('gallery/{image}/toggle-status', [AdminAsdreamsGalleryController::class, 'toggleImageStatus'])->name('gallery.toggle-status');
            Route::post('gallery/categories', [AdminAsdreamsGalleryController::class, 'storeCategory'])->name('gallery.categories.store');
            Route::delete('gallery/categories/{category}', [AdminAsdreamsGalleryController::class, 'destroyCategory'])->name('gallery.categories.destroy');

            // 7. Team Management
            Route::resource('team', AdminAsdreamsTeamController::class)->except(['show']);
            Route::post('team/{team}/toggle-status', [AdminAsdreamsTeamController::class, 'toggleStatus'])->name('team.toggle-status');

            // 8. Contact Page Management
            Route::get('contact', [AdminAsdreamsContactController::class, 'index'])->name('contact.index');
            Route::post('contact', [AdminAsdreamsContactController::class, 'update'])->name('contact.update');

            // 9. Enquiry Management (Offer & Contact)
            Route::get('enquiries', [AdminAsdreamsEnquiryController::class, 'index'])->name('enquiries.index');
            Route::get('enquiries/offers/{enquiry}', [AdminAsdreamsEnquiryController::class, 'showOffer'])->name('enquiries.show-offer');
            Route::put('enquiries/offers/{enquiry}', [AdminAsdreamsEnquiryController::class, 'updateOfferStatus'])->name('enquiries.update-offer-status');
            Route::delete('enquiries/offers/{enquiry}', [AdminAsdreamsEnquiryController::class, 'destroyOffer'])->name('enquiries.destroy-offer');
            Route::get('enquiries/contact/{enquiry}', [AdminAsdreamsEnquiryController::class, 'showContact'])->name('enquiries.show-contact');
            Route::put('enquiries/contact/{enquiry}', [AdminAsdreamsEnquiryController::class, 'updateContactStatus'])->name('enquiries.update-contact-status');
            Route::delete('enquiries/contact/{enquiry}', [AdminAsdreamsEnquiryController::class, 'destroyContact'])->name('enquiries.destroy-contact');

            // 10. Shoot Booking Management
            Route::get('shoot-bookings', [AdminAsdreamsShootBookingController::class, 'index'])->name('shoot-bookings.index');
            Route::get('shoot-bookings/{booking}', [AdminAsdreamsShootBookingController::class, 'show'])->name('shoot-bookings.show');
            Route::match(['put', 'patch'], 'shoot-bookings/{booking}', [AdminAsdreamsShootBookingController::class, 'updateStatus'])->name('shoot-bookings.update-status');
            Route::match(['put', 'patch'], 'shoot-bookings/{booking}/status', [AdminAsdreamsShootBookingController::class, 'updateStatus'])->name('shoot-bookings.status');
            Route::delete('shoot-bookings/{booking}', [AdminAsdreamsShootBookingController::class, 'destroy'])->name('shoot-bookings.destroy');

            // 11. Studio Booking Enquiries
            Route::get('studio-bookings', [AdminAsdreamsStudioBookingController::class, 'index'])->name('studio-bookings.index');
            Route::get('studio-bookings/{booking}', [AdminAsdreamsStudioBookingController::class, 'show'])->name('studio-bookings.show');
            Route::match(['put', 'patch'], 'studio-bookings/{booking}', [AdminAsdreamsStudioBookingController::class, 'updateStatus'])->name('studio-bookings.update-status');
            Route::match(['put', 'patch'], 'studio-bookings/{booking}/status', [AdminAsdreamsStudioBookingController::class, 'updateStatus'])->name('studio-bookings.status');
            Route::delete('studio-bookings/{booking}', [AdminAsdreamsStudioBookingController::class, 'destroy'])->name('studio-bookings.destroy');

            // 12. Our Clients Management
            Route::resource('clients', AdminAsdreamsClientController::class)->except(['show']);
            Route::post('clients/{client}/toggle-status', [AdminAsdreamsClientController::class, 'toggleStatus'])->name('clients.toggle-status');
            Route::post('clients/{client}/set-featured', [AdminAsdreamsClientController::class, 'setFeatured'])->name('clients.set-featured');

            // 13. Moving Client Logos Management
            Route::resource('moving-logos', AdminAsdreamsMovingLogoController::class)->except(['show']);
            Route::post('moving-logos/{movingLogo}/toggle-status', [AdminAsdreamsMovingLogoController::class, 'toggleStatus'])->name('moving-logos.toggle-status');

            // 14. Studio Rental Management
            Route::get('studio-rental', [AdminAsdreamsStudioRentalController::class, 'index'])->name('studio-rental.index');
            Route::match(['post', 'put'], 'studio-rental/pricing', [AdminAsdreamsStudioRentalController::class, 'updatePricingSettings'])->name('studio-rental.pricing.update');

            Route::post('studio-facilities/settings', [AdminAsdreamsStudioFacilityController::class, 'updateSettings'])->name('studio-facilities.settings');
            Route::resource('studio-facilities', AdminAsdreamsStudioFacilityController::class)
                ->except(['show'])
                ->parameters(['studio-facilities' => 'facility']);
            Route::post('studio-facilities/{facility}/toggle-status', [AdminAsdreamsStudioFacilityController::class, 'toggleStatus'])->name('studio-facilities.toggle-status');

            Route::post('studio-equipment/settings', [AdminAsdreamsStudioEquipmentController::class, 'updateSettings'])->name('studio-equipment.settings');
            Route::resource('studio-equipment', AdminAsdreamsStudioEquipmentController::class)
                ->except(['show'])
                ->parameters(['studio-equipment' => 'equipment']);
            Route::post('studio-equipment/{equipment}/toggle-status', [AdminAsdreamsStudioEquipmentController::class, 'toggleStatus'])->name('studio-equipment.toggle-status');

            Route::resource('studio-gallery', AdminAsdreamsStudioGalleryController::class)
                ->except(['show'])
                ->parameters(['studio-gallery' => 'studioGallery']);
            Route::post('studio-gallery/{studioGallery}/toggle-status', [AdminAsdreamsStudioGalleryController::class, 'toggleStatus'])->name('studio-gallery.toggle-status');

            // 15. Website Settings
            Route::get('settings', [AdminAsdreamsSettingController::class, 'index'])->name('settings.index');
            Route::match(['post', 'put'], 'settings', [AdminAsdreamsSettingController::class, 'update'])->name('settings.update');
        });
    });
});

// ==========================================
// 5. Aliases & Legacy HTML Redirections (All 21 Original Pages)
// ==========================================
// IDFR Core Pages
Route::redirect('/home', '/');
Route::redirect('/index.html', '/');
Route::redirect('/idfr_about.html', '/idfr/about');
Route::redirect('/idfr_models.html', '/idfr/models');
Route::redirect('/professionals.html', '/idfr/professionals');
Route::redirect('/idfr_gallery.html', '/idfr/gallery');
Route::redirect('/idfr_contact.html', '/idfr/contact');
Route::redirect('/ticket_booking.html', '/idfr/tickets');

// Backward compatibility redirects for previous root-level IDFR paths:
Route::redirect('/models', '/idfr/models');
Route::redirect('/professionals', '/idfr/professionals');
Route::redirect('/tickets', '/idfr/tickets');
Route::redirect('/about', '/idfr/about');
Route::redirect('/gallery', '/idfr/gallery');
Route::redirect('/contact', '/idfr/contact');

// ASDreamStudio Pages
Route::redirect('/asdreams.html', '/asdreams');
Route::redirect('/asd_about.html', '/asdreams/about');
Route::redirect('/asd_gallery.html', '/asdreams/gallery');
Route::redirect('/asd_contact.html', '/asdreams/contact');
Route::redirect('/team_members.html', '/asdreams/team');
Route::redirect('/team', '/asdreams/team');
Route::redirect('/offer_details.html', '/asdreams/offers');
Route::redirect('/contact_brand.html', '/asdreams/contact');

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
