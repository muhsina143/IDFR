<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\EnquiryOtp;
use App\Models\ProfileEnquiry;
use App\Models\Season;
use App\Models\User;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class FormValidationPhaseOneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);
    }

    public function test_all_phase_one_pages_include_validator_and_data_validate_markup(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $modelUser = User::factory()->create([
            'role' => UserRole::Model->value,
            'email_verified_at' => now(),
        ]);

        $creativeUser = User::factory()->create([
            'role' => UserRole::Photographer->value,
            'email_verified_at' => now(),
        ]);

        Season::create([
            'title' => 'Season 4',
            'number' => 4,
            'registration_fee' => 9999,
            'is_active' => true,
        ]);

        $this->get('/idfr')
            ->assertOk()
            ->assertSee('js/validator.js', false)
            ->assertSee('id="panelLogin"', false)
            ->assertSee('id="panelRegister"', false)
            ->assertSee('id="panelVerifyOtp"', false)
            ->assertSee('id="panelForgotPassword"', false)
            ->assertSee('id="sponsorEnquiryForm"', false)
            ->assertSee('data-validate', false);

        $this->get('/idfr/contact')
            ->assertOk()
            ->assertSee('id="asdEnquiryForm"', false)
            ->assertSee('data-rules="required|phone"', false);

        $this->get('/idfr/tickets')
            ->assertOk()
            ->assertSee('id="bookingForm"', false)
            ->assertSee('data-rules="required|min:2|max:150|name"', false);

        $this->get('/asdreams/contact')
            ->assertOk()
            ->assertSee('id="asdEnquiryForm"', false)
            ->assertSee('id="shootBookForm"', false)
            ->assertSee('id="studioBookForm"', false);

        $this->get('/asdreams/offers')
            ->assertOk()
            ->assertSee('id="asdEnquiryForm"', false);

        $this->get('/reset-password/sample-token?email=user@example.com')
            ->assertOk()
            ->assertSee('data-rules="required|password"', false)
            ->assertSee('data-rules="required|same:#resetPassword"', false);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('js/validator.js', false)
            ->assertSee('data-validate', false)
            ->assertSee('data-rules="required|email"', false);

        $this->actingAs($modelUser)->get('/profiles/model')
            ->assertOk()
            ->assertSee('id="profileForm"', false)
            ->assertSee('data-rules="group_min:1"', false);

        $this->actingAs($creativeUser)->get('/profiles/photographer')
            ->assertOk()
            ->assertSee('id="profileForm"', false)
            ->assertSee('data-rules="required|phone"', false);

        $this->assertNotNull($admin);
    }

    public function test_register_validation_matrix_and_normalization(): void
    {
        Mail::fake();

        // Empty submit
        $this->postJson('/register', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'role', 'password', 'terms']);

        // Invalid name, email, phone, weak password, mismatch, unchecked terms
        $this->postJson('/register', [
            'name' => 'Aarav 123',
            'email' => 'bad-email',
            'phone' => '+0123456789',
            'role' => 'model',
            'password' => 'onlyletters',
            'password_confirmation' => 'mismatch1',
            'terms' => false,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'phone', 'password', 'terms']);

        // Short password
        $this->postJson('/register', [
            'name' => 'Aarav Menon',
            'email' => 'aarav@example.com',
            'phone' => '9876543210',
            'role' => 'model',
            'password' => 'ab12',
            'password_confirmation' => 'ab12',
            'terms' => true,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Valid submission with normalization of phone and uppercase email
        $this->postJson('/register', [
            'name' => '  Aarav M. Menon  ',
            'email' => '  AARAV.MENON@Example.com  ',
            'phone' => '+91 98765-43210',
            'role' => 'model',
            'password' => 'Runway2026',
            'password_confirmation' => 'Runway2026',
            'terms' => true,
        ])->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'Aarav M. Menon',
            'email' => 'aarav.menon@example.com',
            'phone' => '+919876543210',
        ]);
    }

    public function test_login_and_admin_login_only_require_password_without_complexity(): void
    {
        User::factory()->create([
            'email' => 'shortpass@example.com',
            'password' => Hash::make('abc'),
            'email_verified_at' => now(),
            'is_admin' => false,
            'role' => UserRole::Model->value,
        ]);

        User::factory()->create([
            'email' => 'adminshort@idfr.example',
            'password' => Hash::make('xyz'),
            'email_verified_at' => now(),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        // Empty submit fails
        $this->postJson('/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        // Short non-alphanumeric existing password MUST still succeed on login
        $this->postJson('/login', [
            'email' => '  SHORTPASS@EXAMPLE.COM ',
            'password' => 'abc',
        ])
            ->assertOk()
            ->assertJson(['success' => true]);

        // Admin login empty & invalid email fail
        $this->post('/admin/login', ['email' => 'invalid', 'password' => ''])
            ->assertSessionHasErrors(['email', 'password']);

        // Admin login with short existing password MUST still work
        $this->post('/admin/login', [
            'email' => ' ADMINSHORT@IDFR.EXAMPLE ',
            'password' => 'xyz',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_verify_otp_and_forgot_password_and_reset_password_validation(): void
    {
        $this->postJson('/verify-otp', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'otp']);

        $this->postJson('/verify-otp', [
            'email' => 'bad',
            'otp' => '12a',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'otp']);

        $this->postJson('/forgot-password', ['email' => 'not-an-email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $user = User::factory()->create([
            'email' => 'reset.tester@example.com',
            'password' => Hash::make('OldPass123'),
            'email_verified_at' => now(),
        ]);
        $token = Password::broker()->createToken($user);

        // Weak password (no digit) & mismatch
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'reset.tester@example.com',
            'password' => 'onlyletters',
            'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['password']);

        // Valid strong password resets cleanly
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => ' RESET.TESTER@EXAMPLE.COM ',
            'password' => 'StrongPass9',
            'password_confirmation' => 'StrongPass9',
        ])->assertRedirect(route('password.reset', ['token' => 'completed']));

        $this->assertTrue(Hash::check('StrongPass9', $user->fresh()->password));
    }

    public function test_contact_sponsorship_and_profile_enquiry_validation_matrix(): void
    {
        Mail::fake();

        $season = Season::create([
            'title' => 'Season 8',
            'number' => 8,
            'registration_fee' => 9999,
            'is_active' => true,
        ]);

        // IDFR contact: empty & invalid phone/email
        $this->postJson(route('idfr.contact.submit'), [
            'name' => '',
            'phone' => '0000000000',
            'email' => 'bad',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'email']);

        // IDFR contact valid (normalizes phone)
        $this->postJson(route('idfr.contact.submit'), [
            'name' => 'Riya Nair',
            'phone' => '+91 98765-43210',
            'email' => 'RIYA@EXAMPLE.COM',
            'subject' => 'Audition Query',
            'message' => 'Looking forward to Season 8 registration details.',
        ])->assertSuccessful();

        $this->assertDatabaseHas('contact_enquiries', [
            'name' => 'Riya Nair',
            'phone' => '+919876543210',
            'email' => 'riya@example.com',
        ]);

        // Sponsorship allows & and - in company names while still checking phone/email
        $this->postJson(route('sponsorship.enquiry'), [
            'name' => '',
            'phone' => '12345',
            'email' => 'bad',
            'sponsorship_type' => '',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'email', 'sponsorship_type']);

        $this->postJson(route('sponsorship.enquiry'), [
            'name' => 'Lux & Craft Co-Lab',
            'phone' => '9876543210',
            'email' => 'PARTNERS@LUX.EXAMPLE',
            'sponsorship_type' => 'Title Sponsor',
            'season_id' => $season->id,
        ])->assertSuccessful();

        $this->assertDatabaseHas('sponsorship_enquiries', [
            'name' => 'Lux & Craft Co-Lab',
            'phone' => '9876543210',
            'email' => 'partners@lux.example',
        ]);

        // Profile enquiry past date & bad phone fail
        $this->postJson(route('enquiry.submit'), [
            'name' => 'Valid Client',
            'phone' => '12345',
            'email' => 'valid@example.com',
            'assignment_type' => ProfileEnquiry::ASSIGNMENT_TYPES[0],
            'preferred_date' => now()->subDay()->toDateString(),
            'location' => 'Kochi',
            'brief' => 'Short brief that is definitely over 10 chars.',
            'professional_type' => 'model',
            'professional_name' => 'Model Name',
            'professional_category' => 'Model',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'preferred_date']);

        EnquiryOtp::create([
            'email' => 'validclient@example.com',
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'verified_at' => now(),
            'attempts' => 0,
        ]);

        $this->withSession(['verified_enquiry_email_'.md5('validclient@example.com') => now()->timestamp])
            ->postJson(route('enquiry.submit'), [
                'name' => 'Valid Client',
                'phone' => '+91 98765 43210',
                'email' => 'validclient@example.com',
                'assignment_type' => ProfileEnquiry::ASSIGNMENT_TYPES[0],
                'preferred_date' => now()->addDays(5)->toDateString(),
                'location' => 'Kochi Studio',
                'brief' => 'Runway lookbook shoot for upcoming launch.',
                'professional_type' => 'model',
                'professional_name' => 'Aarav Sharma',
                'professional_category' => 'Model',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_asdreams_forms_and_season_registration_and_profile_validation(): void
    {
        $season = Season::create([
            'title' => 'Season 8',
            'number' => 8,
            'registration_fee' => 9999,
            'is_active' => true,
        ]);

        // Season registration empty & invalid phone
        $this->postJson(route('idfr.tickets.submit'), [
            'season_id' => $season->id,
            'full_name' => '',
            'whatsapp_number' => '+1234567890123456',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['full_name', 'whatsapp_number']);

        // Conditional guardian check on minor (< 18)
        $this->postJson(route('idfr.tickets.submit'), [
            'season_id' => $season->id,
            'full_name' => 'Diya Nair',
            'whatsapp_number' => '+91 98765 43210',
            'age' => 14,
            'gender' => 'female',
            'category' => 'kids',
            'citizenship' => 'Indian',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['parent_name', 'parent_contact']);

        // ASDreams general contact requires message (min:10), brand contact makes it optional
        $this->postJson(route('asd.contact.enquire'), [
            'name' => 'Meera K.',
            'phone' => '9876543210',
            'message' => 'Hi',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        $this->postJson(route('asd.contact.enquire'), [
            'brand' => 'athuls',
            'name' => 'Meera K.',
            'phone' => '+91 98765-43210',
        ])->assertOk();

        // Offer details enquiry validates past date & bad phone
        $offer = AsdreamsOffer::first();
        $this->postJson(route('asd.offers.enquire'), [
            'offer' => $offer?->slug ?? 'golden-vows',
            'name' => 'Test User',
            'phone' => '123',
            'date' => now()->subDay()->toDateString(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'date']);

        // Book a shoot rejects past date and invalid email
        $this->postJson(route('asd.shoot-booking.submit'), [
            'name' => 'Vikram Das',
            'phone' => '9876543210',
            'email' => 'bad',
            'shoot_date' => now()->subDay()->toDateString(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'shoot_date']);

        // Studio booking allows company chars & rejects end < start
        $this->postJson(route('asd.studio-booking.submit'), [
            'name' => 'Prism & Light Films',
            'phone' => '9876543210',
            'email' => 'studio@prism.example',
            'days' => 2,
            'purpose' => 'Lookbook Film',
            'start' => now()->addDays(4)->toDateString(),
            'end' => now()->addDays(2)->toDateString(),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['end']);

        $this->postJson(route('asd.studio-booking.submit'), [
            'name' => 'Prism & Light Films',
            'phone' => '+91 98765 43210',
            'email' => 'STUDIO@PRISM.EXAMPLE',
            'days' => 2,
            'purpose' => 'Lookbook Film',
            'start' => now()->addDays(4)->toDateString(),
            'end' => now()->addDays(5)->toDateString(),
        ])->assertOk();

        // Model & Creative profile validation
        $modelUser = User::factory()->create([
            'role' => UserRole::Model->value,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($modelUser)->postJson(route('profile.model.save'), [
            'name' => '123Invalid',
            'gender' => 'female',
            'place' => 'Kochi',
            'age' => 10,
            'height' => 50,
            'category' => 'new-face',
            'experience' => 2,
            'skills' => 'Runway',
            'modelType' => [],
            'outstation' => 'yes',
            'international' => 'no',
            'profile_photo' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'age', 'height', 'modelType', 'profile_photo']);

        $creativeUser = User::factory()->create([
            'role' => UserRole::Photographer->value,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($creativeUser)->postJson(route('profile.photographer.save'), [
            'name' => 'Surya Menon',
            'place' => 'Kochi',
            'experience' => 4,
            'phone' => '12345',
            'email' => 'bad',
            'about' => 'Too short',
            'outstation' => 'yes',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['phone', 'email', 'about']);
    }

    public function test_international_e164_phone_matrix_across_all_phone_forms_and_existing_records(): void
    {
        Mail::fake();

        $season = Season::create([
            'title' => 'Season 9',
            'number' => 9,
            'registration_fee' => 9999,
            'is_active' => true,
        ]);

        $offer = AsdreamsOffer::first();

        $validExamples = [
            ['+971541786786', '+971541786786'],
            ['+91 98765 43210', '+919876543210'],
            ['9876543210', '9876543210'],
            ['+1 (415) 555-2671', '+14155552671'],
            ['+44 7911 123456', '+447911123456'],
            ['+966 50 123 4567', '+966501234567'],
        ];

        $invalidExamples = [
            'abc123',
            '12345',
            '+0123456789',
            '0000000000',
            '+1234567890123456',
        ];

        // 1. Register: test every valid and invalid phone format
        $idx = 0;
        foreach ($validExamples as [$raw, $normalized]) {
            $idx++;
            $email = "intl_reg_{$idx}@example.com";
            $this->postJson('/register', [
                'name' => 'Intl Member',
                'email' => $email,
                'phone' => $raw,
                'role' => 'model',
                'password' => 'Runway2026',
                'password_confirmation' => 'Runway2026',
                'terms' => true,
            ])->assertStatus(201);

            $this->assertDatabaseHas('users', [
                'email' => $email,
                'phone' => $normalized,
            ]);
        }

        foreach ($invalidExamples as $bad) {
            $this->postJson('/register', [
                'name' => 'Intl Member',
                'email' => 'badreg@example.com',
                'phone' => $bad,
                'role' => 'model',
                'password' => 'Runway2026',
                'password_confirmation' => 'Runway2026',
                'terms' => true,
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('sponsorship.enquiry'), [
                'name' => 'Global Brand Co',
                'phone' => $bad,
                'email' => 'brand@example.com',
                'sponsorship_type' => 'Title Sponsor',
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('idfr.contact.submit'), [
                'name' => 'Global Contact',
                'phone' => $bad,
                'email' => 'contact@example.com',
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('idfr.tickets.submit'), [
                'season_id' => $season->id,
                'full_name' => 'Global Applicant',
                'whatsapp_number' => $bad,
                'age' => 22,
                'gender' => 'female',
                'category' => 'adult',
                'citizenship' => 'UAE',
            ])->assertStatus(422)->assertJsonValidationErrors(['whatsapp_number']);

            $this->postJson(route('asd.contact.enquire'), [
                'name' => 'Studio Contact',
                'phone' => $bad,
                'message' => 'Enquiring about international campaign booking.',
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('asd.contact.enquire'), [
                'brand' => 'athuls',
                'name' => 'Academy Lead',
                'phone' => $bad,
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('asd.offers.enquire'), [
                'offer' => $offer?->slug ?? 'golden-vows',
                'name' => 'Offer Client',
                'phone' => $bad,
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('asd.shoot-booking.submit'), [
                'name' => 'Shoot Client',
                'phone' => $bad,
                'email' => 'shoot@example.com',
                'shoot_date' => now()->addDays(5)->toDateString(),
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);

            $this->postJson(route('asd.studio-booking.submit'), [
                'name' => 'Rental House LLC',
                'phone' => $bad,
                'email' => 'rental@example.com',
                'days' => 2,
                'purpose' => 'Editorial Shoot',
                'start' => now()->addDays(4)->toDateString(),
                'end' => now()->addDays(5)->toDateString(),
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);
        }

        // 2. Verify international examples across all other phone endpoints (using UAE, US brackets, UK, Saudi, India)
        $this->postJson(route('sponsorship.enquiry'), [
            'name' => 'Emirates Luxury Group',
            'phone' => '+971 54 178 6786',
            'email' => 'sponsor@emirates.example',
            'sponsorship_type' => 'Title Sponsor',
            'season_id' => $season->id,
        ])->assertSuccessful();
        $this->assertDatabaseHas('sponsorship_enquiries', ['phone' => '+971541786786']);

        $this->postJson(route('idfr.contact.submit'), [
            'name' => 'Sarah Jones',
            'phone' => '+1 (415) 555-2671',
            'email' => 'sarah@example.com',
        ])->assertSuccessful();
        $this->assertDatabaseHas('contact_enquiries', ['phone' => '+14155552671']);

        $this->postJson(route('idfr.tickets.submit'), [
            'season_id' => $season->id,
            'full_name' => 'Zayd Al Harbi',
            'whatsapp_number' => '+966 50 123 4567',
            'age' => 15,
            'gender' => 'male',
            'category' => 'kids',
            'citizenship' => 'Saudi',
            'parent_name' => 'Tariq Al Harbi',
            'parent_contact' => '+971541786786',
        ])->assertSuccessful();
        $this->assertDatabaseHas('season_model_registrations', [
            'whatsapp_number' => '+966501234567',
            'parent_contact' => '+971541786786',
        ]);

        $this->postJson(route('asd.contact.enquire'), [
            'name' => 'Oliver Grant',
            'phone' => '+44 7911 123456',
            'message' => 'Booking a multi-day fashion campaign in Dubai.',
        ])->assertOk();
        $this->assertDatabaseHas('asdreams_contact_enquiries', ['phone' => '+447911123456']);

        $this->postJson(route('asd.contact.enquire'), [
            'brand' => 'lakme',
            'name' => 'Nadia Karim',
            'phone' => '+971541786786',
        ])->assertOk();
        $this->assertDatabaseHas('asdreams_contact_enquiries', ['brand' => 'lakme', 'phone' => '+971541786786']);

        $this->postJson(route('asd.offers.enquire'), [
            'offer' => $offer?->slug ?? 'golden-vows',
            'name' => 'Emma Watson',
            'phone' => '+1 (415) 555-2671',
        ])->assertOk();
        $this->assertDatabaseHas('asdreams_offer_enquiries', ['phone' => '+14155552671']);

        $this->postJson(route('asd.shoot-booking.submit'), [
            'name' => 'Tariq Mansoor',
            'phone' => '+971541786786',
            'email' => 'tariq@example.com',
            'shoot_date' => now()->addDays(6)->toDateString(),
        ])->assertOk();
        $this->assertDatabaseHas('asdreams_shoot_bookings', ['phone' => '+971541786786']);

        $this->postJson(route('asd.studio-booking.submit'), [
            'name' => 'London Film Co',
            'phone' => '+44 7911 123456',
            'email' => 'studio@londonfilm.example',
            'days' => 2,
            'purpose' => 'Lookbook Shoot',
            'start' => now()->addDays(8)->toDateString(),
            'end' => now()->addDays(9)->toDateString(),
        ])->assertOk();
        $this->assertDatabaseHas('asdreams_shoot_bookings', ['booking_type' => 'studio', 'phone' => '+447911123456']);

        // Profile enquiry with OTP
        EnquiryOtp::create([
            'email' => 'intlclient@example.com',
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'sent_at' => now(),
            'verified_at' => now(),
            'attempts' => 0,
        ]);

        $this->withSession(['verified_enquiry_email_'.md5('intlclient@example.com') => now()->timestamp])
            ->postJson(route('enquiry.submit'), [
                'name' => 'Intl Producer',
                'phone' => '+971541786786',
                'email' => 'intlclient@example.com',
                'assignment_type' => ProfileEnquiry::ASSIGNMENT_TYPES[0],
                'preferred_date' => now()->addDays(4)->toDateString(),
                'location' => 'Dubai Design District',
                'brief' => 'Runway lookbook shoot for Dubai collection.',
                'professional_type' => 'model',
                'professional_name' => 'Aarav Sharma',
                'professional_category' => 'Model',
            ])->assertOk();
        $this->assertDatabaseHas('profile_enquiries', ['phone' => '+971541786786']);

        // Model profile + Fashion Designer (/profiles/fashion-designer) with valid & invalid phones
        $modelUser = User::factory()->create([
            'role' => UserRole::Model->value,
            'email_verified_at' => now(),
            'phone' => '+91 98765 43210',
        ]);

        foreach ($invalidExamples as $bad) {
            $this->actingAs($modelUser)->postJson(route('profile.model.save'), [
                'name' => 'Elena Rostova',
                'phone' => $bad,
                'email' => 'elena.m@example.com',
                'gender' => 'female',
                'place' => 'Dubai',
                'age' => 23,
                'height' => 176,
                'category' => 'experienced',
                'experience' => 3,
                'skills' => 'Editorial, Runway',
                'modelType' => ['editorial'],
                'outstation' => 'yes',
                'international' => 'yes',
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);
        }

        $this->actingAs($modelUser)->postJson(route('profile.model.save'), [
            'name' => 'Elena Rostova',
            'phone' => '+971541786786',
            'email' => 'elena.m@example.com',
            'gender' => 'female',
            'place' => 'Dubai',
            'age' => 23,
            'height' => 176,
            'category' => 'experienced',
            'experience' => 3,
            'skills' => 'Editorial, Runway',
            'modelType' => ['editorial'],
            'outstation' => 'yes',
            'international' => 'yes',
        ])->assertOk();
        $this->assertDatabaseHas('models', ['phone' => '+971541786786']);

        $designerUser = User::factory()->create([
            'role' => UserRole::FashionDesigner->value,
            'email_verified_at' => now(),
        ]);

        foreach ($invalidExamples as $bad) {
            $this->actingAs($designerUser)->postJson(route('profile.fashion-designer.save'), [
                'name' => 'Amira Al Maktoum',
                'place' => 'Dubai',
                'experience' => 6,
                'phone' => $bad,
                'email' => 'amira@atelier.example',
                'about' => 'Haute couture and resort wear label based in Dubai and Mumbai.',
                'service' => ['bridal-couture'],
                'outstation' => 'yes',
            ])->assertStatus(422)->assertJsonValidationErrors(['phone']);
        }

        foreach ($validExamples as [$raw, $normalized]) {
            $this->actingAs($designerUser)->postJson(route('profile.fashion-designer.save'), [
                'name' => 'Amira Al Maktoum',
                'place' => 'Dubai',
                'experience' => 6,
                'phone' => $raw,
                'email' => 'amira@atelier.example',
                'about' => 'Haute couture and resort wear label based in Dubai and Mumbai.',
                'service' => ['bridal-couture'],
                'outstation' => 'yes',
            ])->assertOk();

            $this->assertDatabaseHas('designers', [
                'user_id' => $designerUser->id,
                'phone' => $normalized,
            ]);
        }

        // Confirm existing saved phone formats (local 10-digit + +91 spaced + international) still work
        $legacyModelUser = User::factory()->create([
            'role' => UserRole::Model->value,
            'email_verified_at' => now(),
            'phone' => '9876543210',
        ]);
        $this->actingAs($legacyModelUser)->postJson(route('profile.model.save'), [
            'name' => 'Kavya Nair',
            'phone' => '+91 98765 43210',
            'email' => 'kavya@example.com',
            'gender' => 'female',
            'place' => 'Kochi',
            'age' => 24,
            'height' => 174,
            'category' => 'experienced',
            'experience' => 4,
            'skills' => 'Runway, Print',
            'modelType' => ['runway'],
            'outstation' => 'yes',
            'international' => 'no',
        ])->assertOk();
        $this->assertDatabaseHas('models', ['user_id' => $legacyModelUser->id, 'phone' => '+919876543210']);
    }
}
