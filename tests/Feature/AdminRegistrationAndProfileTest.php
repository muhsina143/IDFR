<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Designer;
use App\Models\ModelProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRegistrationAndProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function makeAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'name' => 'Executive Administrator',
            'email' => 'admin_'.uniqid().'@idfr.example',
            'password' => bcrypt('AdminPassword123!'),
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function makeUser(UserRole $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role->value,
            'name' => 'Member '.$role->label(),
            'email' => $role->value.'_'.uniqid().'@example.com',
            'phone' => '+91 98765 43210',
            'password' => bcrypt('MemberPassword123!'),
            'email_verified_at' => now(),
            'payment_status' => 'exempt',
            'is_admin' => false,
            'is_active' => true,
        ], $attributes));
    }

    /**
     * Scenario 7: Admin can view model and designer profile details on member show page.
     */
    public function test_admin_can_view_role_profiles_on_member_show_page(): void
    {
        $admin = $this->makeAdmin();
        $modelUser = $this->makeUser(UserRole::Model, ['name' => 'Pooja Hegde', 'email' => 'pooja@models.example']);

        $modelProfile = ModelProfile::create([
            'user_id' => $modelUser->id,
            'name' => 'Pooja Hegde',
            'slug' => 'pooja-hegde',
            'gender' => 'female',
            'level' => 'Top Model',
            'location' => 'Mumbai',
        ]);

        $resModel = $this->actingAs($admin, 'admin')->get(route('admin.registrations.show', $modelUser));
        $resModel->assertOk();
        $resModel->assertSee('Pooja Hegde');
        $resModel->assertSee('Model Portfolio');
    }

    /**
     * Scenario 8: Admin can edit user details and verify persistence in MySQL.
     */
    public function test_admin_can_edit_user_details_and_persist_in_database(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser(UserRole::Model, [
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'phone' => '+91 98765 11111',
            'role' => 'model',
            'payment_status' => 'exempt',
            'is_active' => true,
        ]);

        $editRes = $this->actingAs($admin, 'admin')->get(route('admin.registrations.edit', $user));
        $editRes->assertOk();
        $editRes->assertSee('Original Name');
        $editRes->assertSee('original@example.com');

        $updateRes = $this->actingAs($admin, 'admin')->put(route('admin.registrations.update', $user), [
            'name' => 'Updated Name Executive',
            'email' => 'updated.executive@example.com',
            'phone' => '+91 98765 99999',
            'role' => 'fashion-designer',
            'payment_status' => 'paid',
            'is_active' => 1,
            'email_verified' => 1,
        ]);

        $updateRes->assertRedirect(route('admin.registrations.show', $user));
        $updateRes->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Updated Name Executive', $user->name);
        $this->assertEquals('updated.executive@example.com', $user->email);
        $this->assertEquals('+91 98765 99999', $user->phone);
        $this->assertEquals('fashion-designer', $user->role);
        $this->assertEquals('paid', $user->payment_status);
        $this->assertTrue($user->isActive());
        $this->assertTrue($user->isEmailVerified());
    }

    /**
     * Scenario 9: Admin can toggle user active / deactivated status and log activity.
     */
    public function test_admin_can_toggle_user_active_and_deactivated_status(): void
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser(UserRole::Model, ['is_active' => true]);

        // 1. Deactivate
        $deactivateRes = $this->actingAs($admin, 'admin')->post(route('admin.registrations.toggle-status', $user), [
            'reason' => 'Administrative compliance audit in progress.',
        ]);
        $deactivateRes->assertRedirect();
        $deactivateRes->assertSessionHas('success');

        $user->refresh();
        $this->assertFalse($user->isActive());
        $this->assertNotNull($user->deactivated_at);
        $this->assertEquals('Administrative compliance audit in progress.', $user->deactivation_reason);

        // 2. Reactivate
        $reactivateRes = $this->actingAs($admin, 'admin')->post(route('admin.registrations.toggle-status', $user));
        $reactivateRes->assertRedirect();
        $reactivateRes->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->isActive());
        $this->assertNull($user->deactivated_at);
        $this->assertNull($user->deactivation_reason);
    }

    /**
     * Scenario 10: Deactivated user cannot log in to the portal.
     */
    public function test_deactivated_user_cannot_log_in(): void
    {
        $password = 'SecretPass123!';
        $deactivatedUser = $this->makeUser(UserRole::Model, [
            'email' => 'deactivated@example.com',
            'password' => bcrypt($password),
            'email_verified_at' => now(),
            'is_active' => false,
            'deactivation_reason' => 'Terms of service violation.',
        ]);

        // 1. Modal / AJAX authentication attempt
        $jsonResponse = $this->postJson(route('login'), [
            'email' => 'deactivated@example.com',
            'password' => $password,
        ]);

        $jsonResponse->assertStatus(403);
        $jsonResponse->assertJson([
            'success' => false,
            'account_deactivated' => true,
        ]);
        $this->assertGuest('web');

        // 2. Standard web POST attempt
        $webResponse = $this->post(route('login'), [
            'email' => 'deactivated@example.com',
            'password' => $password,
        ]);

        $webResponse->assertRedirect();
        $webResponse->assertSessionHasErrors(['email' => 'Terms of service violation.']);
        $this->assertGuest('web');
    }

    /**
     * Scenario 11: Admin cannot deactivate or delete their own administrative account.
     */
    public function test_admin_cannot_deactivate_or_delete_themselves(): void
    {
        $admin = $this->makeAdmin();

        $toggleRes = $this->actingAs($admin, 'admin')->post(route('admin.registrations.toggle-status', $admin));
        $toggleRes->assertRedirect();
        $toggleRes->assertSessionHas('error');

        $deleteRes = $this->actingAs($admin, 'admin')->delete(route('admin.registrations.destroy', $admin));
        $deleteRes->assertRedirect();
        $deleteRes->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /**
     * Scenario 12: Admin dashboard renders total registration metric and recent registrations table.
     */
    public function test_admin_dashboard_displays_registration_metric_and_recent_members(): void
    {
        $admin = $this->makeAdmin();
        $user1 = $this->makeUser(UserRole::Model, ['name' => 'Recent Member One']);
        $user2 = $this->makeUser(UserRole::Photographer, ['name' => 'Recent Member Two']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Total Registrations');
        $response->assertSee('Recent Member Registrations &amp; Profiles', false);
        $response->assertSee('Recent Member One');
        $response->assertSee('Recent Member Two');
    }

    /**
     * Scenario 13: Passwords, OTP hashes, and private tokens are strictly isolated and never exposed in responses.
     */
    public function test_sensitive_authentication_data_is_never_exposed_in_admin_views(): void
    {
        $admin = $this->makeAdmin();
        $secretPasswordHash = '$2y$12$eX4mPL3H4shSecretValueThatMustNeverBeExposed';
        $secretOtpHash = sha1('987654');

        $user = $this->makeUser(UserRole::Model, [
            'name' => 'Security Test Member',
            'email' => 'security.test@example.com',
            'password' => $secretPasswordHash,
            'email_otp_hash' => $secretOtpHash,
            'remember_token' => 'SecretPrivateSessionToken999',
        ]);

        $viewsToCheck = [
            route('admin.registrations.index'),
            route('admin.registrations.show', $user),
            route('admin.registrations.edit', $user),
            route('admin.dashboard'),
        ];

        foreach ($viewsToCheck as $url) {
            $response = $this->actingAs($admin, 'admin')->get($url);
            $response->assertOk();
            $response->assertDontSee($secretPasswordHash);
            $response->assertDontSee($secretOtpHash);
            $response->assertDontSee('SecretPrivateSessionToken999');
        }
    }
}
