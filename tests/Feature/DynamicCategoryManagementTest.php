<?php

namespace Tests\Feature;

use App\Models\CategoryFormField;
use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DynamicCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DynamicProfileFormSeeder::class);
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

    protected function makeUser(string $role = 'fashion-stylist', array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'name' => 'Aria Thorne',
            'email' => $role.'_'.uniqid().'@example.com',
            'phone' => '+91 98765 43210',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ], $attributes));
    }

    /**
     * 1. Test Admin can create a new category and standard fields are automatically attached.
     */
    public function test_admin_can_create_new_category_and_standard_fields_are_auto_attached(): void
    {
        $admin = $this->makeAdmin();

        $createRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.categories.create'));
        $createRes->assertOk();
        $createRes->assertSee('Add New');
        $createRes->assertSee('Category Name');

        $payload = [
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion <em>Stylist</em>',
            'description' => 'Curating iconic runway looks and celebrity wardrobe direction.',
            'form_title' => 'Fashion Stylist Details',
            'form_lead' => 'Fill in your wardrobe styling experience and celebrity portfolio.',
            'display_order' => 90,
            'is_active' => 1,
        ];

        $postRes = $this->actingAs($admin, 'admin')->post(route('admin.dynamic-forms.categories.store'), $payload);
        $postRes->assertRedirect(route('admin.dynamic-forms.index'));
        $postRes->assertSessionHas('success');

        $category = ProfessionalCategory::where('slug', 'fashion-stylist')->first();
        $this->assertNotNull($category);
        $this->assertSame('Fashion Stylist', $category->name);
        $this->assertSame('Fashion <em>Stylist</em>', $category->hero_title);
        $this->assertTrue($category->is_active);

        // Verify standard common fields are attached automatically
        $assignedFieldKeys = $category->fields()->pluck('field_key')->toArray();
        $this->assertContains('name', $assignedFieldKeys);
        $this->assertContains('profession', $assignedFieldKeys);
        $this->assertContains('place', $assignedFieldKeys);
        $this->assertContains('experience', $assignedFieldKeys);
        $this->assertContains('phone', $assignedFieldKeys);
        $this->assertContains('email', $assignedFieldKeys);
        $this->assertContains('about', $assignedFieldKeys);
        $this->assertContains('outstation', $assignedFieldKeys);
    }

    /**
     * 2. Test new category button appears on /professionals and in header navigation.
     */
    public function test_new_category_appears_in_professionals_page_and_header_navigation(): void
    {
        ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion <em>Stylist</em>',
            'description' => 'Curating iconic runway looks.',
            'form_title' => 'Fashion Stylist Details',
            'form_lead' => 'Styling details.',
            'display_order' => 90,
            'is_active' => true,
        ]);

        $res = $this->get(route('idfr.professionals'));
        $res->assertOk();
        $res->assertSee('Fashion Stylist');
        $res->assertSee('data-category="fashion-stylist"', false);
        $res->assertSee('id="creative-fashion-stylist"', false);

        // Header navigation check
        $res->assertSee('#creative-fashion-stylist');
    }

    /**
     * 3. Test dedicated category listing page loads with hero, description, and empty state.
     */
    public function test_dedicated_category_listing_page_loads_with_hero_and_description(): void
    {
        $category = ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion <em>Stylist</em>',
            'description' => 'Curating iconic runway looks and celebrity wardrobe direction.',
            'form_title' => 'Fashion Stylist Details',
            'form_lead' => 'Styling details.',
            'display_order' => 90,
            'is_active' => true,
        ]);

        $res = $this->get(route('idfr.professionals.category', 'fashion-stylist'));
        $res->assertOk();
        $res->assertSee('Fashion');
        $res->assertSee('Stylist');
        $res->assertSee('Curating iconic runway looks and celebrity wardrobe direction.');
        $res->assertSee('data-role="fashion-stylist"', false);
    }

    /**
     * 4. Test Admin can configure custom fields for the new category.
     */
    public function test_admin_can_add_custom_field_to_new_category(): void
    {
        $admin = $this->makeAdmin();

        $category = ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion Stylist',
            'form_title' => 'Fashion Stylist Details',
            'is_active' => true,
        ]);

        // Create new custom field
        $field = ProfessionalFormField::create([
            'label' => 'Wardrobe Kit & Styling Equipment',
            'field_key' => 'styling_kit',
            'field_type' => 'textarea',
            'placeholder' => 'Steamer, safety pins, seam rippers, runway kit...',
            'width' => 'full',
            'display_order' => 75,
            'is_required' => false,
            'is_common' => false,
            'is_active' => true,
        ]);

        // Attach field to category
        $attachRes = $this->actingAs($admin, 'admin')->post(route('admin.dynamic-forms.categories.fields.attach', $category), [
            'field_id' => $field->id,
            'display_order' => 75,
            'is_required' => 0,
        ]);
        $attachRes->assertRedirect();

        $this->assertDatabaseHas('category_form_fields', [
            'category_id' => $category->id,
            'field_id' => $field->id,
        ]);

        $activeFields = $category->getActiveFormFields();
        $this->assertTrue($activeFields->contains('field_key', 'styling_kit'));
    }

    /**
     * 5. Test user can register under new dynamic category role.
     */
    public function test_user_can_register_under_dynamic_category_role(): void
    {
        ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion Stylist',
            'form_title' => 'Fashion Stylist Details',
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Natasha Romanoff',
            'email' => 'natasha@stylist.test',
            'phone' => '+91 99988 77766',
            'role' => 'fashion-stylist',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'terms' => 'on',
        ];

        $res = $this->postJson(route('register'), $payload);
        $res->assertCreated();
        $res->assertJsonPath('success', true);

        $user = User::where('email', 'natasha@stylist.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('fashion-stylist', $user->role);
        $this->assertSame('Fashion Stylist', $user->roleLabel());
        $this->assertStringContainsString('/profiles/fashion-stylist', $user->profileUrl());
    }

    /**
     * 6. Test user can view and submit dynamic profile with custom fields.
     */
    public function test_user_can_view_and_submit_dynamic_profile_form(): void
    {
        $category = ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion <em>Stylist</em>',
            'form_title' => 'Fashion Stylist Details',
            'form_lead' => 'Fill in your wardrobe styling experience.',
            'is_active' => true,
        ]);

        // Attach core fields
        $commonFields = ProfessionalFormField::where('is_common', true)->get();
        foreach ($commonFields as $cf) {
            CategoryFormField::firstOrCreate([
                'category_id' => $category->id,
                'field_id' => $cf->id,
            ], [
                'display_order' => $cf->display_order,
                'is_active' => true,
            ]);
        }

        // Attach custom field
        $specialityField = ProfessionalFormField::create([
            'label' => 'Styling Speciality',
            'field_key' => 'styling_speciality',
            'field_type' => 'text',
            'placeholder' => 'e.g. High Fashion & Runway',
            'width' => 'half',
            'display_order' => 65,
            'is_required' => false,
            'is_common' => false,
            'is_active' => true,
        ]);

        CategoryFormField::create([
            'category_id' => $category->id,
            'field_id' => $specialityField->id,
            'display_order' => 65,
            'is_active' => true,
        ]);

        $user = $this->makeUser('fashion-stylist', [
            'name' => 'Maya Sen',
            'email' => 'maya.sen@stylist.test',
        ]);

        // View form
        $viewRes = $this->actingAs($user)->get(route('profile.category', 'fashion-stylist'));
        $viewRes->assertOk();
        $viewRes->assertSee('Fashion Stylist Details');
        $viewRes->assertSee('Styling Speciality');

        // Submit form
        $submitPayload = [
            'name' => 'Maya Sen',
            'profession' => 'Lead Fashion Stylist',
            'place' => 'Kochi',
            'experience' => 6,
            'phone' => '+91 99988 77766',
            'email' => 'maya.sen@stylist.test',
            'about' => 'Celebrity wardrobe stylist with 6 years experience in luxury couture and editorial fashion.',
            'service' => ['Wardrobe Styling'],
            'outstation' => 'yes',
            'styling_speciality' => 'High Fashion & Runway Editorial',
        ];

        $postRes = $this->actingAs($user)->post(route('profile.category.save', 'fashion-stylist'), $submitPayload);
        $postRes->assertRedirect();
        $postRes->assertSessionHas('success');

        $profile = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertSame('fashion-stylist', $profile->category);
        $this->assertSame('Maya Sen', $profile->name);
        $this->assertSame('Kochi', $profile->location);
        $this->assertSame('High Fashion & Runway Editorial', $profile->custom_fields['styling_speciality'] ?? null);
    }

    /**
     * 7. Test approved profile appears in category listing and main professionals page.
     */
    public function test_approved_profile_appears_in_category_listing_and_professionals_page(): void
    {
        $category = ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion <em>Stylist</em>',
            'form_title' => 'Fashion Stylist Details',
            'is_active' => true,
        ]);

        $pro = CreativeProfessional::create([
            'name' => 'Devika Roy',
            'slug' => 'devika-roy-stylist',
            'category' => 'fashion-stylist',
            'professional_title' => 'Celebrity Stylist',
            'brand' => 'IDFR',
            'location' => 'Mumbai',
            'experience' => '8',
            'biography' => 'International runway and bridal stylist.',
            'services' => ['Wardrobe Styling', 'Runway Direction'],
            'outstation' => 'yes',
            'status' => 'published',
            'display_order' => 1,
        ]);

        // Category listing page
        $catListingRes = $this->get(route('idfr.professionals.category', 'fashion-stylist'));
        $catListingRes->assertOk();
        $catListingRes->assertSee('Devika Roy');
        $catListingRes->assertSee('Wardrobe Styling');
        $catListingRes->assertSee('Mumbai');

        // Main creative professionals page
        $prosPageRes = $this->get(route('idfr.professionals'));
        $prosPageRes->assertOk();
        $prosPageRes->assertSee('Devika Roy');
        $prosPageRes->assertSee('data-category="fashion-stylist"', false);
    }

    /**
     * 8. Test Admin can update category and upload banner and thumbnail images.
     */
    public function test_admin_can_update_category_and_upload_images(): void
    {
        $admin = $this->makeAdmin();

        $category = ProfessionalCategory::create([
            'name' => 'Fashion Stylist',
            'slug' => 'fashion-stylist',
            'hero_title' => 'Fashion Stylist',
            'form_title' => 'Fashion Stylist Details',
            'is_active' => true,
        ]);

        $fakeThumb = UploadedFile::fake()->image('stylist_thumb.jpg', 600, 600);
        $fakeBanner = UploadedFile::fake()->image('stylist_banner.jpg', 1200, 400);

        $updateRes = $this->actingAs($admin, 'admin')->put(route('admin.dynamic-forms.categories.update', $category), [
            'name' => 'Master Fashion Stylist',
            'hero_title' => 'Master Fashion <em>Stylist</em>',
            'description' => 'Updated elite styling description.',
            'form_title' => 'Master Stylist Application',
            'form_lead' => 'Submit your credentials.',
            'display_order' => 15,
            'is_active' => 1,
            'default_photo' => $fakeThumb,
            'banner_image' => $fakeBanner,
        ]);

        $updateRes->assertRedirect(route('admin.dynamic-forms.index'));
        $updateRes->assertSessionHas('success');

        $fresh = $category->fresh();
        $this->assertSame('Master Fashion Stylist', $fresh->name);
        $this->assertStringContainsString('uploads/categories/cat_thumb_', $fresh->default_photo);
        $this->assertStringContainsString('uploads/categories/cat_banner_', $fresh->banner_image);
    }

    /**
     * 9. Test safe deletion protection rules.
     */
    public function test_category_safe_deletion_rules(): void
    {
        $admin = $this->makeAdmin();

        // 9a: Cannot delete core category (e.g. makeup-artist)
        $coreCat = ProfessionalCategory::where('slug', 'makeup-artist')->firstOrFail();
        $delCoreRes = $this->actingAs($admin, 'admin')->delete(route('admin.dynamic-forms.categories.destroy', $coreCat));
        $delCoreRes->assertRedirect();
        $delCoreRes->assertSessionHas('error');
        $this->assertDatabaseHas('professional_categories', ['slug' => 'makeup-artist']);

        // 9b: Cannot delete custom category with existing profiles
        $customCat = ProfessionalCategory::create([
            'name' => 'Temporary Cat',
            'slug' => 'temp-cat',
            'hero_title' => 'Temp',
            'form_title' => 'Temp Details',
            'is_active' => true,
        ]);

        CreativeProfessional::create([
            'name' => 'John Doe',
            'slug' => 'john-doe-temp',
            'category' => 'temp-cat',
            'location' => 'Kochi',
            'status' => 'published',
        ]);

        $delWithProfileRes = $this->actingAs($admin, 'admin')->delete(route('admin.dynamic-forms.categories.destroy', $customCat));
        $delWithProfileRes->assertRedirect();
        $delWithProfileRes->assertSessionHas('error');
        $this->assertDatabaseHas('professional_categories', ['slug' => 'temp-cat']);

        // 9c: Can delete custom category with zero profiles
        CreativeProfessional::where('category', 'temp-cat')->delete();

        $safeDelRes = $this->actingAs($admin, 'admin')->delete(route('admin.dynamic-forms.categories.destroy', $customCat));
        $safeDelRes->assertRedirect(route('admin.dynamic-forms.index'));
        $safeDelRes->assertSessionHas('success');
        $this->assertDatabaseMissing('professional_categories', ['slug' => 'temp-cat']);
    }

    /**
     * 10. Test that all 8 original categories remain fully operational.
     */
    public function test_all_eight_original_categories_remain_operational(): void
    {
        $expectedCoreSlugs = [
            'makeup-artist',
            'hairstylist',
            'photographer',
            'videographer',
            'fashion-designer',
            'drone-pilot',
            'photo-video-editor',
            'jewellery-designer',
        ];

        foreach ($expectedCoreSlugs as $slug) {
            $cat = ProfessionalCategory::where('slug', $slug)->first();
            $this->assertNotNull($cat, "Core category {$slug} must exist in database.");
            $this->assertTrue($cat->is_active);

            // Category dedicated page loads
            $catPageRes = $this->get(route('idfr.professionals.category', $slug));
            $catPageRes->assertOk();

            // Registration role option available
            $this->assertContains($slug, ProfessionalCategory::where('is_active', true)->pluck('slug')->toArray());
        }
    }
}
