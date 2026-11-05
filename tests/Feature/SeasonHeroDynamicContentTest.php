<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonHeroDynamicContentTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

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
    }

    public function test_homepage_displays_custom_hero_heading_highlighted_and_description_for_active_season(): void
    {
        $season = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2028',
            'location' => 'Kannur',
            'dates' => '15 – 17 Dec 2028',
            'status' => 'published',
            'hero_heading' => 'The Grand Runway',
            'hero_highlighted' => 'Awakens',
            'description' => 'A dazzling spectacle of haute couture and emerging icons in Kannur.',
        ]);

        $response = $this->get(route('idfr.home', ['season' => 8]));

        $response->assertStatus(200);

        // Custom hero content should appear
        $response->assertSee('The Grand Runway', false);
        $response->assertSee('<em>Awakens</em>', false);
        $response->assertSee('A dazzling spectacle of haute couture and emerging icons in Kannur.', false);

        // Backend season metadata must be preserved
        $response->assertSee('IDFR Fashion Show · Season 8', false);
        $response->assertSee('15 – 17 Dec 2028 · Kannur', false);
        $response->assertSee('Reserve Your Seat', false);
    }

    public function test_homepage_falls_back_gracefully_when_hero_fields_are_null(): void
    {
        $season = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2028',
            'location' => 'Kannur',
            'dates' => '15 – 17 Dec 2028',
            'status' => 'published',
            'hero_heading' => null,
            'hero_highlighted' => null,
            'description' => null,
        ]);

        $response = $this->get(route('idfr.home', ['season' => 8]));

        $response->assertStatus(200);

        // Should render fallback text
        $response->assertSee('The Runway', false);
        $response->assertSee('<em>Returns</em>', false);
        $response->assertSee('Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.', false);

        // Existing backend fields preserved
        $response->assertSee('IDFR Fashion Show · Season 8', false);
        $response->assertSee('15 – 17 Dec 2028 · Kannur', false);
        $response->assertSee('Reserve Your Seat', false);
    }

    public function test_admin_can_update_season_hero_heading_and_description(): void
    {
        $season = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2028',
            'location' => 'Kannur',
            'dates' => '15 – 17 Dec 2028',
            'status' => 'published',
            'hero_heading' => 'The Runway',
            'hero_highlighted' => 'Returns',
            'description' => 'Original description',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.seasons.update', $season), [
                'number' => 8,
                'title' => 'Season 8',
                'year' => '2028',
                'location' => 'Kannur',
                'dates' => '15 – 17 Dec 2028',
                'status' => 'published',
                'hero_heading' => 'THE RUNWAY',
                'hero_highlighted' => 'Returns Live',
                'description' => 'Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.',
            ]);

        $response->assertRedirect(route('admin.seasons'));

        $season->refresh();
        $this->assertEquals('THE RUNWAY', $season->hero_heading);
        $this->assertEquals('Returns Live', $season->hero_highlighted);
        $this->assertEquals('Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.', $season->description);

        // Verify homepage now displays the updated content
        $homeResponse = $this->get(route('idfr.home', ['season' => 8]));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('THE RUNWAY', false);
        $homeResponse->assertSee('<em>Returns Live</em>', false);
        $homeResponse->assertSee('Couture, heritage and new faces under the lights — three nights of glamour in Kozhikode. Claim your place on the front row.', false);
    }

    public function test_admin_can_create_new_season_with_hero_fields(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.seasons.store'), [
                'number' => 9,
                'title' => 'Season 9',
                'year' => '2029',
                'location' => 'Mumbai',
                'dates' => '10 – 12 Dec 2029',
                'status' => 'published',
                'hero_heading' => 'Haute Couture',
                'hero_highlighted' => 'Unveiled',
                'description' => 'Experience the grand runway in Mumbai.',
            ]);

        $response->assertRedirect(route('admin.seasons'));

        $this->assertDatabaseHas('seasons', [
            'number' => 9,
            'title' => 'Season 9',
            'hero_heading' => 'Haute Couture',
            'hero_highlighted' => 'Unveiled',
            'description' => 'Experience the grand runway in Mumbai.',
        ]);
    }

    public function test_admin_seasons_page_renders_modal_with_all_structured_fields_and_layout(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.seasons'));

        $response->assertStatus(200);

        // Modal dialog container and form
        $response->assertSee('id="seasonModal"', false);
        $response->assertSee('class="admin-modal__dialog"', false);
        $response->assertSee('class="admin-modal__form"', false);

        // Two-column fields
        $response->assertSee('id="seasonNumber"', false);
        $response->assertSee('id="seasonTitle"', false);
        $response->assertSee('id="seasonYear"', false);
        $response->assertSee('id="seasonLocation"', false);
        $response->assertSee('id="seasonHeroHeading"', false);
        $response->assertSee('id="seasonHeroHighlighted"', false);

        // Full width fields
        $response->assertSee('id="seasonDates"', false);
        $response->assertSee('id="seasonHeroImage"', false);
        $response->assertSee('id="seasonStatus"', false);
        $response->assertSee('id="seasonDesc"', false);

        // Controls & Previews
        $response->assertSee('id="seasonHeroPreview"', false);
        $response->assertSee('id="seasonSubmitBtn"', false);
    }
}
