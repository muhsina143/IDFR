<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsShootBooking;
use App\Models\User;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsdreamsShootBookingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_test@idfr.example'],
            [
                'name' => 'IDFR Executive Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    public function test_shoot_booking_form_can_be_submitted_successfully_via_ajax(): void
    {
        $payload = [
            'name' => 'Ananya Sharma',
            'phone' => '+91 98765 43210',
            'email' => 'ananya@example.com',
            'shoot_date' => now()->addDays(5)->format('Y-m-d'),
            'shoot_type' => 'Editorial Fashion Shoot',
            'notes' => 'Looking for a studio with dark backdrop and 3-point lighting setup.',
        ];

        $response = $this->postJson(route('asd.shoot-booking.submit'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('asdreams_shoot_bookings', [
            'name' => 'Ananya Sharma',
            'phone' => '+91 98765 43210',
            'email' => 'ananya@example.com',
            'shoot_type' => 'Editorial Fashion Shoot',
            'subject' => 'Editorial Fashion Shoot',
            'status' => 'new',
        ]);
    }

    public function test_shoot_booking_form_validates_required_fields(): void
    {
        $response = $this->postJson(route('asd.shoot-booking.submit'), [
            'name' => '',
            'phone' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'email']);
    }

    public function test_duplicate_shoot_booking_prevented_within_cooldown(): void
    {
        $payload = [
            'name' => 'Rahul Verma',
            'phone' => '+91 98989 89898',
            'email' => 'rahul@example.com',
            'shoot_date' => now()->addDays(3)->format('Y-m-d'),
            'shoot_type' => 'Bridal Portrait',
            'notes' => 'Outdoor shoot requested.',
        ];

        // First submit
        $res1 = $this->postJson(route('asd.shoot-booking.submit'), $payload);
        $res1->assertStatus(200);

        // Immediate second submit (same phone within 60s)
        $res2 = $this->postJson(route('asd.shoot-booking.submit'), $payload);
        $res2->assertStatus(200);

        // Ensure only 1 record created in database
        $this->assertEquals(1, AsdreamsShootBooking::where('phone', '+91 98989 89898')->count());
    }

    public function test_guest_cannot_access_admin_shoot_bookings(): void
    {
        $response = $this->get(route('admin.asdreams.shoot-bookings.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_shoot_bookings_list_and_filter_by_status(): void
    {
        $booking1 = AsdreamsShootBooking::create([
            'name' => 'Client Alpha',
            'phone' => '+91 91111 11111',
            'email' => 'alpha@example.com',
            'shoot_date' => now()->addDays(2),
            'subject' => 'Commercial Ad',
            'shoot_type' => 'Commercial Ad',
            'status' => 'new',
        ]);

        $booking2 = AsdreamsShootBooking::create([
            'name' => 'Client Beta',
            'phone' => '+91 92222 22222',
            'email' => 'beta@example.com',
            'shoot_date' => now()->addDays(4),
            'subject' => 'Lookbook Shoot',
            'shoot_type' => 'Lookbook Shoot',
            'status' => 'contacted',
        ]);

        // Access all
        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.shoot-bookings.index'));
        $response->assertStatus(200);
        $response->assertSee('Client Alpha');
        $response->assertSee('Client Beta');

        // Filter status=new
        $responseNew = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.shoot-bookings.index', ['status' => 'new']));
        $responseNew->assertStatus(200);
        $responseNew->assertSee('Client Alpha');
        $responseNew->assertDontSee('Client Beta');

        // Filter status=contacted
        $responseContacted = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.shoot-bookings.index', ['status' => 'contacted']));
        $responseContacted->assertStatus(200);
        $responseContacted->assertDontSee('Client Alpha');
        $responseContacted->assertSee('Client Beta');
    }

    public function test_admin_can_search_shoot_bookings(): void
    {
        AsdreamsShootBooking::create([
            'name' => 'Zara Fashion House',
            'phone' => '+91 93333 33333',
            'email' => 'zara@example.com',
            'subject' => 'Runway Preview',
            'shoot_type' => 'Runway Preview',
            'status' => 'new',
        ]);

        AsdreamsShootBooking::create([
            'name' => 'Dev Anand',
            'phone' => '+91 94444 44444',
            'email' => 'dev@example.com',
            'subject' => 'Family Portrait',
            'shoot_type' => 'Family Portrait',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.shoot-bookings.index', ['search' => 'Zara']));
        $response->assertStatus(200);
        $response->assertSee('Zara Fashion House');
        $response->assertDontSee('Dev Anand');
    }

    public function test_admin_can_filter_shoot_bookings_by_date(): void
    {
        $futureBooking = AsdreamsShootBooking::create([
            'name' => 'Future Bride',
            'phone' => '+91 95555 55555',
            'email' => 'future@example.com',
            'shoot_date' => now()->addDays(20),
            'status' => 'new',
        ]);

        $todayBooking = AsdreamsShootBooking::create([
            'name' => 'Today Shoot',
            'phone' => '+91 96666 66666',
            'email' => 'today@example.com',
            'shoot_date' => now(),
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.shoot-bookings.index', [
            'date_from' => now()->addDays(10)->format('Y-m-d'),
        ]));
        $response->assertStatus(200);
        $response->assertSee('Future Bride');
        $response->assertDontSee('Today Shoot');
    }

    public function test_admin_can_view_single_shoot_booking(): void
    {
        $booking = AsdreamsShootBooking::create([
            'name' => 'Vikram Seth',
            'phone' => '+91 97777 77777',
            'email' => 'vikram@example.com',
            'shoot_date' => now()->addDays(7),
            'subject' => 'Album Cover Shoot',
            'message' => 'Needs 4 costume changes.',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.asdreams.shoot-bookings.show', $booking));
        $response->assertStatus(200);
        $response->assertSee('Vikram Seth');
        $response->assertSee('+91 97777 77777');
        $response->assertSee('vikram@example.com');
        $response->assertSee('Album Cover Shoot');
        $response->assertSee('Needs 4 costume changes.');
    }

    public function test_admin_can_update_booking_status_and_notes(): void
    {
        $booking = AsdreamsShootBooking::create([
            'name' => 'Meera Patel',
            'phone' => '+91 98888 88888',
            'email' => 'meera@example.com',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->put(route('admin.asdreams.shoot-bookings.update-status', $booking), [
            'status' => 'confirmed',
            'admin_notes' => 'Advance payment received, booked studio 02 for 2pm.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('asdreams_shoot_bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
            'admin_notes' => 'Advance payment received, booked studio 02 for 2pm.',
        ]);
    }

    public function test_admin_can_delete_shoot_booking(): void
    {
        $booking = AsdreamsShootBooking::create([
            'name' => 'To Be Deleted',
            'phone' => '+91 99999 00000',
            'email' => 'delete@example.com',
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->delete(route('admin.asdreams.shoot-bookings.destroy', $booking));

        $response->assertRedirect(route('admin.asdreams.shoot-bookings.index'));
        $this->assertDatabaseMissing('asdreams_shoot_bookings', [
            'id' => $booking->id,
        ]);
    }
}
