<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SingleActiveSeasonSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Season $season4;

    protected Season $season7;

    protected Season $season8;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin@idfr.example'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );

        $this->season4 = Season::create([
            'number' => 4,
            'title' => 'Season 4',
            'year' => '2026',
            'location' => 'Kochi, Kerala',
            'dates' => '12 – 15 December 2026',
            'status' => 'published',
            'is_current' => false,
            'description' => 'Season 4 fashion show in Kochi.',
            'hero_heading' => 'The Runway',
            'hero_highlighted' => 'In Kochi',
        ]);

        $this->season7 = Season::create([
            'number' => 7,
            'title' => 'Season 7',
            'year' => '2027',
            'location' => 'Bengaluru, Karnataka',
            'dates' => '10 – 12 December 2027',
            'status' => 'published',
            'is_current' => false,
            'description' => 'Season 7 runway showcase in Bengaluru.',
            'hero_heading' => 'The Runway',
            'hero_highlighted' => 'In Bengaluru',
        ]);

        $this->season8 = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2028',
            'location' => 'Kannur',
            'dates' => '15 – 17 December 2028',
            'status' => 'published',
            'is_current' => true,
            'description' => 'Season 8 couture fest in Kannur.',
            'hero_heading' => 'THE RUNWAY',
            'hero_highlighted' => 'Returns',
        ]);
    }

    public function test_season_eight_is_the_active_season_by_default_across_entire_website(): void
    {
        // 1. Season::current() returns Season 8
        $current = Season::current();
        $this->assertNotNull($current);
        $this->assertEquals(8, $current->number);
        $this->assertEquals('Season 8', $current->title);
        $this->assertTrue($current->is_current);

        // 2. Only one active season exists
        $this->assertEquals(1, Season::where('is_current', true)->count());

        // 3. Homepage reflects Season 8
        $home = $this->get(route('idfr.home'));
        $home->assertStatus(200);
        $home->assertSee('IDFR Fashion Show · Season 8', false);
        $home->assertSee('THE RUNWAY', false);
        $home->assertSee('<em>Returns</em>', false);
        $home->assertSee('15 – 17 December 2028 · Kannur', false);

        // 4. Model registration page reflects Season 8
        $tickets = $this->get(route('idfr.tickets'));
        $tickets->assertStatus(200);
        $tickets->assertSee('IDFR Fashion Show · Season 8', false);
        $tickets->assertSee('value="'.$this->season8->id.'"', false);
        $tickets->assertDontSee('Select Runway Season', false);

        // 5. About page reflects Season 8
        $about = $this->get(route('idfr.about'));
        $about->assertStatus(200);
        $about->assertSee('Season 8', false);
    }

    public function test_admin_can_activate_season_seven_and_switch_entire_website(): void
    {
        // Admin activates Season 7
        $response = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.seasons.activate', $this->season7));

        $response->assertRedirect(route('admin.seasons'));
        $response->assertSessionHas('success');

        // Confirm database state: Season 7 is active, Season 8 is deactivated
        $this->season7->refresh();
        $this->season8->refresh();
        $this->assertTrue($this->season7->is_current);
        $this->assertFalse($this->season8->is_current);
        $this->assertEquals(1, Season::where('is_current', true)->count());

        // Season::current() now returns Season 7
        $this->assertEquals('Season 7', Season::current()->title);

        // Homepage now reflects Season 7
        $home = $this->get(route('idfr.home'));
        $home->assertStatus(200);
        $home->assertSee('IDFR Fashion Show · Season 7', false);
        $home->assertSee('10 – 12 December 2027 · Bengaluru, Karnataka', false);
        $home->assertDontSee('IDFR Fashion Show · Season 8', false);

        // Tickets page now reflects Season 7
        $tickets = $this->get(route('idfr.tickets'));
        $tickets->assertStatus(200);
        $tickets->assertSee('IDFR Fashion Show · Season 7', false);
        $tickets->assertSee('value="'.$this->season7->id.'"', false);
        $tickets->assertDontSee('value="'.$this->season8->id.'"', false);
    }

    public function test_admin_can_create_and_activate_season_nine(): void
    {
        // Admin creates Season 9 and sets is_current = 1
        $response = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.seasons.store'), [
                'number' => 9,
                'title' => 'Season 9',
                'year' => '2029',
                'location' => 'Mumbai, Maharashtra',
                'dates' => '20 – 22 December 2029',
                'status' => 'published',
                'is_current' => 1,
                'hero_heading' => 'Couture Elegance',
                'hero_highlighted' => 'Mumbai Edition',
                'description' => 'The ultimate fashion spectacle in Mumbai.',
            ]);

        $response->assertRedirect(route('admin.seasons'));

        $season9 = Season::where('number', 9)->first();
        $this->assertNotNull($season9);
        $this->assertTrue($season9->is_current);

        // Only Season 9 should be active; Season 8 must be deactivated
        $this->season8->refresh();
        $this->assertFalse($this->season8->is_current);
        $this->assertEquals(1, Season::where('is_current', true)->count());

        // Entire website now reflects Season 9
        $home = $this->get(route('idfr.home'));
        $home->assertStatus(200);
        $home->assertSee('IDFR Fashion Show · Season 9', false);
        $home->assertSee('Couture Elegance', false);
        $home->assertSee('<em>Mumbai Edition</em>', false);
        $home->assertSee('20 – 22 December 2029 · Mumbai, Maharashtra', false);

        $tickets = $this->get(route('idfr.tickets'));
        $tickets->assertStatus(200);
        $tickets->assertSee('IDFR Fashion Show · Season 9', false);
        $tickets->assertSee('value="'.$season9->id.'"', false);
    }

    public function test_admin_dashboard_shows_all_seasons_with_active_indicator(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.seasons'));

        $response->assertStatus(200);

        // All seasons are listed
        $response->assertSee('Season 8', false);
        $response->assertSee('Season 7', false);
        $response->assertSee('Season 4', false);

        // Active indicator on Season 8
        $response->assertSee('★ Active Season', false);
        $response->assertSee('Live Active', false);

        // Inactive seasons have "Set as Active" action
        $response->assertSee('Set as Active', false);
    }

    public function test_model_registration_only_accepted_for_active_season(): void
    {
        // 1. Registration for active Season 8 succeeds
        $response8 = $this->post(route('idfr.tickets.submit'), [
            'season_id' => $this->season8->id,
            'full_name' => 'Kavya Nair',
            'whatsapp_number' => '+91 98765 00001',
            'age' => 20,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
        ]);
        $response8->assertRedirect();
        $response8->assertSessionHas('success');

        $this->assertDatabaseHas('season_model_registrations', [
            'season_id' => $this->season8->id,
            'full_name' => 'Kavya Nair',
        ]);

        // 2. Attempting to register for inactive Season 4 is rejected
        $response4 = $this->post(route('idfr.tickets.submit'), [
            'season_id' => $this->season4->id,
            'full_name' => 'Rahul Verma',
            'whatsapp_number' => '+91 98765 00002',
            'age' => 22,
            'gender' => 'male',
            'category' => 'adult',
            'citizenship' => 'Indian',
        ]);
        $response4->assertSessionHasErrors('season_id');

        $this->assertDatabaseMissing('season_model_registrations', [
            'season_id' => $this->season4->id,
            'full_name' => 'Rahul Verma',
        ]);
    }
}
