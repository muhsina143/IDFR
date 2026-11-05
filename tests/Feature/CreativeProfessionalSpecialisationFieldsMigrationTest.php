<?php

namespace Tests\Feature;

use App\Models\CategoryFormField;
use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\ProfessionalFormField;
use App\Models\User;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CreativeProfessionalSpecialisationFieldsMigrationTest extends TestCase
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
            'name' => 'Managing Director',
            'email' => 'admin_'.uniqid().'@idfr.example',
            'password' => bcrypt('AdminPassword123!'),
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function makeUser(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'name' => 'Professional '.ucfirst($role),
            'email' => str_replace('-', '_', $role).'_'.uniqid().'@example.com',
            'phone' => '+91 98765 43210',
            'email_verified_at' => now(),
            'payment_status' => 'paid',
        ], $attributes));
    }

    /**
     * 1. Test that all 9 professions have their dedicated specialisation fields in the form fields library.
     */
    public function test_all_nine_professions_have_dedicated_specialisation_fields_with_options(): void
    {
        $expected = [
            'service_makeup_artist' => [
                'label' => 'Makeup Styles & Specialisations',
                'category' => 'makeup-artist',
                'sample_option' => 'bridal-makeup',
            ],
            'service_hairstylist' => [
                'label' => 'Hairstyling Specialisations',
                'category' => 'hairstylist',
                'sample_option' => 'bridal-hairstyling',
            ],
            'service_photographer' => [
                'label' => 'Photography Specialisations',
                'category' => 'photographer',
                'sample_option' => 'wedding-photography',
            ],
            'service_videographer' => [
                'label' => 'Videography Specialisations',
                'category' => 'videographer',
                'sample_option' => 'cinematic-wedding-films',
            ],
            'service_fashion_designer' => [
                'label' => 'Design Focus & Specialisations',
                'category' => 'fashion-designer',
                'sample_option' => 'bridal-and-occasion-wear',
            ],
            'service_drone_pilot' => [
                'label' => 'Drone Operation Specialisations',
                'category' => 'drone-pilot',
                'sample_option' => 'aerial-wedding-films',
            ],
            'service_photo_video_editor' => [
                'label' => 'Editing Specialisations',
                'category' => 'photo-video-editor',
                'sample_option' => 'wedding-film-editing',
            ],
            'service_jewellery_designer' => [
                'label' => 'Jewellery Design Specialisations',
                'category' => 'jewellery-designer',
                'sample_option' => 'bridal-jewellery-sets',
            ],
            'service_party_event_management' => [
                'label' => 'Event Management Services & Specialisations',
                'category' => 'party-event-management',
                'sample_option' => 'fashion-show-production',
            ],
        ];

        foreach ($expected as $fieldKey => $info) {
            $field = ProfessionalFormField::where('field_key', $fieldKey)->first();
            $this->assertNotNull($field, "Field {$fieldKey} should exist in professional_form_fields library");
            $this->assertSame($info['label'], $field->label);
            $this->assertSame('checkbox_group', $field->field_type);
            $this->assertTrue($field->is_required);
            $this->assertIsArray($field->options);
            $this->assertCount(8, $field->options, "Field {$fieldKey} must have 8 choices");
            $this->assertArrayHasKey($info['sample_option'], $field->options);

            // Verify attached to the correct category
            $category = ProfessionalCategory::where('slug', $info['category'])->first();
            $this->assertNotNull($category, "Category {$info['category']} should exist");
            $assignedKeys = $category->fields()->pluck('field_key')->toArray();
            $this->assertContains($fieldKey, $assignedKeys, "Category {$info['category']} must have {$fieldKey} attached");
        }
    }

    /**
     * 2. Test Admin can view the specialisation fields in the Fields Library and filter by category.
     */
    public function test_admin_can_view_fields_library_and_filter_by_category(): void
    {
        $admin = $this->makeAdmin();

        $res = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.fields.index'));
        $res->assertOk();
        $res->assertSee('Form Fields');
        $res->assertSee('Makeup Styles &amp; Specialisations', false);
        $res->assertSee('service_makeup_artist');
        $res->assertSee('8 choices');

        // Test filtering by category: makeup-artist
        $filterRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.fields.index', ['category' => 'makeup-artist']));
        $filterRes->assertOk();
        $filterRes->assertSee('service_makeup_artist');
        $filterRes->assertSee('kit');
    }

    /**
     * 3. Test Admin can edit options of a specialisation field in Fields Library and changes reflect on website forms.
     */
    public function test_admin_can_edit_options_in_fields_library_and_reflects_on_form(): void
    {
        $admin = $this->makeAdmin();
        $field = ProfessionalFormField::where('field_key', 'service_makeup_artist')->firstOrFail();

        $editRes = $this->actingAs($admin, 'admin')->get(route('admin.dynamic-forms.fields.edit', $field));
        $editRes->assertOk();
        $editRes->assertSee('Makeup Styles & Specialisations');
        $editRes->assertSee('bridal-makeup: Bridal makeup');

        // Admin updates options by adding a new option: prosthetic-sfx
        $newOptionsText = "bridal-makeup: Bridal makeup\nengagement-makeup: Engagement makeup\nprosthetic-sfx: Prosthetic & SFX Makeup";
        $updatePayload = [
            'label' => 'Makeup Styles & Specialisations (Updated)',
            'field_type' => 'checkbox_group',
            'width' => 'full',
            'display_order' => 80,
            'is_required' => 1,
            'is_common' => 0,
            'is_active' => 1,
            'options' => $newOptionsText,
            'categories' => [$field->categories->first()->id],
        ];

        $putRes = $this->actingAs($admin, 'admin')->put(route('admin.dynamic-forms.fields.update', $field), $updatePayload);
        $putRes->assertRedirect(route('admin.dynamic-forms.fields.index'));

        $field->refresh();
        $this->assertSame('Makeup Styles & Specialisations (Updated)', $field->label);
        $this->assertArrayHasKey('prosthetic-sfx', $field->options);
        $this->assertSame('Prosthetic & SFX Makeup', $field->options['prosthetic-sfx']);

        // Now view website profile form as makeup artist user
        $user = $this->makeUser('makeup-artist');
        $formRes = $this->actingAs($user, 'web')->get(route('profile.makeup-artist'));
        $formRes->assertOk();
        $formRes->assertSee('Makeup Styles & Specialisations (Updated)');
        $formRes->assertSee('Prosthetic &amp; SFX Makeup', false);
    }

    /**
     * 4. Test user on /profiles/makeup-artist sees checkboxes, validation blocks empty submit, and successful submit saves to DB.
     */
    public function test_makeup_artist_profile_form_renders_checkboxes_and_saves_services(): void
    {
        $user = $this->makeUser('makeup-artist');

        // 1. Initial view: options are visible
        $getRes = $this->actingAs($user)->get(route('profile.makeup-artist'));
        $getRes->assertOk();
        $getRes->assertSee('Makeup Styles &amp; Specialisations', false);
        $getRes->assertSee('Bridal makeup');
        $getRes->assertSee('HD / airbrush makeup');

        // 2. Submit without service selection fails validation
        $emptyPayload = [
            'name' => 'Ananya Menon',
            'profession' => 'Makeup Artist',
            'place' => 'Kochi',
            'experience' => 5,
            'phone' => '+91 99988 77766',
            'email' => $user->email,
            'about' => 'Celebrity and bridal makeup artist based in Kochi with luxury experience.',
            'outstation' => 'yes',
            // service omitted
        ];
        $emptyRes = $this->actingAs($user)->post(route('profile.makeup-artist.save'), $emptyPayload);
        $emptyRes->assertSessionHasErrors('service');

        // 3. Submit with valid services succeeds
        $validPayload = array_merge($emptyPayload, [
            'service' => ['bridal-makeup', 'hd-airbrush-makeup'],
            'kit' => 'MAC, Kryolan, Huda Beauty, Temptu Pro Airbrush',
        ]);
        $postRes = $this->actingAs($user)->post(route('profile.makeup-artist.save'), $validPayload);
        $postRes->assertRedirect();
        $postRes->assertSessionHasNoErrors();

        // 4. Check database record
        $profile = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertSame('Ananya Menon', $profile->name);
        $this->assertSame('makeup-artist', $profile->category);
        $this->assertEquals(['bridal-makeup', 'hd-airbrush-makeup'], $profile->services);
        $this->assertSame('MAC, Kryolan, Huda Beauty, Temptu Pro Airbrush', $profile->equipment);

        // 5. Subsequent visit has selected checkboxes checked
        $viewRes = $this->actingAs($user)->get(route('profile.makeup-artist'));
        $viewRes->assertOk();
        $viewRes->assertSee('value="bridal-makeup"', false);
        $viewRes->assertSee('checked', false);
    }

    /**
     * 5. Test another category (e.g. Hairstylist and Photographer) also works dynamically.
     */
    public function test_hairstylist_and_photographer_forms_work_with_migrated_dynamic_fields(): void
    {
        // Hairstylist
        $hairUser = $this->makeUser('hairstylist');
        $hairRes = $this->actingAs($hairUser)->get(route('profile.hairstylist'));
        $hairRes->assertOk();
        $hairRes->assertSee('Hairstyling Specialisations');
        $hairRes->assertSee('Bridal hairstyling');
        $hairRes->assertSee('Quick-change backstage looks');

        $hairPayload = [
            'name' => 'Aiswarya Mohan',
            'profession' => 'Hairstylist',
            'place' => 'Calicut',
            'experience' => 4,
            'phone' => '+91 98877 66554',
            'email' => $hairUser->email,
            'about' => 'Creative hairstylist specializing in South Indian bridal updos and runway models.',
            'outstation' => 'no',
            'service' => ['bridal-hairstyling', 'hair-updos'],
            'kit' => 'Dyson Supersonic, Schwarzkopf, Ikonic Pro',
        ];
        $postHair = $this->actingAs($hairUser)->post(route('profile.hairstylist.save'), $hairPayload);
        $postHair->assertSessionHasNoErrors();

        $hairProfile = CreativeProfessional::where('user_id', $hairUser->id)->first();
        $this->assertNotNull($hairProfile);
        $this->assertEquals(['bridal-hairstyling', 'hair-updos'], $hairProfile->services);

        // Photographer
        $photoUser = $this->makeUser('photographer');
        $photoRes = $this->actingAs($photoUser)->get(route('profile.photographer'));
        $photoRes->assertOk();
        $photoRes->assertSee('Photography Specialisations');
        $photoRes->assertSee('Fashion &amp; editorial', false);

        $photoPayload = [
            'name' => 'Adithya Raj',
            'profession' => 'Photographer',
            'place' => 'Kochi',
            'experience' => 6,
            'phone' => '+91 97766 55443',
            'email' => $photoUser->email,
            'about' => 'High-fashion editorial and runway photographer based in Kochi.',
            'outstation' => 'yes',
            'service' => ['fashion-editorial', 'model-portfolios'],
            'equipment' => 'Sony A7 IV, 24-70mm GM II, Godox AD600 Pro',
        ];
        $postPhoto = $this->actingAs($photoUser)->post(route('profile.photographer.save'), $photoPayload);
        $postPhoto->assertSessionHasNoErrors();

        $photoProfile = CreativeProfessional::where('user_id', $photoUser->id)->first();
        $this->assertNotNull($photoProfile);
        $this->assertEquals(['fashion-editorial', 'model-portfolios'], $photoProfile->services);
    }

    /**
     * 6. Test that dynamic fields with numeric or raw rule strings (e.g. "100") do not throw BadMethodCallException validate100.
     */
    public function test_saving_profile_with_custom_field_having_numeric_or_raw_rule_does_not_crash(): void
    {
        $partyCat = ProfessionalCategory::where('slug', 'party-event-management')->firstOrFail();

        // Create a custom field with numeric rule "100" just like what user entered in the dashboard
        $customField = ProfessionalFormField::create([
            'label' => 'Event Mangemnt',
            'field_key' => 'custom_notes_12',
            'field_type' => 'text',
            'validation_rules' => '100', // raw number that previously triggered validate100
            'is_required' => false,
            'is_common' => false,
            'width' => 'full',
            'display_order' => 85,
            'is_active' => true,
        ]);

        CategoryFormField::create([
            'category_id' => $partyCat->id,
            'field_id' => $customField->id,
            'display_order' => 85,
            'is_required' => false,
            'is_active' => true,
        ]);

        $user = $this->makeUser('party-event-management');

        $payload = [
            'name' => 'Moideen Event Director',
            'profession' => 'Party & Event Management',
            'place' => 'Kochi',
            'experience' => 8,
            'phone' => '+91 99988 77766',
            'email' => $user->email,
            'about' => 'High-fashion runway and luxury wedding producer with over seven years experience across south India.',
            'outstation' => 'yes',
            'service' => ['fashion-show-production', 'sound-light-av-production'],
            'custom_notes_12' => 'VIP arrangements and sound systems ready.',
        ];

        $postRes = $this->actingAs($user, 'web')->post(route('profile.category.save', 'party-event-management'), $payload);
        $postRes->assertSessionHasNoErrors();
        $postRes->assertRedirect();

        $profile = CreativeProfessional::where('user_id', $user->id)->first();
        $this->assertNotNull($profile);
        $this->assertSame('Moideen Event Director', $profile->name);
        $this->assertEquals(['fashion-show-production', 'sound-light-av-production'], $profile->services);
        $this->assertArrayHasKey('custom_notes_12', $profile->custom_fields);
        $this->assertSame('VIP arrangements and sound systems ready.', $profile->custom_fields['custom_notes_12']);
    }

    /**
     * 7. Comprehensive verification that ALL nine creative professional profile pages load and submit without 500 error.
     */
    public function test_all_nine_creative_professional_profile_pages_load_and_submit_successfully(): void
    {
        $categories = [
            'makeup-artist' => [
                'service' => ['bridal-makeup'],
                'specific_field' => 'kit',
                'specific_val' => 'MAC and Kryolan Pro Kit',
            ],
            'party-event-management' => [
                'service' => ['fashion-show-production'],
                'specific_field' => 'equipment',
                'specific_val' => 'Stage line-array and LED panels',
            ],
            'hairstylist' => [
                'service' => ['bridal-hairstyling'],
                'specific_field' => 'kit',
                'specific_val' => 'Dyson Supersonic & Schwarzkopf',
            ],
            'photographer' => [
                'service' => ['fashion-editorial'],
                'specific_field' => 'equipment',
                'specific_val' => 'Sony A7R V and Godox AD600',
            ],
            'videographer' => [
                'service' => ['fashion-films'],
                'specific_field' => 'equipment',
                'specific_val' => 'Sony FX3 and DJI Ronin RS3',
            ],
            'fashion-designer' => [
                'service' => ['custom-couture'],
                'specific_field' => 'atelier',
                'specific_val' => 'Handloom silk atelier in Kochi',
            ],
            'drone-pilot' => [
                'service' => ['aerial-wedding-films'],
                'specific_field' => 'equipment',
                'specific_val' => 'DJI Mavic 3 Cine & DGCA license',
            ],
            'photo-video-editor' => [
                'service' => ['fashion-film-edits'],
                'specific_field' => 'software',
                'specific_val' => 'DaVinci Resolve Studio & Premiere Pro',
            ],
            'jewellery-designer' => [
                'service' => ['bridal-jewellery-sets'],
                'specific_field' => 'materials',
                'specific_val' => '22K Gold, Polki and Kundan',
            ],
        ];

        foreach ($categories as $slug => $data) {
            $user = $this->makeUser($slug, ['name' => 'Talent for '.ucfirst($slug)]);

            // 1. GET page
            $url = "/profiles/{$slug}";
            $getRes = $this->actingAs($user, 'web')->get($url);
            $this->assertSame(200, $getRes->getStatusCode(), "GET {$url} must return HTTP 200, received {$getRes->getStatusCode()}");
            $getRes->assertSee('profileForm');

            // 2. POST save profile
            $payload = [
                'name' => 'Talent for '.ucfirst($slug),
                'profession' => ucfirst(str_replace('-', ' ', $slug)),
                'place' => 'Kochi',
                'experience' => 5,
                'phone' => '+91 99988 77766',
                'email' => $user->email,
                'about' => 'Highly experienced creative professional in South India fashion and media industry.',
                'outstation' => 'yes',
                'service' => $data['service'],
                $data['specific_field'] => $data['specific_val'],
            ];

            $saveUrl = Route::has("profile.{$slug}.save")
                ? route("profile.{$slug}.save")
                : route('profile.category.save', ['category' => $slug]);

            $postRes = $this->actingAs($user, 'web')->post($saveUrl, $payload);
            $this->assertNotSame(500, $postRes->getStatusCode(), "POST {$saveUrl} must not return 500 error");
            $postRes->assertSessionHasNoErrors();

            // Verify database entry
            $profile = CreativeProfessional::where('user_id', $user->id)->first();
            $this->assertNotNull($profile, "CreativeProfessional must be saved for {$slug}");
            $this->assertSame($slug, $profile->category);
            $this->assertEquals($data['service'], $profile->services);
        }
    }
}
