<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsShootBooking;
use App\Models\User;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsdreamsStudioBookingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_studio_test@idfr.example'],
            [
                'name' => 'IDFR Executive Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    public function test_studio_booking_form_can_be_submitted_successfully_via_ajax(): void
    {
        $payload = [
            'name' => 'Kalyan Media Works',
            'phone' => '+91 98765 11223',
            'email' => 'contact@kalyanmedia.example',
            'days' => 3,
            'purpose' => 'Fashion Lookbook and Video Campaign',
            'start' => now()->addDays(5)->format('Y-m-d'),
            'end' => now()->addDays(7)->format('Y-m-d'),
            'notes' => 'Requires cyclorama and warm continuous lights on Day 2.',
        ];

        $response = $this->postJson(route('asd.studio-booking.submit'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('asdreams_shoot_bookings', [
            'name' => 'Kalyan Media Works',
            'phone' => '+919876511223',
            'email' => 'contact@kalyanmedia.example',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Fashion Lookbook and Video Campaign',
            'days' => 3,
            'status' => 'new',
        ]);
    }

    public function test_studio_booking_form_validates_required_fields(): void
    {
        $response = $this->postJson(route('asd.studio-booking.submit'), [
            'name' => '',
            'phone' => '',
            'email' => 'invalid-email',
            'days' => '',
            'purpose' => '',
            'start' => '',
            'end' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'email', 'days', 'purpose', 'start', 'end']);
    }

    public function test_studio_booking_form_validates_end_date_after_or_equal_start(): void
    {
        $response = $this->postJson(route('asd.studio-booking.submit'), [
            'name' => 'Studio User',
            'phone' => '+91 98765 43210',
            'email' => 'user@example.com',
            'days' => 1,
            'purpose' => 'Commercial Shoot',
            'start' => now()->addDays(5)->format('Y-m-d'),
            'end' => now()->addDays(2)->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end']);
    }

    public function test_duplicate_studio_booking_prevented_within_cooldown(): void
    {
        $payload = [
            'name' => 'Rohan Kapoor',
            'phone' => '+91 97777 66666',
            'email' => 'rohan@example.com',
            'days' => 2,
            'purpose' => 'Music Video Production',
            'start' => now()->addDays(4)->format('Y-m-d'),
            'end' => now()->addDays(5)->format('Y-m-d'),
        ];

        $res1 = $this->postJson(route('asd.studio-booking.submit'), $payload);
        $res1->assertStatus(200);

        $res2 = $this->postJson(route('asd.studio-booking.submit'), $payload);
        $res2->assertStatus(200);

        $this->assertEquals(1, AsdreamsShootBooking::where('phone', '+919777766666')->count());
    }

    public function test_guest_cannot_access_admin_studio_bookings(): void
    {
        $response = $this->get(route('admin.asdreams.studio-bookings.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_studio_bookings_list_and_filter_by_status(): void
    {
        $studio1 = AsdreamsShootBooking::create([
            'name' => 'Studio Client One',
            'phone' => '+91 91111 22222',
            'email' => 'one@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'E-Commerce Catalogue',
            'days' => 1,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2),
            'status' => 'new',
        ]);

        $studio2 = AsdreamsShootBooking::create([
            'name' => 'Studio Client Two',
            'phone' => '+91 93333 44444',
            'email' => 'two@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Ad Film Shoot',
            'days' => 3,
            'start_date' => now()->addDays(4),
            'end_date' => now()->addDays(6),
            'status' => 'contacted',
        ]);

        // Shoot booking that should NOT appear in studio bookings
        AsdreamsShootBooking::create([
            'name' => 'Separate Shoot Client',
            'phone' => '+91 95555 66666',
            'email' => 'shoot@example.com',
            'booking_type' => 'shoot',
            'shoot_type' => 'Editorial Portrait',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.studio-bookings.index'));
        $response->assertStatus(200);
        $response->assertSee('Studio Client One');
        $response->assertSee('Studio Client Two');
        $response->assertDontSee('Separate Shoot Client');

        // Filter status=new
        $responseNew = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.studio-bookings.index', ['status' => 'new']));
        $responseNew->assertStatus(200);
        $responseNew->assertSee('Studio Client One');
        $responseNew->assertDontSee('Studio Client Two');

        // Filter status=contacted
        $responseContacted = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.studio-bookings.index', ['status' => 'contacted']));
        $responseContacted->assertStatus(200);
        $responseContacted->assertDontSee('Studio Client One');
        $responseContacted->assertSee('Studio Client Two');
    }

    public function test_admin_can_search_studio_bookings_by_purpose_or_name(): void
    {
        AsdreamsShootBooking::create([
            'name' => 'Fashion Studio Lab',
            'phone' => '+91 98888 12345',
            'email' => 'lab@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Underwater Hologram Shoot',
            'days' => 2,
            'status' => 'new',
        ]);

        AsdreamsShootBooking::create([
            'name' => 'Standard Client',
            'phone' => '+91 97777 54321',
            'email' => 'standard@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Product Catalogue',
            'days' => 1,
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.studio-bookings.index', ['search' => 'Underwater']));
        $response->assertStatus(200);
        $response->assertSee('Fashion Studio Lab');
        $response->assertSee('Underwater Hologram Shoot');
        $response->assertDontSee('Standard Client');
    }

    public function test_admin_can_view_single_studio_booking_details(): void
    {
        $booking = AsdreamsShootBooking::create([
            'name' => 'Ace Productions',
            'phone' => '+91 96666 77777',
            'email' => 'ace@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Brand Video Campaign',
            'days' => 4,
            'start_date' => now()->addDays(8),
            'end_date' => now()->addDays(11),
            'message' => 'Needs 4000 sq ft space and green room access.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.studio-bookings.show', $booking));
        $response->assertStatus(200);
        $response->assertSee('Ace Productions');
        $response->assertSee('+91 96666 77777');
        $response->assertSee('ace@example.com');
        $response->assertSee('Brand Video Campaign');
        $response->assertSee('Needs 4000 sq ft space and green room access.');
        $response->assertSee('4 days');
    }

    public function test_admin_can_update_studio_booking_status_and_notes(): void
    {
        $booking = AsdreamsShootBooking::create([
            'name' => 'Cine Studio Group',
            'phone' => '+91 95555 44444',
            'email' => 'cine@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'purpose' => 'Feature Film Scene',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->put(route('admin.asdreams.studio-bookings.update-status', $booking), [
            'status' => 'confirmed',
            'admin_notes' => 'Advance token payment received. Studio 01 reserved.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('asdreams_shoot_bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'admin_notes' => 'Advance token payment received. Studio 01 reserved.',
        ]);
    }

    public function test_admin_can_delete_studio_booking(): void
    {
        $booking = AsdreamsShootBooking::create([
            'name' => 'To Be Removed',
            'phone' => '+91 94444 33333',
            'email' => 'remove@example.com',
            'booking_type' => 'studio',
            'shoot_type' => 'Studio Rental',
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->delete(route('admin.asdreams.studio-bookings.destroy', $booking));

        $response->assertRedirect(route('admin.asdreams.studio-bookings.index'));
        $this->assertDatabaseMissing('asdreams_shoot_bookings', [
            'id' => $booking->id,
        ]);
    }
}
