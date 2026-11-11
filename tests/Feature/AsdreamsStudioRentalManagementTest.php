<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsStudioEquipment;
use App\Models\Asdreams\AsdreamsStudioFacility;
use App\Models\Asdreams\AsdreamsStudioGalleryImage;
use App\Models\Asdreams\AsdreamsWebsiteSetting;
use App\Models\User;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AsdreamsStudioRentalManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    /**
     * Paths to uploaded test files inside public/uploads/asdreams to remove on tearDown.
     *
     * @var list<string>
     */
    protected array $testFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_studio_rental_test@idfr.example'],
            [
                'name' => 'Studio Rental Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    protected function tearDown(): void
    {
        foreach ($this->testFiles as $filePath) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        parent::tearDown();
    }

    public function test_guests_cannot_access_studio_rental_admin_routes(): void
    {
        $this->get(route('admin.asdreams.studio-rental.index'))->assertRedirect(route('admin.login'));
        $this->post(route('admin.asdreams.studio-rental.pricing.update'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.studio-facilities.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.studio-facilities.create'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.studio-equipment.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.studio-equipment.create'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.studio-gallery.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.studio-gallery.create'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_sidebar_shows_single_compact_studio_rental_link_and_hub_contains_all_sections(): void
    {
        $dashboardResponse = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.dashboard'));

        $dashboardResponse->assertOk();
        $html = $dashboardResponse->getContent();

        // Extract only the sidebar navigation HTML to assert sidebar compactness strictly
        preg_match('/<nav class="admin-sidebar__nav"[^>]*>.*?<\/nav>/s', $html, $navMatch);
        $sidebarHtml = $navMatch[0] ?? '';

        $this->assertStringContainsString('id="sidebar-scroll"', $sidebarHtml);
        $this->assertStringContainsString('STUDIO RENTAL MANAGEMENT', $sidebarHtml);
        $this->assertStringContainsString(route('admin.asdreams.studio-rental.index'), $sidebarHtml);
        $this->assertStringContainsString('<span>Studio Rental</span>', $sidebarHtml);

        $this->assertStringNotContainsString('<span>Facilities Provided</span>', $sidebarHtml);
        $this->assertStringNotContainsString('<span>Equipment Included</span>', $sidebarHtml);
        $this->assertStringNotContainsString('<span>Studio Gallery</span>', $sidebarHtml);
        $this->assertStringNotContainsString('<span>Studio Settings</span>', $sidebarHtml);

        // Main Studio Rental hub page must expose all 4 sections
        $hubResponse = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.studio-rental.index'));

        $hubResponse->assertOk();
        $hubResponse->assertSee('1. Facilities Provided');
        $hubResponse->assertSee('2. Equipment Included');
        $hubResponse->assertSee('3. Studio Gallery');
        $hubResponse->assertSee('4. Studio Settings');
        $hubResponse->assertSee('Lobby');
        $hubResponse->assertSee('2 SK 400 Photo Lights');
        $hubResponse->assertSee('Main Floor');
        $hubResponse->assertSee('A. Rental Pricing');
        $hubResponse->assertSee('B. Studio Information');
        $hubResponse->assertSee('<svg class="sprite"', false);
        $hubResponse->assertDontSee('<div class="alert alert--success">', false);

        foreach ([public_path('css/admin.css'), base_path('IDFR_LATEST/css/admin.css')] as $cssPath) {
            $css = File::get($cssPath);
            $this->assertMatchesRegularExpression(
                '/\.sprite\s*\{[^}]*position:\s*absolute;[^}]*width:\s*0;[^}]*height:\s*0;[^}]*overflow:\s*hidden;/s',
                $css
            );
        }

        // Flash alerts must still render when saving Studio Rental settings without AJAX
        $flashResponse = $this->actingAs($this->adminUser, 'admin')
            ->followingRedirects()
            ->post(route('admin.asdreams.studio-rental.pricing.update'), [
                'title' => 'Book Studio for Rent',
                'price_amount' => '1000',
                'currency' => 'INR',
                'rental_unit' => 'Per Day',
                'price_enabled' => '1',
                'from_hub' => '1',
            ]);

        $flashResponse->assertOk();
        $flashResponse->assertSee('<div class="alert alert--success">', false);
        $flashResponse->assertSee('Studio rental pricing and information updated successfully.');
    }

    public function test_admin_sidebar_renders_early_scroll_persistence_and_active_centering_script(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.shoot-bookings.index'));

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('<nav class="admin-sidebar__nav" id="sidebar-scroll">', $html);
        $this->assertMatchesRegularExpression('/class="admin-nav__link\s+active"[^>]*>\s*<svg[^>]*>.*?<span>Shoot Bookings<\/span>/s', $html);

        $extracted = preg_match('/<\/aside>\s*(<script>.*?<\/script>)\s*<!-- Layout Container -->/s', $html, $matches);
        $this->assertSame(1, $extracted);

        $script = $matches[1];
        $this->assertStringContainsString("getElementById('sidebar-scroll')", $script);
        $this->assertStringContainsString("'sidebarScrollPos'", $script);
        $this->assertStringContainsString('sessionStorage', $script);
        $this->assertStringContainsString('try {', $script);
        $this->assertStringContainsString('catch', $script);
        $this->assertStringContainsString("addEventListener('scroll'", $script);
        $this->assertStringContainsString("addEventListener('beforeunload'", $script);
        $this->assertStringContainsString("addEventListener('DOMContentLoaded'", $script);
        $this->assertStringContainsString("scrollIntoView({ block: 'center' })", $script);
    }

    public function test_seeded_studio_rental_content_renders_on_landing_and_asdreams_home(): void
    {
        foreach ([route('landing'), route('asd.home')] as $url) {
            $response = $this->get($url);
            $response->assertOk();
            $response->assertSee('Book Studio for Rent at <em>ASDreamStudio</em>', false);
            $response->assertSee('Facilities Provided');
            $response->assertSee('All areas are air-conditioned.');
            $response->assertSee('Lobby');
            $response->assertSee('Makeup Room');
            $response->assertSee('Changing Room');
            $response->assertSee('Pantry');
            $response->assertSee('Photography Room');
            $response->assertSee('2 Restrooms');

            $response->assertSee('Equipment Included');
            $response->assertSee('Available with every studio rental booking.');
            $response->assertSee('2 SK 400 Photo Lights');
            $response->assertSee('1 AD 400 Photo Light');
            $response->assertSee('2 SL 100 Video Lights');
            $response->assertSee('4 Stick Lights (Video Lights)');
            $response->assertSee('Blue Sofa');
            $response->assertSee('Model Stool');
            $response->assertSee('Table and Chairs');

            $response->assertSee('id="studioRentalGallery"', false);
            $response->assertSee('Main Floor');
            $response->assertSee('Set Lighting');
            $response->assertSee('data-studio-book', false);

            // Seeded dynamic pricing defaults (no hardcoded mock price text)
            $response->assertSee('Studio Area');
            $response->assertSee('4,000 sq. ft.');
            $response->assertSee('Studio Rental');
            $response->assertSee('Starting From');
            $response->assertSee('₹1000');
            $response->assertSee('/ day');
            $response->assertDontSee('₹1,000');
            $response->assertSee('Professional photography and creative studio space available for your projects.');
            $response->assertSee('Final pricing may vary depending on rental duration, studio requirements and additional services.');
            $response->assertDontSee('Mock price');

            // Original Google Fonts & CSS classes must match original HTML reference
            $response->assertSee('family=Jost:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600', false);
            $response->assertSee('class="section studio-rental"', false);
            $response->assertSee('class="studio-rental__area"', false);
            $response->assertSee('class="studio-rental__panels"', false);
            $response->assertSee('class="studio-rental__panel"', false);
            $response->assertSee('class="studio-rental__panel-head"', false);
            $response->assertSee('class="studio-rental__facilities"', false);
            $response->assertSee('class="studio-rental__equipment"', false);
            $response->assertSee('class="studio-rental__gallery-head"', false);
            $response->assertSee('class="studio-rental__cta"', false);
        }

        $css = File::get(public_path('css/style.css'));
        $this->assertStringContainsString('.studio-rental__area-icon {', $css);
        $this->assertStringContainsString('.studio-rental__facilities li {', $css);
        $this->assertStringContainsString('.studio-rental__equipment li {', $css);
        $this->assertStringContainsString('.studio-rental__equip-img {', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(3, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(2, minmax(0, 1fr));', $css);
        $this->assertStringContainsString('object-fit: cover;', $css);
        $this->assertStringContainsString('transform: scale(1.05);', $css);
        $this->assertStringContainsString('text-align: left;', $css);
        $this->assertStringContainsString('.studio-rental__pricing-tag {', $css);
        $this->assertStringContainsString('.studio-rental__short-desc {', $css);
        $this->assertStringContainsString('.studio-rental__booking-info {', $css);
    }

    public function test_admin_can_crud_facilities_and_update_heading_and_description(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // Update section heading & description
        $settingsResponse = $this->post(route('admin.asdreams.studio-facilities.settings'), [
            'heading' => 'Studio Spaces & Facilities',
            'description' => 'Climate-controlled rooms across all floors.',
        ]);
        $settingsResponse->assertRedirect();

        // Create facility
        $storeResponse = $this->post(route('admin.asdreams.studio-facilities.store'), [
            'name' => 'Private VIP Lounge',
            'icon' => 'star',
            'display_order' => 0,
            'is_active' => '1',
        ]);
        $storeResponse->assertRedirect(route('admin.asdreams.studio-facilities.index'));

        $facility = AsdreamsStudioFacility::where('name', 'Private VIP Lounge')->firstOrFail();
        $this->assertSame(0, $facility->display_order);
        $this->assertTrue($facility->is_active);

        // Verify visible on both landing routes beforeLobby
        $homeHtml = $this->get(route('asd.home'))->assertOk()->getContent();
        $this->assertStringContainsString('Studio Spaces &amp; Facilities', $homeHtml);
        $this->assertStringContainsString('Climate-controlled rooms across all floors.', $homeHtml);
        $this->assertTrue(strpos($homeHtml, 'Private VIP Lounge') < strpos($homeHtml, 'Lobby'));

        // Edit facility
        $this->put(route('admin.asdreams.studio-facilities.update', $facility), [
            'name' => 'Executive Green Room',
            'icon' => 'sparkles',
            'display_order' => 10,
            'is_active' => '1',
        ])->assertRedirect(route('admin.asdreams.studio-facilities.index'));

        // Verify updated & disabled hides it from frontend
        $this->post(route('admin.asdreams.studio-facilities.toggle-status', $facility))->assertRedirect();
        $facility->refresh();
        $this->assertFalse($facility->is_active);

        $this->get(route('landing'))->assertOk()->assertDontSee('Executive Green Room');

        // Delete facility
        $this->delete(route('admin.asdreams.studio-facilities.destroy', $facility))
            ->assertRedirect(route('admin.asdreams.studio-facilities.index'));
        $this->assertDatabaseMissing('asdreams_studio_facilities', ['id' => $facility->id]);
    }

    public function test_admin_can_crud_equipment_with_optional_image_and_settings(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $this->post(route('admin.asdreams.studio-equipment.settings'), [
            'heading' => 'Included Gear & Props',
            'description' => 'Complimentary lighting kit & studio props.',
        ])->assertRedirect();

        $uploadedThumb = UploadedFile::fake()->image('profoto-pro-11.png', 200, 200);

        $this->post(route('admin.asdreams.studio-equipment.store'), [
            'name' => 'Profoto Strobe Pack',
            'icon' => 'light',
            'image' => $uploadedThumb,
            'display_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.asdreams.studio-equipment.index'));

        $equipment = AsdreamsStudioEquipment::where('name', 'Profoto Strobe Pack')->firstOrFail();
        $this->assertNotNull($equipment->image_path);
        $storedFullPath = public_path($equipment->image_path);
        $this->testFiles[] = $storedFullPath;
        $this->assertFileExists($storedFullPath);

        $asdRes = $this->get(route('asd.home'))->assertOk();
        $asdRes->assertSee('Included Gear &amp; Props', false);
        $asdRes->assertSee('Complimentary lighting kit &amp; studio props.', false);
        $asdRes->assertSee('Profoto Strobe Pack');
        $asdRes->assertSee('studio-rental__equip-img', false);
        $asdRes->assertSee($equipment->image_path, false);

        // Update and remove custom image
        $this->put(route('admin.asdreams.studio-equipment.update', $equipment), [
            'name' => 'Profoto Dual Pack',
            'icon' => 'camera',
            'remove_image' => '1',
            'display_order' => 2,
            'is_active' => '1',
        ])->assertRedirect(route('admin.asdreams.studio-equipment.index'));

        $equipment->refresh();
        $this->assertNull($equipment->image_path);
        $this->assertFileDoesNotExist($storedFullPath);

        // Disable hides on frontend
        $this->post(route('admin.asdreams.studio-equipment.toggle-status', $equipment))->assertRedirect();
        $this->get(route('asd.home'))->assertOk()->assertDontSee('Profoto Dual Pack');

        // Delete cleanly
        $this->delete(route('admin.asdreams.studio-equipment.destroy', $equipment))
            ->assertRedirect(route('admin.asdreams.studio-equipment.index'));
        $this->assertDatabaseMissing('asdreams_studio_equipments', ['id' => $equipment->id]);
    }

    public function test_admin_can_batch_upload_edit_toggle_and_delete_studio_gallery_images(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $fileOne = UploadedFile::fake()->image('cyclorama-wall.jpg', 800, 1000);
        $fileTwo = UploadedFile::fake()->image('color-gels-rig.png', 800, 1000);

        $this->post(route('admin.asdreams.studio-gallery.store'), [
            'images' => [$fileOne, $fileTwo],
            'titles' => ['Infinity Cyclorama', 'Color Gel Rig'],
            'descriptions' => ['Seamless white cyc curve wall', 'RGB and optical snoot lighting bay'],
            'orders' => [20, 19],
            'is_active' => '1',
        ])->assertRedirect(route('admin.asdreams.studio-gallery.index'));

        $shotOne = AsdreamsStudioGalleryImage::where('title', 'Infinity Cyclorama')->firstOrFail();
        $shotTwo = AsdreamsStudioGalleryImage::where('title', 'Color Gel Rig')->firstOrFail();

        $shotOnePath = public_path($shotOne->image_path);
        $shotTwoPath = public_path($shotTwo->image_path);
        $this->testFiles[] = $shotOnePath;
        $this->testFiles[] = $shotTwoPath;
        $this->assertFileExists($shotOnePath);
        $this->assertFileExists($shotTwoPath);

        // Verify order on frontend (19 Color Gel Rig before 20 Infinity Cyclorama)
        $homeContent = $this->get(route('asd.home'))->assertOk()->getContent();
        $this->assertStringContainsString('Seamless white cyc curve wall', $homeContent);
        $this->assertStringContainsString('RGB and optical snoot lighting bay', $homeContent);
        $this->assertTrue(strpos($homeContent, 'Color Gel Rig') < strpos($homeContent, 'Infinity Cyclorama'));

        // Replace image & update title on shotOne
        $replacement = UploadedFile::fake()->image('cyclorama-updated.jpg', 800, 1000);
        $this->put(route('admin.asdreams.studio-gallery.update', $shotOne), [
            'title' => 'Heated Cyclorama Bay',
            'description' => 'Updated studio floor description',
            'image' => $replacement,
            'display_order' => 25,
            'is_active' => '1',
        ])->assertRedirect(route('admin.asdreams.studio-gallery.index'));

        $shotOne->refresh();
        $newShotOnePath = public_path($shotOne->image_path);
        $this->testFiles[] = $newShotOnePath;
        $this->assertFileExists($newShotOnePath);
        $this->assertFileDoesNotExist($shotOnePath);
        $this->assertSame('Heated Cyclorama Bay', $shotOne->title);

        // Disable hides on frontend
        $this->post(route('admin.asdreams.studio-gallery.toggle-status', $shotOne))->assertRedirect();
        $this->get(route('landing'))->assertOk()->assertDontSee('Heated Cyclorama Bay');

        // Delete removes DB records and files
        $this->delete(route('admin.asdreams.studio-gallery.destroy', $shotOne))->assertRedirect();
        $this->delete(route('admin.asdreams.studio-gallery.destroy', $shotTwo))->assertRedirect();

        $this->assertDatabaseMissing('asdreams_studio_gallery_images', ['id' => $shotOne->id]);
        $this->assertDatabaseMissing('asdreams_studio_gallery_images', ['id' => $shotTwo->id]);
        $this->assertFileDoesNotExist($newShotOnePath);
        $this->assertFileDoesNotExist($shotTwoPath);
    }

    public function test_studio_booking_submission_remains_functional_with_dynamic_studio_rental_section(): void
    {
        $response = $this->postJson(route('asd.studio-booking.submit'), [
            'name' => 'Rohan Visuals',
            'phone' => '+91 98111 22334',
            'email' => 'rohan@visuals.example',
            'days' => 2,
            'purpose' => 'Editorial Lookbook',
            'start' => now()->addDays(4)->format('Y-m-d'),
            'end' => now()->addDays(5)->format('Y-m-d'),
            'notes' => 'Morning call time at 8 AM.',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('asdreams_shoot_bookings', [
            'name' => 'Rohan Visuals',
            'booking_type' => 'studio',
            'purpose' => 'Editorial Lookbook',
        ]);
    }

    public function test_all_studio_rental_admin_pages_render_successfully(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $facility = AsdreamsStudioFacility::firstOrFail();
        $equipment = AsdreamsStudioEquipment::firstOrFail();
        $gallery = AsdreamsStudioGalleryImage::firstOrFail();

        $this->get(route('admin.asdreams.studio-rental.index'))->assertOk();
        $this->get(route('admin.asdreams.studio-rental.index', ['tab' => 'settings']))->assertOk();

        $this->get(route('admin.asdreams.studio-facilities.index'))->assertOk();
        $this->get(route('admin.asdreams.studio-facilities.create'))->assertOk();
        $this->get(route('admin.asdreams.studio-facilities.edit', $facility))->assertOk();

        $this->get(route('admin.asdreams.studio-equipment.index'))->assertOk();
        $this->get(route('admin.asdreams.studio-equipment.create'))->assertOk();
        $this->get(route('admin.asdreams.studio-equipment.edit', $equipment))->assertOk();

        $this->get(route('admin.asdreams.studio-gallery.index'))->assertOk();
        $this->get(route('admin.asdreams.studio-gallery.create'))->assertOk();
        $this->get(route('admin.asdreams.studio-gallery.edit', $gallery))->assertOk();
    }

    public function test_admin_can_dynamically_update_studio_pricing_currency_unit_and_visibility(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // 1. Update price to 1500, currency AED, unit Per Hour via AJAX from hub
        $ajaxResponse = $this->postJson(route('admin.asdreams.studio-rental.pricing.update'), [
            'title' => 'Commercial Soundstage Hire',
            'studio_area' => '5,200 sq. ft.',
            'short_description' => 'Acoustic cyclorama floor available for editorial & commercial shoots.',
            'booking_info' => '50% advance reservation deposit required.',
            'additional_note' => 'Late night surcharge applies after 10 PM.',
            'price_amount' => '1500',
            'currency' => 'AED',
            'rental_unit' => 'Per Hour',
            'pricing_label' => 'Rates From',
            'price_description' => 'Custom full-day production quotes available on request.',
            'price_enabled' => '1',
        ]);

        $ajaxResponse->assertOk()
            ->assertJson([
                'success' => true,
                'tab' => 'settings',
            ]);

        foreach ([route('landing'), route('asd.home')] as $url) {
            $res = $this->get($url)->assertOk();
            $res->assertSee('Commercial Soundstage Hire');
            $res->assertSee('5,200 sq. ft.');
            $res->assertSee('Rates From');
            $res->assertSee('د.إ 1500');
            $res->assertSee('/ hour');
            $res->assertSee('Acoustic cyclorama floor available for editorial &amp; commercial shoots.', false);
            $res->assertSee('50% advance reservation deposit required.');
            $res->assertSee('Late night surcharge applies after 10 PM.');
            $res->assertSee('Custom full-day production quotes available on request.');
            $res->assertDontSee('Mock price');
        }

        // 2. Switch to USD + Per Session
        $this->post(route('admin.asdreams.studio-rental.pricing.update'), [
            'title' => 'Studio Rental',
            'short_description' => 'Flexible production sessions.',
            'booking_info' => '',
            'additional_note' => '',
            'price_amount' => '2500',
            'currency' => 'USD',
            'rental_unit' => 'Per Session',
            'pricing_label' => 'Starting At',
            'price_description' => 'Includes basic lighting rig.',
            'price_enabled' => '1',
            'from_hub' => '1',
        ])->assertRedirect(route('admin.asdreams.studio-rental.index', ['tab' => 'settings']));

        $usdRes = $this->get(route('asd.home'))->assertOk();
        $usdRes->assertSee('Starting At');
        $usdRes->assertSee('$2500');
        $usdRes->assertSee('/ session');
        $usdRes->assertSee('Includes basic lighting rig.');

        // 2b. Decimal INR price retains decimals without thousands separator and uses lowercase "/ day"
        $this->postJson(route('admin.asdreams.studio-rental.pricing.update'), [
            'title' => 'Studio Rental',
            'short_description' => 'Half-rate custom block.',
            'booking_info' => '',
            'additional_note' => '',
            'price_amount' => '1250.5',
            'currency' => 'INR',
            'rental_unit' => 'Per Day',
            'pricing_label' => 'Starting From',
            'price_description' => '',
            'price_enabled' => '1',
        ])->assertOk()->assertJson(['success' => true]);

        $decRes = $this->get(route('asd.home'))->assertOk();
        $decRes->assertSee('₹1250.50');
        $decRes->assertSee('/ day');
        $decRes->assertDontSee('₹1,250.50');

        // 3. Disable price display -> price amount, unit, and notes hidden while Book Studio & title stay visible
        $this->postJson(route('admin.asdreams.studio-rental.pricing.update'), [
            'title' => 'Private Studio Booking',
            'short_description' => 'Contact us for bespoke quotes.',
            'booking_info' => 'Weekdays and weekends available.',
            'additional_note' => 'Hidden additional note when disabled',
            'price_amount' => '2500',
            'currency' => 'USD',
            'rental_unit' => 'Per Session',
            'pricing_label' => 'Starting At',
            'price_description' => 'Hidden price description when disabled',
            'price_enabled' => '0',
        ])->assertOk()->assertJson(['success' => true]);

        $hiddenRes = $this->get(route('asd.home'))->assertOk();
        $hiddenRes->assertSee('Private Studio Booking');
        $hiddenRes->assertSee('Contact us for bespoke quotes.');
        $hiddenRes->assertSee('Weekdays and weekends available.');
        $hiddenRes->assertSee('data-studio-book', false);
        $hiddenRes->assertDontSee('$2500');
        $hiddenRes->assertDontSee('/ session');
        $hiddenRes->assertDontSee('Starting At');
        $hiddenRes->assertDontSee('Hidden price description when disabled');
        $hiddenRes->assertDontSee('Hidden additional note when disabled');

        // Verify no duplicate setting rows exist in database
        $this->assertSame(
            1,
            AsdreamsWebsiteSetting::where('key', 'studio_rental_price_amount')->count()
        );
    }

    public function test_hub_ajax_crud_works_without_page_reload_and_preserves_existing_data(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        $res = $this->postJson(route('admin.asdreams.studio-facilities.store'), [
            'name' => 'Cyclorama Lounge',
            'icon' => 'star',
            'display_order' => 99,
            'is_active' => '1',
            'from_hub' => '1',
        ]);

        $res->assertOk()
            ->assertJson([
                'success' => true,
                'tab' => 'facilities',
            ]);
        $this->assertStringContainsString('Cyclorama Lounge', $res->json('tabHtml'));

        // Seeded items remain intact
        $this->assertDatabaseHas('asdreams_studio_facilities', ['name' => 'Lobby']);
        $this->assertDatabaseHas('asdreams_studio_equipments', ['name' => '2 SK 400 Photo Lights']);
        $this->assertDatabaseHas('asdreams_studio_gallery_images', ['title' => 'Main Floor']);
    }
}
