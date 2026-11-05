<?php

namespace Tests\Feature;

use App\Models\ModelProfile;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageModelsSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2025',
            'location' => 'Kochi',
            'dates' => '15 — 17 December 2025',
            'status' => 'published',
            'is_current' => true,
        ]);

        ModelProfile::create([
            'name' => 'Aarav Patel',
            'gender' => 'Male',
            'age' => 24,
            'height' => "6'1\"",
            'location' => 'Mumbai',
            'level' => 'Professional',
            'types' => ['Runway', 'Editorial'],
            'profile_photo' => 'images/models/aarav.jpg',
            'status' => 'published',
            'is_featured' => true,
            'display_order' => 1,
        ]);
    }

    /**
     * Test homepage loads models section with correct structure.
     */
    public function test_homepage_models_section_structure(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Models section exists on homepage without models--page class
        $response->assertSee('class="section section--light models"', false);
        $response->assertDontSee('class="section section--light models models--page"', false);

        // Model grid container exists
        $response->assertSee('id="modelGrid"', false);

        // Models data template contains dynamic model
        $response->assertSee('id="modelsData"', false);
        $response->assertSee('Aarav Patel', false);
    }

    /**
     * Test /home redirects to homepage.
     */
    public function test_home_route_redirects_to_root(): void
    {
        $response = $this->get('/home');
        $response->assertRedirect('/');

        $follow = $this->get('/');
        $follow->assertStatus(200);
    }

    /**
     * Test script.js contains single View Model button for homepage models section.
     */
    public function test_script_js_homepage_models_card_actions(): void
    {
        $scriptPath = public_path('js/script.js');
        $this->assertFileExists($scriptPath);

        $scriptContent = file_get_contents($scriptPath);

        // Confirms isHome logic in modelCardHTML
        $this->assertStringContainsString('isHome', $scriptContent);

        // Confirms View Model button exists with data-model-open
        $this->assertStringContainsString('data-model-open="${m.id}"', $scriptContent);
        $this->assertStringContainsString('<span>View Model</span>', $scriptContent);

        // Confirms grid-template-columns: 1fr for single button
        $this->assertStringContainsString('grid-template-columns: 1fr;', $scriptContent);
    }

    /**
     * Test style.css rules for homepage models card actions.
     */
    public function test_style_css_homepage_models_card_actions_rule(): void
    {
        $cssPath = public_path('css/style.css');
        $this->assertFileExists($cssPath);

        $cssContent = file_get_contents($cssPath);

        $this->assertStringContainsString('.models:not(.models--page) .model-card__actions', $cssContent);
        $this->assertStringContainsString('grid-template-columns: 1fr;', $cssContent);
        $this->assertStringContainsString('.models:not(.models--page) .model-card__actions [data-model-contact]', $cssContent);
    }

    /**
     * Test contact functionality in model profile modal remains untouched.
     */
    public function test_modal_contact_functionality_preserved(): void
    {
        $scriptPath = public_path('js/script.js');
        $scriptContent = file_get_contents($scriptPath);

        // Confirms Contact / Enquire button still exists in profile modal
        $this->assertStringContainsString('data-model-contact="${m.id}"><span>Contact / Enquire</span>', $scriptContent);

        // Confirms enquiry modal template still exists on homepage
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('id="modelEnquiryTemplate"', false);
    }
}
