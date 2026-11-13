<?php

namespace Tests\Feature;

use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonTicketPageDynamicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Season::create([
            'number' => 4,
            'title' => 'Season 4',
            'year' => '2026',
            'location' => 'Kochi, Kerala',
            'dates' => '12 – 15 December 2026',
            'status' => 'published',
            'description' => 'Season 4 fashion show in Kochi.',
            'hero_image' => 'uploads/seasons/hero_season_4.png',
        ]);

        Season::create([
            'number' => 7,
            'title' => 'Season 7',
            'year' => '2027',
            'location' => 'Bengaluru, Karnataka',
            'dates' => '10 – 12 December 2027',
            'status' => 'published',
            'description' => 'Season 7 runway showcase in Bengaluru.',
            'hero_image' => 'uploads/seasons/hero_season_7.png',
        ]);

        Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2028',
            'location' => 'Kannur',
            'dates' => '15 – 17 December 2028',
            'status' => 'published',
            'description' => 'Season 8 couture fest in Kannur.',
            'hero_image' => 'uploads/seasons/hero_season_8.png',
        ]);
    }

    public function test_tickets_page_displays_only_latest_active_published_season_and_no_season_selector(): void
    {
        $response = $this->get(route('idfr.tickets'));
        $response->assertStatus(200);

        // Confirm only Season 8 is displayed
        $response->assertSee('IDFR Fashion Show · Season 8', false);
        $response->assertSee('Kannur', false);
        $response->assertSee('15 – 17 December 2028', false);
        $response->assertSee('Season 8 couture fest in Kannur', false);
        $response->assertSee('uploads/seasons/hero_season_8.png', false);
        $response->assertSee('data-season-number="8"', false);
        $response->assertSee('<span class="booking__poster-label">Season 8</span>', false);

        // Confirm form hidden season_id is Season 8's ID
        $season8 = Season::where('number', 8)->first();
        $response->assertSee('value="'.$season8->id.'"', false);

        // Confirm NO season selector bar or buttons remain
        $response->assertDontSee('Select Runway Season', false);
        $response->assertDontSee('season-filter-bar', false);
    }

    public function test_tickets_page_ignores_manual_season_switching_parameters(): void
    {
        // Attempt to pass ?season=4
        $response = $this->get(route('idfr.tickets', ['season' => 4]));
        $response->assertStatus(200);

        // Must still show only the latest active Season 8
        $response->assertSee('IDFR Fashion Show · Season 8', false);
        $response->assertSee('<span class="booking__poster-label">Season 8</span>', false);
        $response->assertDontSee('IDFR Fashion Show · Season 4', false);

        // Attempt to pass ?season=7
        $response7 = $this->get(route('idfr.tickets', ['season' => 7]));
        $response7->assertStatus(200);
        $response7->assertSee('IDFR Fashion Show · Season 8', false);
        $response7->assertDontSee('IDFR Fashion Show · Season 7', false);
    }

    public function test_tickets_page_automatically_updates_when_latest_active_season_changes(): void
    {
        // When a new Season 9 is created and published in admin
        $season9 = Season::create([
            'number' => 9,
            'title' => 'Season 9',
            'year' => '2029',
            'location' => 'Mumbai',
            'dates' => '20 – 22 December 2029',
            'status' => 'published',
            'description' => 'Season 9 grand showcase in Mumbai.',
            'hero_image' => 'uploads/seasons/hero_season_9.png',
        ]);

        $response = $this->get(route('idfr.tickets'));
        $response->assertStatus(200);

        // Should now automatically display Season 9 without any hardcoding
        $response->assertSee('IDFR Fashion Show · Season 9', false);
        $response->assertSee('Mumbai', false);
        $response->assertSee('20 – 22 December 2029', false);
        $response->assertSee('Season 9 grand showcase in Mumbai', false);
        $response->assertSee('<span class="booking__poster-label">Season 9</span>', false);
        $response->assertSee('value="'.$season9->id.'"', false);

        // Previous season details should no longer be displayed as current
        $response->assertDontSee('IDFR Fashion Show · Season 8', false);
        $response->assertDontSee('<span class="booking__poster-label">Season 8</span>', false);
    }

    public function test_tickets_page_displays_graceful_message_when_no_season_available(): void
    {
        // Remove all seasons
        Season::query()->delete();

        $response = $this->get(route('idfr.tickets'));
        $response->assertStatus(200);

        // Should display graceful unavailable message
        $response->assertSee('Model registration is currently unavailable.', false);
        $response->assertSee('Return to Home', false);
        $response->assertDontSee('Participant <em>Details</em>', false);
        $response->assertDontSee('Select Runway Season', false);
    }

    public function test_ticket_booking_submission_succeeds_with_current_season(): void
    {
        $season8 = Season::where('number', 8)->first();

        $response = $this->post(route('idfr.tickets.submit'), [
            'season_id' => $season8->id,
            'full_name' => 'Aarav Sharma',
            'whatsapp_number' => '+919876543210',
            'email' => 'aarav@example.com',
            'age' => 22,
            'gender' => 'male',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'additional_info' => 'Height 6ft 1in, experienced ramp walker',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('season_model_registrations', [
            'season_id' => $season8->id,
            'full_name' => 'Aarav Sharma',
            'whatsapp_number' => '+919876543210',
            'payment_status' => 'unpaid',
            'registration_status' => 'pending',
        ]);
    }
}
