<?php

namespace Tests\Feature;

use App\Models\Season;
use App\Models\SeasonImage;
use App\Models\User;
use Database\Seeders\SeasonHeroImagesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SeasonHeroImageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected Season $season8;

    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'director@idfr.example'],
            [
                'name' => 'IDFR Director',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );

        $this->season8 = Season::firstOrCreate(
            ['number' => 8],
            [
                'title' => 'Season 8',
                'year' => '2026',
                'location' => 'Kochi, Kerala',
                'dates' => '18 — 20 December 2026',
                'status' => 'published',
                'description' => 'Season 8 brings together emerging talent, established models, designers and fashion enthusiasts.',
            ]
        );

        $this->seed(SeasonHeroImagesSeeder::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $path) {
            if (File::exists(public_path($path))) {
                @unlink(public_path($path));
            }
        }
        parent::tearDown();
    }

    /**
     * Test 1 & 2 & 3: Multi-image upload saves 5 records to MySQL with correct season_id and accessible paths.
     */
    public function test_admin_can_upload_five_images_to_season_8(): void
    {
        $files = [
            UploadedFile::fake()->image('test_look_01.jpg', 1100, 1650),
            UploadedFile::fake()->image('test_look_02.png', 1100, 1650),
            UploadedFile::fake()->image('test_look_03.webp', 1100, 1650),
            UploadedFile::fake()->image('test_look_04.jpg', 1100, 1650),
            UploadedFile::fake()->image('test_look_05.jpg', 1100, 1650),
        ];

        $response = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.seasons.images.upload', $this->season8), [
                'images' => $files,
                'designer_name' => 'Test Couture House',
                'caption' => 'Runway Test Collection',
                'look_number_prefix' => 'Look',
            ]);

        $response->assertRedirect(route('admin.seasons.images', $this->season8));
        $response->assertSessionHas('success');

        // Confirm all 5 records exist in MySQL
        $latestFive = SeasonImage::where('season_id', $this->season8->id)
            ->where('designer_name', 'Test Couture House')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $this->assertCount(5, $latestFive);

        foreach ($latestFive as $img) {
            $this->createdFiles[] = $img->image_path;
            $this->assertEquals($this->season8->id, $img->season_id);
            $this->assertTrue(File::exists(public_path($img->image_path)));
            $this->assertNotEmpty($img->image_url);
            $this->assertTrue($img->is_active);
        }
    }

    /**
     * Test 4: Refresh admin page and confirm images are visible.
     */
    public function test_admin_image_manager_renders_season_images(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.seasons.images', $this->season8));

        $response->assertOk();
        $response->assertSee('Season Hero Images');
        $response->assertSee('Season 8');
        $response->assertSee('Upload Runway Looks to Season 8');
    }

    /**
     * Test 5 & 6: Open homepage and confirm images are dynamically rendered.
     */
    public function test_homepage_loads_season_8_images_dynamically(): void
    {
        $response = $this->get(route('idfr.home'));

        $response->assertOk();
        $response->assertSee('Season');
        $response->assertSee('-8');
        $response->assertSee('id="heroSlidesData"', false);
        $response->assertSee('data-designer=', false);
        $response->assertSee('data-look=', false);
    }

    /**
     * Test 7: Edit image caption and verify change reflects on homepage.
     */
    public function test_edit_image_caption_reflects_on_homepage(): void
    {
        $image = SeasonImage::where('season_id', $this->season8->id)->first();
        $this->assertNotNull($image);

        $uniqueCaption = 'Exclusive Velvet Symphony '.time();

        $response = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.season-images.update', $image), [
                'look_number' => 'Look 01',
                'image_title' => $uniqueCaption,
                'caption' => $uniqueCaption,
                'designer_name' => 'Aarav Khanna Haute Couture',
                'alt_text' => 'Updated alt text for verification',
                'display_order' => 1,
                'is_active' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('season_images', [
            'id' => $image->id,
            'caption' => $uniqueCaption,
        ]);

        // Verify on homepage
        $homeResponse = $this->get(route('idfr.home'));
        $homeResponse->assertOk();
        $homeResponse->assertSee($uniqueCaption);
    }

    /**
     * Test 8: Add another image and verify existing images are preserved.
     */
    public function test_add_additional_image_preserves_existing(): void
    {
        $initialCount = SeasonImage::where('season_id', $this->season8->id)->count();

        $newFile = UploadedFile::fake()->image('additional_look.jpg', 1100, 1650);

        $response = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.seasons.images.upload', $this->season8), [
                'images' => [$newFile],
                'designer_name' => 'Atelier Preservation Test',
                'caption' => 'Preservation Check Look',
            ]);

        $response->assertRedirect();
        $this->assertEquals($initialCount + 1, SeasonImage::where('season_id', $this->season8->id)->count());

        $newImg = SeasonImage::where('season_id', $this->season8->id)
            ->where('designer_name', 'Atelier Preservation Test')
            ->first();
        if ($newImg) {
            $this->createdFiles[] = $newImg->image_path;
        }
    }

    /**
     * Test 9: Deactivate an image and confirm it is excluded from active homepage slider.
     */
    public function test_deactivate_image_excludes_from_active_slider(): void
    {
        $image = SeasonImage::where('season_id', $this->season8->id)
            ->where('is_active', true)
            ->first();

        $this->assertNotNull($image);

        // Deactivate via toggle
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.season-images.toggle-status', $image));

        $this->assertDatabaseHas('season_images', [
            'id' => $image->id,
            'is_active' => false,
        ]);

        // Active images count on season should not include this image
        $activeImages = $this->season8->fresh()->activeImages;
        $this->assertFalse($activeImages->contains('id', $image->id));

        // Re-activate so state is clean
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.season-images.toggle-status', $image));
    }

    /**
     * Test 10: Delete an image and confirm only that image is removed.
     */
    public function test_delete_image_removes_only_selected(): void
    {
        // Create an isolated image to delete
        $tempImg = SeasonImage::create([
            'season_id' => $this->season8->id,
            'image_path' => 'uploads/seasons/images/to_be_deleted.jpg',
            'image_title' => 'Temporary Delete Target',
            'caption' => 'Temporary Delete Target',
            'display_order' => 999,
            'is_active' => true,
        ]);

        $countBefore = SeasonImage::where('season_id', $this->season8->id)->count();

        $response = $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.season-images.destroy', $tempImg));

        $response->assertRedirect();
        $this->assertDatabaseMissing('season_images', ['id' => $tempImg->id]);
        $this->assertEquals($countBefore - 1, SeasonImage::where('season_id', $this->season8->id)->count());
    }

    /**
     * Test 11: Set as first look updates display order to 1.
     */
    public function test_set_as_first_look(): void
    {
        $targetImg = SeasonImage::where('season_id', $this->season8->id)
            ->where('display_order', '>', 1)
            ->first();

        if ($targetImg) {
            $response = $this->actingAs($this->adminUser, 'admin')
                ->post(route('admin.season-images.set-first', $targetImg));

            $response->assertRedirect();
            $this->assertEquals(1, $targetImg->fresh()->display_order);
        }
    }
}
