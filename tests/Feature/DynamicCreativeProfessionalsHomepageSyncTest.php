<?php

namespace Tests\Feature;

use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicCreativeProfessionalsHomepageSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'name' => 'IDFR Master Admin',
            'email' => 'admin_'.uniqid().'@idfr.test',
            'password' => bcrypt('AdminPassword123!'),
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * 1. Test Admin adds a new Creative Professional category and it immediately appears on the homepage tabs and header dropdown.
     */
    public function test_admin_created_category_appears_on_homepage_tabs_and_header_dropdown(): void
    {
        $admin = $this->makeAdmin();

        $categoryPayload = [
            'name' => 'Fashion Stylist & Wardrobe',
            'slug' => 'fashion-stylist-wardrobe',
            'hero_title' => 'Fashion Stylist & <em>Wardrobe</em>',
            'description' => 'Curating bespoke runway aesthetics, wardrobe architecture and editorial fashion styling.',
            'form_title' => 'Stylist Portfolio Submission',
            'form_lead' => 'Submit your celebrity styling projects, runway shows and fashion brand lookbooks.',
            'display_order' => 50,
            'is_active' => 1,
        ];

        // 1 & 2. Admin creates the category
        $response = $this->actingAs($admin, 'admin')->post(route('admin.dynamic-forms.categories.store'), $categoryPayload);
        $response->assertRedirect(route('admin.dynamic-forms.index'));

        $this->assertDatabaseHas('professional_categories', [
            'slug' => 'fashion-stylist-wardrobe',
            'name' => 'Fashion Stylist & Wardrobe',
            'is_active' => 1,
        ]);

        // 5 & 6. Open homepage and verify category appears under Creative Professionals tabs and navigation
        $homeRes = $this->get(route('idfr.home'));
        $homeRes->assertOk();

        // Check header dropdown navigation contains new category
        $homeRes->assertSee('Fashion Stylist & Wardrobe');
        $homeRes->assertSee('#creative-fashion-stylist-wardrobe');

        // Check homepage category tab button
        $homeRes->assertSee('id="creativeTab-fashion-stylist-wardrobe"', false);
        $homeRes->assertSee('data-category="fashion-stylist-wardrobe"', false);
        $homeRes->assertSee('aria-controls="creative-fashion-stylist-wardrobe"', false);

        // Check homepage panel and grid exist
        $homeRes->assertSee('id="creative-fashion-stylist-wardrobe"', false);
        $homeRes->assertSee('data-category="fashion-stylist-wardrobe"', false);
    }

    /**
     * 2. Test Admin adds a professional profile under the new category and it renders dynamically on the homepage.
     */
    public function test_admin_created_profile_under_new_category_appears_on_homepage(): void
    {
        $admin = $this->makeAdmin();

        // Find or create category
        $category = ProfessionalCategory::firstOrCreate(
            ['slug' => 'party-event-management'],
            [
                'name' => 'Party & Event Management',
                'hero_title' => 'Party & <em>Event Management</em>',
                'description' => 'Runway shows, luxury weddings and fashion galas.',
                'form_title' => 'Event Management Details',
                'form_lead' => 'Submit your event production details.',
                'display_order' => 60,
                'is_active' => true,
            ]
        );

        // Admin creates a creative professional under that category
        $proPayload = [
            'name' => 'Vikram Roy Productions',
            'category' => 'party-event-management',
            'professional_title' => 'Lead Event Director & Runway Producer',
            'location' => 'Kochi & Mumbai',
            'experience' => '10+ Years',
            'biography' => 'Pioneering luxury fashion events, celebrity galas and grand runway architecture across India.',
            'services' => 'Runway Stage Production, Luxury VIP Lounge, Sound & Lighting Engineering',
            'status' => 'published',
            'display_order' => 1,
        ];

        $proResponse = $this->actingAs($admin, 'admin')->post(route('admin.professionals.store'), $proPayload);
        $proResponse->assertRedirect(route('admin.professionals'));
        $proResponse->assertSessionHas('success');

        $pro = CreativeProfessional::where('name', 'Vikram Roy Productions')->first();
        $this->assertNotNull($pro);
        $this->assertSame('party-event-management', $pro->category);
        $this->assertSame('published', $pro->status);

        // Verify profile data appears inside the homepage template and is ready for frontend rendering
        $homeRes = $this->get(route('idfr.home'));
        $homeRes->assertOk();

        // Check tab button and panel exist
        $homeRes->assertSee('id="creativeTab-party-event-management"', false);
        $homeRes->assertSee('id="creative-party-event-management"', false);

        // Check template contains Vikram Roy Productions
        $homeRes->assertSee('Vikram Roy Productions');
        $homeRes->assertSee('data-category="party-event-management"', false);
        $homeRes->assertSee('Kochi & Mumbai');
        $homeRes->assertSee('10+ Years');
        $homeRes->assertSee('Runway Stage Production');
        $homeRes->assertSee('Sound & Lighting Engineering');
    }

    /**
     * 3. Test Editing a profile updates details on the homepage.
     */
    public function test_updating_profile_reflects_changes_on_homepage(): void
    {
        $admin = $this->makeAdmin();

        $category = ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion <em>Stylist</em>',
            'description' => 'Styling experts.',
            'display_order' => 50,
            'is_active' => true,
        ]);

        $pro = CreativeProfessional::create([
            'name' => 'Original Stylist Name',
            'category' => 'fashion-stylist',
            'professional_title' => 'Junior Wardrobe Stylist',
            'location' => 'Calicut',
            'experience' => '3 Years',
            'biography' => 'Original biography statement.',
            'services' => ['Lookbook Styling', 'Runway Wardrobe'],
            'status' => 'published',
            'display_order' => 1,
        ]);

        $homeResBefore = $this->get(route('idfr.home'));
        $homeResBefore->assertSee('Original Stylist Name');
        $homeResBefore->assertSee('Junior Wardrobe Stylist');

        // Admin updates the profile
        $updatePayload = [
            'name' => 'Aria Thorne Haute Couture Stylist',
            'category' => 'fashion-stylist',
            'professional_title' => 'Senior Celebrity Wardrobe Director',
            'location' => 'Kochi & Paris',
            'experience' => '8 Years',
            'biography' => 'Updated high-fashion editorial styling biography.',
            'services' => 'Editorial Haute Couture, Celebrity Wardrobe, Red Carpet Direction',
            'status' => 'published',
            'display_order' => 1,
        ];

        $updateRes = $this->actingAs($admin, 'admin')->put(route('admin.professionals.update', $pro), $updatePayload);
        $updateRes->assertRedirect(route('admin.professionals'));

        // Verify updated details appear on homepage
        $homeResAfter = $this->get(route('idfr.home'));
        $homeResAfter->assertDontSee('Original Stylist Name');
        $homeResAfter->assertSee('Aria Thorne Haute Couture Stylist');
        $homeResAfter->assertSee('Kochi & Paris');
        $homeResAfter->assertSee('8 Years');
        $homeResAfter->assertSee('Editorial Haute Couture');
        $homeResAfter->assertSee('Red Carpet Direction');
    }

    /**
     * 4. Test Unpublishing or Deleting a profile removes it from homepage public display.
     */
    public function test_unpublishing_or_deleting_profile_removes_from_homepage(): void
    {
        $admin = $this->makeAdmin();

        $category = ProfessionalCategory::firstOrCreate(
            ['slug' => 'drone-pilot'],
            [
                'name' => 'Drone Pilot',
                'is_active' => true,
            ]
        );

        $pro = CreativeProfessional::create([
            'name' => 'SkyView Aerial Cine',
            'category' => 'drone-pilot',
            'professional_title' => '4K FPV Drone Cinematographer',
            'location' => 'Kochi',
            'experience' => '5 Years',
            'status' => 'published',
            'display_order' => 1,
        ]);

        $homeRes1 = $this->get(route('idfr.home'));
        $homeRes1->assertSee('SkyView Aerial Cine');

        // 1. Unpublish (change to draft)
        $pro->update(['status' => 'draft']);
        $homeRes2 = $this->get(route('idfr.home'));
        $homeRes2->assertDontSee('SkyView Aerial Cine');

        // 2. Republish
        $pro->update(['status' => 'published']);
        $homeRes3 = $this->get(route('idfr.home'));
        $homeRes3->assertSee('SkyView Aerial Cine');

        // 3. Delete
        $pro->delete();
        $homeRes4 = $this->get(route('idfr.home'));
        $homeRes4->assertDontSee('SkyView Aerial Cine');
    }

    /**
     * 5. Test Deactivating a category removes it from homepage tabs, panels, and header dropdown.
     */
    public function test_deactivating_category_removes_it_from_homepage_display(): void
    {
        $admin = $this->makeAdmin();

        $category = ProfessionalCategory::create([
            'name' => 'Temporary Experimental Category',
            'slug' => 'temp-experimental-category',
            'hero_title' => 'Temporary Category',
            'display_order' => 99,
            'is_active' => true,
        ]);

        $homeRes1 = $this->get(route('idfr.home'));
        $homeRes1->assertSee('Temporary Experimental Category');
        $homeRes1->assertSee('id="creativeTab-temp-experimental-category"', false);
        $homeRes1->assertSee('#creative-temp-experimental-category');

        // Admin deactivates category via toggle-status
        $toggleRes = $this->actingAs($admin, 'admin')->post(route('admin.dynamic-forms.categories.toggle-status', $category));
        $toggleRes->assertRedirect();

        $category->refresh();
        $this->assertFalse((bool) $category->is_active);

        $homeRes2 = $this->get(route('idfr.home'));
        $homeRes2->assertDontSee('Temporary Experimental Category');
        $homeRes2->assertDontSee('id="creativeTab-temp-experimental-category"', false);
        $homeRes2->assertDontSee('#creative-temp-experimental-category');
    }
}
