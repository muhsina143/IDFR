<?php

namespace Tests\Feature;

use App\Models\ModelProfile;
use App\Models\Season;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsPageCardActionsTest extends TestCase
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
            'name' => 'Meera Nair',
            'gender' => 'Female',
            'age' => 22,
            'height' => "5'9\"",
            'location' => 'Kochi',
            'level' => 'Professional',
            'types' => ['Runway', 'Editorial'],
            'profile_photo' => 'images/models/meera.jpg',
            'status' => 'published',
            'is_featured' => true,
            'display_order' => 1,
        ]);
    }

    /**
     * Test /models page loads with model grid container and data template.
     */
    public function test_models_page_structure(): void
    {
        $response = $this->get(route('idfr.models'));
        $response->assertStatus(200);

        // Container and dynamic template exist
        $response->assertSee('id="modelGrid"', false);
        $response->assertSee('id="modelsData"', false);
        $response->assertSee('Meera Nair', false);
    }

    /**
     * Test script.js generates a single View Model button for model cards on all pages.
     */
    public function test_script_js_model_card_actions_has_only_view_model_button(): void
    {
        $scriptPath = public_path('js/script.js');
        $this->assertFileExists($scriptPath);

        $scriptContent = file_get_contents($scriptPath);

        // Confirms View Model button exists with data-model-open
        $this->assertStringContainsString('data-model-open="${m.id}"', $scriptContent);
        $this->assertStringContainsString('<span>View Model</span>', $scriptContent);

        // Confirms model-card__actions has single column layout
        $this->assertStringContainsString('grid-template-columns: 1fr;', $scriptContent);

        // Confirms Contact / Enquire button still exists in profile modal
        $this->assertStringContainsString('data-model-contact="${m.id}"><span>Contact / Enquire</span>', $scriptContent);
    }

    /**
     * Test style.css rules for models page card actions.
     */
    public function test_style_css_models_page_card_actions_rule(): void
    {
        $cssPath = public_path('css/style.css');
        $this->assertFileExists($cssPath);

        $cssContent = file_get_contents($cssPath);

        $this->assertStringContainsString('.models--page .model-card__actions', $cssContent);
        $this->assertStringContainsString('.models--page .model-card__actions [data-model-contact]', $cssContent);
    }

    /**
     * Test navigation Contact link and Contact page remain intact.
     */
    public function test_contact_page_and_navigation_remain_untouched(): void
    {
        $response = $this->get(route('idfr.contact'));
        $response->assertStatus(200);

        $modelsResponse = $this->get(route('idfr.models'));
        $modelsResponse->assertStatus(200);
        $modelsResponse->assertSee('Contact', false);
    }
}
