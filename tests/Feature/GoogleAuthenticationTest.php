<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper to mock Socialite Google driver callback.
     */
    protected function mockSocialiteGoogleUser(string $email, string $id = 'google-id-123456', string $name = 'Google User', ?string $avatar = 'https://lh3.googleusercontent.com/avatar.jpg'): void
    {
        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($avatar);

        $provider = Mockery::mock(AbstractProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    /**
     * Test 1: Main website Login modal displays the Continue with Google button and OR divider.
     */
    public function test_main_website_login_modal_displays_google_sign_in_button_and_or_divider(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Continue with Google');
        $response->assertSee(route('auth.google.redirect'));
        $response->assertSee('auth-divider', false);
        $response->assertSee('<span>OR</span>', false);
        $response->assertSee('google-icon', false);
    }

    /**
     * Test 2: Admin login page does NOT display Google sign-in.
     */
    public function test_admin_login_page_does_not_contain_google_sign_in(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertDontSee(route('auth.google.redirect'));
        $response->assertDontSee('Continue with Google');
    }

    /**
     * Test 3: Google redirect route redirects to Google OAuth consent screen.
     */
    public function test_google_redirect_route_redirects_to_google_oauth(): void
    {
        $response = $this->get(route('auth.google.redirect'));

        $response->assertStatus(302);
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com', (string) $targetUrl);
    }

    /**
     * Test 4: Existing verified user can log in via Google without duplicate accounts.
     */
    public function test_existing_verified_user_can_log_in_via_google(): void
    {
        $user = User::factory()->create([
            'name' => 'Priya Nair',
            'email' => 'priya.nair@example.com',
            'role' => 'model',
            'google_id' => null,
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->mockSocialiteGoogleUser(
            email: 'priya.nair@example.com',
            id: 'google-999888',
            name: 'Priya Nair',
            avatar: 'https://lh3.googleusercontent.com/priya.jpg'
        );

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/');
        $response->assertSessionHas('status', 'Welcome back, Priya Nair!');

        // Check authentication
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, Auth::guard('web')->id());
        $this->assertFalse(Auth::guard('admin')->check());

        // Check account linking
        $user->refresh();
        $this->assertSame('google-999888', $user->google_id);
        $this->assertSame('https://lh3.googleusercontent.com/priya.jpg', $user->avatar);
        $this->assertSame('model', $user->role);

        // Ensure no duplicate account was created
        $this->assertSame(1, User::where('email', 'priya.nair@example.com')->count());
    }

    /**
     * Test 5: Existing unverified user is verified and logged in via Google.
     */
    public function test_existing_unverified_user_is_verified_and_logged_in_via_google(): void
    {
        $user = User::factory()->create([
            'name' => 'Rohan Varma',
            'email' => 'rohan.varma@example.com',
            'role' => 'photographer',
            'google_id' => null,
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => null,
        ]);

        $this->mockSocialiteGoogleUser(
            email: 'rohan.varma@example.com',
            id: 'google-rohan-123'
        );

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/');
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, Auth::guard('web')->id());

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('google-rohan-123', $user->google_id);
    }

    /**
     * Test 6: New user logs in via Google: account created as public user, verified, with 'user' role.
     */
    public function test_new_user_is_registered_and_logged_in_as_public_user(): void
    {
        $this->assertNull(User::where('email', 'ananya.k@example.com')->first());

        $this->mockSocialiteGoogleUser(
            email: 'ananya.k@example.com',
            id: 'google-ananya-456',
            name: 'Ananya Kapoor',
            avatar: 'https://lh3.googleusercontent.com/ananya.jpg'
        );

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/');
        $response->assertSessionHas('status', 'Welcome to IDFR, Ananya Kapoor!');

        $this->assertTrue(Auth::guard('web')->check());
        $user = User::where('email', 'ananya.k@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Ananya Kapoor', $user->name);
        $this->assertSame('google-ananya-456', $user->google_id);
        $this->assertSame('https://lh3.googleusercontent.com/ananya.jpg', $user->avatar);
        $this->assertSame('user', $user->role);
        $this->assertFalse($user->is_admin);
        $this->assertTrue($user->is_active);
        $this->assertNotNull($user->email_verified_at);
        $this->assertFalse(Auth::guard('admin')->check());
    }

    /**
     * Test 7: Administrator accounts are strictly rejected from public Google OAuth and redirected to admin login.
     */
    public function test_administrator_account_is_blocked_from_public_google_login(): void
    {
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@idfr.com',
            'role' => 'admin',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->mockSocialiteGoogleUser(
            email: 'admin@idfr.com',
            id: 'google-admin-001'
        );

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors(['email']);

        $this->assertFalse(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('admin')->check());
    }

    /**
     * Test 8: Deactivated user account is rejected and not authenticated.
     */
    public function test_deactivated_user_is_blocked_from_google_login(): void
    {
        $user = User::factory()->create([
            'name' => 'Deactivated User',
            'email' => 'deactivated@example.com',
            'is_active' => false,
            'deactivation_reason' => 'Violation of terms',
        ]);

        $this->mockSocialiteGoogleUser(
            email: 'deactivated@example.com',
            id: 'google-deact-789'
        );

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('idfr.home'));
        $response->assertSessionHas('error', 'Violation of terms');
        $this->assertFalse(Auth::guard('web')->check());
    }

    /**
     * Test 9: Public user authenticated via Google cannot access admin dashboard.
     */
    public function test_public_user_authenticated_via_google_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'role' => 'user',
            'is_admin' => false,
            'google_id' => 'google-reg-111',
        ]);

        $this->actingAs($user, 'web');

        $response = $this->get('/admin/dashboard');

        // Admin middleware redirects unauthorized users to /admin/login
        $response->assertRedirect('/admin/login');
    }
}

