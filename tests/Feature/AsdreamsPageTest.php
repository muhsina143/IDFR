<?php

namespace Tests\Feature;

use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsdreamsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
    }

    /**
     * Test ASDreams home page loads successfully with the Brands Popup slider and all required elements.
     */
    public function test_asdreams_home_page_loads_with_brands_popup_and_slider(): void
    {
        $response = $this->get('/asdreams');

        $response->assertStatus(200);

        // Brands popup modal and banner
        $response->assertSee('id="brandsPopup"', false);
        $response->assertSee('class="brands-pop__banner"', false);
        $response->assertSee('id="brandsPopSlides"', false);

        // Slide counter and live status
        $response->assertSee('id="brandsPopCurrent"', false);
        $response->assertSee('id="brandsPopTotal"', false);
        $response->assertSee('id="brandsPopStatus"', false);
        $response->assertSee('id="brandsPopDots"', false);

        // Navigation arrows
        $response->assertSee('brands-pop__arrow--prev', false);
        $response->assertSee('brands-pop__arrow--next', false);

        // All 7 slide titles and metas
        $response->assertSee('Behind the', false);
        $response->assertSee('Viewfinder', false);
        $response->assertSee('Portrait Session', false);

        $response->assertSee('Prints &amp; <em>Polaroids</em>', false);
        $response->assertSee('Studio Archive', false);

        $response->assertSee('The First <em>Look</em>', false);
        $response->assertSee('Wedding Story', false);

        $response->assertSee('Studio <em>Light</em>', false);
        $response->assertSee('Fashion Editorial', false);

        $response->assertSee('Street <em>Frames</em>', false);
        $response->assertSee('Documentary', false);

        $response->assertSee('Golden <em>Vows</em>', false);
        $response->assertSee('Munnar', false);

        $response->assertSee('Above the <em>Clouds</em>', false);
        $response->assertSee('Wayanad', false);

        // 4 brand cards
        $response->assertSee('ASDreamStudio', false);
        $response->assertSee('IDFR Fashion', false);
        $response->assertSee('Athul’s Academy', false);
        $response->assertSee('Lakmé Academy', false);

        // All required modals
        $response->assertSee('id="asdReel"', false);
        $response->assertSee('id="shootBookModal"', false);
        $response->assertSee('id="studioBookModal"', false);

        // Studio rental section
        $response->assertSee('id="studio_rental"', false);
        $response->assertSee('Book Studio for Rent at', false);
        $response->assertSee('Facilities Provided', false);
        $response->assertSee('Equipment Included', false);
        $response->assertSee('id="studioRentalGallery"', false);
        $response->assertSee('data-studio-book', false);
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

        // Card 3: Athul's Academy
        $response->assertSee('brands-pop__logo--tall', false);
        $response->assertSee('athuls-academy-logo.png', false);
        $response->assertSee('Athul’s Academy', false);
        $response->assertSee('Fashion &amp; Modelling', false);

        // Card 4: Lakme / Beauty & Wellness
        $response->assertSee('brands-pop__logo--lakme', false);
        $response->assertSee('lakme-academy.jpg', false);
        $response->assertSee('Beauty &amp; Wellness', false);

        // Arrow button in every card
        $response->assertSee('brands-pop__go', false);
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
     * Test studio rental booking validation errors.
     */
    public function test_asdreams_studio_booking_validation(): void
    {
        $response = $this->postJson(route('asd.studio-booking.submit'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone']);
    }

    /**
     * Test legacy static HTML redirects to proper Laravel routes.
     */
    public function test_asdreams_legacy_redirects(): void
    {
        $this->get('/asdreams.html')->assertRedirect('/asdreams');
        $this->get('/offer_details.html')->assertRedirect('/asdreams/contact');
        $this->get('/contact_brand.html')->assertRedirect('/asdreams/contact');
    }
}
