<?php

namespace Tests\Feature;

use App\Models\ProfessionalCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderAuthAndHireTalentVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        ProfessionalCategory::create([
            'name' => 'Makeup Artist',
            'slug' => 'makeup-artist',
            'description' => 'Professional makeup artists',
            'display_order' => 1,
            'is_active' => true,
        ]);
    }

    public function test_guest_sees_login_and_register_buttons_in_header(): void
    {
        $response = $this->get(route('idfr.home'));
        $response->assertStatus(200);

        // Guest sees login & register in header
        $response->assertSee('data-auth="login"', false);
        $response->assertSee('data-auth="register"', false);
        $response->assertSee('nav__register', false);

        // Guest does not see authenticated avatar menu
        $response->assertDontSee('id="navAccount"', false);
        $response->assertDontSee('nav__avatar-circle', false);
    }

    public function test_authenticated_user_does_not_see_register_or_login_in_header(): void
    {
        $user = User::factory()->create([
            'name' => 'Muhsin Model',
            'email' => 'muhsin@example.com',
            'role' => 'model',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'web')->get(route('idfr.home'));
        $response->assertStatus(200);

        // Header shows avatar initial and dropdown menu
        $response->assertSee('id="navAccount"', false);
        $response->assertSee('nav__avatar-circle', false);
        $response->assertSee('>M</span>', false);
        $response->assertSee('My Account', false);
        $response->assertSee('My Profile', false);
        $response->assertSee('Logout', false);

        // Header must NOT contain nav__register or nav__login
        $response->assertDontSee('nav__register', false);
        $response->assertDontSee('nav__login', false);

        // Authenticated user on home page must NOT see "Hire Talent"
        $response->assertDontSee('Hire Talent', false);
    }

    public function test_authenticated_user_on_about_page_has_no_register_or_hire_talent(): void
    {
        $user = User::factory()->create([
            'name' => 'Sophia Artist',
            'email' => 'sophia@example.com',
            'role' => 'professional',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'web')->get(route('idfr.about'));
        $response->assertStatus(200);

        // Header shows avatar initial S
        $response->assertSee('id="navAccount"', false);
        $response->assertSee('nav__avatar-circle', false);
        $response->assertSee('>S</span>', false);

        // Header must NOT contain nav__register or nav__login
        $response->assertDontSee('nav__register', false);
        $response->assertDontSee('nav__login', false);

        // About page must NOT contain "Hire Talent"
        $response->assertDontSee('Hire Talent', false);

        // About page CTA must display "My Profile", never "Register as Model"
        $response->assertDontSee('Register as Model', false);
        $response->assertSee('My Profile', false);
    }

    public function test_authenticated_user_on_creative_profile_page_has_no_header_register(): void
    {
        $user = User::factory()->create([
            'name' => 'Zara Artist',
            'email' => 'zara@example.com',
            'role' => 'makeup-artist',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user, 'web')->get('/profiles/makeup-artist');
        $response->assertStatus(200);

        // Avatar initial Z shown in header
        $response->assertSee('id="navAccount"', false);
        $response->assertSee('nav__avatar-circle', false);
        $response->assertSee('>Z</span>', false);

        // Header must NOT contain nav__register or nav__login
        $response->assertDontSee('nav__register', false);
        $response->assertDontSee('nav__login', false);
    }
}
