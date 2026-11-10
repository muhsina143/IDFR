<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Test Case 1: Existing registered user + Google Login = Successful login.
     */
    public function test_existing_registered_user_can_log_in_via_google_successfully(): void
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

        $response->assertRedirect(route('idfr.home'));
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
     * Test Case 2: Unregistered user + Google Login = Access denied with an error message.
     */
    public function test_unregistered_user_cannot_log_in_via_google_and_sees_error_message(): void
    {
        $this->assertNull(User::where('email', 'unknown.guest@example.com')->first());

        $this->mockSocialiteGoogleUser(
            email: 'unknown.guest@example.com',
            id: 'google-stranger-123',
            name: 'Unknown Guest'
        );

        $response = $this->get(route('auth.google.callback'));

        // Redirects back to login with required error message
        $response->assertRedirect(route('idfr.home', ['auth' => 'login']));
        $response->assertSessionHas('error', 'You are not registered yet. Please register first to access your account.');

        // User must NOT be authenticated
        $this->assertFalse(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('admin')->check());
    }

    /**
     * Test Case 3: Existing user + normal email/password login = Successful login.
     */
    public function test_existing_user_normal_email_password_login_successful(): void
    {
        $user = User::factory()->create([
            'name' => 'Kavya Menon',
            'email' => 'kavya.menon@example.com',
            'password' => Hash::make('secret123456'),
            'role' => 'hairstylist',
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'kavya.menon@example.com',
            'password' => 'secret123456',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Welcome back, Kavya Menon!',
            ]);

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, Auth::guard('web')->id());
    }

    /**
     * Test Case 4: Google Login must never create a new user account.
     */
    public function test_google_login_must_never_create_a_new_user_account(): void
    {
        $initialUserCount = User::count();

        $this->mockSocialiteGoogleUser(
            email: 'newbie.applicant@example.com',
            id: 'google-newbie-999',
            name: 'Newbie Applicant'
        );

        $response = $this->get(route('auth.google.callback'));

        // Verify zero database rows were inserted into users table
        $this->assertSame($initialUserCount, User::count());
        $this->assertNull(User::where('email', 'newbie.applicant@example.com')->first());
        $this->assertNull(User::where('google_id', 'google-newbie-999')->first());
        $this->assertFalse(Auth::guard('web')->check());
    }

    /**
     * Test: Existing unverified user logging in via verified Google account gets verified and authenticated.
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

        $response->assertRedirect(route('idfr.home'));
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, Auth::guard('web')->id());

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('google-rohan-123', $user->google_id);
    }

    /**
     * Test: Administrator accounts are strictly rejected from public Google OAuth.
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
     * Test: Deactivated user account is rejected and not authenticated.
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

        $response->assertRedirect(route('idfr.home', ['auth' => 'login']));
        $response->assertSessionHas('error', 'Violation of terms');
        $this->assertFalse(Auth::guard('web')->check());
    }

    /**
     * Test: Main website Login modal displays the Continue with Google button and OR divider.
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
     * Test: Admin login page does NOT display Google sign-in.
     */
    public function test_admin_login_page_does_not_contain_google_sign_in(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertDontSee(route('auth.google.redirect'));
        $response->assertDontSee('Continue with Google');
    }

    /**
     * Test: Public user authenticated via Google cannot access admin dashboard.
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
