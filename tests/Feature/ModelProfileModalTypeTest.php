<?php

namespace Tests\Feature;

use App\Models\ModelProfile;
use App\Models\Season;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelProfileModalTypeTest extends TestCase
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
    }

    /**
     * Test registering as a model saves the selected model_type into the database.
     */
    public function test_model_registration_saves_selected_model_type_in_database(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Meera Jasmine',
            'email' => 'meera.jasmine@example.com',
            'phone' => '+91 98765 43210',
            'role' => 'model',
            'model_type' => 'Runway / Ramp Model',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'terms' => true,
        ]);

        $this->assertContains($response->getStatusCode(), [200, 201, 502]);

        $user = User::where('email', 'meera.jasmine@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('model', $user->role);

        $modelProfile = ModelProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($modelProfile);
        $this->assertEquals(['Runway / Ramp Model'], $modelProfile->types);
    }

    /**
     * Test updating model profile in profile controller saves types and clears specialities.
     */
    public function test_model_profile_update_saves_model_types_and_clears_specialities(): void
    {
        $user = User::factory()->create([
            'role' => 'model',
            'email_verified_at' => now(),
        ]);

        $model = ModelProfile::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+91 99999 88888',
            'gender' => 'Female',
            'level' => 'New Face',
            'types' => ['Commercial Model'],
            'specialities' => ['outstation' => 'yes', 'international' => 'no'],
            'status' => 'published',
        ]);

        $response = $this->actingAs($user, 'web')->post(route('profile.model.save'), [
            'name' => $user->name,
            'gender' => 'female',
            'place' => 'Kochi',
            'age' => 22,
            'height' => 175,
            'category' => 'experienced',
            'experience' => 3,
            'skills' => 'Runway walk, Posing',
            'modelType' => ['commercial', 'runway'],
            'outstation' => 'yes',
            'international' => 'no',
        ]);

        $response->assertSessionHasNoErrors();

        $model->refresh();
        $this->assertEquals(['commercial', 'runway'], $model->types);
        $this->assertNull($model->specialities);
        $this->assertEquals(['Commercial Model', 'Runway / Ramp Model'], $model->formatted_types);
    }

    /**
     * Test homepage and models page render formatted types in modelsData template and do not render specialities.
     */
    public function test_homepage_and_models_page_render_model_types_and_no_specialities(): void
    {
        ModelProfile::create([
            'name' => 'Zara Khan',
            'gender' => 'Female',
            'age' => 23,
            'height' => "5'10\"",
            'location' => 'Bangalore',
            'level' => 'Professional',
            'types' => ['runway', 'editorial'],
            'specialities' => ['yes', 'no'],
            'profile_photo' => 'images/models/zara.jpg',
            'status' => 'published',
            'is_featured' => true,
            'display_order' => 1,
        ]);

        // Check homepage
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('data-types="Runway / Ramp Model,Editorial Model"', false);
        $homeResponse->assertSee('<li>Runway / Ramp Model</li>', false);
        $homeResponse->assertSee('<li>Editorial Model</li>', false);
        $homeResponse->assertDontSee('data-specialities', false);

        // Check models page
        $modelsResponse = $this->get('/models');
        $modelsResponse->assertStatus(200);
        $modelsResponse->assertSee('data-types="Runway / Ramp Model,Editorial Model"', false);
        $modelsResponse->assertSee('<li>Runway / Ramp Model</li>', false);
        $modelsResponse->assertSee('<li>Editorial Model</li>', false);
        $modelsResponse->assertDontSee('data-specialities', false);
    }

    /**
     * Test frontend JavaScript templates contain Model Type and remove Specialities and login reminders.
     */
    public function test_javascript_templates_contain_model_type_and_no_specialities_or_login_reminders(): void
    {
        $publicJs = file_get_contents(public_path('js/script.js'));
        $this->assertStringContainsString('<h3 class="mm__heading">Model Type</h3>', $publicJs);
        $this->assertStringNotContainsString('<h3 class="mm__heading">Specialities</h3>', $publicJs);
        $this->assertStringContainsString('formatModelType', $publicJs);
        $this->assertStringNotContainsString('Please log in to contact or enquire about this professional.', $publicJs);
        $this->assertStringNotContainsString('login as a recruiter to hire this model', $publicJs);
        $this->assertStringNotContainsString('class="mm__note"', $publicJs);
        $this->assertStringContainsString('data-pro-enquire', $publicJs);
        $this->assertStringContainsString('data-pro-share', $publicJs);

        $staticJs = file_get_contents(base_path('IDFR_LATEST/js/script.js'));
        $this->assertStringContainsString('<h3 class="mm__heading">Model Type</h3>', $staticJs);
        $this->assertStringNotContainsString('<h3 class="mm__heading">Specialities</h3>', $staticJs);
        $this->assertStringContainsString('formatModelType', $staticJs);
        $this->assertStringNotContainsString('Please log in to contact or enquire about this professional.', $staticJs);
        $this->assertStringNotContainsString('login as a recruiter to hire this model', $staticJs);
        $this->assertStringNotContainsString('class="mm__note"', $staticJs);
        $this->assertStringContainsString('data-pro-enquire', $staticJs);
        $this->assertStringContainsString('data-pro-share', $staticJs);

        $newFolderJs = dirname(base_path()).'/IDFR_LATEST_new/IDFR_LATEST/js/script.js';
        if (file_exists($newFolderJs)) {
            $newJs = file_get_contents($newFolderJs);
            $this->assertStringNotContainsString('Please log in to contact or enquire about this professional.', $newJs);
            $this->assertStringNotContainsString('login as a recruiter to hire this model', $newJs);
            $this->assertStringNotContainsString('class="mm__note"', $newJs);
        }
    }

    /**
     * Test pages do not output the login reminder message in any modal or template.
     */
    public function test_pages_do_not_output_login_reminder_in_modals(): void
    {
        foreach (['/', '/models', '/professionals', '/gallery', '/about', '/contact'] as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('Please log in to contact or enquire about this professional.', false);
            $response->assertDontSee('Please login as a recruiter to hire this model', false);
        }
    }
}
