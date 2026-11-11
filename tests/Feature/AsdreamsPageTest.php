<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsBanner;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\Asdreams\AsdreamsOfferEnquiry;
use App\Models\User;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AsdreamsPageTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_asdreams@idfr.example'],
            [
                'name' => 'IDFR Executive Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    /**
     * Test ASDreams home page loads successfully with the Brands Popup slider and all required elements.
     */
    public function test_asdreams_home_page_loads_with_brands_popup_and_slider(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Brands popup modal and banner
        $response->assertSee('id="brandsPopup"', false);
        $response->assertSee('class="brands-pop__banner"', false);
        $response->assertSee('id="brandsPopSlides"', false);

        // Slide counter and live status (dots navigation removed, counter only)
        $response->assertSee('id="brandsPopCurrent"', false);
        $response->assertSee('id="brandsPopTotal"', false);
        $response->assertSee('id="brandsPopStatus"', false);
        $response->assertSee('id="brandsPopDots"', false);
        $response->assertDontSee('data-banner-dot', false);

        // Navigation arrows
        $response->assertSee('brands-pop__arrow--prev', false);
        $response->assertSee('brands-pop__arrow--next', false);

        // Slide titles and metas
        $response->assertSee('Behind the', false);
        $response->assertSee('Viewfinder', false);
        $response->assertSee('Portrait Session', false);

        // Every offer details CTA link points directly to its offer URL without #home or redirects
        $expectedSlugs = [
            'viewfinder',
            'polaroids',
            'first-look',
            'studio-light',
            'street-frames',
            'golden-vows',
            'above-clouds',
        ];

        foreach ($expectedSlugs as $slug) {
            $expectedUrl = route('asd.offers.show', $slug);
            $response->assertSee('href="'.$expectedUrl.'"', false);
        }

        // None of the offer CTAs use an intermediate #home anchor
        $response->assertDontSee('class="brands-pop__cta" href="#home"', false);
        $response->assertDontSee('class="brands-pop__cta" href="/asdreams#home"', false);

        // 4 brand cards and their direct URLs
        $response->assertSee('ASDreamStudio', false);
        $response->assertSee('href="/asdreams"', false);
        $response->assertSee('images/logo/idfr-logo.png', false);
        $response->assertSee('href="/idfr"', false);
        $response->assertSee('Athul', false);
        $response->assertSee('href="/asdreams/contact?brand=athuls"', false);
        $response->assertSee('Lakme', false);
        $response->assertSee('href="/asdreams/contact?brand=lakme"', false);
        $response->assertDontSee('Beauty &amp; Wellness', false);

        // All required modals
        $response->assertSee('id="asdReel"', false);
        $response->assertSee('id="shootBookModal"', false);
        $response->assertSee('id="studioBookModal"', false);

        // Studio rental section
        $response->assertSee('id="studio_rental"', false);
        $response->assertSee('Book Studio for Rent at', false);
        $response->assertSee('Facilities Provided', false);
        $response->assertSee('Equipment Included', false);
    }

    /**
     * Test ASDreams contact page loads successfully and contains all four brand cards with arrows.
     */
    public function test_asdreams_contact_page_loads_with_brand_cards(): void
    {
        $response = $this->get('/asdreams/contact');

        $response->assertStatus(200);

        // Check Our Brands section inside brandsPopup modal on contact page
        $response->assertSee('id="brandsPopup"', false);
        $response->assertSee('class="brands-pop__grid"', false);

        // Card 1: ASDreamStudio
        $response->assertSee('images/asdreams/asd-logo.png', false);
        $response->assertSee('ASDreamStudio', false);
        $response->assertSee('Photography &amp; Videography', false);

        // Card 2: IDFR
        $response->assertSee('brands-pop__logo--wide', false);
        $response->assertSee('images/logo/idfr-logo.png', false);
        $response->assertSee('IDFR', false);
        $response->assertSee('Indian Designer Fashion Runway', false);

        // Card 3: Athul's Academy
        $response->assertSee('brands-pop__logo--tall', false);
        $response->assertSee('athuls-academy-logo.png', false);
        $response->assertSee('Athul', false);
        $response->assertSee('Modelling &amp; Grooming', false);

        // Card 4: Lakme
        $response->assertSee('brands-pop__logo--lakme', false);
        $response->assertSee('Lakme', false);
        $response->assertDontSee('Beauty &amp; Wellness', false);

        // Arrow button in every card
        $response->assertSee('brands-pop__go', false);
    }

    /**
     * Test all brand card destination pages load directly without intermediate redirects.
     */
    public function test_brand_card_destination_pages_load_directly(): void
    {
        // 1. IDFR brand card destination
        $idfrResponse = $this->get('/idfr');
        $idfrResponse->assertStatus(200);

        // 2. Athul's Academy brand card destination
        $athulResponse = $this->get('/asdreams/contact?brand=athuls');
        $athulResponse->assertStatus(200);
        $athulResponse->assertSee('Athul', false);
        $athulResponse->assertSee('Modelling &amp; Grooming', false);
        $athulResponse->assertSee('Back to Home', false);
        $athulResponse->assertDontSee('id="brandsPopup"', false);

        // 3. Lakme brand card destination
        $lakmeResponse = $this->get('/asdreams/contact?brand=lakme');
        $lakmeResponse->assertStatus(200);
        $lakmeResponse->assertSee('Lakm', false);
        $lakmeResponse->assertSee('Beauty &amp; Wellness', false);
        $lakmeResponse->assertSee('Back to Home', false);
        $lakmeResponse->assertDontSee('id="brandsPopup"', false);

        // 4. ASDreamStudio brand card destination
        $asdResponse = $this->get('/asdreams');
        $asdResponse->assertStatus(200);
    }

    /**
     * Test legacy redirections to /idfr.
     */
    public function test_legacy_url_redirections(): void
    {
        $this->get('/models')->assertRedirect('/idfr/models');
        $this->get('/professionals')->assertRedirect('/idfr/professionals');
        $this->get('/tickets')->assertRedirect('/idfr/tickets');
        $this->get('/about')->assertRedirect('/idfr/about');
        $this->get('/gallery')->assertRedirect('/idfr/gallery');
        $this->get('/contact')->assertRedirect('/idfr/contact');
    }

    /**
     * Test ASDreams About, Gallery, and Team pages load successfully.
     */
    public function test_asdreams_subpages_load(): void
    {
        $aboutResponse = $this->get('/asdreams/about')->assertStatus(200);
        $aboutResponse->assertSee('About Us')
            ->assertSee('id="siteHeader"', false)
            ->assertSee('Behind the Lens,')
            ->assertSee('Ahead of the Curve')
            ->assertSee('Studio 01')
            ->assertSee('Kozhikode, Kerala')
            ->assertSee('images/asdreams/ab2.png', false)
            ->assertSee('images/asdreams/about-video.jpg', false)
            ->assertSee('images/asdreams/about-studio.jpg', false);

        $this->get('/asdreams/gallery')->assertStatus(200)->assertSee('Gallery')->assertSee('id="siteHeader"', false);
        $this->get('/asdreams/team')->assertStatus(200)->assertSee('Team')->assertSee('id="siteHeader"', false);
    }

    /**
     * Test ASDreams Offers index and detail pages.
     */
    public function test_asdreams_offers_pages(): void
    {
        // Default offers index must not render offer selection dropdown
        $response = $this->get('/asdreams/offers');
        $response->assertStatus(200);
        $response->assertSee('Behind the');
        $response->assertSee('Viewfinder');
        $this->assertNoSelectInsideOfferEnquiryForm($response->getContent());
        $response->assertDontSee('for="enqOffer"', false);
        $response->assertSee('type="hidden" name="offer_id"', false);
        $response->assertDontSee('id="brandsPopup"', false);
        $response->assertDontSee('brands-pop-first', false);
        $response->assertDontSee('id="siteHeader"', false);
        $response->assertDontSee('data-shoot-book', false);
        $response->assertSee('Back to Home', false);

        // All 7 individual offer URLs
        $allOffers = [
            'viewfinder' => 'Behind the',
            'polaroids' => 'Prints &amp;',
            'first-look' => 'The First',
            'studio-light' => 'Studio',
            'street-frames' => 'Street',
            'golden-vows' => 'Golden',
            'above-clouds' => 'Above the',
        ];

        foreach ($allOffers as $slug => $heading) {
            $showResponse = $this->get('/asdreams/offers/'.$slug);
            $showResponse->assertStatus(200);
            $showResponse->assertSee('data-offer="'.$slug.'"', false);
            $showResponse->assertSee($heading, false);
            $showResponse->assertSee('id="asdEnquiryForm"', false);
            $showResponse->assertSee('class="profile__details asd-contact__form', false);
            $showResponse->assertSee('offer-page__layout', false);
            $showResponse->assertSee('Enquire about', false);
            $showResponse->assertSee('this offer', false);
            $showResponse->assertSee('id="enqName"', false);
            $showResponse->assertSee('id="enqPhone"', false);
            $showResponse->assertSee('id="enqEmail"', false);
            $showResponse->assertSee('id="enqDate"', false);
            $showResponse->assertSee('id="enqOffer"', false);
            $showResponse->assertSee('type="hidden" name="offer" id="enqOffer" value="'.$slug.'"', false);
            $this->assertNoSelectInsideOfferEnquiryForm($showResponse->getContent());
            $showResponse->assertDontSee('for="enqOffer"', false);
            $showResponse->assertSee('id="enqMessage"', false);
            $showResponse->assertSee('getOfferFromUrl', false);
            $showResponse->assertDontSee('id="brandsPopup"', false);
            $showResponse->assertDontSee('brands-pop-first', false);
            $showResponse->assertDontSee('id="siteHeader"', false);
            $showResponse->assertDontSee('data-shoot-book', false);
            $showResponse->assertSee('Back to Home', false);
        }

        // Query parameter ?offer=first-look
        $queryResponse = $this->get('/asdreams/offers?offer=first-look');
        $queryResponse->assertStatus(200);
        $queryResponse->assertSee('data-offer="first-look"', false);

        // Invalid slug fallback defaults to Behind the Viewfinder without 404
        $fallbackResponse = $this->get('/asdreams/offers/non-existent-offer');
        $fallbackResponse->assertStatus(200);
        $fallbackResponse->assertSee('Behind the');
    }

    /**
     * Test client offer enquiry form submission.
     */
    public function test_asdreams_offer_enquiry_submission(): void
    {
        $offer = AsdreamsOffer::first();

        $payload = [
            'offer' => $offer ? $offer->slug : 'viewfinder',
            'name' => 'Aarav Patel',
            'phone' => '+91 9845012345',
            'email' => 'aarav@example.com',
            'date' => '2026-12-20',
            'message' => 'Interested in the bridal portrait session package.',
        ];

        $response = $this->post(route('asd.offers.enquire'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('asdreams_offer_enquiries', [
            'name' => 'Aarav Patel',
            'phone' => '+91 9845012345',
            'email' => 'aarav@example.com',
        ]);
    }

    /**
     * Test contact page enquiry form submission.
     */
    public function test_asdreams_contact_enquiry_submission(): void
    {
        $payload = [
            'name' => 'Priya Nair',
            'phone' => '+91 9745123456',
            'email' => 'priya@example.com',
            'subject' => 'Fashion Editorial Shoot',
            'message' => 'Looking to shoot our upcoming winter fashion collection.',
        ];

        $response = $this->post(route('asd.contact.enquire'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('asdreams_contact_enquiries', [
            'name' => 'Priya Nair',
            'phone' => '+91 9745123456',
            'subject' => 'Fashion Editorial Shoot',
        ]);
    }

    /**
     * Test Athul's Academy and Lakme Academy brand enquiry submissions and admin filtering.
     */
    public function test_asdreams_brand_enquiry_submissions_and_admin_filtering(): void
    {
        // 1. Athul's Academy enquiry
        $athulPayload = [
            'brand' => 'athuls',
            'name' => 'Kavya Suresh',
            'phone' => '+91 9847111222',
            'email' => 'kavya@example.com',
            'subject' => 'Weekend batch enquiry',
            'message' => 'Interested in the beginners runway and grooming programme.',
        ];

        $athulResponse = $this->post(route('asd.contact.enquire'), $athulPayload);
        $athulResponse->assertRedirect();
        $athulResponse->assertSessionHas('success');

        $this->assertDatabaseHas('asdreams_contact_enquiries', [
            'brand' => 'athuls',
            'name' => 'Kavya Suresh',
            'phone' => '+91 9847111222',
        ]);

        // 2. Lakme Academy enquiry
        $lakmePayload = [
            'brand' => 'lakme',
            'name' => 'Ananya Sharma',
            'phone' => '+91 9847333444',
            'email' => 'ananya@example.com',
            'subject' => 'Makeup artistry course',
            'message' => 'Tell me more about the 16-week professional makeup certification.',
        ];

        $lakmeResponse = $this->post(route('asd.contact.enquire'), $lakmePayload);
        $lakmeResponse->assertRedirect();
        $lakmeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('asdreams_contact_enquiries', [
            'brand' => 'lakme',
            'name' => 'Ananya Sharma',
            'phone' => '+91 9847333444',
        ]);

        // 3. Admin can view and filter contact enquiries by brand
        $adminIndex = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.enquiries.index', ['type' => 'contact']));
        $adminIndex->assertStatus(200);
        $adminIndex->assertSee("Athul's Academy", false);
        $adminIndex->assertSee('Lakm', false);
        $adminIndex->assertSee('name="brand"', false);

        // Filter by athuls
        $athulFiltered = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.enquiries.index', ['type' => 'contact', 'brand' => 'athuls']));
        $athulFiltered->assertStatus(200);
        $athulFiltered->assertSee('Kavya Suresh', false);
        $athulFiltered->assertDontSee('Ananya Sharma', false);
    }

    /**
     * Test photoshoot booking submission via AJAX / form POST.
     */
    public function test_asdreams_shoot_booking_submission(): void
    {
        $payload = [
            'name' => 'Test Client',
            'phone' => '+91 9876543210',
            'email' => 'client@example.com',
            'service_type' => 'Fashion Portfolio',
            'shoot_date' => '2026-12-15',
            'notes' => 'Looking for full day studio fashion shoot',
        ];

        $response = $this->postJson(route('asd.shoot-booking.submit'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('asdreams_shoot_bookings', [
            'name' => 'Test Client',
            'phone' => '+91 9876543210',
        ]);
    }

    /**
     * Test photoshoot booking validation errors.
     */
    public function test_asdreams_shoot_booking_validation(): void
    {
        $response = $this->postJson(route('asd.shoot-booking.submit'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone']);
    }

    /**
     * Test studio rental booking submission via AJAX / form POST.
     */
    public function test_asdreams_studio_booking_submission(): void
    {
        $payload = [
            'name' => 'Studio Renter',
            'phone' => '+91 9988776655',
            'email' => 'renter@example.com',
            'hire_date' => '2026-11-20',
            'duration' => 'Full Day (8 Hours)',
            'production_type' => 'Commercial Video & Stills',
            'notes' => 'Need lighting kit and makeup suite',
        ];

        $response = $this->postJson(route('asd.studio-booking.submit'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test legacy static HTML redirects to proper Laravel routes.
     */
    public function test_asdreams_legacy_redirects(): void
    {
        $this->get('/asdreams.html')->assertRedirect('/asdreams');
        $this->get('/offer_details.html')->assertRedirect('/asdreams/offers');
        $this->get('/contact_brand.html')->assertRedirect('/asdreams/contact');
    }

    /**
     * Test guest cannot access admin ASDreamStudio portal.
     */
    public function test_guest_cannot_access_asdreams_admin(): void
    {
        $this->get(route('admin.asdreams.dashboard'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.banners.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.offers.index'))->assertRedirect(route('admin.login'));
    }

    /**
     * Test authenticated admin can access ASDreamStudio management sections.
     */
    public function test_admin_can_access_asdreams_modules(): void
    {
        $routes = [
            route('admin.asdreams.dashboard'),
            route('admin.asdreams.banners.index'),
            route('admin.asdreams.brands.index'),
            route('admin.asdreams.offers.index'),
            route('admin.asdreams.gallery.index'),
            route('admin.asdreams.team.index'),
            route('admin.asdreams.contact.index'),
            route('admin.asdreams.enquiries.index'),
            route('admin.asdreams.shoot-bookings.index'),
            route('admin.asdreams.settings.index'),
        ];

        $this->assertFalse(Route::has('admin.asdreams.about.index'));
        $this->assertFalse(Route::has('admin.asdreams.about.update'));

        foreach ($routes as $url) {
            $response = $this->actingAs($this->adminUser, 'admin')->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('<span>About Page</span>', false);
        }
    }

    /**
     * Test ASDreamStudio header has Book a Shoot button and NO Login button on both desktop and mobile,
     * while IDFR retains its Login and Register functionality.
     */
    public function test_asdreams_header_has_book_shoot_and_no_login_button(): void
    {
        // 1. Check ASDreamStudio landing & subpages
        foreach (['/', '/asdreams', '/asdreams/about', '/asdreams/gallery', '/asdreams/team'] as $path) {
            $response = $this->get($path);
            $response->assertStatus(200);

            // Must have Book a Shoot
            $response->assertSee('data-shoot-book', false);
            $response->assertSee('Book a Shoot', false);

            // Must NOT have Login button in desktop or mobile navigation
            $response->assertDontSee('class="nav__login"', false);
            $response->assertDontSee('data-auth="login"', false);
        }

        // 2. IDFR homepage must retain its login & register buttons
        $idfrResponse = $this->get('/idfr');
        $idfrResponse->assertStatus(200);
        $idfrResponse->assertSee('data-auth="login"', false);
        $idfrResponse->assertSee('data-auth="register"', false);
    }

    /**
     * Test admin can create a new dynamic banner with integrated offer details in a single form.
     */
    public function test_admin_can_create_banner_with_dedicated_offer_details(): void
    {
        $payload = [
            'title' => 'Couture Editorial',
            'highlight_title' => 'Couture Fashion',
            'label' => 'HIGH FASHION · EDITORIAL',
            'description' => 'A high-impact cinematic fashion session.',
            'image' => UploadedFile::fake()->image('couture_banner.jpg', 1920, 1080),
            'order' => 15,
            'is_active' => 1,
            'button_text' => 'Offer Details',

            // Offer Details (Section 2)
            'offer_title' => 'Couture Fashion Runway Special',
            'offer_category_label' => 'FASHION &amp; RUNWAY',
            'offer_lead_text' => 'Designed for luxury fashion brands and high-end portfolios.',
            'offer_full_description' => 'Comprehensive editorial production including hair, makeup and creative direction.',
            'offer_price' => '₹45,000 + GST',
            'offer_validity' => 'Valid until 31 Dec 2026',
            'offer_notes' => 'Advance booking of 7 days required.',
            'offer_terms' => '50% non-refundable deposit upon booking.',
            'offer_important_notes' => 'Includes 2 wardrobe changes and lookbook license.',
            'offer_features' => [
                'Full runway styling and creative direction',
                '6 hours in studio with high-end strobes',
                '30 retouched high-resolution images',
            ],
        ];

        $response = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.banners.store'), $payload);

        $response->assertRedirect(route('admin.asdreams.banners.index'));
        $response->assertSessionHas('success');

        // Verify banner saved in DB
        $banner = AsdreamsBanner::where('title', 'Couture Editorial')->first();
        $this->assertNotNull($banner);
        $this->assertEquals('HIGH FASHION · EDITORIAL', $banner->label);
        $this->assertEquals(15, $banner->order);
        $this->assertTrue((bool) $banner->is_active);

        // Verify linked offer saved in DB with all fields
        $offer = AsdreamsOffer::where('slug', 'couture-fashion-runway-special')->first();
        $this->assertNotNull($offer);
        $this->assertEquals($banner->id, $offer->banner_id);
        $this->assertEquals($offer->id, $banner->linked_offer_id);
        $this->assertEquals('₹45,000 + GST', $offer->price);
        $this->assertEquals('Valid until 31 Dec 2026', $offer->validity);
        $this->assertEquals('50% non-refundable deposit upon booking.', $offer->terms);
        $this->assertEquals('Includes 2 wardrobe changes and lookbook license.', $offer->important_notes);

        // Verify features saved in DB
        $features = $offer->features()->pluck('feature_text')->all();
        $this->assertContains('Full runway styling and creative direction', $features);
        $this->assertContains('6 hours in studio with high-end strobes', $features);
        $this->assertContains('30 retouched high-resolution images', $features);

        // Clean up created file if stored
        if ($banner->image && file_exists(public_path($banner->image))) {
            @unlink(public_path($banner->image));
        }
    }

    /**
     * Test dynamic banner appears in the homepage slider with direct offer link and details.
     */
    public function test_dynamic_banner_appears_in_homepage_slider_and_navigates_to_offer(): void
    {
        // 1. Create a dynamic banner with linked offer
        $banner = AsdreamsBanner::create([
            'title' => 'Fine Art Portrait',
            'highlight_title' => 'Master Collection',
            'label' => 'EXCLUSIVE · FINE ART',
            'description' => 'Timeless large-format black & white portraits.',
            'image' => 'images/asdreams/hero-01.jpg',
            'order' => 12,
            'is_active' => true,
            'button_text' => 'Offer Details',
        ]);

        $offer = AsdreamsOffer::create([
            'banner_id' => $banner->id,
            'title' => 'Master Fine Art Portrait Collection',
            'slug' => 'master-fine-art-portrait',
            'category_label' => 'EXCLUSIVE · FINE ART',
            'heading_main' => 'Fine Art Portrait',
            'heading_highlight' => 'Master Collection',
            'lead_text' => 'Timeless large-format black & white portraits.',
            'full_description' => 'Museum quality giclee prints crafted with archival inks.',
            'price' => '₹65,000 all inclusive',
            'validity' => 'Limited to 10 clients per month',
            'notes' => 'Frame consultation included.',
            'terms' => 'Studio booking requires 50% deposit.',
            'important_notes' => 'Proofing session scheduled within 48 hours.',
            'order' => 12,
            'status' => 'published',
        ]);

        $offer->features()->createMany([
            ['feature_text' => 'Handcrafted museum canvas print (24x36)', 'order' => 1],
            ['feature_text' => 'Bespoke medium format monochrome workflow', 'order' => 2],
        ]);

        $banner->update([
            'linked_offer_id' => $offer->id,
            'linked_offer_slug' => $offer->slug,
        ]);

        // 2. Check homepage slider renders the dynamic banner
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Fine Art Portrait', false);
        $homeResponse->assertSee('Master Collection', false);
        $homeResponse->assertSee('EXCLUSIVE · FINE ART', false);

        $expectedUrl = route('asd.offers.show', 'master-fine-art-portrait');
        $homeResponse->assertSee('href="'.$expectedUrl.'"', false);

        // 3. Visit the dynamic offer details page directly
        $offerResponse = $this->get('/asdreams/offers/master-fine-art-portrait');
        $offerResponse->assertStatus(200);
        $offerResponse->assertSee('Master Fine Art Portrait Collection', false);
        $offerResponse->assertSee('EXCLUSIVE · FINE ART', false);
        $offerResponse->assertSee('₹65,000 all inclusive', false);
        $offerResponse->assertSee('Limited to 10 clients per month', false);
        $offerResponse->assertSee('Handcrafted museum canvas print (24x36)', false);
        $offerResponse->assertSee('Bespoke medium format monochrome workflow', false);
        $offerResponse->assertSee('Studio booking requires 50% deposit.', false);
        $offerResponse->assertSee('Proofing session scheduled within 48 hours.', false);

        // Auto-populated hidden offer input for the viewed Offer Details page
        $this->assertNoSelectInsideOfferEnquiryForm($offerResponse->getContent());
        $offerResponse->assertSee('type="hidden" name="offer" id="enqOffer" value="master-fine-art-portrait"', false);
        $offerResponse->assertSee('type="hidden" name="offer_id" id="enqOfferId" value="'.$offer->id.'"', false);
    }

    /**
     * Test multiple offer details pages omit any offer dropdown and auto-bind the active offer on submission.
     */
    public function test_offer_details_enquiry_auto_identifies_offer_without_dropdown(): void
    {
        $lakmeOffer = AsdreamsOffer::create([
            'title' => 'The Lakmé Beauty Experience',
            'slug' => 'lakme-beauty-experience',
            'category_label' => 'BEAUTY · STYLING',
            'heading_main' => 'The Lakmé',
            'heading_highlight' => 'Beauty Experience',
            'lead_text' => 'Bridal and editorial makeup artistry.',
            'price' => '₹35,000',
            'status' => 'published',
        ]);

        // 1. Newly created offer page renders without dropdown and auto-binds hidden offer & offer_id
        $lakmePage = $this->get('/asdreams/offers/lakme-beauty-experience');
        $lakmePage->assertStatus(200);
        $this->assertNoSelectInsideOfferEnquiryForm($lakmePage->getContent());
        $lakmePage->assertDontSee('for="enqOffer"', false);
        $lakmePage->assertSee('type="hidden" name="offer_id" id="enqOfferId" value="'.$lakmeOffer->id.'"', false);
        $lakmePage->assertSee('type="hidden" name="offer" id="enqOffer" value="lakme-beauty-experience"', false);

        // 2. Submitting form payload sends active offer_id & slug and saves exact offer details to DB
        $submitRes = $this->postJson(route('asd.offers.enquire'), [
            'offer_id' => $lakmeOffer->id,
            'offer' => 'lakme-beauty-experience',
            'name' => 'Divya Nair',
            'phone' => '+91 9811223344',
            'email' => 'divya@example.com',
            'date' => '2026-12-10',
            'message' => 'Looking for bridal styling session.',
        ]);

        $submitRes->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('asdreams_offer_enquiries', [
            'offer_id' => $lakmeOffer->id,
            'offer_title' => 'The Lakmé Beauty Experience',
            'name' => 'Divya Nair',
            'phone' => '+91 9811223344',
        ]);

        // 3. Also test seeded offer submission (Golden Vows)
        $goldenOffer = AsdreamsOffer::where('slug', 'golden-vows')->firstOrFail();
        $this->postJson(route('asd.offers.enquire'), [
            'offer_id' => $goldenOffer->id,
            'offer' => 'golden-vows',
            'name' => 'Rohan Menon',
            'phone' => '+91 9822334455',
        ])->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('asdreams_offer_enquiries', [
            'offer_id' => $goldenOffer->id,
            'offer_title' => $goldenOffer->title,
            'name' => 'Rohan Menon',
        ]);

        // 4. Verify Admin Dashboard Enquiry Management displays both enquiries with accurate offer titles
        $adminRes = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.enquiries.index', ['type' => 'offers']));
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Divya Nair', false);
        $adminRes->assertSee('The Lakmé Beauty Experience', false);
        $adminRes->assertSee('Rohan Menon', false);
        $adminRes->assertSee($goldenOffer->title, false);
    }

    /**
     * Test admin can update banner and its integrated offer details, and deactivating hides it.
     */
    public function test_admin_can_update_banner_and_deactivate_it(): void
    {
        $banner = AsdreamsBanner::create([
            'title' => 'Original Banner Title',
            'highlight_title' => 'Original Highlight',
            'label' => 'PORTRAIT · STUDIO',
            'description' => 'Original description.',
            'image' => 'images/asdreams/hero-01.jpg',
            'order' => 20,
            'is_active' => true,
        ]);

        $offer = AsdreamsOffer::create([
            'banner_id' => $banner->id,
            'title' => 'Original Offer Title',
            'slug' => 'original-offer-title',
            'category_label' => 'PORTRAIT · STUDIO',
            'heading_main' => 'Original Banner Title',
            'heading_highlight' => 'Original Highlight',
            'lead_text' => 'Original description.',
            'price' => '₹10,000',
            'status' => 'published',
        ]);

        $banner->update([
            'linked_offer_id' => $offer->id,
            'linked_offer_slug' => $offer->slug,
        ]);

        // 1. Update banner and offer via admin controller
        $updatePayload = [
            'title' => 'Updated Banner Title',
            'highlight_title' => 'Updated Highlight',
            'label' => 'UPDATED · CATEGORY',
            'description' => 'Updated banner description.',
            'order' => 25,
            'is_active' => 1,
            'button_text' => 'Explore Details',
            'offer_title' => 'Updated Offer Title',
            'offer_category_label' => 'UPDATED · CATEGORY',
            'offer_price' => '₹25,000',
            'offer_validity' => 'Through end of season',
            'offer_terms' => 'Updated terms and conditions apply.',
            'offer_important_notes' => 'Updated important notes.',
            'offer_features' => [
                'Updated feature point 1',
                'Updated feature point 2',
            ],
        ];

        $updateResponse = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.asdreams.banners.update', $banner), $updatePayload);

        $updateResponse->assertRedirect(route('admin.asdreams.banners.index'));

        // Refresh models
        $banner->refresh();
        $offer->refresh();

        $this->assertEquals('Updated Banner Title', $banner->title);
        $this->assertEquals('Updated Offer Title', $offer->title);
        $this->assertEquals('₹25,000', $offer->price);
        $this->assertEquals('Updated terms and conditions apply.', $offer->terms);

        // 2. Deactivate banner
        $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.asdreams.banners.update', $banner), array_merge($updatePayload, [
                'is_active' => 0,
            ]));

        $banner->refresh();
        $this->assertFalse((bool) $banner->is_active);

        // Verify inactive banner does NOT appear on homepage slider
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertDontSee('Updated Banner Title', false);
    }

    /**
     * Test enquiry submission records originating banner_id and displays in Admin enquiries.
     */
    public function test_enquiry_submission_records_banner_id_and_displays_in_admin(): void
    {
        $banner = AsdreamsBanner::create([
            'title' => 'Atmospheric Lighting',
            'highlight_title' => 'Cinematic Stills',
            'label' => 'COMMERCIAL · BRAND',
            'description' => 'High concept commercial imagery.',
            'image' => 'images/asdreams/hero-01.jpg',
            'order' => 30,
            'is_active' => true,
        ]);

        $offer = AsdreamsOffer::create([
            'banner_id' => $banner->id,
            'title' => 'Commercial Brand Visuals',
            'slug' => 'commercial-brand-visuals',
            'category_label' => 'COMMERCIAL · BRAND',
            'heading_main' => 'Atmospheric Lighting',
            'price' => '₹80,000',
            'status' => 'published',
        ]);

        $banner->update([
            'linked_offer_id' => $offer->id,
            'linked_offer_slug' => $offer->slug,
        ]);

        // Submit client offer enquiry with banner_id
        $enquiryPayload = [
            'banner_id' => $banner->id,
            'offer' => $offer->slug,
            'name' => 'Aditya Roy',
            'phone' => '+91 9900112233',
            'email' => 'aditya@example.com',
            'date' => '2026-11-28',
            'message' => 'Looking to shoot a commercial campaign for our new luxury line.',
        ];

        $enquiryResponse = $this->post(route('asd.offers.enquire'), $enquiryPayload);
        $enquiryResponse->assertRedirect();
        $enquiryResponse->assertSessionHas('success');

        // Assert DB has enquiry linked to both banner and offer
        $enquiry = AsdreamsOfferEnquiry::where('phone', '+91 9900112233')->first();
        $this->assertNotNull($enquiry);
        $this->assertEquals($banner->id, $enquiry->banner_id);
        $this->assertEquals($offer->id, $enquiry->offer_id);
        $this->assertEquals('Commercial Brand Visuals', $enquiry->offer_title);

        // Admin enquiries listing shows the enquiry and banner info
        $adminListing = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.enquiries.index', ['type' => 'offers']));
        $adminListing->assertStatus(200);
        $adminListing->assertSee('Aditya Roy', false);
        $adminListing->assertSee('Commercial Brand Visuals', false);
        $adminListing->assertSee('Atmospheric Lighting', false);

        // Admin enquiry show page displays all details and allows updating status & notes
        $adminDetail = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.enquiries.show-offer', $enquiry->id));
        $adminDetail->assertStatus(200);
        $adminDetail->assertSee('Aditya Roy', false);
        $adminDetail->assertSee('+91 9900112233', false);
        $adminDetail->assertSee('Commercial Brand Visuals', false);
        $adminDetail->assertSee('Atmospheric Lighting', false);

        // Admin updates status and internal notes
        $statusUpdate = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.asdreams.enquiries.update-offer-status', $enquiry->id), [
                'status' => 'contacted',
                'admin_notes' => 'Called customer, meeting arranged for Tuesday.',
            ]);
        $statusUpdate->assertRedirect();

        $enquiry->refresh();
        $this->assertEquals('contacted', $enquiry->status);
        $this->assertEquals('Called customer, meeting arranged for Tuesday.', $enquiry->admin_notes);
    }

    /**
     * Assert that the Offer Details enquiry form contains no <select> dropdown.
     */
    protected function assertNoSelectInsideOfferEnquiryForm(string $html): void
    {
        $matched = preg_match('/<form[^>]+id="asdEnquiryForm"[^>]*>(.*?)<\/form>/s', $html, $matches);
        $this->assertSame(1, $matched, 'Expected #asdEnquiryForm to be present in page HTML.');
        $this->assertStringNotContainsString('<select', $matches[1]);
        $this->assertStringNotContainsString('<option', $matches[1]);
        $this->assertStringNotContainsString('<select id="enqOffer"', $html);
    }
}
