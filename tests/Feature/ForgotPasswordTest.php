<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use Mockery;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Scenario 1: Registered email -> Reset email sent and token stored in password_reset_tokens.
     */
    public function test_scenario_1_registered_email_triggers_password_reset_email_and_token_storage(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Sara Ali',
            'email' => 'sara.ali@example.com',
            'role' => 'model',
            'is_admin' => false,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('password.email'), [
            'email' => 'sara.ali@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        // Verify Mail was sent to user
        Mail::assertSent(PasswordResetMail::class, function ($mail) {
            return $mail->hasTo('sara.ali@example.com')
                && str_contains($mail->resetUrl, '/reset-password/')
                && str_contains($mail->resetUrl, 'email=sara.ali%40example.com');
        });

        // Verify token exists in database table
        $record = DB::table('password_reset_tokens')->where('email', 'sara.ali@example.com')->first();
        $this->assertNotNull($record);
        $this->assertNotEmpty($record->token);
    }

    /**
     * Scenario 2: Unregistered email -> Safe generic response, zero emails sent, no token saved.
     */
    public function test_scenario_2_unregistered_email_returns_safe_generic_response(): void
    {
        Mail::fake();

        $this->assertNull(User::where('email', 'notregistered@example.com')->first());

        $response = $this->postJson(route('password.email'), [
            'email' => 'notregistered@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'If an account exists with this email address, we have sent a password reset link.',
            ]);

        Mail::assertNothingSent();
        $this->assertNull(DB::table('password_reset_tokens')->where('email', 'notregistered@example.com')->first());
    }

    /**
     * Scenario 3: Valid reset link -> Reset password page opens with form.
     */
    public function test_scenario_3_valid_reset_link_opens_reset_password_page(): void
    {
        $user = User::factory()->create([
            'email' => 'tara.singh@example.com',
            'role' => 'fashion-designer',
            'is_admin' => false,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => 'tara.singh@example.com',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
        $response->assertSee('Reset Your Password');
        $response->assertSee('tara.singh@example.com');
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
    }

    /**
     * Scenario 4: Valid token + matching passwords -> Password successfully changed.
     */
    public function test_scenario_4_valid_token_and_matching_passwords_resets_password(): void
    {
        $user = User::factory()->create([
            'email' => 'kabir.khan@example.com',
            'password' => Hash::make('old_password_123'),
            'role' => 'photographer',
            'is_admin' => false,
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'kabir.khan@example.com',
            'password' => 'new_secret_456',
            'password_confirmation' => 'new_secret_456',
        ]);

        $response->assertRedirect(route('password.reset', ['token' => 'completed']));
        $response->assertSessionHas('status', 'Your password has been reset successfully. You can now log in with your new password.');

        $user->refresh();
        $this->assertTrue(Hash::check('new_secret_456', $user->password));
        $this->assertFalse(Hash::check('old_password_123', $user->password));
    }

    /**
     * Scenario 5: Invalid token -> Reset rejected.
     */
    public function test_scenario_5_invalid_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'maya.verma@example.com',
            'password' => Hash::make('original_pass_123'),
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'completely-invalid-bogus-token',
            'email' => 'maya.verma@example.com',
            'password' => 'new_attempt_456',
            'password_confirmation' => 'new_attempt_456',
        ]);

        $response->assertSessionHasErrors(['email']);

        $user->refresh();
        $this->assertTrue(Hash::check('original_pass_123', $user->password));
    }

    /**
     * Scenario 6: Expired token (>60 mins) -> Reset rejected.
     */
    public function test_scenario_6_expired_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'varun.roy@example.com',
            'password' => Hash::make('start_pass_123'),
        ]);

        $token = Password::broker()->createToken($user);

        // Advance clock past expiration (61 minutes)
        Carbon::setTestNow(now()->addMinutes(61));

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'varun.roy@example.com',
            'password' => 'new_pass_after_expiry_1',
            'password_confirmation' => 'new_pass_after_expiry_1',
        ]);

        $response->assertSessionHasErrors(['email']);

        $user->refresh();
        $this->assertTrue(Hash::check('start_pass_123', $user->password));
    }

    /**
     * Scenario 7: Mismatched passwords -> Validation error.
     */
    public function test_scenario_7_mismatched_passwords_returns_validation_error(): void
    {
        $user = User::factory()->create([
            'email' => 'neha.joshi@example.com',
            'password' => Hash::make('current_pass_123'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'neha.joshi@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'mismatched_confirm_456',
        ]);

        $response->assertSessionHasErrors(['password']);

        $user->refresh();
        $this->assertTrue(Hash::check('current_pass_123', $user->password));
    }

    /**
     * Scenario 8: Reused token -> Reset rejected.
     */
    public function test_scenario_8_reused_token_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'arjun.sen@example.com',
            'password' => Hash::make('pass_one_123'),
        ]);

        $token = Password::broker()->createToken($user);

        // First reset: succeeds
        $first = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'arjun.sen@example.com',
            'password' => 'pass_two_456',
            'password_confirmation' => 'pass_two_456',
        ]);
        $first->assertRedirect(route('password.reset', ['token' => 'completed']));

        // Second reset with SAME token: must fail
        $second = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'arjun.sen@example.com',
            'password' => 'pass_three_789',
            'password_confirmation' => 'pass_three_789',
        ]);
        $second->assertSessionHasErrors(['email']);

        $user->refresh();
        $this->assertTrue(Hash::check('pass_two_456', $user->password));
        $this->assertFalse(Hash::check('pass_three_789', $user->password));
    }

    /**
     * Scenario 9 & 10: Old password fails, new password logs in successfully.
     */
    public function test_scenario_9_and_10_old_password_fails_and_new_password_logs_in(): void
    {
        $user = User::factory()->create([
            'email' => 'disha.patel@example.com',
            'password' => Hash::make('old_valid_pass_123'),
            'role' => 'model',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $token = Password::broker()->createToken($user);

        // Reset password
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'disha.patel@example.com',
            'password' => 'brand_new_pass_789',
            'password_confirmation' => 'brand_new_pass_789',
        ]);

        // Attempt login with OLD password -> rejected
        $oldLogin = $this->postJson(route('login'), [
            'email' => 'disha.patel@example.com',
            'password' => 'old_valid_pass_123',
        ]);
        $oldLogin->assertStatus(422);
        $this->assertFalse(Auth::guard('web')->check());

        // Attempt login with NEW password -> successful
        $newLogin = $this->postJson(route('login'), [
            'email' => 'disha.patel@example.com',
            'password' => 'brand_new_pass_789',
        ]);
        $newLogin->assertStatus(200);
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, Auth::guard('web')->id());
    }

    /**
     * Scenario 11: Google login functionality remains intact.
     */
    public function test_scenario_11_google_login_remains_intact(): void
    {
        $user = User::factory()->create([
            'name' => 'Pooja Hegde',
            'email' => 'pooja.h@example.com',
            'role' => 'model',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-pooja-777');
        $socialiteUser->shouldReceive('getName')->andReturn('Pooja Hegde');
        $socialiteUser->shouldReceive('getEmail')->andReturn('pooja.h@example.com');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock(AbstractProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));
        $response->assertRedirect(route('idfr.home'));
        $this->assertTrue(Auth::guard('web')->check());
        $this->assertSame($user->id, Auth::guard('web')->id());
    }

    /**
     * Scenario 12: Admin login isolation remains intact (Admin cannot use public forgot password).
     */
    public function test_scenario_12_admin_login_isolation_remains_intact(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'name' => 'Root Admin',
            'email' => 'admin@idfr.com',
            'role' => 'admin',
            'is_admin' => true,
            'is_active' => true,
        ]);

        // Attempting to send public forgot password link to an admin returns generic response without emailing token
        $response = $this->postJson(route('password.email'), [
            'email' => 'admin@idfr.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'If an account exists with this email address, we have sent a password reset link.',
            ]);

        Mail::assertNothingSent();
        $this->assertNull(DB::table('password_reset_tokens')->where('email', 'admin@idfr.com')->first());
    }
}
