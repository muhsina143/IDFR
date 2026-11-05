<?php

namespace Tests\Feature;

use App\Models\CreativeProfessional;
use App\Models\ModelProfile;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileRegistrationApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_review@idfr.example'],
            [
                'name' => 'IDFR Director',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }

    /**
     * Test newly registered Model profile defaults to pending approval status.
     */
    public function test_model_registration_and_profile_submission_defaults_to_pending(): void
    {
        $user = User::create([
            'name' => 'Aria Thorne',
            'email' => 'aria.model@idfr.example',
            'phone' => '+91 98765 43210',
            'password' => bcrypt('password123'),
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $fakePhoto = UploadedFile::fake()->image('aria_headshot.jpg', 600, 800);
        $fakePortfolio1 = UploadedFile::fake()->image('aria_look1.jpg', 600, 800);
        $fakePortfolio2 = UploadedFile::fake()->image('aria_look2.jpg', 600, 800);

        $response = $this->from(route('profile.model'))->post(route('profile.model.save'), [
            'name' => 'Aria Thorne',
            'gender' => 'female',
            'category' => 'experienced',
            'place' => 'Kochi',
            'age' => 24,
            'height' => 178,
            'experience' => 3,
            'bust' => '34',
            'waist' => '26',
            'hips' => '36',
            'shoe' => '7',
            'about' => 'Experienced high-fashion and runway editorial model based in Kochi.',
            'instagram' => 'https://instagram.com/ariathorne',
            'portfolio' => 'https://ariathorne.example',
            'modelType' => ['runway', 'editorial'],
            'skills' => 'Catwalk, High Fashion, Posing',
            'outstation' => 'yes',
            'international' => 'no',
            'profile_photo' => $fakePhoto,
            'portfolio_images' => [$fakePortfolio1, $fakePortfolio2],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.model'));

        // Profile record must exist and have pending status
        $profile = ModelProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('pending', $profile->status);
        $this->assertTrue($profile->isPending());
        $this->assertFalse($profile->isApproved());
        $this->assertEquals('pending', $user->fresh()->profile_status);
        $this->assertFalse($user->fresh()->isProfileApproved());

        // Measurements & images saved
        $this->assertEquals('34', $profile->measurements['Bust'] ?? null);
        $this->assertEquals('26', $profile->measurements['Waist'] ?? null);
        $this->assertEquals('36', $profile->measurements['Hips'] ?? null);
        $this->assertNotNull($profile->profile_photo);
        $this->assertNotEmpty($profile->portfolio_images);
    }

    /**
     * Test newly registered Creative Professional defaults to pending approval status.
     */
    public function test_creative_professional_registration_defaults_to_pending(): void
    {
        $this->seed(DynamicProfileFormSeeder::class);

        $user = User::create([
            'name' => 'Kiran Varma',
            'email' => 'kiran.makeup@idfr.example',
            'phone' => '+91 98450 12345',
            'password' => bcrypt('password123'),
            'role' => 'makeup-artist',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $fakePhoto = UploadedFile::fake()->image('kiran_profile.jpg', 600, 800);
        $fakeWork1 = UploadedFile::fake()->image('kiran_work1.jpg', 600, 800);

        $response = $this->post(route('profile.category.save', ['category' => 'makeup-artist']), [
            'name' => 'Kiran Varma',
            'profession' => 'Lead Editorial Makeup Artist',
            'place' => 'Kozhikode',
            'experience' => 6,
            'phone' => '+91 98450 12345',
            'email' => 'kiran.makeup@idfr.example',
            'about' => 'Specializing in high-definition bridal and runway makeup looks.',
            'service' => ['Runway Makeup', 'Bridal Glam'],
            'outstation' => 'yes',
            'instagram_url' => 'https://instagram.com/kiranmakeup',
            'profile_photo' => $fakePhoto,
            'portfolio_images' => [$fakeWork1],
        ]);

        $response->assertSessionHasNoErrors();

        $pro = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($pro);
        $this->assertEquals('pending', $pro->status);
        $this->assertTrue($pro->isPending());
        $this->assertFalse($pro->isApproved());
        $this->assertEquals('pending', $user->fresh()->profile_status);
    }

    /**
     * Test pending and rejected profiles are never displayed on public pages.
     */
    public function test_pending_and_rejected_profiles_are_not_displayed_on_public_pages(): void
    {
        // 1. Pending Model
        $pendingUser = User::create([
            'name' => 'Hidden Pending Model',
            'email' => 'pending.model@idfr.example',
            'password' => bcrypt('secret123'),
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        ModelProfile::create([
            'user_id' => $pendingUser->id,
            'name' => 'Hidden Pending Model',
            'slug' => 'hidden-pending-model',
            'gender' => 'Female',
            'level' => 'New Face',
            'status' => 'pending',
            'location' => 'Kochi',
        ]);

        // 2. Rejected Model
        $rejectedUser = User::create([
            'name' => 'Hidden Rejected Model',
            'email' => 'rejected.model@idfr.example',
            'password' => bcrypt('secret123'),
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        ModelProfile::create([
            'user_id' => $rejectedUser->id,
            'name' => 'Hidden Rejected Model',
            'slug' => 'hidden-rejected-model',
            'gender' => 'Female',
            'level' => 'Experienced',
            'status' => 'rejected',
            'location' => 'Kozhikode',
        ]);

        // 3. Approved Live Model
        $approvedUser = User::create([
            'name' => 'Live Approved Model',
            'email' => 'approved.model@idfr.example',
            'password' => bcrypt('secret123'),
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        ModelProfile::create([
            'user_id' => $approvedUser->id,
            'name' => 'Live Approved Model',
            'slug' => 'live-approved-model',
            'gender' => 'Female',
            'level' => 'Experienced',
            'status' => 'published',
            'location' => 'Kochi',
        ]);

        // 4. Pending Creative Pro
        $pendingProUser = User::create([
            'name' => 'Hidden Pending Artist',
            'email' => 'pending.artist@idfr.example',
            'password' => bcrypt('secret123'),
            'role' => 'makeup-artist',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        CreativeProfessional::create([
            'user_id' => $pendingProUser->id,
            'name' => 'Hidden Pending Artist',
            'slug' => 'hidden-pending-artist',
            'category' => 'makeup-artist',
            'status' => 'pending',
            'location' => 'Kochi',
        ]);

        // Public Models page
        $modelsResponse = $this->get(route('idfr.models'));
        $modelsResponse->assertStatus(200);
        $modelsResponse->assertSee('Live Approved Model');
        $modelsResponse->assertDontSee('Hidden Pending Model');
        $modelsResponse->assertDontSee('Hidden Rejected Model');

        // Homepage Models section
        $homeResponse = $this->get(route('idfr.home'));
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Live Approved Model');
        $homeResponse->assertDontSee('Hidden Pending Model');
        $homeResponse->assertDontSee('Hidden Rejected Model');

        // Public Professionals page
        $prosResponse = $this->get(route('idfr.professionals'));
        $prosResponse->assertStatus(200);
        $prosResponse->assertDontSee('Hidden Pending Artist');
    }

    /**
     * Test admin review page displays all submitted details, photos, and portfolio images.
     */
    public function test_admin_dashboard_displays_complete_profile_review_information_and_images(): void
    {
        $user = User::create([
            'name' => 'Zara Phoenix',
            'email' => 'zara@idfr.example',
            'password' => bcrypt('secret123'),
            'phone' => '+91 99988 77766',
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $model = ModelProfile::create([
            'user_id' => $user->id,
            'name' => 'Zara Phoenix',
            'slug' => 'zara-phoenix',
            'gender' => 'Female',
            'level' => 'Experienced',
            'status' => 'pending',
            'height' => '5ft 11in',
            'age' => 22,
            'location' => 'Mumbai / Kochi',
            'experience' => '4 Years International',
            'measurements' => [
                'Bust' => '34B',
                'Waist' => '25',
                'Hips' => '35',
                'Shoe' => '8',
            ],
            'description' => 'Runway specialist with international editorial experience.',
            'profile_photo' => 'uploads/models/test_zara_headshot.jpg',
            'portfolio_images' => [
                'uploads/models/test_zara_look1.jpg',
                'uploads/models/test_zara_look2.jpg',
            ],
            'types' => ['runway', 'high-fashion'],
        ]);

        $this->actingAs($this->adminUser, 'admin');

        $response = $this->get(route('admin.registrations.show', $user));
        $response->assertStatus(200);

        // Verification of review elements:
        // 1. Heading and status banner
        $response->assertSee('Zara Phoenix');
        $response->assertSee('Pending Approval');
        $response->assertSee('Accept Profile');
        $response->assertSee('Reject Profile');

        // 2. Details & Measurements
        $response->assertSee('5ft 11in');
        $response->assertSee('22 yrs');
        $response->assertSee('34B');
        $response->assertSee('25');
        $response->assertSee('35');
        $response->assertSee('8');
        $response->assertSee('Mumbai / Kochi');
        $response->assertSee('Runway specialist with international editorial experience.');

        // 3. Images rendered directly
        $response->assertSee('test_zara_headshot.jpg');
        $response->assertSee('test_zara_look1.jpg');
        $response->assertSee('test_zara_look2.jpg');
        $response->assertSee('Look #1');
        $response->assertSee('Look #2');
    }

    /**
     * Test admin can accept a profile, turning it published and visible on the website.
     */
    public function test_admin_can_accept_profile_and_make_it_live(): void
    {
        $user = User::create([
            'name' => 'Nikhil Nair',
            'email' => 'nikhil@idfr.example',
            'password' => bcrypt('secret123'),
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $model = ModelProfile::create([
            'user_id' => $user->id,
            'name' => 'Nikhil Nair',
            'slug' => 'nikhil-nair',
            'gender' => 'Male',
            'level' => 'New Face',
            'status' => 'pending',
            'location' => 'Kochi',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // Confirm not visible publicly yet
        $this->get(route('idfr.models'))->assertDontSee('Nikhil Nair');

        // Admin accepts profile
        $response = $this->post(route('admin.registrations.approval-status', $user), [
            'status' => 'published',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('published', $model->fresh()->status);
        $this->assertTrue($model->fresh()->isApproved());
        $this->assertTrue($user->fresh()->isProfileApproved());

        // Now visible on public models page
        $this->get(route('idfr.models'))->assertSee('Nikhil Nair');
    }

    /**
     * Test admin can reject a profile, keeping it hidden from the public website.
     */
    public function test_admin_can_reject_profile_and_keep_it_hidden(): void
    {
        $user = User::create([
            'name' => 'Sanjay Roy',
            'email' => 'sanjay@idfr.example',
            'password' => bcrypt('secret123'),
            'role' => 'model',
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $model = ModelProfile::create([
            'user_id' => $user->id,
            'name' => 'Sanjay Roy',
            'slug' => 'sanjay-roy',
            'gender' => 'Male',
            'level' => 'New Face',
            'status' => 'pending',
            'location' => 'Kozhikode',
        ]);

        $this->actingAs($this->adminUser, 'admin');

        // Admin rejects profile
        $response = $this->post(route('admin.registrations.approval-status', $user), [
            'status' => 'rejected',
            'reason' => 'Portfolio images do not meet resolution criteria.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('rejected', $model->fresh()->status);
        $this->assertTrue($model->fresh()->isRejected());
        $this->assertFalse($user->fresh()->isProfileApproved());

        // Remains hidden on public models page
        $this->get(route('idfr.models'))->assertDontSee('Sanjay Roy');
    }
}
