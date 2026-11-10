<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsClient;
use App\Models\Asdreams\AsdreamsMovingLogo;
use App\Models\Client;
use App\Models\MovingLogo;
use App\Models\User;
use Database\Seeders\AsdreamsClientsAndLogosSeeder;
use Database\Seeders\AsdreamsDataSeeder;
use Database\Seeders\DynamicProfileFormSeeder;
use Database\Seeders\PartnersSponsorsClientsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AsdreamsClientsAndMovingLogosTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    /**
     * @var list<string>
     */
    protected array $testFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
        $this->seed(AsdreamsDataSeeder::class);
        $this->seed(PartnersSponsorsClientsSeeder::class);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_asdreams_clients@idfr.example'],
            [
                'name' => 'ASDreamStudio Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    public function test_guests_cannot_access_asdreams_clients_or_moving_logos_admin(): void
    {
        $this->get(route('admin.asdreams.clients.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.clients.create'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.moving-logos.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.asdreams.moving-logos.create'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_sidebar_contains_both_separate_asdreams_menus(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.asdreams.clients.index'), false);
        $response->assertSee('<span>Our Clients</span>', false);
        $response->assertSee(route('admin.asdreams.moving-logos.index'), false);
        $response->assertSee('<span>Moving Client Logos</span>', false);
    }

    public function test_admin_can_crud_asdreams_our_clients_independently(): void
    {
        $initialMovingLogosCount = AsdreamsMovingLogo::count();
        $initialIdfrClientsCount = Client::count();

        // 1. Index page
        $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.clients.index'))
            ->assertOk()
            ->assertSee('Maison Kerala');

        // 2. Create page
        $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.clients.create'))
            ->assertOk()
            ->assertSee('Client Name')
            ->assertSee('Client Logo')
            ->assertSee('Client Category')
            ->assertSee('Featured Client Image')
            ->assertSee('Client Description / Story')
            ->assertSee('Display Order');

        // 3. Store new client with uploaded logo and featured image
        $logoFile = UploadedFile::fake()->image('kerala-atelier-logo.png', 300, 120);
        $featuredFile = UploadedFile::fake()->image('kerala-atelier-featured.jpg', 1000, 1250);

        $storeRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.clients.store'), [
                'name' => 'Atelier Zuri',
                'category' => 'Luxury Couture',
                'logo' => $logoFile,
                'featured_image' => $featuredFile,
                'description' => 'ASDreamStudio photographed our entire monsoon lookbook seamlessly in one studio session.',
                'display_order' => 1,
                'is_active' => '1',
                'is_featured' => '1',
            ]);

        $storeRes->assertRedirect(route('admin.asdreams.clients.index'));

        $client = AsdreamsClient::where('name', 'Atelier Zuri')->first();
        $this->assertNotNull($client);
        $this->assertSame('Luxury Couture', $client->category);
        $this->assertSame(1, $client->display_order);
        $this->assertTrue($client->is_active);
        $this->assertTrue($client->is_featured);
        $this->assertNotEmpty($client->logo_path);
        $this->assertNotEmpty($client->featured_image);
        $this->assertFileExists(public_path($client->logo_path));
        $this->assertFileExists(public_path($client->featured_image));
        $this->testFiles[] = public_path($client->logo_path);
        $this->testFiles[] = public_path($client->featured_image);

        // Enforces single featured client without touching moving logos or IDFR clients
        $this->assertSame(1, AsdreamsClient::where('is_featured', true)->count());
        $this->assertSame($initialMovingLogosCount, AsdreamsMovingLogo::count());
        $this->assertSame($initialIdfrClientsCount, Client::count());

        // Verify frontend ASDreams home & landing show the new client in #clientsData, NOT in #movingLogosData
        $homeRes = $this->get(route('asd.home'));
        $homeRes->assertOk();
        $homeHtml = $homeRes->getContent();
        $clientsTpl = $this->extractTemplate($homeHtml, 'clientsData');
        $movingTpl = $this->extractTemplate($homeHtml, 'movingLogosData');

        $this->assertStringContainsString('Atelier Zuri', $clientsTpl);
        $this->assertStringContainsString('Luxury Couture', $clientsTpl);
        $this->assertStringContainsString($client->logo_url, $clientsTpl);
        $this->assertStringContainsString($client->featured_image_url, $clientsTpl);
        $this->assertStringNotContainsString('Atelier Zuri', $movingTpl);

        // 4. Edit & Update
        $oldLogoPath = $client->logo_path;
        $newLogoFile = UploadedFile::fake()->image('zuri-v2.png', 320, 120);

        $updateRes = $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.asdreams.clients.update', $client), [
                'name' => 'Atelier Zuri Bespoke',
                'category' => 'Bridal Couture',
                'logo' => $newLogoFile,
                'description' => 'Updated studio story for Atelier Zuri Bespoke.',
                'display_order' => 2,
                'is_active' => '1',
                'is_featured' => '1',
            ]);

        $updateRes->assertRedirect(route('admin.asdreams.clients.index'));
        $client->refresh();
        $this->assertSame('Atelier Zuri Bespoke', $client->name);
        $this->assertSame('Bridal Couture', $client->category);
        $this->assertNotEquals($oldLogoPath, $client->logo_path);
        $this->assertFileDoesNotExist(public_path($oldLogoPath));
        $this->assertFileExists(public_path($client->logo_path));
        $this->testFiles[] = public_path($client->logo_path);

        // 5. Toggle status inactive hides it from ASDreams frontend
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.clients.toggle-status', $client))
            ->assertRedirect();

        $client->refresh();
        $this->assertFalse($client->is_active);
        $this->get(route('landing'))->assertDontSee('Atelier Zuri Bespoke');

        // 6. Delete removes record and uploaded files without affecting Moving Client Logos
        $logoToDelete = public_path($client->logo_path);
        $featuredToDelete = public_path($client->featured_image);

        $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.asdreams.clients.destroy', $client))
            ->assertRedirect(route('admin.asdreams.clients.index'));

        $this->assertNull(AsdreamsClient::find($client->id));
        $this->assertFileDoesNotExist($logoToDelete);
        $this->assertFileDoesNotExist($featuredToDelete);
        $this->assertSame($initialMovingLogosCount, AsdreamsMovingLogo::count());
    }

    public function test_admin_can_crud_asdreams_moving_client_logos_independently(): void
    {
        $initialClientsCount = AsdreamsClient::count();
        $initialIdfrLogosCount = MovingLogo::count();

        // 1. Index page & live strip preview
        $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.moving-logos.index'))
            ->assertOk()
            ->assertSee('Live Moving Strip Preview');

        // 2. Create page
        $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.moving-logos.create'))
            ->assertOk()
            ->assertSee('Logo Name')
            ->assertSee('Logo Image Upload')
            ->assertSee('Display Order');

        // 3. Store new moving logo
        $logoUpload = UploadedFile::fake()->image('malabar-studio-strip.png', 280, 90);

        $storeRes = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.moving-logos.store'), [
                'name' => 'Malabar Couture',
                'logo' => $logoUpload,
                'display_order' => 1,
                'is_active' => '1',
            ]);

        $storeRes->assertRedirect(route('admin.asdreams.moving-logos.index'));

        $movingLogo = AsdreamsMovingLogo::where('name', 'Malabar Couture')->first();
        $this->assertNotNull($movingLogo);
        $this->assertSame(1, $movingLogo->display_order);
        $this->assertTrue($movingLogo->is_active);
        $this->assertNotEmpty($movingLogo->logo_path);
        $this->assertFileExists(public_path($movingLogo->logo_path));
        $this->testFiles[] = public_path($movingLogo->logo_path);

        // Zero impact on Our Clients or IDFR moving logos
        $this->assertSame($initialClientsCount, AsdreamsClient::count());
        $this->assertSame($initialIdfrLogosCount, MovingLogo::count());

        // Verify appears ONLY inside #movingLogosData on ASDreams frontend, NOT #clientsData
        $homeRes = $this->get(route('landing'));
        $homeRes->assertOk();
        $homeHtml = $homeRes->getContent();
        $clientsTpl = $this->extractTemplate($homeHtml, 'clientsData');
        $movingTpl = $this->extractTemplate($homeHtml, 'movingLogosData');

        $this->assertStringContainsString('Malabar Couture', $movingTpl);
        $this->assertStringContainsString($movingLogo->logo_url, $movingTpl);
        $this->assertStringNotContainsString('Malabar Couture', $clientsTpl);

        // 4. Edit & Update
        $oldPath = $movingLogo->logo_path;
        $replacementLogo = UploadedFile::fake()->image('malabar-v2.png', 280, 90);

        $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.asdreams.moving-logos.update', $movingLogo), [
                'name' => 'Malabar Couture Group',
                'logo' => $replacementLogo,
                'display_order' => 3,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.asdreams.moving-logos.index'));

        $movingLogo->refresh();
        $this->assertSame('Malabar Couture Group', $movingLogo->name);
        $this->assertSame(3, $movingLogo->display_order);
        $this->assertNotEquals($oldPath, $movingLogo->logo_path);
        $this->assertFileDoesNotExist(public_path($oldPath));
        $this->assertFileExists(public_path($movingLogo->logo_path));
        $this->testFiles[] = public_path($movingLogo->logo_path);

        // 5. Toggle status inactive hides it from ASDreams moving logos
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.moving-logos.toggle-status', $movingLogo))
            ->assertRedirect();

        $movingLogo->refresh();
        $this->assertFalse($movingLogo->is_active);
        $this->get(route('asd.home'))->assertDontSee('Malabar Couture Group');

        // 6. Delete removes record & uploaded file without affecting Our Clients
        $pathToDelete = public_path($movingLogo->logo_path);

        $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.asdreams.moving-logos.destroy', $movingLogo))
            ->assertRedirect(route('admin.asdreams.moving-logos.index'));

        $this->assertNull(AsdreamsMovingLogo::find($movingLogo->id));
        $this->assertFileDoesNotExist($pathToDelete);
        $this->assertSame($initialClientsCount, AsdreamsClient::count());
    }

    public function test_modules_render_strictly_from_own_tables_when_other_is_empty(): void
    {
        // Delete all Our Clients while keeping Moving Client Logos intact
        AsdreamsClient::query()->delete();

        $resOnlyStrip = $this->get(route('asd.home'));
        $resOnlyStrip->assertOk();
        $resOnlyStrip->assertDontSee('id="clientsGrid"', false);
        $resOnlyStrip->assertSee('id="clientStrip"', false);
        $resOnlyStrip->assertSee('id="movingLogosData"', false);

        // Re-seed clients and delete all Moving Client Logos
        $this->seed(AsdreamsClientsAndLogosSeeder::class);
        AsdreamsMovingLogo::query()->delete();

        $resOnlyClients = $this->get(route('asd.home'));
        $resOnlyClients->assertOk();
        $resOnlyClients->assertSee('id="clientsGrid"', false);
        $resOnlyClients->assertDontSee('id="clientStrip"', false);

        // Verify IDFR website still shows IDFR's independent seeded clients and moving logos
        $idfrRes = $this->get(route('idfr.home'));
        $idfrRes->assertOk();
        $idfrRes->assertSee('id="clientsGrid"', false);
        $idfrRes->assertSee('id="clientStrip"', false);
    }

    protected function extractTemplate(string $html, string $templateId): string
    {
        $pattern = '/<template id="'.preg_quote($templateId, '/').'">(.*?)<\/template>/s';
        preg_match($pattern, $html, $matches);

        return $matches[1] ?? '';
    }

    protected function tearDown(): void
    {
        foreach ($this->testFiles as $filePath) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        parent::tearDown();
    }
}
