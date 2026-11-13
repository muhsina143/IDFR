<?php

namespace Tests\Feature;

use App\Mail\AdminSponsorshipEnquiryMail;
use App\Models\Season;
use App\Models\SponsorshipEnquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SponsorshipEnquirySystemTest extends TestCase
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

    protected function makeRegularUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'model',
            'name' => 'Regular User',
            'email' => 'user_'.uniqid().'@example.com',
            'password' => bcrypt('UserPassword123!'),
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    public function test_homepage_renders_sponsorship_trigger_and_modal_with_active_season(): void
    {
        $season = Season::create([
            'number' => 8,
            'title' => 'Season 8: The Grand Runway',
            'year' => '2026',
            'is_current' => true,
            'status' => 'published',
            'dates' => 'Nov 14 - 16, 2026',
            'location' => 'Kozhikode International Centre',
        ]);

        $response = $this->get(route('idfr.home'));
        $response->assertStatus(200);

        // Verify trigger attribute on Become a Sponsor CTA
        $response->assertSee('data-sponsor-enquiry', false);
        $response->assertSee('Become a Sponsor', false);

        // Verify modal container, form and dynamic season display
        $response->assertSee('id="sponsorEnquiryModal"', false);
        $response->assertSee('id="sponsor-enquiry"', false);
        $response->assertSee('data-modal-name="sponsor-enquiry"', false);
        $response->assertSee('id="sponsorEnquiryForm"', false);
        $response->assertSee('Season 8: The Grand Runway', false);
        $response->assertSee('name="sponsorship_type"', false);
        $response->assertSee('Title Sponsor', false);

        // Verify footer also provides Become a Sponsor link
        $response->assertSee(route('idfr.home').'#sponsor-enquiry', false);
    }

    public function test_script_contains_robust_trigger_hash_and_modal_support(): void
    {
        $script = file_get_contents(public_path('js/script.js'));

        $this->assertStringContainsString('window.Modal = Modal;', $script);
        $this->assertStringContainsString('isSponsorTrigger', $script);
        $this->assertStringContainsString('#sponsor-enquiry', $script);
        $this->assertStringContainsString('checkHashAndOpen', $script);
        $this->assertStringContainsString('hashchange', $script);
        $this->assertStringContainsString("document.readyState === 'loading'", $script);
    }

    public function test_user_can_submit_sponsorship_enquiry_via_json(): void
    {
        Mail::fake();

        $season = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2026',
            'is_current' => true,
            'status' => 'published',
        ]);

        $payload = [
            'name' => 'Rolex Luxury Timepieces',
            'email' => 'partnerships@rolex-example.com',
            'phone' => '+91 98765 43210',
            'sponsorship_type' => 'Title Sponsor',
            'message' => 'Interested in main title sponsorship for upcoming runway show.',
            'season_id' => $season->id,
        ];

        $response = $this->postJson(route('sponsorship.enquiry'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'enquiry' => [
                    'id',
                    'name',
                    'email',
                    'sponsorship_type',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('sponsorship_enquiries', [
            'name' => 'Rolex Luxury Timepieces',
            'email' => 'partnerships@rolex-example.com',
            'phone' => '+919876543210',
            'sponsorship_type' => 'Title Sponsor',
            'status' => SponsorshipEnquiry::STATUS_NEW,
            'season_id' => $season->id,
            'season_title' => 'Season 8',
        ]);

        // Verify email dispatched
        Mail::assertSent(AdminSponsorshipEnquiryMail::class, function ($mail) {
            return $mail->enquiry->email === 'partnerships@rolex-example.com' &&
                $mail->enquiry->name === 'Rolex Luxury Timepieces';
        });

        // Verify activity logged
        $this->assertDatabaseHas('admin_activities', [
            'activity' => 'sponsorship_enquiry_submitted',
        ]);
    }

    public function test_sponsorship_enquiry_validation_rules(): void
    {
        Mail::fake();

        // 1. Missing required fields
        $response = $this->postJson(route('sponsorship.enquiry'), []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'sponsorship_type']);

        // 2. Invalid email and phone format
        $response = $this->postJson(route('sponsorship.enquiry'), [
            'name' => 'A', // too short
            'email' => 'not-an-email',
            'phone' => '123', // too short
            'sponsorship_type' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'sponsorship_type']);

        Mail::assertNothingSent();
        $this->assertEquals(0, SponsorshipEnquiry::count());
    }

    public function test_unauthenticated_or_regular_user_cannot_access_admin_sponsorship_routes(): void
    {
        $enquiry = SponsorshipEnquiry::create([
            'name' => 'Hermes Paris',
            'email' => 'contact@hermes.example',
            'phone' => '+91 98765 11111',
            'sponsorship_type' => 'Official Brand Partner',
            'status' => SponsorshipEnquiry::STATUS_NEW,
        ]);

        // Guest access redirects to admin.login
        $this->get(route('admin.sponsorship-enquiries.index'))
            ->assertRedirect(route('admin.login'));

        $this->get(route('admin.sponsorship-enquiries.show', $enquiry))
            ->assertRedirect(route('admin.login'));

        // Regular user access with admin guard is rejected
        $regularUser = $this->makeRegularUser();

        $this->actingAs($regularUser, 'admin')
            ->get(route('admin.sponsorship-enquiries.index'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs($regularUser, 'admin')
            ->get(route('admin.sponsorship-enquiries.show', $enquiry))
            ->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_sponsorship_enquiries_index_and_filter_by_status(): void
    {
        $admin = $this->makeAdmin();

        $newEnquiry = SponsorshipEnquiry::create([
            'name' => 'Prada Milano',
            'email' => 'press@prada.example',
            'phone' => '+91 98765 22222',
            'sponsorship_type' => 'Title Sponsor',
            'status' => SponsorshipEnquiry::STATUS_NEW,
        ]);

        $contactedEnquiry = SponsorshipEnquiry::create([
            'name' => 'Gucci Group',
            'email' => 'partnerships@gucci.example',
            'phone' => '+91 98765 33333',
            'sponsorship_type' => 'Powered By Sponsor',
            'status' => SponsorshipEnquiry::STATUS_CONTACTED,
        ]);

        $completedEnquiry = SponsorshipEnquiry::create([
            'name' => 'Vogue India',
            'email' => 'editor@vogue.example',
            'phone' => '+91 98765 44444',
            'sponsorship_type' => 'Media Partner',
            'status' => SponsorshipEnquiry::STATUS_COMPLETED,
        ]);

        // 1. All enquiries listed
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.sponsorship-enquiries.index'));

        $response->assertStatus(200);
        $response->assertSee('Prada Milano');
        $response->assertSee('Gucci Group');
        $response->assertSee('Vogue India');
        $response->assertSee('Sponsorship Enquiries');

        // 2. Filter by status: contacted
        $contactedResponse = $this->actingAs($admin, 'admin')
            ->get(route('admin.sponsorship-enquiries.index', ['status' => 'contacted']));

        $contactedResponse->assertStatus(200);
        $contactedResponse->assertSee('Gucci Group');
        $contactedResponse->assertDontSee('Prada Milano');
        $contactedResponse->assertDontSee('Vogue India');

        // 3. Search query
        $searchResponse = $this->actingAs($admin, 'admin')
            ->get(route('admin.sponsorship-enquiries.index', ['search' => 'Prada']));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Prada Milano');
        $searchResponse->assertDontSee('Gucci Group');
    }

    public function test_admin_can_view_and_update_sponsorship_enquiry_status_and_notes(): void
    {
        $admin = $this->makeAdmin();

        $enquiry = SponsorshipEnquiry::create([
            'name' => 'Mercedes-Benz Fashion',
            'email' => 'automotive@mercedes.example',
            'phone' => '+91 98765 99999',
            'sponsorship_type' => 'Automobile Partner',
            'message' => 'We want to provide luxury VIP vehicles and have red carpet presence.',
            'status' => SponsorshipEnquiry::STATUS_NEW,
        ]);

        // View detail page
        $showResponse = $this->actingAs($admin, 'admin')
            ->get(route('admin.sponsorship-enquiries.show', $enquiry));

        $showResponse->assertStatus(200);
        $showResponse->assertSee('Mercedes-Benz Fashion');
        $showResponse->assertSee('automotive@mercedes.example');
        $showResponse->assertSee('luxury VIP vehicles');

        // Update status to contacted with notes
        $updateResponse = $this->actingAs($admin, 'admin')
            ->put(route('admin.sponsorship-enquiries.update', $enquiry), [
                'status' => SponsorshipEnquiry::STATUS_CONTACTED,
                'admin_notes' => 'Spoke with marketing director on phone; deck sent via email.',
            ]);

        $updateResponse->assertRedirect(route('admin.sponsorship-enquiries.show', $enquiry));

        $enquiry->refresh();
        $this->assertEquals(SponsorshipEnquiry::STATUS_CONTACTED, $enquiry->status);
        $this->assertEquals('Spoke with marketing director on phone; deck sent via email.', $enquiry->admin_notes);
    }

    public function test_admin_can_delete_sponsorship_enquiry(): void
    {
        $admin = $this->makeAdmin();

        $enquiry = SponsorshipEnquiry::create([
            'name' => 'Spam Submission Co',
            'email' => 'spam@test.example',
            'phone' => '+91 99999 00000',
            'sponsorship_type' => 'Other',
            'status' => SponsorshipEnquiry::STATUS_NEW,
        ]);

        $deleteResponse = $this->actingAs($admin, 'admin')
            ->delete(route('admin.sponsorship-enquiries.destroy', $enquiry));

        $deleteResponse->assertRedirect(route('admin.sponsorship-enquiries.index'));

        $this->assertDatabaseMissing('sponsorship_enquiries', [
            'id' => $enquiry->id,
        ]);
    }

    public function test_submission_succeeds_even_if_mail_transport_fails(): void
    {
        // Don't fake mail, configure an invalid or broken driver to ensure exception is handled safely
        Mail::shouldReceive('to->send')->andThrow(new \Exception('SMTP Connection timeout'));

        $payload = [
            'name' => 'Chanel Haute Couture',
            'email' => 'runway@chanel.example',
            'phone' => '+91 98765 88888',
            'sponsorship_type' => 'Title Sponsor',
            'message' => 'Paris haute couture runway collaboration enquiry.',
        ];

        $response = $this->postJson(route('sponsorship.enquiry'), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('sponsorship_enquiries', [
            'name' => 'Chanel Haute Couture',
            'email' => 'runway@chanel.example',
        ]);
    }
}
