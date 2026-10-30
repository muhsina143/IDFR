<?php

namespace Tests\Feature;

use App\Mail\AdminEnquiryNotificationMail;
use App\Mail\EnquiryVerificationOtpMail;
use App\Mail\VisitorEnquiryConfirmationMail;
use App\Models\CreativeProfessional;
use App\Models\EnquiryOtp;
use App\Models\ModelProfile;
use App\Models\ProfileEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfileEnquiryAndOtpTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $regularUser;

    protected ModelProfile $model;

    protected CreativeProfessional $creativePro;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@idfr.example'],
            [
                'name' => 'IDFR Director',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );

        $this->regularUser = User::firstOrCreate(
            ['email' => 'regular_test@idfr.example'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'role' => 'member',
            ]
        );

        $this->model = ModelProfile::create([
            'name' => 'Aarav Sharma',
            'slug' => 'aarav-sharma-test',
            'gender' => 'Male',
            'level' => 'Editorial & Runway',
            'age' => 24,
            'height' => "6'1\"",
            'location' => 'Mumbai, India',
            'experience' => '4 years',
            'profile_photo' => 'images/models/model-01.jpg',
            'types' => ['Runway', 'High Fashion'],
            'skills' => ['Ramp Walk', 'Editorial'],
            'specialities' => ['Couture', 'Bridal'],
            'availability' => 'Available for National & International Bookings',
            'description' => 'Experienced runway and couture model.',
            'status' => 'published',
        ]);

        $this->creativePro = CreativeProfessional::create([
            'name' => 'Zara Khan',
            'slug' => 'zara-khan-makeup',
            'category' => 'makeup-artist',
            'brand' => 'IDFR',
            'professional_title' => 'Senior Bridal & High Fashion Makeup Artist',
            'location' => 'Kochi, India',
            'experience' => '8 years',
            'biography' => 'Specializing in high fashion editorial and runway bridal looks.',
            'services' => ['Runway Makeup', 'Bridal Makeup', 'Editorial'],
            'profile_photo' => 'images/pros/makeup-01.jpg',
            'status' => 'published',
        ]);
    }

    /**
     * Test sending OTP generates code, stores hash and dispatches verification email.
     */
    public function test_visitor_can_request_otp(): void
    {
        Mail::fake();

        $response = $this->postJson(route('enquiry.send-otp'), [
            'email' => 'client@fashionhouse.com',
            'name' => 'Fashion Client',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cooldown_seconds' => 60,
            ]);

        $this->assertDatabaseHas('enquiry_otps', [
            'email' => 'client@fashionhouse.com',
            'attempts' => 0,
        ]);

        // Email OTP verification MUST still work and send the verification code
        Mail::assertSent(EnquiryVerificationOtpMail::class, function ($mail) {
            return $mail->hasTo('client@fashionhouse.com') && strlen($mail->otp) === 6;
        });
    }

    /**
     * Test resend cooldown rejects immediate second OTP request.
     */
    public function test_otp_resend_cooldown_is_enforced(): void
    {
        Mail::fake();

        // First OTP request
        $this->postJson(route('enquiry.send-otp'), [
            'email' => 'cooldown@client.com',
            'name' => 'Cooldown Tester',
        ])->assertOk();

        // Second OTP request immediately afterwards
        $second = $this->postJson(route('enquiry.send-otp'), [
            'email' => 'cooldown@client.com',
            'name' => 'Cooldown Tester',
        ]);

        $second->assertStatus(429)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test invalid OTP is rejected and remaining attempts decremented.
     */
    public function test_invalid_otp_is_rejected(): void
    {
        EnquiryOtp::create([
            'email' => 'verifier@client.com',
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'attempts' => 0,
        ]);

        $response = $this->postJson(route('enquiry.verify-otp'), [
            'email' => 'verifier@client.com',
            'otp' => '999999',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'attempts_remaining' => 4,
            ]);
    }

    /**
     * Test valid OTP is verified and stores session timestamp.
     */
    public function test_valid_otp_is_verified_successfully(): void
    {
        $otpRecord = EnquiryOtp::create([
            'email' => 'success@client.com',
            'otp_hash' => Hash::make('654321'),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'attempts' => 0,
        ]);

        $response = $this->postJson(route('enquiry.verify-otp'), [
            'email' => 'success@client.com',
            'otp' => '654321',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertNotNull($otpRecord->fresh()->verified_at);
    }

    /**
     * Test submitting enquiry without verifying email is rejected.
     */
    public function test_submitting_enquiry_without_email_otp_is_rejected(): void
    {
        $response = $this->postJson(route('enquiry.submit'), [
            'name' => 'Unverified Client',
            'email' => 'unverified@fashion.com',
            'phone' => '+91 9876543210',
            'assignment_type' => 'Runway Show',
            'preferred_date' => now()->addWeeks(2)->toDateString(),
            'location' => 'Kochi Fashion Arena',
            'brief' => 'Looking to book model for grand opening ceremony runway look.',
            'professional_type' => 'model',
            'professional_id' => $this->model->id,
            'professional_name' => $this->model->name,
            'professional_category' => 'Model',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test verified Model enquiry saves to DB with NO budget field and sends NO enquiry emails.
     */
    public function test_verified_model_enquiry_submission_succeeds_without_sending_emails(): void
    {
        Mail::fake();

        // Pre-verify email OTP
        EnquiryOtp::create([
            'email' => 'confirmed@client.com',
            'otp_hash' => Hash::make('888888'),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'verified_at' => now(),
            'attempts' => 0,
        ]);

        $response = $this->withSession([
            'verified_enquiry_email_'.md5('confirmed@client.com') => now()->timestamp,
        ])->postJson(route('enquiry.submit'), [
            'name' => 'Couture Producer',
            'email' => 'confirmed@client.com',
            'phone' => '+91 9998887777',
            'assignment_type' => 'Fashion Campaign',
            'preferred_date' => now()->addDays(10)->toDateString(),
            'location' => 'Bangalore Palace',
            'brief' => 'Editorial couture fashion campaign for Season 8 collection.',
            'professional_type' => 'model',
            'professional_id' => $this->model->id,
            'professional_name' => $this->model->name,
            'professional_category' => 'Model',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Your enquiry has been submitted successfully. Our team will contact you soon.',
            ]);

        $this->assertDatabaseHas('profile_enquiries', [
            'email' => 'confirmed@client.com',
            'professional_name' => 'Aarav Sharma',
            'professional_type' => 'model',
            'status' => 'new',
            'is_email_verified' => 1,
            'location' => 'Bangalore Palace',
        ]);

        // Assert budget column is completely removed from database
        $this->assertFalse(Schema::hasColumn('profile_enquiries', 'budget'));

        // Assert NO emails are sent on enquiry submission (neither to admin nor to visitor)
        Mail::assertNothingSent();
        Mail::assertNotSent(AdminEnquiryNotificationMail::class);
        Mail::assertNotSent(VisitorEnquiryConfirmationMail::class);
    }

    /**
     * Test verified Creative Professional enquiry saves to DB and sends NO emails.
     */
    public function test_verified_creative_professional_enquiry_succeeds_without_sending_emails(): void
    {
        Mail::fake();

        EnquiryOtp::create([
            'email' => 'creative.client@example.com',
            'otp_hash' => Hash::make('112233'),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'verified_at' => now(),
            'attempts' => 0,
        ]);

        $response = $this->withSession([
            'verified_enquiry_email_'.md5('creative.client@example.com') => now()->timestamp,
        ])->postJson(route('enquiry.submit'), [
            'name' => 'Bridal Agency',
            'email' => 'creative.client@example.com',
            'phone' => '+91 9888899999',
            'assignment_type' => 'Event',
            'preferred_date' => now()->addMonth()->toDateString(),
            'location' => 'Grand Hyatt Kochi',
            'brief' => 'High fashion runway bridal looks for gala event.',
            'professional_type' => 'creative_professional',
            'professional_id' => $this->creativePro->id,
            'professional_name' => $this->creativePro->name,
            'professional_category' => 'Makeup Artist',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('profile_enquiries', [
            'email' => 'creative.client@example.com',
            'professional_name' => 'Zara Khan',
            'professional_category' => 'Makeup Artist',
            'location' => 'Grand Hyatt Kochi',
        ]);

        // Zero emails sent on submission
        Mail::assertNothingSent();
    }

    /**
     * Test duplicate submissions within 60 seconds are safely handled.
     */
    public function test_duplicate_submission_within_60_seconds_is_prevented(): void
    {
        Mail::fake();

        ProfileEnquiry::create([
            'professional_type' => 'creative_professional',
            'professional_id' => $this->creativePro->id,
            'professional_name' => $this->creativePro->name,
            'professional_category' => 'Makeup Artist',
            'name' => 'Bridal Client',
            'email' => 'bridal@client.com',
            'phone' => '+91 9888877777',
            'assignment_type' => 'Event',
            'preferred_date' => now()->addMonth()->toDateString(),
            'location' => 'Grand Hyatt Kochi',
            'brief' => 'High fashion runway bridal look for gala night.',
            'is_email_verified' => true,
            'verified_at' => now(),
            'status' => 'new',
        ]);

        // Attempt duplicate submission
        $response = $this->withSession([
            'verified_enquiry_email_'.md5('bridal@client.com') => now()->timestamp,
        ])->postJson(route('enquiry.submit'), [
            'name' => 'Bridal Client',
            'email' => 'bridal@client.com',
            'phone' => '+91 9888877777',
            'assignment_type' => 'Event',
            'preferred_date' => now()->addMonth()->toDateString(),
            'location' => 'Grand Hyatt Kochi',
            'brief' => 'High fashion runway bridal look for gala night.',
            'professional_type' => 'creative_professional',
            'professional_id' => $this->creativePro->id,
            'professional_name' => $this->creativePro->name,
            'professional_category' => 'Makeup Artist',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        // Ensure only 1 record exists
        $this->assertEquals(1, ProfileEnquiry::where('email', 'bridal@client.com')->count());
    }

    /**
     * Test admin dashboard management and confirm budget is not displayed.
     */
    public function test_admin_can_manage_profile_enquiries_without_budget_field(): void
    {
        $enquiry = ProfileEnquiry::create([
            'professional_type' => 'model',
            'professional_id' => $this->model->id,
            'professional_name' => $this->model->name,
            'professional_category' => 'Model',
            'name' => 'Event Director',
            'email' => 'director@event.com',
            'phone' => '+91 9123456789',
            'assignment_type' => 'Runway Show',
            'location' => 'Goa Beach Resort',
            'brief' => 'Resort wear summer runway showcase.',
            'is_email_verified' => true,
            'status' => 'new',
        ]);

        // 1. Guest cannot access admin profile enquiries
        $this->get(route('admin.profile-enquiries.index'))
            ->assertRedirect(route('admin.login'));

        // 2. Admin can access index and see enquiry details without budget field
        $indexRes = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.profile-enquiries.index'));
        $indexRes->assertOk()
            ->assertSee('Event Director')
            ->assertSee('Aarav Sharma')
            ->assertDontSee('Budget:');

        // 3. Admin can view show page without budget field
        $showRes = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.profile-enquiries.show', $enquiry));
        $showRes->assertOk()
            ->assertSee('Resort wear summer runway showcase.')
            ->assertSee('Goa Beach Resort')
            ->assertDontSee('<span class="detail-label">Budget</span>', false);

        // 4. Admin can update status and notes
        $updateRes = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.profile-enquiries.update', $enquiry), [
                'status' => 'in_progress',
                'admin_notes' => 'Contract sent to client. Awaiting signature.',
            ]);
        $updateRes->assertRedirect();

        $this->assertDatabaseHas('profile_enquiries', [
            'id' => $enquiry->id,
            'status' => 'in_progress',
            'admin_notes' => 'Contract sent to client. Awaiting signature.',
        ]);

        // 5. Admin can delete enquiry
        $delRes = $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.profile-enquiries.destroy', $enquiry));
        $delRes->assertRedirect(route('admin.profile-enquiries.index'));

        $this->assertDatabaseMissing('profile_enquiries', [
            'id' => $enquiry->id,
        ]);
    }
}
