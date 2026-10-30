<?php

namespace Tests\Feature;

use App\Models\AboutStatistic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutStatisticsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $regularUser;

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

        $this->regularUser = User::firstOrCreate(
            ['email' => 'regular_test@idfr.example'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'role' => 'model',
            ]
        );
    }

    /**
     * Test guest cannot access admin about statistics.
     */
    public function test_guest_cannot_access_about_statistics_module(): void
    {
        $response = $this->get(route('admin.about-statistics.index'));
        $response->assertRedirect(route('admin.login'));
    }

    /**
     * Test regular user cannot access admin about statistics.
     */
    public function test_non_admin_cannot_access_about_statistics_module(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.about-statistics.index'));
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('error');
    }

    /**
     * Test administrator can view statistics list and table.
     */
    public function test_admin_can_view_about_statistics_list(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')->get(route('admin.about-statistics.index'));
        $response->assertOk();
        $response->assertSee('About Page');
        $response->assertSee('Statistics');
        $response->assertSee('Add New Statistic');
    }

    /**
     * Test public about page displays dynamic statistics from database.
     */
    public function test_about_page_displays_active_statistics_in_display_order(): void
    {
        // Ensure our 4 initial statistics exist
        AboutStatistic::truncate();

        AboutStatistic::create([
            'title' => 'Seasons',
            'value' => '04',
            'prefix' => null,
            'suffix' => null,
            'display_order' => 1,
            'status' => 'active',
            'is_active' => true,
        ]);

        AboutStatistic::create([
            'title' => 'Registered Models',
            'value' => '1200',
            'prefix' => null,
            'suffix' => '+',
            'display_order' => 2,
            'status' => 'active',
            'is_active' => true,
        ]);

        AboutStatistic::create([
            'title' => 'Creative Talents',
            'value' => '350',
            'prefix' => null,
            'suffix' => '+',
            'display_order' => 3,
            'status' => 'active',
            'is_active' => true,
        ]);

        AboutStatistic::create([
            'title' => 'Designers Showcased',
            'value' => '85',
            'prefix' => null,
            'suffix' => '+',
            'display_order' => 4,
            'status' => 'active',
            'is_active' => true,
        ]);

        $response = $this->get(route('idfr.about'));
        $response->assertOk();

        $response->assertSee('Seasons');
        $response->assertSee('data-pad="2"', false);
        $response->assertSee('data-count="4"', false);
        $response->assertSee('Registered Models');
        $response->assertSee('data-count="1200"', false);
        $response->assertSee('+');
        $response->assertSee('Creative Talents');
        $response->assertSee('data-count="350"', false);
        $response->assertSee('Designers Showcased');
        $response->assertSee('data-count="85"', false);
    }

    /**
     * Test adding a new statistic through the admin panel.
     */
    public function test_admin_can_create_new_statistic(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')->post(route('admin.about-statistics.store'), [
            'title' => 'Global Fashion Weeks',
            'value' => '12',
            'prefix' => '#',
            'suffix' => '+',
            'display_order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.about-statistics.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('about_statistics', [
            'title' => 'Global Fashion Weeks',
            'value' => '12',
            'prefix' => '#',
            'suffix' => '+',
            'display_order' => 5,
            'is_active' => true,
            'status' => 'active',
        ]);

        // Check public about page shows the new card
        $aboutResponse = $this->get(route('idfr.about'));
        $aboutResponse->assertSee('Global Fashion Weeks');
        $aboutResponse->assertSee('#');
        $aboutResponse->assertSee('data-count="12"', false);
    }

    /**
     * Test editing a statistic (e.g. 1200 to 1500).
     */
    public function test_admin_can_edit_statistic_value(): void
    {
        $stat = AboutStatistic::where('title', 'Registered Models')->first() ?? AboutStatistic::create([
            'title' => 'Registered Models',
            'value' => '1200',
            'suffix' => '+',
            'display_order' => 2,
            'is_active' => true,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->put(route('admin.about-statistics.update', $stat), [
            'title' => 'Registered Models',
            'value' => '1500',
            'prefix' => null,
            'suffix' => '+',
            'display_order' => 2,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.about-statistics.index'));
        $this->assertDatabaseHas('about_statistics', [
            'id' => $stat->id,
            'value' => '1500',
        ]);

        // Verify public about page now displays 1500
        $aboutResponse = $this->get(route('idfr.about'));
        $aboutResponse->assertSee('data-count="1500"', false);
    }

    /**
     * Test toggling active/inactive status hides it from About page.
     */
    public function test_deactivating_statistic_hides_it_from_about_page(): void
    {
        $stat = AboutStatistic::create([
            'title' => 'Temporary Statistic',
            'value' => '999',
            'display_order' => 10,
            'is_active' => true,
            'status' => 'active',
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->adminUser, 'admin')->post(route('admin.about-statistics.toggle-status', $stat));
        $response->assertRedirect();

        $stat->refresh();
        $this->assertFalse($stat->is_active);
        $this->assertEquals('inactive', $stat->status);

        // Verify public page does NOT show Temporary Statistic
        $aboutResponse = $this->get(route('idfr.about'));
        $aboutResponse->assertDontSee('Temporary Statistic');
    }

    /**
     * Test deleting a statistic removes it from database and About page.
     */
    public function test_deleting_statistic_removes_from_database_and_about_page(): void
    {
        $stat = AboutStatistic::create([
            'title' => 'Delete Me Stat',
            'value' => '77',
            'display_order' => 20,
            'is_active' => true,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')->delete(route('admin.about-statistics.destroy', $stat));
        $response->assertRedirect(route('admin.about-statistics.index'));

        $this->assertDatabaseMissing('about_statistics', [
            'id' => $stat->id,
        ]);

        $aboutResponse = $this->get(route('idfr.about'));
        $aboutResponse->assertDontSee('Delete Me Stat');
    }

    /**
     * Test reordering statistics.
     */
    public function test_admin_can_reorder_statistics(): void
    {
        $stat1 = AboutStatistic::create(['title' => 'First', 'value' => '1', 'display_order' => 1, 'is_active' => true, 'status' => 'active']);
        $stat2 = AboutStatistic::create(['title' => 'Second', 'value' => '2', 'display_order' => 2, 'is_active' => true, 'status' => 'active']);

        $response = $this->actingAs($this->adminUser, 'admin')->post(route('admin.about-statistics.reorder'), [
            'orders' => [
                ['id' => $stat1->id, 'display_order' => 20],
                ['id' => $stat2->id, 'display_order' => 10],
            ],
        ]);

        $response->assertRedirect();

        $this->assertEquals(20, $stat1->fresh()->display_order);
        $this->assertEquals(10, $stat2->fresh()->display_order);
    }
}
