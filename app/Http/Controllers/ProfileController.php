<?php

namespace App\Http\Controllers;

use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ModelProfile;
use App\Models\ProfessionalCategory;
use Database\Seeders\DynamicProfileFormSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Authorize that the current authenticated user's registered role matches the requested role.
     */
    protected function authorizeRole(string $expectedRole): ?RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return redirect()->route('idfr.home', ['auth' => 'login']);
        }

        // Administrators can view all forms
        if ($user->isAdmin()) {
            return null;
        }

        // Lock users strictly to their registered role's form
        if ($user->role !== $expectedRole) {
            // Safe alias resolution for party-event-management
            if (($user->role === 'party-event-mnagement' && $expectedRole === 'party-event-management') ||
                ($user->role === 'party-event-management' && $expectedRole === 'party-event-mnagement')) {
                if ($user->role === 'party-event-mnagement') {
                    $user->update(['role' => 'party-event-management']);
                }

                return null;
            }

            return redirect($user->profileUrl())
                ->with('error', 'You can only view and edit the profile form matching your registered role.');
        }

        return null;
    }

    /**
     * Display the Model Profile page.
     */
    public function model(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->authorizeRole('model')) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();
        $model = ModelProfile::where('user_id', $user->id)->first();

        // Connect pre-existing seeded profile by email or unassigned name matching
        if (! $model) {
            $model = ModelProfile::where('email', $user->email)->whereNull('user_id')->first();
            if (! $model && ! empty($user->name)) {
                $model = ModelProfile::where('name', $user->name)
                    ->whereNull('user_id')
                    ->where(function ($q) use ($user) {
                        $q->whereNull('email')->orWhere('email', '')->orWhere('email', $user->email);
                    })
                    ->first();
            }

            if ($model) {
                $model->user_id = $user->id;
                if (empty($model->email)) {
                    $model->email = $user->email;
                }
                if (empty($model->phone) && ! empty($user->phone)) {
                    $model->phone = $user->phone;
                }
                $model->save();
            } else {
                // Initialize dedicated model profile record for the authenticated model
                $model = ModelProfile::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'gender' => 'Female',
                    'level' => 'New Face',
                    'age' => null,
                    'height' => null,
                    'location' => '',
                    'experience' => '0',
                    'types' => ['Commercial'],
                    'skills' => ['Runway walk', 'Editorial posing'],
                    'outstation' => 'yes',
                    'international' => 'no',
                    'status' => 'pending',
                ]);
            }
        }

        return view('profiles.model', compact('model', 'user'));
    }

    /**
     * Save the Model Profile.
     */
    public function saveModel(Request $request): JsonResponse|RedirectResponse
    {
        if ($redirect = $this->authorizeRole('model')) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'gender' => ['required', 'string', 'in:male,female,other,Male,Female,Other'],
            'place' => ['required', 'string', 'max:100'],
            'age' => ['required', 'integer', 'min:14', 'max:70'],
            'height' => ['required', 'numeric', 'min:100', 'max:230'],
            'category' => ['required', 'string', 'in:experienced,new-face,Experienced,New Face'],
            'experience' => ['required', 'numeric', 'min:0', 'max:50'],
            'skills' => ['required', 'string', 'min:2'],
            'modelType' => ['required', 'array', 'min:1'],
            'modelType.*' => ['string'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:150'],
            'about' => ['nullable', 'string'],
            'bust' => ['nullable', 'string', 'max:30'],
            'waist' => ['nullable', 'string', 'max:30'],
            'hips' => ['nullable', 'string', 'max:30'],
            'shoe' => ['nullable', 'string', 'max:30'],
            'outstation' => ['required', 'in:yes,no,Yes,No'],
            'international' => ['required', 'in:yes,no,Yes,No'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'modelType.required' => 'Please select at least one model type.',
            'category.required' => 'Please select your experience category (Experienced or New Face).',
            'place.required' => 'Please select your city or location.',
        ]);

        $existingModel = ModelProfile::where('user_id', $user->id)->first();
        if (! $existingModel) {
            $existingModel = ModelProfile::where('email', $user->email)->whereNull('user_id')->first()
                ?? ModelProfile::where('name', $user->name)->whereNull('user_id')->first();
        }

        $photoPath = $existingModel?->profile_photo;
        $uploadDir = public_path('uploads/models');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'model_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $photoPath = 'uploads/models/'.$filename;
        }

        $portfolio = $existingModel?->portfolio_images ?? [];
        if (! is_array($portfolio)) {
            $portfolio = [];
        }

        if ($request->hasFile('portfolio_image_1')) {
            $f1 = $request->file('portfolio_image_1');
            $fn1 = 'model_port_'.time().'_1_'.Str::random(8).'.'.$f1->getClientOriginalExtension();
            $f1->move($uploadDir, $fn1);
            $portfolio[0] = 'uploads/models/'.$fn1;
        }

        if ($request->hasFile('portfolio_image_2')) {
            $f2 = $request->file('portfolio_image_2');
            $fn2 = 'model_port_'.time().'_2_'.Str::random(8).'.'.$f2->getClientOriginalExtension();
            $f2->move($uploadDir, $fn2);
            $portfolio[1] = 'uploads/models/'.$fn2;
        }

        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'model_port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move($uploadDir, $pFilename);
                $portfolio[] = 'uploads/models/'.$pFilename;
            }
        }

        $measurements = $existingModel?->measurements ?? [];
        if (! is_array($measurements)) {
            $measurements = [];
        }
        if ($request->filled('bust')) {
            $measurements['Bust'] = trim($request->input('bust'));
        }
        if ($request->filled('waist')) {
            $measurements['Waist'] = trim($request->input('waist'));
        }
        if ($request->filled('hips')) {
            $measurements['Hips'] = trim($request->input('hips'));
        }
        if ($request->filled('shoe')) {
            $measurements['Shoe'] = trim($request->input('shoe'));
        }

        $level = in_array(strtolower($validated['category']), ['experienced', 'exp'], true) ? 'Experienced' : 'New Face';
        $skillsArray = is_array($request->input('skills'))
            ? $request->input('skills')
            : array_values(array_filter(array_map('trim', explode(',', $validated['skills']))));

        $model = ModelProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => trim($validated['name']),
                'gender' => ucfirst(strtolower($validated['gender'])),
                'location' => trim($validated['place']),
                'phone' => trim($validated['phone'] ?? $user->phone ?? ''),
                'email' => trim($validated['email'] ?? $user->email ?? ''),
                'age' => (int) $validated['age'],
                'height' => (string) $validated['height'],
                'level' => $level,
                'experience' => (string) $validated['experience'],
                'skills' => $skillsArray,
                'types' => $validated['modelType'],
                'measurements' => ! empty($measurements) ? $measurements : null,
                'description' => $validated['about'] ?? null,
                'outstation' => strtolower($validated['outstation']),
                'international' => strtolower($validated['international']),
                'availability' => 'Outstation: '.ucfirst(strtolower($validated['outstation'])).', International: '.ucfirst(strtolower($validated['international'])),
                'specialities' => null,
                'profile_photo' => $photoPath,
                'portfolio_images' => array_values($portfolio),
                'status' => ($existingModel && $existingModel->status === 'published') ? 'published' : 'pending',
            ]
        );

        $userUpdates = [];
        if ($user->name !== trim($validated['name'])) {
            $userUpdates['name'] = trim($validated['name']);
        }
        if (! empty($validated['phone']) && empty($user->phone)) {
            $userUpdates['phone'] = trim($validated['phone']);
        }
        if (! empty($userUpdates)) {
            $user->update($userUpdates);
        }

        $message = "Thank you, {$user->name}. Your model profile has been saved successfully.";

        if ($request->expectsJson() || $request->ajax()) {
            $freshModel = $model->fresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'profile' => $freshModel,
                'photo_url' => $freshModel->photo_url,
                'profile_completion' => $user->fresh()->profileCompletion(),
            ]);
        }

        return redirect()->back()->with('success', $message)->with('status', $message);
    }

    /**
     * Display the Fashion Designer Profile page.
     */
    public function fashionDesigner(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('fashion-designer');
    }

    /**
     * Save the Fashion Designer Profile.
     */
    public function saveDesigner(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'fashion-designer');
    }

    /**
     * Display a Creative Professional profile page.
     */
    public function showCreativeProfile(string $category): View|RedirectResponse
    {
        if ($category === 'party-event-mnagement') {
            $authUser = Auth::guard('web')->user();
            if ($authUser && $authUser->role === 'party-event-mnagement') {
                $authUser->update(['role' => 'party-event-management']);
            }

            return redirect()->route('profile.category', ['category' => 'party-event-management']);
        }
        if ($redirect = $this->authorizeRole($category)) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();
        $categoryModel = ProfessionalCategory::where('slug', $category)->first();

        if (! $categoryModel) {
            (new DynamicProfileFormSeeder)->run();
            $categoryModel = ProfessionalCategory::where('slug', $category)->first();
        }

        if (! $categoryModel) {
            abort(404, 'Creative category not found.');
        }

        $fields = $categoryModel->getActiveFormFields();

        $profile = CreativeProfessional::where('category', $category)
            ->where('user_id', $user->id)
            ->first();

        if (! $profile) {
            $profile = CreativeProfessional::where('category', $category)
                ->where('name', $user->name)
                ->whereNull('user_id')
                ->first();

            if ($profile) {
                $profile->user_id = $user->id;
                $profile->save();
            }
        }

        $designer = null;
        if ($category === 'fashion-designer') {
            $designer = Designer::where('user_id', $user->id)->first();
            if (! $designer) {
                $designer = Designer::where('name', $user->name)->whereNull('user_id')->first();
                if ($designer) {
                    $designer->user_id = $user->id;
                    $designer->save();
                }
            }

            if (! $profile && $designer) {
                $profile = CreativeProfessional::updateOrCreate(
                    ['user_id' => $user->id, 'category' => 'fashion-designer'],
                    [
                        'name' => $designer->name,
                        'brand' => $designer->brand_name,
                        'professional_title' => 'Fashion Designer',
                        'location' => $designer->location,
                        'phone' => $designer->phone,
                        'email' => $designer->email,
                        'experience' => $designer->experience,
                        'biography' => $designer->biography,
                        'equipment' => $designer->atelier_notes,
                        'services' => $designer->services,
                        'outstation' => $designer->outstation,
                        'profile_photo' => $designer->profile_photo,
                        'portfolio_images' => $designer->portfolio_images,
                        'status' => $designer->status ?? 'pending',
                    ]
                );
            }
        }

        if (view()->exists("profiles.{$category}")) {
            return view("profiles.{$category}", compact('profile', 'designer', 'user', 'categoryModel', 'fields'));
        }

        return view('profiles.dynamic-profile', compact('profile', 'designer', 'user', 'categoryModel', 'fields'));
    }

    /**
     * Save a Creative Professional profile.
     */
    public function handleSaveCreative(Request $request, string $category): JsonResponse|RedirectResponse
    {
        if ($category === 'party-event-mnagement') {
            $category = 'party-event-management';
        }
        if ($redirect = $this->authorizeRole($category)) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();
        $categoryModel = ProfessionalCategory::where('slug', $category)->first();

        if (! $categoryModel) {
            (new DynamicProfileFormSeeder)->run();
            $categoryModel = ProfessionalCategory::where('slug', $category)->first();
        }

        if (! $categoryModel) {
            abort(404, 'Creative category not found.');
        }

        $activeFields = $categoryModel->getActiveFormFields();

        // Base validation rules
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'profession' => ['nullable', 'string', 'max:150'],
            'place' => ['required', 'string', 'max:100'],
            'experience' => ['required', 'numeric', 'min:0', 'max:40'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['required', 'email', 'max:150'],
            'about' => ['required', 'string', 'min:20'],
            'kit' => ['nullable', 'string'],
            'equipment' => ['nullable', 'string'],
            'software' => ['nullable', 'string'],
            'materials' => ['nullable', 'string'],
            'atelier' => ['nullable', 'string'],
            'atelier_notes' => ['nullable', 'string'],
            'service' => ['nullable', 'array'],
            'service.*' => ['string'],
            'outstation' => ['required', 'in:yes,no,Yes,No'],
            'brand' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];

        $customMessages = [
            'service.required' => 'Please select at least one specialisation or service.',
            'about.min' => 'Please write at least 20 characters in your bio.',
            'place.required' => 'Please select your city or location.',
        ];

        $hasServiceField = false;
        foreach ($activeFields as $field) {
            $key = $field->field_key;
            $isServiceField = ($key === 'service' || str_starts_with($key, 'service_') || str_contains($key, 'service'));

            if ($isServiceField) {
                $hasServiceField = true;
                $options = $field->options;
                if (is_string($options)) {
                    $decoded = json_decode($options, true);
                    $options = is_array($decoded) ? $decoded : [];
                }
                $hasConfiguredOptions = ! empty($options);

                $serviceRule = ($field->is_required && $hasConfiguredOptions)
                    ? ['required', 'array', 'min:1']
                    : ['nullable', 'array'];

                $rules['service'] = $serviceRule;
                $rules['service.*'] = ['string'];

                if ($key !== 'service') {
                    $rules[$key] = ['nullable', 'array'];
                    $rules["{$key}.*"] = ['string'];
                }
            } elseif (in_array($key, ['kit', 'equipment', 'software', 'materials', 'atelier'], true)) {
                $rules[$key] = $field->is_required ? ['required', 'string'] : ['nullable', 'string'];
            } elseif (! isset($rules[$key])) {
                $rules[$key] = self::sanitizeValidationRules($field->validation_rules, (bool) $field->is_required, (string) $field->field_type);
            }
        }
        if (! $hasServiceField) {
            unset($rules['service'], $rules['service.*']);
        }

        // Merge category-specific service field into 'service' if needed
        if (! $request->has('service')) {
            foreach ($activeFields as $field) {
                if (str_starts_with($field->field_key, 'service_') && $request->has($field->field_key)) {
                    $request->merge(['service' => $request->input($field->field_key)]);
                    break;
                }
            }
        }

        $validated = $request->validate($rules, $customMessages);

        $existingProfile = CreativeProfessional::where('category', $category)
            ->where('user_id', $user->id)
            ->first();
        if (! $existingProfile) {
            $existingProfile = CreativeProfessional::where('category', $category)
                ->where('name', $user->name)
                ->whereNull('user_id')
                ->first();
        }

        $photoPath = $existingProfile?->profile_photo;
        $uploadDir = public_path('uploads/professionals');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'pro_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $photoPath = 'uploads/professionals/'.$filename;
        }

        $portfolio = $existingProfile?->portfolio_images ?? [];
        if (! is_array($portfolio)) {
            $portfolio = [];
        }

        if ($request->hasFile('portfolio_image_1')) {
            $f1 = $request->file('portfolio_image_1');
            $fn1 = 'port_'.time().'_1_'.Str::random(8).'.'.$f1->getClientOriginalExtension();
            $f1->move($uploadDir, $fn1);
            $portfolio[0] = ['image' => 'uploads/professionals/'.$fn1, 'caption' => 'Portfolio Work 1'];
        }

        if ($request->hasFile('portfolio_image_2')) {
            $f2 = $request->file('portfolio_image_2');
            $fn2 = 'port_'.time().'_2_'.Str::random(8).'.'.$f2->getClientOriginalExtension();
            $f2->move($uploadDir, $fn2);
            $portfolio[1] = ['image' => 'uploads/professionals/'.$fn2, 'caption' => 'Portfolio Work 2'];
        }

        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move($uploadDir, $pFilename);
                $portfolio[] = ['image' => 'uploads/professionals/'.$pFilename, 'caption' => 'Portfolio Work'];
            }
        }

        $equipment = $validated['kit']
            ?? $validated['equipment']
            ?? $validated['software']
            ?? $validated['materials']
            ?? $validated['atelier']
            ?? $validated['atelier_notes']
            ?? $request->input('kit')
            ?? $request->input('equipment')
            ?? $request->input('software')
            ?? $request->input('materials')
            ?? $request->input('atelier')
            ?? $request->input('atelier_notes')
            ?? '';

        $standardKeys = [
            'name', 'profession', 'place', 'experience', 'phone', 'email', 'about',
            'kit', 'equipment', 'software', 'materials', 'atelier', 'service', 'outstation',
            'brand', 'profile_photo', 'portfolio_image_1', 'portfolio_image_2', 'portfolio_images',
            '_token', 'category',
        ];

        $customFields = $existingProfile?->custom_fields ?? [];
        foreach ($activeFields as $field) {
            $fKey = $field->field_key;
            if (! in_array($fKey, $standardKeys, true) && ! str_starts_with($fKey, 'service_')) {
                if ($request->hasFile($fKey)) {
                    if ($field->field_type === 'multiple_images') {
                        $files = $request->file($fKey);
                        $filePaths = [];
                        if (is_array($files)) {
                            foreach ($files as $f) {
                                $fn = 'custom_'.time().'_'.Str::random(8).'.'.$f->getClientOriginalExtension();
                                $f->move($uploadDir, $fn);
                                $filePaths[] = 'uploads/professionals/'.$fn;
                            }
                        }
                        $customFields[$fKey] = $filePaths;
                    } else {
                        $f = $request->file($fKey);
                        $fn = 'custom_'.time().'_'.Str::random(8).'.'.$f->getClientOriginalExtension();
                        $f->move($uploadDir, $fn);
                        $customFields[$fKey] = 'uploads/professionals/'.$fn;
                    }
                } elseif ($request->has($fKey)) {
                    $customFields[$fKey] = $request->input($fKey);
                }
            }
        }

        $roleLabel = $categoryModel->name;

        $submittedServices = $validated['service']
            ?? $request->input('service')
            ?? [];
        if (empty($submittedServices)) {
            foreach ($activeFields as $field) {
                if (str_starts_with($field->field_key, 'service_') && $request->has($field->field_key)) {
                    $submittedServices = (array) $request->input($field->field_key);
                    break;
                }
            }
        }

        $profile = CreativeProfessional::updateOrCreate(
            ['user_id' => $user->id, 'category' => $category],
            [
                'name' => trim($validated['name']),
                'category' => $category,
                'brand' => trim($validated['brand'] ?? 'IDFR'),
                'professional_title' => $validated['profession'] ?? $roleLabel,
                'location' => trim($validated['place']),
                'phone' => trim($validated['phone']),
                'email' => trim($validated['email']),
                'experience' => (string) $validated['experience'],
                'biography' => trim($validated['about']),
                'equipment' => trim($equipment),
                'services' => array_values((array) $submittedServices),
                'outstation' => strtolower($validated['outstation']),
                'profile_photo' => $photoPath,
                'portfolio_images' => array_values($portfolio),
                'custom_fields' => $customFields,
                'status' => ($existingProfile && $existingProfile->status === 'published') ? 'published' : 'pending',
            ]
        );

        if ($category === 'fashion-designer') {
            Designer::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => trim($validated['name']),
                    'brand_name' => trim($validated['brand'] ?? 'Elena Couture'),
                    'location' => trim($validated['place']),
                    'phone' => trim($validated['phone']),
                    'email' => trim($validated['email']),
                    'experience' => (string) $validated['experience'],
                    'biography' => trim($validated['about']),
                    'short_intro' => Str::limit(trim($validated['about']), 200),
                    'atelier_notes' => trim($equipment),
                    'services' => $validated['service'] ?? [],
                    'outstation' => strtolower($validated['outstation']),
                    'profile_photo' => $photoPath,
                    'portfolio_images' => array_values($portfolio),
                    'status' => ($existingProfile && $existingProfile->status === 'published') ? 'published' : 'pending',
                ]
            );
        }

        $userUpdates = [];
        if ($user->name !== trim($validated['name'])) {
            $userUpdates['name'] = trim($validated['name']);
        }
        if (! empty($validated['phone']) && $user->phone !== trim($validated['phone'])) {
            $userUpdates['phone'] = trim($validated['phone']);
        }
        if (! empty($userUpdates)) {
            $user->update($userUpdates);
        }

        $message = "Thank you, {$user->name}. Your ".strtolower($roleLabel).' profile has been saved successfully.';

        if ($request->expectsJson() || $request->ajax()) {
            $freshProfile = $profile->fresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'profile' => $freshProfile,
                'photo_url' => $freshProfile->photo_url,
                'profile_completion' => $user->fresh()->profileCompletion(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    // Role-specific view and save actions:

    public function makeupArtist(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('makeup-artist');
    }

    public function saveMakeupArtist(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'makeup-artist');
    }

    public function hairstylist(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('hairstylist');
    }

    public function saveHairstylist(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'hairstylist');
    }

    public function photographer(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('photographer');
    }

    public function savePhotographer(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'photographer');
    }

    public function videographer(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('videographer');
    }

    public function saveVideographer(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'videographer');

    }

    public function dronePilot(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('drone-pilot');
    }

    public function saveDronePilot(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'drone-pilot');
    }

    public function photoVideoEditor(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('photo-video-editor');
    }

    public function savePhotoVideoEditor(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'photo-video-editor');
    }

    public function jewelleryDesigner(Request $request): View|RedirectResponse
    {
        return $this->showCreativeProfile('jewellery-designer');
    }

    public function saveJewelleryDesigner(Request $request): JsonResponse|RedirectResponse
    {
        return $this->handleSaveCreative($request, 'jewellery-designer');
    }

    /**
     * Safely normalize and sanitize validation rules from dynamic form fields.
     * Prevents BadMethodCallException when an admin enters raw numbers (e.g. 100) or unrecognized rules.
     *
     * @return array<int, string>
     */
    public static function sanitizeValidationRules(mixed $rules, bool $isRequired = false, string $fieldType = 'text'): array
    {
        $raw = [];
        if (is_string($rules)) {
            $tokens = array_filter(array_map('trim', explode('|', $rules)));
            foreach ($tokens as $token) {
                // Normalize "max,100", "max 100", "min,5" before comma splitting
                $token = preg_replace('/^(max|min)[\s=,]+(\d+)$/i', '$1:$2', $token);

                // If token contains commas and isn't a rule taking comma-separated parameters
                if (str_contains($token, ',') && ! preg_match('/^(in|not_in|mimes|mimetypes|between|digits_between|regex):/i', $token)) {
                    foreach (explode(',', $token) as $sub) {
                        $sub = trim($sub);
                        if ($sub !== '') {
                            $sub = preg_replace('/^(max|min)[\s=,]+(\d+)$/i', '$1:$2', $sub);
                            $raw[] = $sub;
                        }
                    }
                } else {
                    $raw[] = $token;
                }
            }
        } elseif (is_array($rules)) {
            $raw = $rules;
        }

        $clean = [];
        foreach ($raw as $r) {
            $r = trim((string) $r);
            if ($r === '') {
                continue;
            }

            // Raw numeric values entered in rule field (e.g. "100" -> "max:100")
            if (is_numeric($r)) {
                $clean[] = 'max:'.(int) $r;

                continue;
            }

            // Normalize formats like "max 100", "max=100", "max,100" -> "max:100"
            if (preg_match('/^(max|min)[\s=,]+(\d+)$/i', $r, $m)) {
                $clean[] = strtolower($m[1]).':'.$m[2];

                continue;
            }

            // Common user-entered aliases
            $lower = strtolower($r);
            if ($lower === 'text') {
                $clean[] = 'string';

                continue;
            }
            if ($lower === 'number') {
                $clean[] = 'numeric';

                continue;
            }

            $parameters = null;
            if (str_contains($r, ':')) {
                [$ruleName, $parameters] = explode(':', $r, 2);
                $ruleName = strtolower(trim($ruleName));
                $parameters = trim($parameters);
            } else {
                $ruleName = strtolower($r);
            }

            // Rules that require at least one parameter
            $rulesRequiringParameters = ['min', 'max', 'between', 'digits', 'digits_between', 'in', 'not_in', 'mimes', 'mimetypes', 'regex'];
            if (in_array($ruleName, $rulesRequiringParameters, true) && ($parameters === null || $parameters === '')) {
                continue;
            }

            // Standard Laravel validation rule names
            $validRules = [
                'required', 'nullable', 'string', 'numeric', 'integer', 'email', 'url',
                'array', 'in', 'not_in', 'min', 'max', 'between', 'digits', 'digits_between',
                'alpha', 'alpha_num', 'alpha_dash', 'date', 'image', 'file', 'mimes',
                'mimetypes', 'boolean', 'uuid', 'sometimes', 'confirmed', 'regex',
            ];

            if (in_array($ruleName, $validRules, true)) {
                $clean[] = $parameters !== null ? "{$ruleName}:{$parameters}" : $ruleName;
            }
        }

        if ($isRequired) {
            $clean = array_values(array_filter($clean, fn ($item) => $item !== 'nullable'));
            if (! in_array('required', $clean, true)) {
                array_unshift($clean, 'required');
            }
        } else {
            $clean = array_values(array_filter($clean, fn ($item) => $item !== 'required'));
            if (! in_array('nullable', $clean, true)) {
                array_unshift($clean, 'nullable');
            }
        }

        // Ensure at least a baseline type rule if none was supplied
        $typeRules = ['string', 'numeric', 'integer', 'array', 'email', 'url', 'boolean', 'image', 'file'];
        if (empty(array_intersect($typeRules, $clean))) {
            if ($fieldType === 'number') {
                $clean[] = 'numeric';
            } elseif (in_array($fieldType, ['checkbox_group', 'multiple_images'], true)) {
                $clean[] = 'array';
            } elseif ($fieldType === 'email') {
                $clean[] = 'email';
            } elseif ($fieldType === 'url') {
                $clean[] = 'url';
            } elseif (! in_array($fieldType, ['image', 'file'], true)) {
                $clean[] = 'string';
            }
        }

        return array_values(array_unique($clean));
    }
}
