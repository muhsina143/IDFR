<?php

namespace Tests\Feature;

use App\Mail\AdminEnquiryNotificationMail;
use App\Mail\EnquiryVerificationOtpMail;
use App\Mail\VisitorEnquiryConfirmationMail;
use App\Models\EnquiryOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AllCategoriesEnquiryTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

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
    }

    /**
     * Test all categories can submit an enquiry successfully without triggering any enquiry emails.
     */
    public function test_all_professional_categories_can_submit_enquiry_without_emails(): void
    {
        $categories = [
            ['type' => 'model', 'category' => 'Model', 'name' => 'Aditi Rao', 'assignment' => 'Runway Show'],
            ['type' => 'creative_professional', 'category' => 'Makeup Artist', 'name' => 'Meera Patel', 'assignment' => 'Photoshoot'],
            ['type' => 'creative_professional', 'category' => 'Hairstylist', 'name' => 'Rohan Varma', 'assignment' => 'Fashion Campaign'],
            ['type' => 'creative_professional', 'category' => 'Photographer', 'name' => 'Karan Kapoor', 'assignment' => 'Commercial Advertisement'],
            ['type' => 'designer', 'category' => 'Fashion Designer', 'name' => 'Sabyasachi Mukherjee', 'assignment' => 'Brand Promotion'],
            ['type' => 'creative_professional', 'category' => 'Videographer', 'name' => 'Vikram Seth', 'assignment' => 'Event'],
            ['type' => 'creative_professional', 'category' => 'Drone Pilot', 'name' => 'Arjun Nair', 'assignment' => 'Other'],
            ['type' => 'creative_professional', 'category' => 'Photo & Video Editor', 'name' => 'Devika Pillai', 'assignment' => 'Fashion Campaign'],
            ['type' => 'creative_professional', 'category' => 'Jewellery Designer', 'name' => 'Ananya Sen', 'assignment' => 'Runway Show'],
        ];

        foreach ($categories as $index => $item) {
            Mail::fake();

            $email = 'client.category_'.$index.'@test.com';

            // Verify email OTP verification works
            $otpRes = $this->postJson(route('enquiry.send-otp'), [
                'email' => $email,
                'name' => 'Client '.$index,
            ]);
            $otpRes->assertOk();
            Mail::assertSent(EnquiryVerificationOtpMail::class);

            // Pre-verify OTP in DB & session
            EnquiryOtp::where('email', $email)->update(['verified_at' => now()]);

            // Submit enquiry without budget
            $submitRes = $this->withSession([
                'verified_enquiry_email_'.md5($email) => now()->timestamp,
            ])->postJson(route('enquiry.submit'), [
                'name' => 'Client for '.$item['category'],
                'email' => $email,
                'phone' => '+91 911112222'.$index,
                'assignment_type' => $item['assignment'],
                'preferred_date' => now()->addDays(14)->toDateString(),
                'location' => 'Kochi Grand Arena',
                'brief' => 'Requirement description for '.$item['category'].' assignment.',
                'professional_type' => $item['type'],
                'professional_id' => $index + 1,
                'professional_name' => $item['name'],
                'professional_category' => $item['category'],
            ]);

            $submitRes->assertOk()
                ->assertJson([
                    'success' => true,
                    'message' => 'Your enquiry has been submitted successfully. Our team will contact you soon.',
                ]);

            // Assert saved in database
            $this->assertDatabaseHas('profile_enquiries', [
                'email' => $email,
                'professional_name' => $item['name'],
                'professional_category' => $item['category'],
                'professional_type' => $item['type'],
                'assignment_type' => $item['assignment'],
                'status' => 'new',
            ]);

            // Assert NO enquiry notification or confirmation emails were sent
            Mail::assertNotSent(AdminEnquiryNotificationMail::class);
            Mail::assertNotSent(VisitorEnquiryConfirmationMail::class);
        }

        // Assert database schema has no budget column
        $this->assertFalse(Schema::hasColumn('profile_enquiries', 'budget'));

        // Assert all 9 enquiries appear in Admin Dashboard
        $adminRes = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.profile-enquiries.index'));
        $adminRes->assertOk();

        foreach ($categories as $item) {
            $adminRes->assertSee($item['name'])
                ->assertSee($item['category']);
        }

        // Assert budget does not appear anywhere in admin index
        $adminRes->assertDontSee('Budget:');
    }
}
