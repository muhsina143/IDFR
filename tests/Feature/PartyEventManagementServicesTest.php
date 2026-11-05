<?php

namespace Tests\Feature;

use App\Models\CategoryFormField;
use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PartyEventManagementServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
    }

    protected function makeUser(string $role = 'party-event-management', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'name' => 'Moideen Event Director',
            'email' => 'moideen_'.uniqid().'@idfr.test',
            'phone' => '+91 99988 77766',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ], $attributes));
    }

    /**
     * 1. Test Party & Event Management category renders services & specialisations checkboxes.
     */
    public function test_party_event_management_form_displays_selectable_services(): void
    {
        $user = $this->makeUser();

        $res = $this->actingAs($user)->get(route('profile.category', 'party-event-management'));
        $res->assertOk();
        $res->assertSee('Event Management Services & Specialisations');
        $res->assertSee('name="service[]"', false);
        $res->assertSee('value="fashion-show-production"', false);
        $res->assertSee('Fashion Show & Stage Production');
        $res->assertSee('value="luxury-wedding-planning"', false);
        $res->assertSee('Luxury Wedding & Sangeet Planning');
    }

    /**
     * 2. Test saving Party & Event Management profile saves services and persists on reopen.
     */
    public function test_party_event_management_saves_and_persists_services(): void
    {
        $user = $this->makeUser();

        $payload = [
            'name' => 'Moideen Event Director',
            'profession' => 'Chief Event Architect',
            'place' => 'Kochi',
            'experience' => 7,
            'phone' => '+91 99988 77766',
            'email' => $user->email,
            'about' => 'High-fashion runway and luxury wedding producer with over seven years experience across south India.',
            'service' => ['fashion-show-production', 'corporate-event-management'],
            'outstation' => 'yes',
        ];

        $postRes = $this->actingAs($user)->post(route('profile.category.save', 'party-event-management'), $payload);
        $postRes->assertRedirect();
        $postRes->assertSessionHas('success');

        $pro = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($pro);
        $this->assertSame('party-event-management', $pro->category);
        $this->assertIsArray($pro->services);
        $this->assertContains('fashion-show-production', $pro->services);
        $this->assertContains('corporate-event-management', $pro->services);

        // Reopen form and verify checkboxes are marked checked
        $reopenRes = $this->actingAs($user)->get(route('profile.category', 'party-event-management'));
        $reopenRes->assertOk();
        $reopenRes->assertSee('value="fashion-show-production"', false);
        $reopenRes->assertSee('checked', false);
    }

    /**
     * 3. Test URL alias resolution for typo slug redirects cleanly.
     */
    public function test_typo_slug_url_redirects_seamlessly_to_standard_slug(): void
    {
        $user = $this->makeUser('party-event-mnagement');

        $res = $this->actingAs($user)->get('/profiles/party-event-mnagement');
        $res->assertRedirect(route('profile.category', 'party-event-management'));

        $this->assertSame('party-event-management', $user->fresh()->role);
    }

    /**
     * 4. Test validation triggers only when no services are selected.
     */
    public function test_validation_requires_at_least_one_service_when_services_exist(): void
    {
        $user = $this->makeUser();

        $payloadWithoutService = [
            'name' => 'Moideen Event Director',
            'profession' => 'Chief Event Architect',
            'place' => 'Kochi',
            'experience' => 7,
            'phone' => '+91 99988 77766',
            'email' => $user->email,
            'about' => 'High-fashion runway and luxury wedding producer with over seven years experience across south India.',
            'outstation' => 'yes',
        ];

        $postRes = $this->actingAs($user)->post(route('profile.category.save', 'party-event-management'), $payloadWithoutService);
        $postRes->assertSessionHasErrors('service');
    }

    /**
     * 5. Test empty services notice when category has no options configured.
     */
    public function test_empty_options_displays_admin_configuration_notice_without_blocking_impossible_requirement(): void
    {
        $cat = ProfessionalCategory::create([
            'name' => 'Art Director',
            'slug' => 'art-director',
            'hero_title' => 'Art Director',
            'form_title' => 'Art Director Details',
            'is_active' => true,
        ]);

        $serviceField = ProfessionalFormField::where('field_key', 'service')->first();

        // Attach service field with NO options override
        CategoryFormField::create([
            'category_id' => $cat->id,
            'field_id' => $serviceField->id,
            'label_override' => 'Services & Specialisations',
            'options_override' => null,
            'is_required' => true,
            'is_active' => true,
        ]);

        $user = $this->makeUser('art-director');

        $res = $this->actingAs($user)->get(route('profile.category', 'art-director'));
        $res->assertOk();
        $res->assertSee('No service options have been configured for this profession yet');

        // Submitting without service passes because no options exist
        $payload = [
            'name' => 'Art Director Lead',
            'profession' => 'Art Director',
            'place' => 'Kochi',
            'experience' => 4,
            'phone' => '+91 99988 77766',
            'email' => $user->email,
            'about' => 'Leading visual direction for haute couture and editorial campaigns in south India.',
            'outstation' => 'yes',
        ];

        $submitRes = $this->actingAs($user)->post(route('profile.category.save', 'art-director'), $payload);
        $submitRes->assertRedirect();
        $submitRes->assertSessionHas('success');
    }
}
