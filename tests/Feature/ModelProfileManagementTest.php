<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ModelProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ModelProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Unauthenticated users are redirected from model profile.
     */
    public function test_unauthenticated_user_redirected_from_model_profile(): void
    {
        $response = $this->get(route('profile.model'));
        $response->assertRedirect(route('idfr.home', ['auth' => 'login']));
    }

    /**
     * Model user accessing /profiles/model sees their own profile.
     */
    public function test_model_user_accessing_profile_sees_own_profile_and_record_is_created(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Model->value,
            'name' => 'Kavya Nair',
            'email' => 'kavya@example.com',
            'phone' => '+91 9123456780',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseMissing('models', [
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('profile.model'));

        $response->assertOk()
            ->assertSee('Kavya Nair')
            ->assertSee('kavya@example.com')
            ->assertSee('IDFR Runway Talent')
            ->assertSee('Measurements &amp; Sizes', false)
            ->assertSee('Shoe Size')
            ->assertSee('About / Biography');

        $this->assertDatabaseHas('models', [
            'user_id' => $user->id,
            'name' => 'Kavya Nair',
            'email' => 'kavya@example.com',
        ]);
    }

    /**
     * Non-model users accessing /profiles/model are redirected to their own profile.
     */
    public function test_non_model_user_is_redirected_to_their_own_profile(): void
    {
        $designerUser = User::factory()->create([
            'role' => UserRole::FashionDesigner->value,
            'name' => 'Sanjay Roy',
            'email' => 'sanjay@example.com',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($designerUser)->get(route('profile.model'));
        $response->assertRedirect(route('profile.fashion-designer'));
    }

    /**
     * Model can save profile with measurements, bio, skills, and image upload.
     */
    public function test_model_can_save_profile_measurements_and_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::Model->value,
            'name' => 'Aria Sen',
            'email' => 'aria@example.com',
            'phone' => '+91 98888 11111',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ]);

        $photo = UploadedFile::fake()->image('profile_headshot.jpg', 600, 800);
        $portfolio1 = UploadedFile::fake()->image('look1.jpg', 600, 800);

        $payload = [
            'name' => 'Aria Sen Updated',
            'gender' => 'female',
            'place' => 'Kochi',
            'age' => 24,
            'height' => 178,
            'bust' => '34B',
            'waist' => '25',
            'hips' => '35',
            'shoe' => 'UK 6',
            'category' => 'experienced',
            'experience' => 4,
            'skills' => 'Runway walk, High fashion, Classical dance',
            'modelType' => ['runway', 'editorial'],
            'phone' => '+91 98888 11111',
            'email' => 'aria@example.com',
            'about' => 'Experienced runway and couture model with 4 years on national ramps.',
            'outstation' => 'yes',
            'international' => 'yes',
            'profile_photo' => $photo,
            'portfolio_image_1' => $portfolio1,
        ];

        $response = $this->actingAs($user)->post(route('profile.model.save'), $payload);

        $response->assertSessionHas('success');

        $modelProfile = ModelProfile::where('user_id', $user->id)->first();
        $this->assertNotNull($modelProfile);
        $this->assertSame('Aria Sen Updated', $modelProfile->name);
        $this->assertSame('Kochi', $modelProfile->location);
        $this->assertSame('178', (string) $modelProfile->height);
        $this->assertSame('Experienced', $modelProfile->level);
        $this->assertSame('Experienced runway and couture model with 4 years on national ramps.', $modelProfile->description);
        $this->assertIsArray($modelProfile->measurements);
        $this->assertSame('34B', $modelProfile->measurements['Bust']);
        $this->assertSame('25', $modelProfile->measurements['Waist']);
        $this->assertSame('35', $modelProfile->measurements['Hips']);
        $this->assertSame('UK 6', $modelProfile->measurements['Shoe']);
        $this->assertNotNull($modelProfile->profile_photo);
        $this->assertNotNull($modelProfile->portfolio_images);
        $this->assertCount(1, $modelProfile->portfolio_images);

        // Access model profile page again and check rendered values
        $reopenResponse = $this->actingAs($user)->get(route('profile.model'));
        $reopenResponse->assertOk()
            ->assertSee('Aria Sen Updated')
            ->assertSee('Experienced runway and couture model')
            ->assertSee('34B')
            ->assertSee('UK 6');
    }

    /**
     * AJAX profile save returns JSON response with photo url and profile completion.
     */
    public function test_ajax_profile_save_returns_json(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Model->value,
            'name' => 'Meera Patel',
            'email' => 'meera@example.com',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ]);

        $payload = [
            'name' => 'Meera Patel',
            'gender' => 'female',
            'place' => 'Kozhikode',
            'age' => 21,
            'height' => 174,
            'bust' => '32',
            'waist' => '24',
            'hips' => '34',
            'shoe' => 'UK 5',
            'category' => 'new-face',
            'experience' => 1,
            'skills' => 'Editorial posing',
            'modelType' => ['editorial'],
            'phone' => '+91 97777 22222',
            'email' => 'meera@example.com',
            'about' => 'Aspiring editorial face from Kozhikode.',
            'outstation' => 'yes',
            'international' => 'no',
        ];

        $response = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('profile.model.save'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'profile_completion',
            ]);
    }
}
