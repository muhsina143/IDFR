<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ModelProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSpecificProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper to create a verified user with a specific role.
     */
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

    /**
     * 1. Unauthenticated users cannot access any profile routes.
     */
    public function test_unauthenticated_users_cannot_access_profile_routes(): void
    {
        $routes = [
            route('profile.model'),
            route('profile.fashion-designer'),
            route('profile.makeup-artist'),
            route('profile.hairstylist'),
            route('profile.photographer'),
            route('profile.videographer'),
            route('profile.drone-pilot'),
            route('profile.photo-video-editor'),
            route('profile.jewellery-designer'),
        ];

        foreach ($routes as $url) {
            $response = $this->get($url);
            $location = (string) $response->headers->get('Location');
            $this->assertTrue(
                str_starts_with($location, route('idfr.home')),
                "Expected redirect to home page, got {$location}"
            );
        }
    }

    /**
     * 2. Profile URL helper maps correctly for each role.
     */
    public function test_user_profile_url_matches_registered_role(): void
    {
        $roleMap = [
            UserRole::Model->value => route('profile.model'),
            UserRole::FashionDesigner->value => route('profile.fashion-designer'),
            UserRole::MakeupArtist->value => route('profile.makeup-artist'),
            UserRole::Hairstylist->value => route('profile.hairstylist'),
            UserRole::Photographer->value => route('profile.photographer'),
            UserRole::Videographer->value => route('profile.videographer'),
            UserRole::DronePilot->value => route('profile.drone-pilot'),
            UserRole::PhotoVideoEditor->value => route('profile.photo-video-editor'),
            UserRole::JewelleryDesigner->value => route('profile.jewellery-designer'),
        ];

        foreach ($roleMap as $roleVal => $expectedUrl) {
            $user = $this->makeUser(UserRole::from($roleVal));
            $this->assertSame($expectedUrl, $user->profileUrl());
        }
    }

    /**
     * 3. Model role can view and save model profile.
     */
    public function test_model_role_can_view_and_save_profile(): void
    {
        $user = $this->makeUser(UserRole::Model, [
            'name' => 'Zara Khan',
            'email' => 'zara@example.com',
            'phone' => '+91 99887 76655',
        ]);

        // View form - checks prefill of user name and email
        $response = $this->actingAs($user)->get(route('profile.model'));
        $response->assertOk()
            ->assertSee('Zara Khan')
            ->assertSee('zara@example.com');

        // Save profile
        $payload = [
            'name' => 'Zara Khan',
            'gender' => 'female',
            'place' => 'Kochi',
            'age' => 22,
            'height' => 175,
            'category' => 'experienced',
            'experience' => '3',
            'skills' => 'Runway walk, editorial posing, Bharatanatyam',
            'modelType' => ['runway', 'editorial'],
            'phone' => '+91 99887 76655',
            'email' => 'zara@example.com',
            'about' => 'High fashion runway and editorial model based in Kochi.',
            'outstation' => 'yes',
            'international' => 'no',
        ];

        $saveResponse = $this->actingAs($user)->post(route('profile.model.save'), $payload);
        $saveResponse->assertSessionHas('success');

        // Verify in database
        $this->assertDatabaseHas('models', [
            'user_id' => $user->id,
            'name' => 'Zara Khan',
            'location' => 'Kochi',
            'height' => '175',
            'phone' => '+91 99887 76655',
            'email' => 'zara@example.com',
        ]);

        // Reopen form - verify saved attributes are present
        $reopenResponse = $this->actingAs($user)->get(route('profile.model'));
        $reopenResponse->assertOk()
            ->assertSee('Runway walk, editorial posing, Bharatanatyam');
    }

    /**
     * 4. Fashion Designer role can view and save designer profile.
     */
    public function test_fashion_designer_can_view_and_save_profile(): void
    {
        $user = $this->makeUser(UserRole::FashionDesigner, [
            'name' => 'Elena Roy',
            'email' => 'elena@example.com',
            'phone' => '+91 91234 56789',
        ]);

        $response = $this->actingAs($user)->get(route('profile.fashion-designer'));
        $response->assertOk()
            ->assertSee('Elena Roy');

        $payload = [
            'name' => 'Elena Roy',
            'brand' => 'Elena Couture',
            'place' => 'Kochi',
            'experience' => '6',
            'phone' => '+91 91234 56789',
            'email' => 'elena@example.com',
            'about' => 'Sustainable luxury bespoke bridal and evening wear brand.',
            'service' => ['bridal-couture', 'pret-wear'],
            'atelier' => 'In-house embroidery and draping unit in Fort Kochi.',
            'outstation' => 'yes',
        ];

        $saveResponse = $this->actingAs($user)->post(route('profile.fashion-designer.save'), $payload);
        $saveResponse->assertSessionHas('success');

        $this->assertDatabaseHas('designers', [
            'user_id' => $user->id,
            'brand_name' => 'Elena Couture',
            'location' => 'Kochi',
            'phone' => '+91 91234 56789',
            'email' => 'elena@example.com',
        ]);

        // Reopen form
        $reopenResponse = $this->actingAs($user)->get(route('profile.fashion-designer'));
        $reopenResponse->assertOk()
            ->assertSee('Elena Couture')
            ->assertSee('In-house embroidery and draping unit in Fort Kochi.');
    }

    /**
     * 5. Test all remaining 7 creative roles can view and save profile.
     */
    public function test_all_seven_creative_roles_can_view_and_save_profile(): void
    {
        $creativeRoles = [
            [
                'role' => UserRole::MakeupArtist,
                'viewRoute' => 'profile.makeup-artist',
                'saveRoute' => 'profile.makeup-artist.save',
                'category' => 'makeup-artist',
                'services' => ['bridal-glam', 'editorial-high-fashion'],
                'equipmentKey' => 'equipment',
                'equipmentVal' => 'MAC, Charlotte Tilbury, Huda Beauty kits',
            ],
            [
                'role' => UserRole::Hairstylist,
                'viewRoute' => 'profile.hairstylist',
                'saveRoute' => 'profile.hairstylist.save',
                'category' => 'hairstylist',
                'services' => ['bridal-hair-styling', 'editorial-avant-garde'],
                'equipmentKey' => 'equipment',
                'equipmentVal' => 'Dyson Pro, Balmain Hair Couture',
            ],
            [
                'role' => UserRole::Photographer,
                'viewRoute' => 'profile.photographer',
                'saveRoute' => 'profile.photographer.save',
                'category' => 'photographer',
                'services' => ['fashion-editorials', 'runway-coverage'],
                'equipmentKey' => 'equipment',
                'equipmentVal' => 'Sony A1, Profoto lights',
            ],
            [
                'role' => UserRole::Videographer,
                'viewRoute' => 'profile.videographer',
                'saveRoute' => 'profile.videographer.save',
                'category' => 'videographer',
                'services' => ['fashion-films', 'event-highlights'],
                'equipmentKey' => 'equipment',
                'equipmentVal' => 'Sony FX6, Ronin 4D',
            ],
            [
                'role' => UserRole::DronePilot,
                'viewRoute' => 'profile.drone-pilot',
                'saveRoute' => 'profile.drone-pilot.save',
                'category' => 'drone-pilot',
                'services' => ['aerial-wedding-films', 'fpv-accent-shots'],
                'equipmentKey' => 'equipment',
                'equipmentVal' => 'DJI Inspire 3, Remote Pilot Licence',
            ],
            [
                'role' => UserRole::PhotoVideoEditor,
                'viewRoute' => 'profile.photo-video-editor',
                'saveRoute' => 'profile.photo-video-editor.save',
                'category' => 'photo-video-editor',
                'services' => ['colour-grading', 'sound-design'],
                'equipmentKey' => 'software',
                'equipmentVal' => 'DaVinci Resolve Studio, Premiere Pro',
            ],
            [
                'role' => UserRole::JewelleryDesigner,
                'viewRoute' => 'profile.jewellery-designer',
                'saveRoute' => 'profile.jewellery-designer.save',
                'category' => 'jewellery-designer',
                'services' => ['bridal-jewellery-sets', 'bespoke-commissions'],
                'equipmentKey' => 'materials',
                'equipmentVal' => '18k & 22k gold, natural diamonds, CAD studio',
            ],
        ];

        foreach ($creativeRoles as $item) {
            $user = $this->makeUser($item['role'], [
                'name' => 'Creative '.$item['category'],
                'email' => $item['category'].'@example.com',
                'phone' => '+91 98111 22233',
            ]);

            // 1. View route works
            $viewRes = $this->actingAs($user)->get(route($item['viewRoute']));
            $viewRes->assertOk()->assertSee($user->name);

            // 2. Save profile
            $payload = [
                'name' => $user->name,
                'category' => $item['category'],
                'place' => 'Kochi',
                'experience' => '4.5',
                'phone' => '+91 98111 22233',
                'email' => $user->email,
                'about' => 'Professional '.$item['category'].' with extensive fashion experience.',
                'service' => $item['services'],
                $item['equipmentKey'] => $item['equipmentVal'],
                'outstation' => 'yes',
            ];

            $saveRes = $this->actingAs($user)->post(route($item['saveRoute']), $payload);
            $saveRes->assertSessionHas('success');

            // 3. Database assertion
            $this->assertDatabaseHas('creative_professionals', [
                'user_id' => $user->id,
                'category' => $item['category'],
                'name' => $user->name,
                'location' => 'Kochi',
                'email' => $user->email,
            ]);

            // 4. Reopen and verify data persistence
            $reopenRes = $this->actingAs($user)->get(route($item['viewRoute']));
            $reopenRes->assertOk()->assertSee($item['equipmentVal']);
        }
    }

    /**
     * 6. Cross-role unauthorized access is prevented.
     */
    public function test_cross_role_unauthorized_access_redirects_to_own_profile(): void
    {
        $modelUser = $this->makeUser(UserRole::Model);
        $designerUser = $this->makeUser(UserRole::FashionDesigner);

        // Model attempting to access Fashion Designer profile form
        $res1 = $this->actingAs($modelUser)->get(route('profile.fashion-designer'));
        $res1->assertRedirect(route('profile.model'));

        // Fashion Designer attempting to access Model profile form
        $res2 = $this->actingAs($designerUser)->get(route('profile.model'));
        $res2->assertRedirect(route('profile.fashion-designer'));

        // Cross-role POST attempt is also rejected and redirected
        $res3 = $this->actingAs($modelUser)->post(route('profile.fashion-designer.save'), [
            'name' => 'Unauthorized Hack',
        ]);
        $res3->assertRedirect(route('profile.model'));
    }

    /**
     * 7. Decommissioned recruiter role cannot be created as UserRole enum.
     */
    public function test_decommissioned_recruiter_role_not_in_enum(): void
    {
        $this->assertNull(UserRole::tryFrom('recruiter'));
    }

    /**
     * 8. Users cannot overwrite another user's profile.
     */
    public function test_users_cannot_modify_another_users_profile(): void
    {
        $userA = $this->makeUser(UserRole::Model, ['name' => 'User A']);
        $userB = $this->makeUser(UserRole::Model, ['name' => 'User B']);

        // User A creates their profile
        $this->actingAs($userA)->post(route('profile.model.save'), [
            'name' => 'User A Profile',
            'gender' => 'female',
            'place' => 'Mumbai',
            'age' => 20,
            'height' => 170,
            'category' => 'new-face',
            'experience' => '2',
            'skills' => 'Catwalk, commercial posing',
            'modelType' => ['runway'],
            'phone' => '+91 90000 00001',
            'email' => 'usera@example.com',
            'about' => 'Bio for User A',
            'outstation' => 'yes',
            'international' => 'no',
        ]);

        // User B creates their profile
        $this->actingAs($userB)->post(route('profile.model.save'), [
            'name' => 'User B Profile',
            'gender' => 'male',
            'place' => 'Delhi',
            'age' => 24,
            'height' => 182,
            'category' => 'experienced',
            'experience' => '4',
            'skills' => 'Editorial print, fitness posing',
            'modelType' => ['editorial'],
            'phone' => '+91 90000 00002',
            'email' => 'userb@example.com',
            'about' => 'Bio for User B',
            'outstation' => 'no',
            'international' => 'yes',
        ]);

        // Verify User A profile remains intact
        $profileA = ModelProfile::where('user_id', $userA->id)->first();
        $this->assertNotNull($profileA);
        $this->assertSame('User A Profile', $profileA->name);
        $this->assertSame('Mumbai', $profileA->location);

        // Verify User B profile has their own data
        $profileB = ModelProfile::where('user_id', $userB->id)->first();
        $this->assertNotNull($profileB);
        $this->assertSame('User B Profile', $profileB->name);
        $this->assertSame('Delhi', $profileB->location);
    }

    /**
     * 9. AJAX / JSON save requests return JSON with success flag.
     */
    public function test_ajax_save_requests_return_json(): void
    {
        $user = $this->makeUser(UserRole::DronePilot);

        $response = $this->actingAs($user)->postJson(route('profile.drone-pilot.save'), [
            'name' => 'Drone Operator',
            'category' => 'drone-pilot',
            'place' => 'Goa',
            'experience' => '5',
            'phone' => '+91 99999 88888',
            'email' => $user->email,
            'about' => 'Cinematic aerial video coverage across coastal destinations.',
            'service' => ['destination-drone-coverage', 'aerial-wedding-films'],
            'equipment' => 'DJI Mavic 3 Cine',
            'outstation' => 'yes',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('profile.category', 'drone-pilot')
            ->assertJsonPath('profile.location', 'Goa');
    }

    /**
     * 10. Authenticated model user sees direct profile links on the website instead of the auth modal trigger.
     */
    public function test_authenticated_model_user_sees_direct_profile_link_not_auth_modal_trigger(): void
    {
        $user = $this->makeUser(UserRole::Model, [
            'name' => 'Maya Sharma',
            'email' => 'maya@example.com',
            'phone' => '+91 98765 11223',
        ]);

        $response = $this->actingAs($user)->get(route('idfr.home'));
        $response->assertOk();

        // Must include direct link to model profile
        $response->assertSee(route('profile.model'));
        $response->assertSee('Register as Model');

        // Body must have is-authenticated class
        $response->assertSee('is-authenticated');

        // window.__IDFR_USER__ must contain role 'model' and profile_url
        $response->assertSee('"role":"model"', false);
        $response->assertSee(route('profile.model'), false);
    }

    /**
     * 11. Model Profile Form pre-fills available name, email, and phone from the user account.
     */
    public function test_model_profile_form_prefills_name_email_and_phone(): void
    {
        $user = $this->makeUser(UserRole::Model, [
            'name' => 'Rhea Pillai',
            'email' => 'rhea.pillai@example.com',
            'phone' => '+91 98450 12345',
        ]);

        $response = $this->actingAs($user)->get(route('profile.model'));
        $response->assertOk()
            ->assertSee('value="Rhea Pillai"', false)
            ->assertSee('value="rhea.pillai@example.com"', false)
            ->assertSee('value="+91 98450 12345"', false);
    }

    /**
     * 12. Save profile against authenticated user's ID in MySQL and retrieve when user logs out and logs in again.
     */
    public function test_model_profile_persists_in_database_and_survives_logout_and_relogin(): void
    {
        $user = $this->makeUser(UserRole::Model, [
            'name' => 'Pooja Hegde',
            'email' => 'pooja.model@example.com',
            'phone' => '+91 98200 11111',
            'password' => bcrypt('Password123!'),
        ]);

        $savePayload = [
            'name' => 'Pooja Hegde',
            'gender' => 'female',
            'place' => 'Bengaluru',
            'age' => 25,
            'height' => 176,
            'category' => 'experienced',
            'experience' => '5',
            'skills' => 'High fashion runway, commercial shoot, Kathak dancer',
            'modelType' => ['runway', 'commercial', 'editorial'],
            'phone' => '+91 98200 11111',
            'email' => 'pooja.model@example.com',
            'about' => 'Experienced runway and fashion model with Lakme Fashion Week experience.',
            'outstation' => 'yes',
            'international' => 'yes',
        ];

        // Save profile
        $saveResponse = $this->actingAs($user)->post(route('profile.model.save'), $savePayload);
        $saveResponse->assertSessionHas('success');

        // Confirm database persistence
        $this->assertDatabaseHas('models', [
            'user_id' => $user->id,
            'name' => 'Pooja Hegde',
            'location' => 'Bengaluru',
            'phone' => '+91 98200 11111',
            'email' => 'pooja.model@example.com',
            'age' => 25,
            'height' => '176',
            'level' => 'Experienced',
        ]);

        // User logs out
        $this->actingAs($user)->post('/logout');
        $this->assertGuest();

        // User logs back in via AJAX (like public auth modal)
        $loginResponse = $this->postJson('/login', [
            'email' => 'pooja.model@example.com',
            'password' => 'Password123!',
        ]);
        $loginResponse->assertOk()->assertJsonPath('success', true);
        $this->assertAuthenticatedAs($user);

        // Retrieve profile again and verify all fields populate from MySQL
        $reopenResponse = $this->actingAs($user)->get(route('profile.model'));
        $reopenResponse->assertOk()
            ->assertSee('value="Pooja Hegde"', false)
            ->assertSee('value="Bengaluru"', false)
            ->assertSee('value="+91 98200 11111"', false)
            ->assertSee('value="pooja.model@example.com"', false)
            ->assertSee('value="25"', false)
            ->assertSee('value="176"', false)
            ->assertSee('value="5"', false)
            ->assertSee('High fashion runway, commercial shoot, Kathak dancer');

        $this->assertDatabaseHas('models', [
            'user_id' => $user->id,
            'description' => 'Experienced runway and fashion model with Lakme Fashion Week experience.',
        ]);
    }

    /**
     * 13. Non-model roles are redirected away from Model profile to their own profile form.
     */
    public function test_non_model_roles_redirected_away_from_model_profile_to_their_own_profile(): void
    {
        $designer = $this->makeUser(UserRole::FashionDesigner);
        $photographer = $this->makeUser(UserRole::Photographer);

        // Designer trying to open Model profile
        $responseDesigner = $this->actingAs($designer)->get(route('profile.model'));
        $responseDesigner->assertRedirect(route('profile.fashion-designer'));

        // Photographer trying to open Model profile
        $responsePhotographer = $this->actingAs($photographer)->get(route('profile.model'));
        $responsePhotographer->assertRedirect(route('profile.photographer'));
    }

    /**
     * 14. Friendly routes redirect authenticated model to model profile.
     */
    public function test_friendly_routes_redirect_to_model_profile(): void
    {
        $user = $this->makeUser(UserRole::Model);

        $response1 = $this->actingAs($user)->get('/model-profile');
        $response1->assertRedirect(route('profile.model'));

        $response2 = $this->actingAs($user)->get('/register-as-model');
        $response2->assertRedirect(route('profile.model'));
    }
}
