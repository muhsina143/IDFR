<?php

namespace Tests\Feature;

use App\Mail\EmailVerificationOtpMail;
use App\Models\ModelProfile;
use App\Models\Season;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SeparateAuthAndMailDeliveryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Public user registers and receives a real OTP email.
     */
    public function test_scenario_1_public_user_registers_and_receives_otp_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/register', [
            'name' => 'Meera Nair',
            'email' => 'meera.nair@example.com',
            'phone' => '+91 98765 11111',
            'role' => 'model',
            'password' => 'pass123456',
            'password_confirmation' => 'pass123456',
            'terms' => true,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'email' => 'meera.nair@example.com',
                'email_sent' => true,
            ]);

        Mail::assertSent(EmailVerificationOtpMail::class, function ($mail) {
            return $mail->hasTo('meera.nair@example.com');
        });

        $user = User::where('email', 'meera.nair@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isEmailVerified());
        $this->assertNotNull($user->email_otp_hash);
        $this->assertNotNull($user->email_otp_expires_at);
        $this->assertTrue($user->email_otp_expires_at->isFuture());
    }

    /**
     * 1b. If email delivery fails, application shows clear error and allows safe retry without cooldown lockout.
     */
    public function test_scenario_1b_delivery_failure_shows_clear_error_and_allows_safe_retry(): void
    {
        // Mock Mail facade to throw an exception simulating SMTP rejection / failure
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new \RuntimeException('Connection could not be established with host smtp.gmail.com:587'));

        $response = $this->postJson('/register', [
            'name' => 'Rohan Sen',
            'email' => 'rohan.sen@example.com',
            'phone' => '+91 98765 22222',
            'role' => 'photographer',
            'password' => 'pass123456',
            'password_confirmation' => 'pass123456',
            'terms' => true,
        ]);

        $response->assertStatus(502)
            ->assertJson([
                'success' => false,
                'email_sent' => false,
                'can_retry' => true,
            ]);

        $user = User::where('email', 'rohan.sen@example.com')->first();
        $this->assertNotNull($user);

        // Crucial: Cooldown must be 0 so the user can immediately retry sending
        $otpService = app(OtpService::class);
        $this->assertEquals(0, $otpService->resendCooldownRemaining($user));
    }

    /**
     * 1c. Safe retry: user can retry registration with same email without duplicate account creation.
     */
    public function test_scenario_1c_safe_retry_without_duplicate_accounts(): void
    {
        Mail::fake();

        // Initial unverified registration record
        $initialUser = User::create([
            'name' => 'Aditi Sharma',
            'email' => 'aditi.retry@example.com',
            'phone' => '+91 98765 00001',
            'role' => 'model',
            'password' => Hash::make('pass123'),
            'email_verified_at' => null,
            'is_admin' => false,
        ]);

        $this->assertEquals(1, User::where('email', 'aditi.retry@example.com')->count());

        // User retries registration with same email address
        $retryResponse = $this->postJson('/register', [
            'name' => 'Aditi Sharma Updated',
            'email' => 'aditi.retry@example.com',
            'phone' => '+91 98765 00002',
            'role' => 'fashion-designer',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
            'terms' => true,
        ]);

        $retryResponse->assertStatus(201)
            ->assertJson([
                'success' => true,
                'email' => 'aditi.retry@example.com',
                'email_sent' => true,
            ]);

        // Still exactly 1 user row in database (no duplicate account created)
        $this->assertEquals(1, User::where('email', 'aditi.retry@example.com')->count());

        $initialUser->refresh();
        $this->assertEquals('Aditi Sharma Updated', $initialUser->name);
        $this->assertEquals('fashion-designer', $initialUser->role);
        $this->assertNotNull($initialUser->email_otp_hash);
    }

    /**
     * 2. Valid OTP verifies the public account.
     */
    public function test_scenario_2_valid_otp_verifies_public_account(): void
    {
        $user = User::create([
            'name' => 'Kavya Menon',
            'email' => 'kavya.menon@example.com',
            'phone' => '+91 98765 33333',
            'role' => 'makeup-artist',
            'password' => Hash::make('password123'),
            'email_otp_hash' => Hash::make('543210'),
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now(),
            'email_otp_attempts' => 0,
        ]);

        $response = $this->postJson('/verify-otp', [
            'email' => 'kavya.menon@example.com',
            'otp' => '543210',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'verified',
                'user' => [
                    'name' => 'Kavya Menon',
                    'initial' => 'K',
                    'role' => 'makeup-artist',
                ],
            ]);

        $user->refresh();
        $this->assertTrue($user->isEmailVerified());
        $this->assertNull($user->email_otp_hash);
        $this->assertNull($user->email_otp_expires_at);

        // Auto-authenticated on web guard
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($user->id, Auth::guard('web')->id());
    }

    /**
     * 3. An unverified user cannot log in.
     */
    public function test_scenario_3_unverified_user_cannot_log_in(): void
    {
        Mail::fake();

        User::create([
            'name' => 'Unverified Model',
            'email' => 'unverified@example.com',
            'phone' => '+91 98765 44444',
            'role' => 'model',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
            'email_otp_hash' => Hash::make('999888'),
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now(),
        ]);

        $response = $this->postJson('/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'requires_verification' => true,
                'email' => 'unverified@example.com',
            ]);

        $this->assertFalse(Auth::guard('web')->check());
    }

    /**
     * 4. A verified public user can log in and access their profile.
     */
    public function test_scenario_4_verified_public_user_can_log_in_and_access_profile(): void
    {
        $user = User::create([
            'name' => 'Ananya Pillai',
            'email' => 'ananya.pillai@example.com',
            'phone' => '+91 98765 55555',
            'role' => 'hairstylist',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/login', [
            'email' => 'ananya.pillai@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Ananya Pillai',
                    'initial' => 'A',
                    'role' => 'hairstylist',
                ],
            ]);

        $this->assertTrue(Auth::guard('web')->check());

        // Access role-specific protected profile route
        $profileResponse = $this->actingAs($user, 'web')->get(route('profile.hairstylist'));
        $profileResponse->assertStatus(200);
    }

    /**
     * 5. Admin can log in and access the Admin Dashboard.
     */
    public function test_scenario_5_admin_can_log_in_and_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Executive Director',
            'email' => 'director@idfr.example',
            'phone' => '+91 99999 00000',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('directorSecret!'),
            'email_verified_at' => now(),
        ]);

        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => 'director@idfr.example',
            'password' => 'directorSecret!',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertEquals($admin->id, Auth::guard('admin')->id());

        // Dashboard accessible
        $dashResponse = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $dashResponse->assertStatus(200);
    }

    /**
     * 6. Admin login does not automatically authenticate the admin as a public user.
     */
    public function test_scenario_6_admin_login_does_not_authenticate_as_public_user(): void
    {
        $admin = User::create([
            'name' => 'Managing Officer',
            'email' => 'officer@idfr.example',
            'phone' => '+91 99999 11111',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('officerPass123'),
            'email_verified_at' => now(),
        ]);

        $this->post(route('admin.login.submit'), [
            'email' => 'officer@idfr.example',
            'password' => 'officerPass123',
        ]);

        // Admin guard is authenticated
        $this->assertTrue(Auth::guard('admin')->check());

        // Web guard must remain unauthenticated!
        $this->assertFalse(Auth::guard('web')->check());

        // Public /auth/me API must report unauthenticated
        $authMeResponse = $this->getJson(route('auth.me'));
        $authMeResponse->assertStatus(200)
            ->assertJson([
                'authenticated' => false,
                'user' => null,
            ]);

        // Home page view must not display an authenticated user initial
        $homeResponse = $this->get(route('idfr.home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertDontSee('class="nav__user-badge"', false);
    }

    /**
     * 7. Public user login does not grant admin access.
     */
    public function test_scenario_7_public_user_login_does_not_grant_admin_access(): void
    {
        $user = User::create([
            'name' => 'Zoya Khan',
            'email' => 'zoya.model@example.com',
            'phone' => '+91 98765 66666',
            'role' => 'model',
            'password' => Hash::make('zoyaPassword123'),
            'email_verified_at' => now(),
        ]);

        $this->postJson('/login', [
            'email' => 'zoya.model@example.com',
            'password' => 'zoyaPassword123',
        ]);

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertFalse(Auth::guard('admin')->check());

        // Attempting to visit /admin must redirect to admin.login
        $adminAttempt = $this->actingAs($user, 'web')->get(route('admin.dashboard'));
        $adminAttempt->assertRedirect(route('admin.login'));
    }

    /**
     * 8. Admin logout does not unintentionally log out a separate public user session.
     */
    public function test_scenario_8_admin_logout_does_not_log_out_public_user_session(): void
    {
        $publicUser = User::create([
            'name' => 'Siddharth Roy',
            'email' => 'siddharth@example.com',
            'phone' => '+91 98765 77777',
            'role' => 'fashion-designer',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $adminUser = User::create([
            'name' => 'Admin Controller',
            'email' => 'controller@idfr.example',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('adminPass123'),
            'email_verified_at' => now(),
        ]);

        // Authenticate both guards in the same application instance
        Auth::guard('web')->login($publicUser);
        Auth::guard('admin')->login($adminUser);

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertTrue(Auth::guard('admin')->check());

        // Admin logs out
        $response = $this->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));

        // Admin guard is now logged out
        $this->assertFalse(Auth::guard('admin')->check());

        // Public user session MUST remain intact!
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertEquals($publicUser->id, Auth::guard('web')->id());
    }

    /**
     * 9. Public user logout does not unintentionally log out a separate admin session.
     */
    public function test_scenario_9_public_user_logout_does_not_log_out_admin_session(): void
    {
        $publicUser = User::create([
            'name' => 'Maya Joseph',
            'email' => 'maya@example.com',
            'phone' => '+91 98765 88888',
            'role' => 'videographer',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $adminUser = User::create([
            'name' => 'System Supervisor',
            'email' => 'supervisor@idfr.example',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('adminPass123'),
            'email_verified_at' => now(),
        ]);

        Auth::guard('web')->login($publicUser);
        Auth::guard('admin')->login($adminUser);

        $this->assertTrue(Auth::guard('web')->check());
        $this->assertTrue(Auth::guard('admin')->check());

        // Public user logs out
        $response = $this->postJson('/logout');
        $response->assertStatus(200);

        // Public user guard is now logged out
        $this->assertFalse(Auth::guard('web')->check());

        // Admin session MUST remain intact!
        $this->assertTrue(Auth::guard('admin')->check());
        $this->assertEquals($adminUser->id, Auth::guard('admin')->id());
    }

    /**
     * 10. Unauthenticated visitors cannot access protected admin or profile routes.
     */
    public function test_scenario_10_unauthenticated_visitors_rejected_from_protected_routes(): void
    {
        // Protected Admin route -> redirects to admin.login
        $adminRes = $this->get(route('admin.dashboard'));
        $adminRes->assertRedirect(route('admin.login'));

        $adminSeasonsRes = $this->get(route('admin.seasons'));
        $adminSeasonsRes->assertRedirect(route('admin.login'));

        // Protected public profile route -> rejects unauthenticated visitor
        $profileRes = $this->get(route('profile.model'));
        $profileRes->assertRedirect(route('idfr.home', ['auth' => 'login']));

        $profileDroneRes = $this->get(route('profile.drone-pilot'));
        $profileDroneRes->assertRedirect(route('idfr.home', ['auth' => 'login']));
    }

    /**
     * 11. Existing user profiles and admin functionality remain intact.
     */
    public function test_scenario_11_existing_profiles_and_admin_functionality_intact(): void
    {
        $season = Season::create([
            'number' => 9,
            'title' => 'Season 9',
            'year' => 2027,
            'location' => 'Kochi',
            'status' => 'active',
        ]);

        $modelProfile = ModelProfile::create([
            'name' => 'Arya Dev',
            'slug' => 'arya-dev',
            'gender' => 'female',
            'level' => 'professional',
            'status' => 'published',
        ]);

        // Professional workflow: can view models page and browse talent
        $member = User::create([
            'name' => 'Talent Scout',
            'email' => 'scout@creative.example',
            'phone' => '+91 98765 99999',
            'role' => 'photographer',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $this->actingAs($member, 'web');
        $modelsPage = $this->get(route('idfr.models'));
        $modelsPage->assertStatus(200);
        $modelsPage->assertSee('Booking Enquiry');

        // Admin functionality intact
        $admin = User::create([
            'name' => 'Lead Admin',
            'email' => 'lead@idfr.example',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('adminPass123'),
            'email_verified_at' => now(),
        ]);

        $adminSeasons = $this->actingAs($admin, 'admin')->get(route('admin.seasons'));
        $adminSeasons->assertStatus(200);
        $adminSeasons->assertSee('Season 9');
    }
}
