<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Mail\EmailVerificationOtpMail;
use App\Models\User;
use App\Services\RegistrationPaymentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationAndEmailOtpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to build valid registration payload.
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Aditi Sharma',
            'email' => 'aditi@example.com',
            'phone' => '+91 98765 43210',
            'role' => 'model',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'terms' => true,
        ], $overrides);
    }

    /**
     * 1. Registration with each valid role.
     */
    public function test_registration_with_each_valid_role(): void
    {
        Mail::fake();

        $roles = UserRole::registrationValues();

        foreach ($roles as $index => $role) {
            $email = "user_{$index}@idfr-test.com";

            $response = $this->postJson('/register', $this->validPayload([
                'email' => $email,
                'role' => $role,
            ]));

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'email' => $email,
                ]);

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'role' => $role,
                'is_admin' => false,
                'email_verified_at' => null,
            ]);
        }

        Mail::assertSent(EmailVerificationOtpMail::class, count($roles));
    }

    /**
     * 2. Rejection of invalid role values.
     */
    public function test_rejection_of_invalid_role_values(): void
    {
        Mail::fake();

        $invalidRoles = ['admin', 'superadmin', 'hacker', 'guest', ''];

        foreach ($invalidRoles as $invalidRole) {
            $response = $this->postJson('/register', $this->validPayload([
                'email' => 'test_'.uniqid().'@example.com',
                'role' => $invalidRole,
            ]));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['role']);
        }

        Mail::assertNothingSent();
    }

    /**
     * 3. Prevention of multiple-role selection.
     */
    public function test_prevention_of_multiple_role_selection(): void
    {
        Mail::fake();

        $response = $this->postJson('/register', $this->validPayload([
            'email' => 'multirole@example.com',
            'role' => ['model', 'photographer'],
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        $this->assertDatabaseMissing('users', ['email' => 'multirole@example.com']);
    }

    /**
     * 4. Duplicate email validation.
     */
    public function test_duplicate_email_validation(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/register', $this->validPayload([
            'email' => 'existing@example.com',
        ]));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * 5. Password hashing and password validation.
     */
    public function test_password_hashing_and_password_validation(): void
    {
        Mail::fake();

        // 5a. Password confirmation mismatch
        $response = $this->postJson('/register', $this->validPayload([
            'password' => 'secret123',
            'password_confirmation' => 'mismatch456',
        ]));
        $response->assertStatus(422)->assertJsonValidationErrors(['password']);

        // 5b. Password too short
        $responseShort = $this->postJson('/register', $this->validPayload([
            'password' => '123',
            'password_confirmation' => '123',
        ]));
        $responseShort->assertStatus(422)->assertJsonValidationErrors(['password']);

        // 5c. Valid registration stores hashed password
        $responseOk = $this->postJson('/register', $this->validPayload([
            'email' => 'securepass@example.com',
            'password' => 'superSecretPassword!',
            'password_confirmation' => 'superSecretPassword!',
        ]));
        $responseOk->assertStatus(201);

        $user = User::where('email', 'securepass@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('superSecretPassword!', $user->password);
        $this->assertTrue(Hash::check('superSecretPassword!', $user->password));
    }

    /**
     * 6. Actual OTP email delivery when mail service is invoked.
     */
    public function test_otp_email_delivery_when_invoked(): void
    {
        Mail::fake();

        $response = $this->postJson('/register', $this->validPayload([
            'email' => 'mailer_check@example.com',
        ]));

        $response->assertStatus(201);

        Mail::assertSent(EmailVerificationOtpMail::class, function ($mail) {
            return $mail->hasTo('mailer_check@example.com') &&
                   strlen($mail->otp) === 6 &&
                   ctype_digit($mail->otp);
        });
    }

    /**
     * 7. Successful OTP verification.
     */
    public function test_successful_otp_verification(): void
    {
        $user = User::create([
            'name' => 'Meera Patel',
            'email' => 'meera@example.com',
            'phone' => '+91 98765 00000',
            'role' => 'model',
            'password' => Hash::make('password123'),
            'email_otp_hash' => Hash::make('654321'),
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now(),
            'email_otp_attempts' => 0,
        ]);

        $this->assertFalse($user->isEmailVerified());

        $response = $this->postJson('/verify-otp', [
            'email' => 'meera@example.com',
            'otp' => '654321',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Email verified successfully! Welcome to IDFR.',
            ]);

        $user->refresh();
        $this->assertTrue($user->isEmailVerified());
        $this->assertNull($user->email_otp_hash);
        $this->assertNull($user->email_otp_expires_at);
        $this->assertEquals(0, $user->email_otp_attempts);

        // Verify user was logged in
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 8. Rejection of incorrect, expired, and previously used OTPs.
     */
    public function test_rejection_of_incorrect_expired_and_previously_used_otps(): void
    {
        // 8a. Incorrect OTP
        $user = User::create([
            'name' => 'Kiran Rao',
            'email' => 'kiran@example.com',
            'phone' => '+91 98765 11111',
            'role' => 'hairstylist',
            'password' => Hash::make('password123'),
            'email_otp_hash' => Hash::make('123456'),
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now(),
            'email_otp_attempts' => 0,
        ]);

        $resWrong = $this->postJson('/verify-otp', [
            'email' => 'kiran@example.com',
            'otp' => '999999',
        ]);
        $resWrong->assertStatus(422)
            ->assertJson(['success' => false, 'status' => 'invalid']);

        $user->refresh();
        $this->assertEquals(1, $user->email_otp_attempts);
        $this->assertFalse($user->isEmailVerified());

        // 8b. Expired OTP
        $user->email_otp_expires_at = now()->subMinute();
        $user->save();

        $resExpired = $this->postJson('/verify-otp', [
            'email' => 'kiran@example.com',
            'otp' => '123456',
        ]);
        $resExpired->assertStatus(422)
            ->assertJson(['success' => false, 'status' => 'expired']);

        // 8c. Previously used (invalidated) OTP
        $user->email_verified_at = now();
        $user->email_otp_hash = null;
        $user->save();

        $resUsed = $this->postJson('/verify-otp', [
            'email' => 'kiran@example.com',
            'otp' => '123456',
        ]);
        $resUsed->assertStatus(200)
            ->assertJson(['status' => 'already_verified']);
    }

    /**
     * 9. Resend cooldown and verification attempt limits.
     */
    public function test_resend_cooldown_and_verification_attempt_limits(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Rahul Verma',
            'email' => 'rahul@example.com',
            'phone' => '+91 98765 22222',
            'role' => 'photographer',
            'password' => Hash::make('password123'),
            'email_otp_hash' => Hash::make('111222'),
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now()->subSeconds(20), // 20s ago (cooldown 60s)
            'email_otp_attempts' => 0,
        ]);

        // Resend during active cooldown (within 60s)
        $resCooldown = $this->postJson('/resend-otp', ['email' => 'rahul@example.com']);
        $resCooldown->assertStatus(429)
            ->assertJson(['success' => false]);

        // Resend after 60s has passed
        $user->email_otp_sent_at = now()->subSeconds(65);
        $user->save();

        $resCooldownPassed = $this->postJson('/resend-otp', ['email' => 'rahul@example.com']);
        $resCooldownPassed->assertStatus(200)
            ->assertJson(['success' => true]);

        // Attempt limits: 5 failed attempts locks OTP
        $user->refresh();
        $user->email_otp_attempts = 5;
        $user->save();

        $resLocked = $this->postJson('/verify-otp', [
            'email' => 'rahul@example.com',
            'otp' => '111222',
        ]);
        $resLocked->assertStatus(422)
            ->assertJson(['success' => false, 'status' => 'max_attempts_exceeded']);
    }

    /**
     * 10. Prevention of login before email verification.
     */
    public function test_prevention_of_login_before_email_verification(): void
    {
        $user = User::create([
            'name' => 'Sanjay Roy',
            'email' => 'sanjay@example.com',
            'phone' => '+91 98765 33333',
            'role' => 'drone-pilot',
            'password' => Hash::make('secret123'),
            'email_verified_at' => null, // Unverified
        ]);

        $response = $this->postJson('/login', [
            'email' => 'sanjay@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'requires_verification' => true,
                'email' => 'sanjay@example.com',
            ]);

        $this->assertGuest();
    }

    /**
     * 11. Successful login after verification.
     */
    public function test_successful_login_after_verification(): void
    {
        $user = User::create([
            'name' => 'Pooja Hegde',
            'email' => 'pooja@example.com',
            'phone' => '+91 98765 44444',
            'role' => 'fashion-designer',
            'password' => Hash::make('secret123'),
            'email_verified_at' => now(), // Verified
        ]);

        $response = $this->postJson('/login', [
            'email' => 'pooja@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Pooja Hegde',
                    'email' => 'pooja@example.com',
                    'role' => 'fashion-designer',
                ],
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * 12. Registration without any payment gateway.
     */
    public function test_registration_without_any_payment_gateway(): void
    {
        Mail::fake();

        $response = $this->postJson('/register', $this->validPayload([
            'email' => 'nopayment@example.com',
        ]));

        $response->assertStatus(201);

        $user = User::where('email', 'nopayment@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('exempt', $user->payment_status);

        $policy = app(RegistrationPaymentPolicy::class);
        $this->assertFalse($policy->requiresPayment($user));
        $this->assertTrue($policy->isEligible($user));
    }

    /**
     * 13. Preservation of existing users and profile data.
     */
    public function test_preservation_of_existing_users_and_profile_data(): void
    {
        $existingAdmin = User::create([
            'name' => 'Chief Administrator',
            'email' => 'admin@idfr.com',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('adminPass123'),
            'email_verified_at' => now(),
        ]);

        $this->assertTrue($existingAdmin->isAdmin());
        $this->assertTrue($existingAdmin->isEmailVerified());
        $this->assertDatabaseHas('users', ['email' => 'admin@idfr.com', 'is_admin' => true]);
    }

    /**
     * 14. Compatibility with existing website and Admin Dashboard protection.
     */
    public function test_compatibility_with_existing_website_and_admin_protection(): void
    {
        // 14a. Regular verified registered user cannot access admin routes
        $regularUser = User::create([
            'name' => 'Model Member',
            'email' => 'model@example.com',
            'role' => 'model',
            'is_admin' => false,
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($regularUser)->get('/admin');
        // AdminMiddleware should block non-admin and redirect with error
        $response->assertRedirect(route('admin.login'));

        // 14b. Admin user CAN access admin dashboard
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'superadmin@example.com',
            'role' => 'admin',
            'is_admin' => true,
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $responseAdmin = $this->actingAs($adminUser, 'admin')->get('/admin');
        $responseAdmin->assertStatus(200);

        // 14c. Public routes remain accessible
        $responsePublic = $this->get('/');
        $responsePublic->assertStatus(200);
    }
}
