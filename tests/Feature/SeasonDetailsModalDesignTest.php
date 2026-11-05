<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonDetailsModalDesignTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@idfr.example'],
            [
                'name' => 'IDFR Director',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    /**
     * Test homepage renders season details modal matching Reference Screenshot 1 design.
     */
    public function test_homepage_renders_season_details_modal_matching_exact_reference_design(): void
    {
        $season = Season::create([
            'number' => 9,
            'title' => 'Season 9',
            'year' => '2026',
            'location' => 'Kochi',
            'dates' => '15 — 17 December 2026',
            'status' => 'published',
            'is_current' => true,
            'description' => 'Couture, heritage and new faces under the lights — three nights of glamour in Kochi. Claim your place on the front row.',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        // 1. Template container for the season
        $response->assertSee('id="program-season-9"', false);

        // 2. Exact Reference Screenshot 1 header typography: IDFR Fashion Show and gold italic number
        $response->assertSee('<p class="info__label">IDFR Fashion Show</p>', false);
        $response->assertSee('<h2 class="info__title" id="infoModalTitle">Season <em>9</em></h2>', false);
        $response->assertSee('Couture, heritage and new faces under the lights', false);

        // 3. 4-column fact grid (.info__facts)
        $response->assertSee('class="info__facts"', false);
        $response->assertSee('<dt>Dates</dt>', false);
        $response->assertSee('<dd>15 — 17 December 2026</dd>', false);
        $response->assertSee('<dt>Venue</dt>', false);
        $response->assertSee('<dd>Kochi</dd>', false);
        $response->assertSee('<dt>Designers</dt>', false);
        $response->assertSee('<dt>Models</dt>', false);

        // 4. Reference Screenshot 1 Card-based layout (.info__list and .info__item)
        $response->assertSee('class="info__list"', false);
        $response->assertSee('class="info__item"', false);
        $response->assertSee('class="info__item-num"', false);
        $response->assertSee('class="info__item-meta"', false);
        $response->assertSee('class="info__item-name"', false);
        $response->assertSee('class="info__item-desc"', false);

        // Dynamic 3 nights rendered into cards
        $response->assertSee('>01<', false);
        $response->assertSee('15 DECEMBER · 7:00 PM', false);
        $response->assertSee('Night One — New Faces', false);
        $response->assertSee('Debut collections walked by models from the Season 9 open auditions.', false);

        $response->assertSee('>02<', false);
        $response->assertSee('16 DECEMBER · 7:00 PM', false);
        $response->assertSee('Night Two — Heritage &amp; Handloom', false);

        $response->assertSee('>03<', false);
        $response->assertSee('17 DECEMBER · 7:30 PM', false);
        $response->assertSee('Night Three — The Grand Finale', false);
        $response->assertSee('closing walk of Season 9.', false);

        // 5. Action buttons (.info__actions)
        $response->assertSee('class="info__actions"', false);
        $response->assertSee('Reserve Your Seat', false);
        $response->assertSee('Register to Walk', false);

        // 6. Ensure broken unbordered .info__schedule is completely removed
        $response->assertDontSee('class="info__schedule"', false);
    }

    /**
     * Test season model provides dynamic schedule events and supports custom admin-configured events.
     */
    public function test_season_model_provides_dynamic_schedule_events(): void
    {
        $season = Season::create([
            'number' => 4,
            'title' => 'Season 4',
            'dates' => '18 – 20 Dec 2026',
            'status' => 'published',
        ]);

        $defaultEvents = $season->schedule_events;
        $this->assertCount(3, $defaultEvents);
        $this->assertEquals('01', $defaultEvents[0]['number']);
        $this->assertEquals('18 DEC · 7:00 PM', $defaultEvents[0]['meta']);
        $this->assertEquals('Night One — New Faces', $defaultEvents[0]['title']);

        // Custom schedule
        $season->schedule = [
            [
                'number' => '01',
                'meta' => '22 OCTOBER · 6:30 PM',
                'title' => 'Couture Opening Night',
                'description' => 'Exclusive VIP premiere.',
            ],
            [
                'number' => '02',
                'meta' => '23 OCTOBER · 7:00 PM',
                'title' => 'Sustainable Fashion Showcase',
                'description' => 'Eco fabrics and handlooms.',
            ],
            [
                'number' => '03',
                'meta' => '24 OCTOBER · 8:00 PM',
                'title' => 'Grand Finale Awards',
                'description' => 'Runway winners and gala night.',
            ],
        ];
        $season->save();

        $customEvents = $season->fresh()->schedule_events;
        $this->assertCount(3, $customEvents);
        $this->assertEquals('Couture Opening Night', $customEvents[0]['title']);
        $this->assertEquals('22 OCTOBER · 6:30 PM', $customEvents[0]['meta']);
        $this->assertEquals('Sustainable Fashion Showcase', $customEvents[1]['title']);
        $this->assertEquals('Grand Finale Awards', $customEvents[2]['title']);
    }

    /**
     * Test admin can update a season with custom schedule cards and see them reflected on the homepage.
     */
    public function test_admin_can_update_season_with_custom_schedule_events(): void
    {
        $season = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2025',
            'location' => 'Kozhikode',
            'dates' => '10 – 12 Nov 2025',
            'status' => 'published',
            'is_current' => true,
        ]);

        $customSchedule = [
            [
                'number' => '01',
                'meta' => '10 NOVEMBER · 6:00 PM',
                'title' => 'Avant-Garde Evening',
                'description' => 'Futuristic couture presentation.',
            ],
            [
                'number' => '02',
                'meta' => '11 NOVEMBER · 7:00 PM',
                'title' => 'Traditional Silk & Khadi',
                'description' => 'Heritage handloom re-envisioned.',
            ],
            [
                'number' => '03',
                'meta' => '12 NOVEMBER · 8:00 PM',
                'title' => 'Gala Red Carpet Night',
                'description' => 'Star-studded closing ceremony.',
            ],
        ];

        $response = $this->actingAs($this->adminUser, 'admin')->put(route('admin.seasons.update', $season), [
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2025',
            'location' => 'Kozhikode',
            'dates' => '10 – 12 Nov 2025',
            'status' => 'published',
            'is_current' => true,
            'description' => 'Updated season 8 description.',
            'schedule' => $customSchedule,
        ]);

        $response->assertRedirect(route('admin.seasons'));

        $this->assertDatabaseHas('seasons', [
            'id' => $season->id,
            'number' => 8,
        ]);

        $seasonFresh = $season->fresh();
        $this->assertNotNull($seasonFresh->schedule);
        $this->assertEquals('Avant-Garde Evening', $seasonFresh->schedule_events[0]['title']);

        // Check homepage modal renders updated custom schedule
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Avant-Garde Evening', false);
        $homeResponse->assertSee('10 NOVEMBER · 6:00 PM', false);
        $homeResponse->assertSee('Futuristic couture presentation.', false);
        $homeResponse->assertSee('Traditional Silk &amp; Khadi', false);
        $homeResponse->assertSee('Gala Red Carpet Night', false);
    }

    /**
     * Test multiple seasons render distinct modal templates on the homepage.
     */
    public function test_multiple_seasons_render_distinct_modal_templates(): void
    {
        Season::create([
            'number' => 4,
            'title' => 'Season 4',
            'location' => 'Kozhikode',
            'dates' => '18 – 20 Dec 2026',
            'status' => 'published',
            'is_current' => false,
        ]);

        Season::create([
            'number' => 9,
            'title' => 'Season 9',
            'location' => 'Kochi',
            'dates' => '15 — 17 Dec 2026',
            'status' => 'published',
            'is_current' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // Both seasons have their own template
        $response->assertSee('id="program-season-4"', false);
        $response->assertSee('id="program-season-9"', false);

        // Season 4 heading & venue
        $response->assertSee('Season <em>4</em>', false);
        $response->assertSee('<dd>Kozhikode</dd>', false);

        // Season 9 heading & venue
        $response->assertSee('Season <em>9</em>', false);
        $response->assertSee('<dd>Kochi</dd>', false);
    }
}
