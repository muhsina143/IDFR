<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ModelProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CastingInquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function makeUser(UserRole $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role->value,
            'name' => 'Test '.$role->label(),
            'email' => $role->value.'_'.uniqid().'@example.com',
            'phone' => '+91 98765 12345',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
            'is_admin' => false,
        ], $attributes));
    }

    protected function makeAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'name' => 'Admin User',
            'email' => 'admin_'.uniqid().'@idfr.example',
            'is_admin' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * Test 1: Registering as recruiter is rejected by role validation.
     */
    public function test_registering_with_recruiter_role_is_rejected(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Attempted Recruiter',
            'email' => 'recruiter_attempt@example.com',
            'phone' => '+91 98765 43210',
            'role' => 'recruiter',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'terms' => '1',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role']);
    }

    /**
     * Test 2: Casting contact check endpoint is removed and returns 404.
     */
    public function test_casting_contact_check_endpoint_is_removed(): void
    {
        $model = ModelProfile::create([
            'name' => 'Pooja Test',
            'slug' => 'pooja-test',
            'gender' => 'female',
            'level' => 'Professional',
        ]);

        $response = $this->get("/models/{$model->id}/contact-check");
        $response->assertNotFound();
    }

    /**
     * Test 3: Public casting inquiry submission endpoint is removed and returns 404.
     */
    public function test_casting_inquiry_store_endpoint_is_removed(): void
    {
        $response = $this->post('/casting-inquiry', [
            'model_id' => 1,
            'project_name' => 'Test Campaign',
        ]);

        $response->assertNotFound();
    }

    /**
     * Test 4: Admin casting inquiries management routes are removed and return 404.
     */
    public function test_admin_casting_inquiries_routes_are_removed(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'admin')
            ->get('/admin/casting-inquiries')
            ->assertNotFound();

        $this->actingAs($admin, 'admin')
            ->get('/admin/casting-inquiries/1')
            ->assertNotFound();
    }

    /**
     * Test 5: Casting Recruiter is not listed in UserRole enum cases or registration values.
     */
    public function test_recruiter_is_not_in_user_role_enum(): void
    {
        $roles = UserRole::registrationValues();

        $this->assertNotContains('recruiter', $roles);
        $this->assertNull(UserRole::tryFrom('recruiter'));
    }
}
