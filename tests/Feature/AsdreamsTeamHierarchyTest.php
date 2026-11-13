<?php

namespace Tests\Feature;

use App\Models\Asdreams\AsdreamsTeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AsdreamsTeamHierarchyTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    /**
     * Track uploaded file paths during tests so teardown cleans them up cleanly.
     *
     * @var array<int, string>
     */
    private array $uploadedPaths = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_team_test@idfr.example'],
            [
                'name' => 'Team Test Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );
    }

    protected function tearDown(): void
    {
        foreach ($this->uploadedPaths as $relativePath) {
            if (str_starts_with($relativePath, 'uploads/asdreams/team/') && File::exists(public_path($relativePath))) {
                File::delete(public_path($relativePath));
            }
        }

        parent::tearDown();
    }

    public function test_team_member_defaults_to_team_member_and_scopes_work(): void
    {
        $founder = AsdreamsTeamMember::create([
            'name' => 'Athul S',
            'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
            'designation' => 'Founder & Creative Director',
            'bio' => 'Every frame is an heirloom.',
            'photo_path' => 'images/founders/tt6.jpg',
            'display_order' => 2,
            'is_active' => true,
        ]);

        $founderFirst = AsdreamsTeamMember::create([
            'name' => 'Aarav Nair',
            'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
            'designation' => 'Founder & Visual Architect',
            'photo_path' => 'images/founders/tt1.webp',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $coFounder = AsdreamsTeamMember::create([
            'name' => 'Meera Menon',
            'member_type' => AsdreamsTeamMember::TYPE_CO_FOUNDER,
            'designation' => 'Co-Founder & Producer',
            'photo_path' => 'images/founders/tt3.webp',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $defaultCrew = AsdreamsTeamMember::create([
            'name' => 'Rohan Das',
            'designation' => 'Lead Retoucher',
            'photo_path' => 'images/founders/tt5.webp',
            'display_order' => 3,
        ]);

        $inactiveCrew = AsdreamsTeamMember::create([
            'name' => 'Hidden Artist',
            'member_type' => AsdreamsTeamMember::TYPE_TEAM_MEMBER,
            'designation' => 'Stylist',
            'photo_path' => 'images/founders/tt6.jpg',
            'display_order' => 1,
            'is_active' => false,
        ]);

        $this->assertSame(AsdreamsTeamMember::TYPE_TEAM_MEMBER, $defaultCrew->member_type);
        $this->assertTrue($defaultCrew->is_active);
        $this->assertTrue($defaultCrew->is_published);
        $this->assertSame(3, $defaultCrew->order);

        $founders = AsdreamsTeamMember::active()->founders()->ordered()->pluck('id')->all();
        $coFounders = AsdreamsTeamMember::active()->coFounders()->ordered()->pluck('id')->all();
        $teamMembers = AsdreamsTeamMember::active()->teamMembers()->ordered()->pluck('id')->all();

        $this->assertSame([$founderFirst->id, $founder->id], $founders);
        $this->assertSame([$coFounder->id], $coFounders);
        $this->assertSame([$defaultCrew->id], $teamMembers);
        $this->assertNotContains($inactiveCrew->id, $teamMembers);
    }

    public function test_admin_can_create_edit_filter_reorder_toggle_and_delete_all_member_types(): void
    {
        // Reject invalid member_type
        $invalid = $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.team.store'), [
                'member_type' => 'intern',
                'name' => 'Invalid Member',
                'designation' => 'Guest',
            ]);
        $invalid->assertSessionHasErrors('member_type');

        // Create Founder, Co-Founder, and Team Member
        $founderPhoto = UploadedFile::fake()->image('founder.jpg', 600, 800);
        $coFounderPhoto = UploadedFile::fake()->image('cofounder.jpg', 600, 800);
        $crewPhoto = UploadedFile::fake()->image('crew.jpg', 600, 800);

        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.team.store'), [
                'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
                'name' => 'Vikramaditya Rao',
                'designation' => 'Founder & Chief Director',
                'bio' => 'Crafting timeless light.',
                'photo' => $founderPhoto,
                'instagram' => 'https://instagram.com/vikram',
                'display_order' => 2,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.asdreams.team.index'))
            ->assertSessionHas('success');

        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.team.store'), [
                'member_type' => AsdreamsTeamMember::TYPE_CO_FOUNDER,
                'name' => 'Ananya Krishnan',
                'designation' => 'Co-Founder & Executive Producer',
                'bio' => 'Storytelling through luxury editorial direction.',
                'photo' => $coFounderPhoto,
                'display_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.asdreams.team.index'));

        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.team.store'), [
                'member_type' => AsdreamsTeamMember::TYPE_TEAM_MEMBER,
                'name' => 'Kabir Menon',
                'designation' => 'Senior Cinematographer',
                'photo' => $crewPhoto,
                'display_order' => 3,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.asdreams.team.index'));

        $founder = AsdreamsTeamMember::where('name', 'Vikramaditya Rao')->firstOrFail();
        $coFounder = AsdreamsTeamMember::where('name', 'Ananya Krishnan')->firstOrFail();
        $crew = AsdreamsTeamMember::where('name', 'Kabir Menon')->firstOrFail();

        $this->uploadedPaths[] = $founder->photo_path;
        $this->uploadedPaths[] = $coFounder->photo_path;
        $this->uploadedPaths[] = $crew->photo_path;

        $this->assertFileExists(public_path($founder->photo_path));
        $this->assertSame('Founder', $founder->member_type_label);
        $this->assertSame('https://instagram.com/vikram', $founder->instagram);

        // Index shows tabs, counts, and type badges
        $allView = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.team.index'));
        $allView->assertStatus(200);
        $allView->assertSee('All (3)', false);
        $allView->assertSee('Founders (1)', false);
        $allView->assertSee('Co-Founders (1)', false);
        $allView->assertSee('Team Members (1)', false);
        $allView->assertSee('Vikramaditya Rao', false);
        $allView->assertSee('Ananya Krishnan', false);
        $allView->assertSee('Kabir Menon', false);

        // Filter tabs isolate each group cleanly
        $foundersTab = $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.team.index', ['type' => AsdreamsTeamMember::TYPE_FOUNDER]));
        $foundersTab->assertStatus(200);
        $foundersTab->assertSee('Vikramaditya Rao', false);
        $foundersTab->assertDontSee('Ananya Krishnan', false);
        $foundersTab->assertDontSee('Kabir Menon', false);

        // Edit page loads with both $team and $teamMember and updates cleanly
        $this->actingAs($this->adminUser, 'admin')
            ->get(route('admin.asdreams.team.edit', $founder))
            ->assertStatus(200)
            ->assertSee('Vikramaditya Rao', false);

        $replacementPhoto = UploadedFile::fake()->image('founder_v2.jpg', 600, 800);
        $oldFounderPhoto = $founder->photo_path;

        $this->actingAs($this->adminUser, 'admin')
            ->put(route('admin.asdreams.team.update', $founder), [
                'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
                'name' => 'Vikramaditya S. Rao',
                'designation' => 'Founder & Studio Principal',
                'bio' => 'Updated vision quote.',
                'photo' => $replacementPhoto,
                'display_order' => 1,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.asdreams.team.index'));

        $founder->refresh();
        $this->uploadedPaths[] = $founder->photo_path;
        $this->assertSame('Vikramaditya S. Rao', $founder->name);
        $this->assertSame('Updated vision quote.', $founder->bio);
        $this->assertFileDoesNotExist(public_path($oldFounderPhoto));
        $this->assertFileExists(public_path($founder->photo_path));

        // Drag-and-drop reorder persists display_order
        $reorderResponse = $this->actingAs($this->adminUser, 'admin')
            ->postJson(route('admin.asdreams.team.reorder'), [
                'order' => [$crew->id, $coFounder->id, $founder->id],
                'offset' => 0,
            ]);
        $reorderResponse->assertStatus(200)->assertJson(['success' => true]);

        $this->assertSame(1, $crew->fresh()->display_order);
        $this->assertSame(2, $coFounder->fresh()->display_order);
        $this->assertSame(3, $founder->fresh()->display_order);

        // Status toggle hides and restores member
        $this->actingAs($this->adminUser, 'admin')
            ->post(route('admin.asdreams.team.toggle-status', $coFounder))
            ->assertRedirect();
        $this->assertFalse($coFounder->fresh()->is_active);

        $this->actingAs($this->adminUser, 'admin')
            ->patch(route('admin.asdreams.team.toggle-status', $coFounder))
            ->assertRedirect();
        $this->assertTrue($coFounder->fresh()->is_active);

        // Delete removes DB record and uploaded portrait
        $crewPath = $crew->photo_path;
        $this->actingAs($this->adminUser, 'admin')
            ->delete(route('admin.asdreams.team.destroy', $crew))
            ->assertRedirect(route('admin.asdreams.team.index'));
        $this->assertDatabaseMissing('asdreams_team_members', ['id' => $crew->id]);
        $this->assertFileDoesNotExist(public_path($crewPath));
    }

    public function test_public_team_page_renders_three_separated_sections_and_hides_empty_sections(): void
    {
        // All empty -> no Founders/Co-Founders/Creative Team headings rendered
        $emptyResponse = $this->get(route('asd.team'));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('Team <em>Members</em>', false);
        $emptyResponse->assertDontSee('THE VISIONARIES', false);
        $emptyResponse->assertDontSee('Our <em>Founders</em>', false);
        $emptyResponse->assertDontSee('Our <em>Co-Founders</em>', false);
        $emptyResponse->assertSee('No Team Members Yet', false);

        // Only active records in all three groups
        AsdreamsTeamMember::create([
            'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
            'name' => 'Athul StudioFounder',
            'designation' => 'Founder & Creative Director',
            'bio' => 'Vision above trends.',
            'photo_path' => 'images/founders/tt6.jpg',
            'display_order' => 2,
            'is_active' => true,
        ]);

        AsdreamsTeamMember::create([
            'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
            'name' => 'First Order Founder',
            'designation' => 'Principal Founder',
            'photo_path' => 'images/founders/tt1.webp',
            'display_order' => 1,
            'is_active' => true,
        ]);

        AsdreamsTeamMember::create([
            'member_type' => AsdreamsTeamMember::TYPE_FOUNDER,
            'name' => 'Hidden Founder',
            'designation' => 'Founder',
            'photo_path' => 'images/founders/tt1.webp',
            'display_order' => 0,
            'is_active' => false,
        ]);

        AsdreamsTeamMember::create([
            'member_type' => AsdreamsTeamMember::TYPE_CO_FOUNDER,
            'name' => 'Maya CoFounder',
            'designation' => 'Co-Founder & Art Lead',
            'bio' => 'Composition in light and shadow.',
            'photo_path' => 'images/founders/tt3.webp',
            'display_order' => 1,
            'is_active' => true,
        ]);

        AsdreamsTeamMember::create([
            'member_type' => AsdreamsTeamMember::TYPE_TEAM_MEMBER,
            'name' => 'Arjun Crew',
            'designation' => 'Lead Photographer',
            'photo_path' => 'images/founders/tt5.webp',
            'display_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('asd.team'));
        $response->assertStatus(200);
        $response->assertViewHasAll(['founders', 'coFounders', 'teamMembers']);

        $response->assertSee('Team <em>Members</em>', false);
        $response->assertSee('THE VISIONARIES', false);
        $response->assertSee('Our <em>Founders</em>', false);
        $response->assertSee('Our <em>Co-Founders</em>', false);
        $response->assertSee('Creative <em>Team</em>', false);

        $response->assertSeeInOrder(['First Order Founder', 'Athul StudioFounder']);
        $response->assertSee('Maya CoFounder', false);
        $response->assertSee('Arjun Crew', false);
        $response->assertDontSee('Hidden Founder', false);

        // When Co-Founders are all deactivated, the Co-Founders section disappears completely
        AsdreamsTeamMember::where('member_type', AsdreamsTeamMember::TYPE_CO_FOUNDER)->update(['is_active' => false]);

        $partialResponse = $this->get(route('asd.team'));
        $partialResponse->assertStatus(200);
        $partialResponse->assertSee('Our <em>Founders</em>', false);
        $partialResponse->assertDontSee('Our <em>Co-Founders</em>', false);
        $partialResponse->assertDontSee('Maya CoFounder', false);
    }
}
