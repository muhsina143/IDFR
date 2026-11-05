<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\CategoryFormField;
use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicProfileFormSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
    }

    protected function makeUser(UserRole $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role->value,
            'name' => 'Test '.$role->label(),
            'email' => $role->value.'_'.uniqid().'@example.com',
            'phone' => '+91 98765 00000',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ], $attributes));
    }

    protected function makeAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'name' => 'Executive Administrator',
            'email' => 'admin_'.uniqid().'@idfr.example',
            'password' => bcrypt('AdminPassword123!'),
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    /**
     * 1. Test all 8 creative professional categories render their dynamic form with fields.
     */
    public function test_all_eight_categories_render_dynamic_form(): void
    {
        $categories = [
            ['role' => UserRole::MakeupArtist, 'route' => 'profile.makeup-artist', 'title' => 'Makeup Artist Details'],
            ['role' => UserRole::Hairstylist, 'route' => 'profile.hairstylist', 'title' => 'Hairstylist Details'],
            ['role' => UserRole::Photographer, 'route' => 'profile.photographer', 'title' => 'Photographer Details'],
            ['role' => UserRole::Videographer, 'route' => 'profile.videographer', 'title' => 'Videographer Details'],
            ['role' => UserRole::FashionDesigner, 'route' => 'profile.fashion-designer', 'title' => 'Fashion Designer Details'],
            ['role' => UserRole::DronePilot, 'route' => 'profile.drone-pilot', 'title' => 'Drone Pilot Details'],
            ['role' => UserRole::PhotoVideoEditor, 'route' => 'profile.photo-video-editor', 'title' => 'Photo & Video Editor Details'],
            ['role' => UserRole::JewelleryDesigner, 'route' => 'profile.jewellery-designer', 'title' => 'Jewellery Designer Details'],
        ];

        foreach ($categories as $cat) {
            $user = $this->makeUser($cat['role']);
            $res = $this->actingAs($user)->get(route($cat['route']));
            $res->assertOk();
            $res->assertSee($cat['title']);
            $res->assertSee('id="profileForm"', false);
            $res->assertSee('data-profile-kind="creative"', false);
            $res->assertSee('Save Profile');
        }
    }

    /**
     * 2. Test saving creative profile updates database and persists values on reopen.
     */
    public function test_creative_profile_saves_and_persists_data(): void
    {
        $user = $this->makeUser(UserRole::MakeupArtist, ['name' => 'Maya Artist']);

        $payload = [
            'name' => 'Maya Artist',
            'category' => 'makeup-artist',
            'place' => 'Kochi',
            'experience' => '6.5',
            'phone' => '+91 98450 11223',
            'email' => 'maya@idfr.test',
            'about' => 'High fashion runway makeup artist specializing in bridal and airbrush looks.',
            'kit' => 'MAC Pro, Temptu Airbrush, Kryolan HD Palette',
            'service' => ['bridal-makeup', 'editorial-makeup'],
            'outstation' => 'yes',
        ];

        $saveRes = $this->actingAs($user)->post(route('profile.makeup-artist.save'), $payload);
        $saveRes->assertSessionHas('success');

        $this->assertDatabaseHas('creative_professionals', [
            'user_id' => $user->id,
            'category' => 'makeup-artist',
            'name' => 'Maya Artist',
            'location' => 'Kochi',
            'experience' => '6.5',
            'equipment' => 'MAC Pro, Temptu Airbrush, Kryolan HD Palette',
        ]);

        $reopen = $this->actingAs($user)->get(route('profile.makeup-artist'));
        $reopen->assertOk();
        $reopen->assertSee('Maya Artist');
        $reopen->assertSee('MAC Pro, Temptu Airbrush, Kryolan HD Palette');
    }

    /**
     * 3. Test dual-table synchronization for Fashion Designers.
     */
    public function test_fashion_designer_syncs_to_both_tables(): void
    {
        $user = $this->makeUser(UserRole::FashionDesigner, ['name' => 'Elena Couturiere']);

        $payload = [
            'name' => 'Elena Couturiere',
            'category' => 'fashion-designer',
            'place' => 'Calicut',
            'experience' => '8',
            'phone' => '+91 98950 33445',
            'email' => 'elena@idfr.test',
            'about' => 'Bespoke handloom and silk couture bridal gowns with intricate zardozi craftsmanship.',
            'atelier' => 'Private atelier in Kozhikode with 6 master embroiderers.',
            'service' => ['custom-couture', 'bridal-and-occasion-wear'],
            'outstation' => 'yes',
        ];

        $saveRes = $this->actingAs($user)->post(route('profile.fashion-designer.save'), $payload);
        $saveRes->assertSessionHas('success');

        // Check creative_professionals table
        $this->assertDatabaseHas('creative_professionals', [
            'user_id' => $user->id,
            'category' => 'fashion-designer',
            'name' => 'Elena Couturiere',
            'location' => 'Calicut',
            'equipment' => 'Private atelier in Kozhikode with 6 master embroiderers.',
        ]);

        // Check designers table
        $this->assertDatabaseHas('designers', [
            'user_id' => $user->id,
            'name' => 'Elena Couturiere',
            'location' => 'Calicut',
            'atelier_notes' => 'Private atelier in Kozhikode with 6 master embroiderers.',
        ]);
    }

    /**
     * 4. Test dynamic custom fields expansion in JSON column.
     */
    public function test_custom_fields_are_saved_in_json_and_displayed(): void
    {
        // 1. Create a dynamic custom field
        $customField = ProfessionalFormField::create([
            'label' => 'Instagram Handle',
            'field_key' => 'instagram_handle',
            'field_type' => 'text',
            'placeholder' => '@yourinstagram',
            'width' => 'half',
            'display_order' => 65,
            'is_required' => false,
            'is_common' => false,
            'is_active' => true,
        ]);

        // Attach to photographer category
        $photoCat = ProfessionalCategory::where('slug', 'photographer')->first();
        CategoryFormField::create([
            'category_id' => $photoCat->id,
            'field_id' => $customField->id,
            'display_order' => 65,
            'is_active' => true,
        ]);

        $user = $this->makeUser(UserRole::Photographer, ['name' => 'Adithya Clicks']);

        // 2. Submit form including the custom dynamic field
        $payload = [
            'name' => 'Adithya Clicks',
            'category' => 'photographer',
            'place' => 'Bengaluru',
            'experience' => '5',
            'phone' => '+91 97400 55667',
            'email' => 'adithya@idfr.test',
            'about' => 'Editorial and runway fashion photographer with international magazine features.',
            'equipment' => 'Sony A1, Hasselblad X2D, Profoto B10X lights',
            'service' => ['fashion-editorial', 'model-portfolios'],
            'outstation' => 'yes',
            'instagram_handle' => '@adithyaclicks_official',
        ];

        $saveRes = $this->actingAs($user)->post(route('profile.photographer.save'), $payload);
        $saveRes->assertSessionHas('success');

        // Check custom_fields JSON in DB
        $profile = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertEquals('@adithyaclicks_official', $profile->custom_fields['instagram_handle'] ?? null);

        // Reopen form and check that custom field value is rendered
        $reopen = $this->actingAs($user)->get(route('profile.photographer'));
        $reopen->assertOk();
        $reopen->assertSee('@adithyaclicks_official');
    }

    /**
     * 5. Test Admin Dynamic Form Builder routes.
     */
    public function test_admin_can_manage_categories_and_fields(): void
    {
        $admin = $this->makeAdmin();

        // Admin index
        $indexRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Dynamic Form');
        $indexRes->assertSee('Makeup Artist');
        $indexRes->assertSee('Fashion Designer');

        // Admin category edit & update
        $cat = ProfessionalCategory::where('slug', 'makeup-artist')->first();
        $editRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.categories.edit', $cat));
        $editRes->assertOk();

        $updateRes = $this->actingAs($admin, 'admin')->put(route('admin.dynamic-forms.categories.update', $cat), [
            'name' => 'Bridal Makeup Artist',
            'hero_title' => 'Bridal <em>Makeup Artist</em>',
            'form_title' => 'Bridal Makeup Profile',
            'form_lead' => 'Updated lead instructions for bridal makeup artists.',
            'is_active' => '1',
        ]);
        $updateRes->assertRedirect(route('admin.dynamic-forms.index'));

        $cat->refresh();
        $this->assertEquals('Bridal Makeup Artist', $cat->name);

        // Toggle category status
        $toggleRes = $this->actingAs($admin, 'admin')->post(route('admin.dynamic-forms.categories.toggle-status', $cat));
        $toggleRes->assertRedirect();
        $cat->refresh();
        $this->assertFalse($cat->is_active);

        // Fields Library index
        $fieldsRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.fields.index'));
        $fieldsRes->assertOk();
        $fieldsRes->assertSee('Form Fields');

        // Create new field
        $createRes = $this->actingAs($admin, 'admin')->post(route('admin.dynamic-forms.fields.store'), [
            'label' => 'Portfolio Website',
            'field_key' => 'portfolio_website',
            'field_type' => 'text',
            'placeholder' => 'https://...',
            'width' => 'full',
            'display_order' => 95,
            'is_active' => '1',
            'categories' => [$cat->id],
        ]);
        $createRes->assertRedirect(route('admin.dynamic-forms.fields.index'));

        $this->assertDatabaseHas('professional_form_fields', [
            'field_key' => 'portfolio_website',
            'label' => 'Portfolio Website',
        ]);

        // Form live preview
        $previewRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.preview', 'makeup-artist'));
        $previewRes->assertOk();
        $previewRes->assertSee('Live Admin Preview');
    }

    /**
     * 6. Test Admin quick approval toggle for creative professionals.
     */
    public function test_admin_can_toggle_professional_approval_status(): void
    {
        $admin = $this->makeAdmin();

        $pro = CreativeProfessional::create([
            'name' => 'Aiswarya Hair Expert',
            'category' => 'hairstylist',
            'location' => 'Kochi',
            'status' => 'draft',
        ]);

        $this->assertEquals('draft', $pro->status);

        $res = $this->actingAs($admin, 'admin')->post(route('admin.professionals.approval-status', $pro), [
            'status' => 'published',
        ]);
        $res->assertRedirect();

        $pro->refresh();
        $this->assertEquals('published', $pro->status);
    }
}
