<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\MovingLogo;
use App\Models\Partner;
use App\Models\Sponsor;
use App\Models\User;
use Database\Seeders\PartnersSponsorsClientsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PartnersSponsorsClientsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $regularUser;

    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'director@idfr.example'],
            [
                'name' => 'IDFR Director',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );

        $this->regularUser = User::firstOrCreate(
            ['email' => 'regular@idfr.example'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'role' => 'model',
            ]
        );

        $this->seed(PartnersSponsorsClientsSeeder::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $path) {
            if ($path && File::exists(public_path($path))) {
                @unlink(public_path($path));
            }
        }
        parent::tearDown();
    }

    public function test_unauthenticated_and_non_admin_users_cannot_access_collaboration_modules(): void
    {
        $routes = [
            route('admin.partners.index'),
            route('admin.sponsors.index'),
            route('admin.clients.index'),
            route('admin.moving-logos.index'),
        ];

        foreach ($routes as $url) {
            // Guest redirects to login
            $guestRes = $this->get($url);
            $guestRes->assertRedirect(route('admin.login'));

            // Regular user gets forbidden (403) or redirect
            $userRes = $this->actingAs($this->regularUser)->get($url);
            $this->assertTrue(in_array($userRes->status(), [302, 403]));
        }
    }

    public function test_admin_can_manage_partners_end_to_end(): void
    {
        // 1. Index
        $indexRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.partners.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Our Partners');
        $indexRes->assertSee('Lumière Studios');

        // 2. Create with upload
        $logoImage = UploadedFile::fake()->image('test_partner_logo.png', 300, 150);
        $cardImage = UploadedFile::fake()->image('test_partner_card.jpg', 600, 375);

        $createRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.partners.store'), [
                'name' => 'Vogue Atelier Paris',
                'type' => 'International Fashion Partner',
                'description' => 'Exclusive European runway collaborations for Season 5.',
                'website_url' => 'https://vogueatelier.example',
                'display_order' => 1,
                'is_active' => '1',
                'logo_image' => $logoImage,
                'image' => $cardImage,
            ]);

        $createRes->assertRedirect(route('admin.partners.index'));
        $createRes->assertSessionHas('success');

        $partner = Partner::where('name', 'Vogue Atelier Paris')->first();
        $this->assertNotNull($partner);
        $this->assertEquals('International Fashion Partner', $partner->type);
        $this->assertTrue($partner->is_active);

        if ($partner->logo_image) {
            $this->createdFiles[] = $partner->logo_image;
            $this->assertTrue(File::exists(public_path($partner->logo_image)));
        }
        if ($partner->image_path) {
            $this->createdFiles[] = $partner->image_path;
            $this->assertTrue(File::exists(public_path($partner->image_path)));
        }

        // 3. Edit & Update
        $editRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.partners.edit', $partner));
        $editRes->assertOk();
        $editRes->assertSee('Edit Partner');

        $updateRes = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.partners.update', $partner), [
                'name' => 'Vogue Atelier Global',
                'type' => 'Global Couture Partner',
                'description' => 'Updated couture partnership.',
                'website_url' => 'https://vogueglobal.example',
                'display_order' => 2,
                'is_active' => '1',
            ]);

        $updateRes->assertRedirect(route('admin.partners.index'));
        $partner->refresh();
        $this->assertEquals('Vogue Atelier Global', $partner->name);
        $this->assertEquals('Global Couture Partner', $partner->type);

        // 4. Toggle Status
        $toggleRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.partners.toggle-status', $partner));
        $toggleRes->assertRedirect();
        $partner->refresh();
        $this->assertFalse($partner->is_active);

        // 5. Delete
        $deleteRes = $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.partners.destroy', $partner));
        $deleteRes->assertRedirect(route('admin.partners.index'));
        $this->assertNull(Partner::find($partner->id));
    }

    public function test_admin_can_manage_sponsors_end_to_end(): void
    {
        // 1. Index
        $indexRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.sponsors.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Our Sponsors');
        $indexRes->assertSee('Aurum Jewels');

        // 2. Create title sponsor
        $createRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.sponsors.store'), [
                'name' => 'Titanium Couture Watches',
                'tier' => 'Title Sponsor · Season 5',
                'description' => 'Official luxury timepiece sponsor.',
                'website_url' => 'https://titaniumwatches.example',
                'is_title' => '1',
                'is_active' => '1',
                'display_order' => 1,
                'logo_style' => 'crown',
                'logo_mark' => 'T',
                'logo_word' => 'TITANIUM',
                'logo_sub' => 'SWISS HOROLOGY',
            ]);

        $createRes->assertRedirect(route('admin.sponsors.index'));
        $sponsor = Sponsor::where('name', 'Titanium Couture Watches')->first();
        $this->assertNotNull($sponsor);
        $this->assertTrue($sponsor->is_title);
        $this->assertEquals('crown', $sponsor->logo_style);

        // 3. Edit & Update
        $editRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.sponsors.edit', $sponsor));
        $editRes->assertOk();

        $updateRes = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.sponsors.update', $sponsor), [
                'name' => 'Titanium Horology',
                'tier' => 'Luxury Timepiece Partner',
                'description' => 'Official watch partner.',
                'website_url' => 'https://titanium.example',
                'is_title' => '0',
                'is_active' => '1',
                'display_order' => 2,
                'logo_style' => 'diamond',
                'logo_mark' => 'T',
                'logo_word' => 'TITANIUM',
            ]);

        $updateRes->assertRedirect(route('admin.sponsors.index'));
        $sponsor->refresh();
        $this->assertEquals('Titanium Horology', $sponsor->name);
        $this->assertFalse($sponsor->is_title);

        // 4. Toggle Status
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.sponsors.toggle-status', $sponsor));
        $sponsor->refresh();
        $this->assertFalse($sponsor->is_active);

        // 5. Delete
        $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.sponsors.destroy', $sponsor));
        $this->assertNull(Sponsor::find($sponsor->id));
    }

    public function test_admin_can_manage_clients_and_single_featured_testimonial(): void
    {
        // 1. Index
        $indexRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.clients.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Clients &amp; Testimonials', false);
        $indexRes->assertSee('Maison Kerala');

        // Check current featured client
        $initialFeatured = Client::where('is_featured', true)->first();
        $this->assertNotNull($initialFeatured);
        $this->assertEquals('Maison Kerala', $initialFeatured->name);

        // 2. Create new client testimonial
        $createRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.clients.store'), [
                'name' => 'Royal Heritage Silk',
                'category' => 'Couture Textiles',
                'testimonial_text' => 'IDFR models brought unmatched poise to our signature bridal campaign.',
                'author_name' => 'Aarohi Shenoy',
                'author_title' => 'Head of Brand Strategy',
                'website_url' => 'https://royalheritagesilk.example',
                'is_featured' => '0',
                'is_active' => '1',
                'display_order' => 1,
                'logo_style' => 'weave',
                'logo_mark' => 'R',
                'logo_word' => 'ROYAL',
                'logo_sub' => 'HERITAGE SILK',
            ]);

        $createRes->assertRedirect(route('admin.clients.index'));
        $newClient = Client::where('name', 'Royal Heritage Silk')->first();
        $this->assertNotNull($newClient);
        $this->assertFalse($newClient->is_featured);

        // 3. Set New Client as Featured (Enforces single featured rule)
        $setFeatRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.clients.set-featured', $newClient));
        $setFeatRes->assertRedirect();

        $newClient->refresh();
        $this->assertTrue($newClient->is_featured);

        // The previous featured client must now be unfeatured
        $initialFeatured->refresh();
        $this->assertFalse($initialFeatured->is_featured);

        // Total featured count in database must be exactly 1
        $this->assertEquals(1, Client::where('is_featured', true)->count());

        // 4. Edit & Update
        $editRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.clients.edit', $newClient));
        $editRes->assertOk();

        $updateRes = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.clients.update', $newClient), [
                'name' => 'Royal Heritage Silk Atelier',
                'category' => 'Luxury Silks',
                'testimonial_text' => 'Outstanding professionalism and camera presence.',
                'author_name' => 'Aarohi Shenoy',
                'author_title' => 'Chief Brand Officer',
                'is_featured' => '1',
                'is_active' => '1',
                'display_order' => 1,
                'logo_style' => 'weave',
                'logo_mark' => 'R',
                'logo_word' => 'ROYAL',
            ]);

        $updateRes->assertRedirect(route('admin.clients.index'));
        $newClient->refresh();
        $this->assertEquals('Royal Heritage Silk Atelier', $newClient->name);
        $this->assertEquals('Chief Brand Officer', $newClient->author_title);

        // 5. Toggle Status
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.clients.toggle-status', $newClient));
        $newClient->refresh();
        $this->assertFalse($newClient->is_active);

        // 6. Delete
        $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.clients.destroy', $newClient));
        $this->assertNull(Client::find($newClient->id));
    }

    public function test_admin_can_manage_moving_logos_end_to_end(): void
    {
        // 1. Index
        $indexRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.moving-logos.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Moving Logo Strip');

        // 2. Create a new moving logo
        $createRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.moving-logos.store'), [
                'name' => 'Apex Fashion Week',
                'entity_type' => 'standalone',
                'display_order' => 1,
                'is_active' => '1',
                'logo_style' => 'crest',
                'logo_mark' => 'A',
                'logo_word' => 'APEX',
                'logo_sub' => 'FASHION WEEK',
            ]);

        $createRes->assertRedirect(route('admin.moving-logos.index'));
        $logo = MovingLogo::where('company_name', 'Apex Fashion Week')->first();
        $this->assertNotNull($logo);
        $this->assertTrue($logo->is_active);
        $this->assertEquals('crest', $logo->logo_style);

        // 3. Edit & Update
        $editRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.moving-logos.edit', $logo));
        $editRes->assertOk();

        $updateRes = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.moving-logos.update', $logo), [
                'name' => 'Apex Couture Week',
                'display_order' => 2,
                'is_active' => '1',
                'logo_style' => 'star',
                'logo_mark' => 'A',
                'logo_word' => 'APEX',
                'logo_sub' => 'COUTURE',
            ]);

        $updateRes->assertRedirect(route('admin.moving-logos.index'));
        $logo->refresh();
        $this->assertEquals('Apex Couture Week', $logo->name);
        $this->assertEquals('star', $logo->logo_style);

        // 4. Toggle Status
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.moving-logos.toggle-status', $logo));
        $logo->refresh();
        $this->assertFalse($logo->is_active);

        // 5. Delete
        $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.moving-logos.destroy', $logo));
        $this->assertNull(MovingLogo::find($logo->id));
    }

    public function test_public_homepage_dynamically_reflects_active_records_and_hides_inactive(): void
    {
        // Add an active partner and an inactive partner
        $activePartner = Partner::create([
            'name' => 'Active Test Partner',
            'type' => 'Active Partner Type',
            'description' => 'Active partner description text.',
            'display_order' => 99,
            'is_active' => true,
            'logo_style' => 'circle',
            'logo_mark' => 'P',
            'logo_word' => 'PARTNER',
        ]);

        $inactivePartner = Partner::create([
            'name' => 'Hidden Inactive Partner',
            'type' => 'Hidden Partner Type',
            'description' => 'Hidden partner description text.',
            'display_order' => 100,
            'is_active' => false,
            'logo_style' => 'circle',
            'logo_mark' => 'H',
            'logo_word' => 'HIDDEN',
        ]);

        // Add an active sponsor and an inactive sponsor
        $activeSponsor = Sponsor::create([
            'name' => 'Active Test Sponsor',
            'tier' => 'Official Beverage Partner',
            'display_order' => 99,
            'is_active' => true,
            'logo_style' => 'drop',
            'logo_mark' => 'S',
            'logo_word' => 'SPONSOR',
        ]);

        $inactiveSponsor = Sponsor::create([
            'name' => 'Hidden Inactive Sponsor',
            'tier' => 'Hidden Sponsor Tier',
            'display_order' => 100,
            'is_active' => false,
            'logo_style' => 'drop',
            'logo_mark' => 'H',
            'logo_word' => 'HIDDEN',
        ]);

        // Make sure a featured client is set
        $featuredClient = Client::where('is_featured', true)->first();
        $this->assertNotNull($featuredClient);

        // Query the homepage
        $response = $this->get('/');
        $response->assertOk();

        // 1. Partners verification
        $response->assertSee('Active Test Partner');
        $response->assertSee('Active Partner Type');
        $response->assertDontSee('Hidden Inactive Partner');

        // 2. Sponsors verification
        $response->assertSee('Active Test Sponsor');
        $response->assertSee('Official Beverage Partner');
        $response->assertDontSee('Hidden Inactive Sponsor');

        // 3. Featured Testimonial verification
        $response->assertSee($featuredClient->testimonial_text);
        $response->assertSee($featuredClient->author_name);

        // 4. Moving logos verification
        $response->assertSee('id="movingLogosData"', false);
    }
}
