<?php

namespace Tests\Feature;

use App\Http\Controllers\ProfileController;
use App\Models\CreativeProfessional;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CreativeProfileValidationSanitizationTest extends TestCase
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
     * Test sanitizeValidationRules handles raw numeric rules like "100" without throwing BadMethodCallException.
     */
    public function test_raw_numeric_rule_converts_to_max_and_validates_cleanly(): void
    {
        $sanitized = ProfileController::sanitizeValidationRules('100', true, 'text');

        $this->assertContains('max:100', $sanitized);
        $this->assertContains('required', $sanitized);
        $this->assertContains('string', $sanitized);
        $this->assertNotContains('100', $sanitized);

        // Run through Laravel Validator to guarantee no validate100 BadMethodCallException
        $validator = Validator::make(['field' => 'Valid input text'], ['field' => $sanitized]);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test various admin-entered aliases and spacing formats (max,100, max 100, max: 100).
     */
    public function test_user_entered_aliases_and_delimiters_are_safely_normalized(): void
    {
        $cases = [
            'max: 100' => 'max:100',
            'max,100' => 'max:100',
            'max 100' => 'max:100',
            'required, string, 100' => 'max:100',
            'string|100' => 'max:100',
            'nullable|100' => 'max:100',
        ];

        foreach ($cases as $input => $expectedRule) {
            $sanitized = ProfileController::sanitizeValidationRules($input, true, 'text');
            $this->assertContains($expectedRule, $sanitized, "Failed for input: {$input}");

            // Verify validator runs without exceptions
            $validator = Validator::make(['field' => 'Valid input'], ['field' => $sanitized]);
            $this->assertTrue($validator->passes());
        }
    }

    /**
     * Test unrecognized or arbitrary text rules are discarded so BadMethodCallException never occurs.
     */
    public function test_unrecognized_rules_are_stripped_safely(): void
    {
        $sanitized = ProfileController::sanitizeValidationRules('unknown_rule_xyz|nonexistent_check|string', true, 'text');

        $this->assertNotContains('unknown_rule_xyz', $sanitized);
        $this->assertNotContains('nonexistent_check', $sanitized);
        $this->assertContains('string', $sanitized);
        $this->assertContains('required', $sanitized);

        $validator = Validator::make(['field' => 'Valid input'], ['field' => $sanitized]);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test required and nullable rules are mutually exclusive.
     */
    public function test_required_and_nullable_are_mutually_exclusive(): void
    {
        $requiredRules = ProfileController::sanitizeValidationRules('nullable|string|max:100', true, 'text');
        $this->assertContains('required', $requiredRules);
        $this->assertNotContains('nullable', $requiredRules);

        $nullableRules = ProfileController::sanitizeValidationRules('required|string|max:100', false, 'text');
        $this->assertContains('nullable', $nullableRules);
        $this->assertNotContains('required', $nullableRules);
    }

    /**
     * Test saving Party & Event Management profile succeeds and persists without BadMethodCallException.
     */
    public function test_party_event_management_profile_saves_cleanly(): void
    {
        $user = $this->makeUser('party-event-management');

        $payload = [
            'name' => 'Moideen Event Director',
            'profession' => 'Chief Event Architect',
            'place' => 'Kochi',
            'experience' => 7,
            'phone' => '+91 99988 77766',
            'email' => $user->email,
            'about' => 'High-fashion runway and luxury wedding producer with over seven years experience across south India.',
            'service' => ['fashion-show-production', 'corporate-event-management'],
            'service_party_event_management' => ['fashion-show-production', 'corporate-event-management'],
            'outstation' => 'yes',
        ];

        $postRes = $this->actingAs($user)->post(route('profile.category.save', 'party-event-management'), $payload);
        $postRes->assertRedirect();
        $postRes->assertSessionHas('success');

        $pro = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($pro);
        $this->assertSame('party-event-management', $pro->category);
        $this->assertSame('Moideen Event Director', $pro->name);
        $this->assertIsArray($pro->services);
        $this->assertContains('fashion-show-production', $pro->services);
    }
}
