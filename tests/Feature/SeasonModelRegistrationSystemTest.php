<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\SeasonModelRegistration;
use App\Models\SeasonRegistrationPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeasonModelRegistrationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Season $season8;

    protected Season $season9;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
            'name' => 'Admin Executive',
            'email' => 'admin.exec@idfr.test',
        ]);

        $this->season8 = Season::create([
            'number' => 8,
            'title' => 'Season 8',
            'year' => '2028',
            'location' => 'Kannur',
            'dates' => '15 – 17 December 2028',
            'status' => 'published',
            'is_current' => true,
            'description' => 'Season 8 fashion runway in Kannur.',
            'hero_image' => 'uploads/seasons/hero_season_8.png',
        ]);

        $this->season9 = Season::create([
            'number' => 9,
            'title' => 'Season 9',
            'year' => '2029',
            'location' => 'Kochi',
            'dates' => '20 – 22 December 2029',
            'status' => 'published',
            'is_current' => false,
            'description' => 'Season 9 premier in Kochi.',
            'hero_image' => 'uploads/seasons/hero_season_9.png',
        ]);

        // Configure custom price for Season 8: ₹9,999; Season 9: ₹14,999
        SeasonRegistrationPrice::create([
            'season_id' => $this->season8->id,
            'price' => 9999.00,
            'currency' => 'INR',
            'is_active' => true,
            'notes' => 'Season 8 standard registration',
        ]);

        SeasonRegistrationPrice::create([
            'season_id' => $this->season9->id,
            'price' => 14999.00,
            'currency' => 'INR',
            'is_active' => true,
            'notes' => 'Season 9 premium entry fee',
        ]);
    }

    public function test_tickets_page_displays_selected_season_and_dynamic_fee(): void
    {
        // 1. Model registration page automatically locks to the active season (Season 8)
        $response = $this->get(route('idfr.tickets'));
        $response->assertStatus(200);
        $response->assertSee('IDFR Fashion Show · Season 8', false);
        $response->assertSee('₹9,999', false);
        $response->assertSee('Participant <em>Details</em>', false);
        $response->assertSee('Full Name', false);
        $response->assertSee('WhatsApp Number', false);
        $response->assertSee('Registration Category', false);

        // 2. When Season 9 is activated, it dynamically displays Season 9 and ₹14,999
        $this->season9->makeCurrent();
        $response9 = $this->get(route('idfr.tickets'));
        $response9->assertStatus(200);
        $response9->assertSee('IDFR Fashion Show · Season 9', false);
        $response9->assertSee('₹14,999', false);
    }

    public function test_adult_model_registration_submits_successfully_and_snapshots_fee(): void
    {
        $payload = [
            'season_id' => $this->season8->id,
            'full_name' => 'Meera Nambiar',
            'whatsapp_number' => '+91 98765 43210',
            'email' => 'meera@example.com',
            'age' => 22,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'additional_info' => '5ft 9in, runway experience with Kerala Couture.',
        ];

        $response = $this->post(route('idfr.tickets.submit'), $payload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('season_model_registrations', [
            'full_name' => 'Meera Nambiar',
            'whatsapp_number' => '+91 98765 43210',
            'season_id' => $this->season8->id,
            'age' => 22,
            'gender' => 'female',
            'category' => 'adult',
            'registration_fee' => 9999.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
            'registration_status' => 'pending',
        ]);

        $registration = SeasonModelRegistration::where('whatsapp_number', '+91 98765 43210')->first();
        $this->assertNotNull($registration);
        $this->assertStringStartsWith('REG-S8-', $registration->registration_number);
    }

    public function test_kid_model_registration_requires_parent_details(): void
    {
        $payload = [
            'season_id' => $this->season8->id,
            'full_name' => 'Aarav Kumar',
            'whatsapp_number' => '+91 91234 56789',
            'age' => 12,
            'gender' => 'male',
            'category' => 'kids',
            'citizenship' => 'Indian',
            // Missing parent_name & parent_contact
        ];

        $response = $this->post(route('idfr.tickets.submit'), $payload);
        $response->assertSessionHasErrors(['parent_name', 'parent_contact']);

        $this->assertDatabaseMissing('season_model_registrations', [
            'full_name' => 'Aarav Kumar',
        ]);
    }

    public function test_kid_model_registration_succeeds_with_parent_details(): void
    {
        $payload = [
            'season_id' => $this->season8->id,
            'full_name' => 'Aarav Kumar',
            'whatsapp_number' => '+91 91234 56789',
            'age' => 12,
            'gender' => 'male',
            'category' => 'kids',
            'citizenship' => 'Indian',
            'parent_name' => 'Sunil Kumar',
            'parent_contact' => '+91 91234 56780',
            'parent_relationship' => 'Father',
        ];

        $response = $this->post(route('idfr.tickets.submit'), $payload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('season_model_registrations', [
            'full_name' => 'Aarav Kumar',
            'parent_name' => 'Sunil Kumar',
            'parent_contact' => '+91 91234 56780',
            'category' => 'kids',
        ]);
    }

    public function test_duplicate_registration_within_two_minutes_is_prevented(): void
    {
        $payload = [
            'season_id' => $this->season8->id,
            'full_name' => 'Devika Menon',
            'whatsapp_number' => '+91 98989 89898',
            'age' => 20,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
        ];

        // First submission succeeds
        $this->post(route('idfr.tickets.submit'), $payload);

        // Immediate duplicate submission
        $second = $this->post(route('idfr.tickets.submit'), $payload);
        $second->assertSessionHas('info');

        $count = SeasonModelRegistration::where('whatsapp_number', '+91 98989 89898')->count();
        $this->assertEquals(1, $count);
    }

    public function test_admin_can_view_registrations_list_with_metrics_and_filter(): void
    {
        SeasonModelRegistration::create([
            'registration_number' => 'REG-S8-00001',
            'season_id' => $this->season8->id,
            'full_name' => 'Participant One',
            'whatsapp_number' => '+91 90000 00001',
            'age' => 23,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'amount_paid' => 9999.00,
            'payment_status' => 'paid',
            'registration_status' => 'approved',
        ]);

        SeasonModelRegistration::create([
            'registration_number' => 'REG-S8-00002',
            'season_id' => $this->season8->id,
            'full_name' => 'Participant Two',
            'whatsapp_number' => '+91 90000 00002',
            'age' => 10,
            'gender' => 'male',
            'category' => 'kids',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
            'registration_status' => 'pending',
            'parent_name' => 'Guardian Two',
            'parent_contact' => '+91 90000 00022',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.season-registrations.index'));
        $response->assertStatus(200);
        $response->assertSee('Model Registrations', false);
        $response->assertSee('Participant One', false);
        $response->assertSee('Participant Two', false);
        $response->assertSee('REG-S8-00001', false);
        $response->assertSee('REG-S8-00002', false);

        // Filter by category=kids
        $responseFiltered = $this->actingAs($this->adminUser, 'admin')->get(route('admin.season-registrations.index', ['category' => 'kids']));
        $responseFiltered->assertStatus(200);
        $responseFiltered->assertSee('Participant Two', false);
        $responseFiltered->assertDontSee('Participant One', false);
    }

    public function test_admin_can_export_registrations_as_csv(): void
    {
        SeasonModelRegistration::create([
            'registration_number' => 'REG-S8-00099',
            'season_id' => $this->season8->id,
            'full_name' => 'Export Tester',
            'whatsapp_number' => '+91 95555 44444',
            'age' => 25,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
            'registration_status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.season-registrations.export'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains((string) $response->headers->get('content-type'), 'text/csv'));
    }

    public function test_admin_can_view_and_update_registration(): void
    {
        $reg = SeasonModelRegistration::create([
            'registration_number' => 'REG-S8-00100',
            'season_id' => $this->season8->id,
            'full_name' => 'Update Tester',
            'whatsapp_number' => '+91 98765 11111',
            'age' => 24,
            'gender' => 'male',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
            'registration_status' => 'pending',
        ]);

        // 1. Show page
        $showRes = $this->actingAs($this->adminUser, 'admin')->get(route('admin.season-registrations.show', $reg));
        $showRes->assertStatus(200);
        $showRes->assertSee('Update Tester', false);

        // 2. Update page
        $updatePayload = [
            'season_id' => $this->season8->id,
            'full_name' => 'Update Tester Approved',
            'whatsapp_number' => '+91 98765 11111',
            'age' => 24,
            'gender' => 'male',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'registration_status' => 'approved',
            'payment_status' => 'unpaid',
            'admin_notes' => 'Approved after video screening.',
        ];

        $updateRes = $this->actingAs($this->adminUser, 'admin')->put(route('admin.season-registrations.update', $reg), $updatePayload);
        $updateRes->assertRedirect(route('admin.season-registrations.show', $reg));

        $reg->refresh();
        $this->assertEquals('Update Tester Approved', $reg->full_name);
        $this->assertEquals('approved', $reg->registration_status);
        $this->assertEquals('Approved after video screening.', $reg->admin_notes);
    }

    public function test_admin_can_record_manual_payment_and_balance_is_recalculated(): void
    {
        $reg = SeasonModelRegistration::create([
            'registration_number' => 'REG-S8-00200',
            'season_id' => $this->season8->id,
            'full_name' => 'Payment Recipient',
            'whatsapp_number' => '+91 98765 22222',
            'age' => 21,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'amount_paid' => 0.00,
            'payment_status' => 'unpaid',
            'registration_status' => 'approved',
        ]);

        $paymentPayload = [
            'amount' => 5000.00,
            'payment_method' => 'upi',
            'transaction_reference' => 'UPI1234567890',
            'payment_date' => '2026-10-31',
            'payment_status' => 'paid',
            'notes' => 'Partial advance paid via UPI.',
        ];

        $res = $this->actingAs($this->adminUser, 'admin')->post(route('admin.season-registrations.payments.store', $reg), $paymentPayload);
        $res->assertRedirect(route('admin.season-registrations.show', $reg));

        $reg->refresh();
        $this->assertEquals(5000.00, (float) $reg->amount_paid);
        $this->assertEquals(4999.00, (float) $reg->remaining_balance);
        $this->assertEquals('pending', $reg->payment_status); // Partial -> status is 'pending'

        // Record second payment of 4,999.00 to complete
        $paymentPayload2 = [
            'amount' => 4999.00,
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'NEFT999888',
            'payment_date' => '2026-10-31',
            'payment_status' => 'paid',
        ];

        $this->actingAs($this->adminUser, 'admin')->post(route('admin.season-registrations.payments.store', $reg), $paymentPayload2);

        $reg->refresh();
        $this->assertEquals(9999.00, (float) $reg->amount_paid);
        $this->assertEquals(0.00, (float) $reg->remaining_balance);
        $this->assertEquals('paid', $reg->payment_status); // Fully settled -> status is 'paid'
    }

    public function test_admin_can_manage_season_prices(): void
    {
        $newSeason = Season::create([
            'number' => 10,
            'title' => 'Season 10',
            'year' => '2030',
            'location' => 'Dubai',
            'status' => 'published',
        ]);

        // Store new price for Season 10
        $storeRes = $this->actingAs($this->adminUser, 'admin')->post(route('admin.season-prices.store'), [
            'season_id' => $newSeason->id,
            'price' => 19999.00,
            'currency' => 'INR',
            'is_active' => true,
            'notes' => 'Dubai International Edition Fee',
        ]);

        $storeRes->assertRedirect(route('admin.season-prices.index'));

        $this->assertDatabaseHas('season_registration_prices', [
            'season_id' => $newSeason->id,
            'price' => 19999.00,
        ]);

        $this->assertEquals(19999.00, $newSeason->getRegistrationFee());
        $this->assertEquals('₹19,999', $newSeason->formattedRegistrationFee());
    }

    public function test_admin_dashboard_includes_model_registration_statistics(): void
    {
        SeasonModelRegistration::create([
            'registration_number' => 'REG-S8-00300',
            'season_id' => $this->season8->id,
            'full_name' => 'Dash Model',
            'whatsapp_number' => '+91 97777 66666',
            'age' => 20,
            'gender' => 'female',
            'category' => 'adult',
            'citizenship' => 'Indian',
            'registration_fee' => 9999.00,
            'amount_paid' => 9999.00,
            'payment_status' => 'paid',
            'registration_status' => 'approved',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Runway Model Registrations', false);
        $response->assertSee('Runway Model Applications', false);
    }
}
