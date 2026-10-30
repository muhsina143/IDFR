<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ModelProfile;
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

        // Connect pre-existing seeded profile by name if present and unassigned
        if (! $model) {
            $model = ModelProfile::where('name', $user->name)->whereNull('user_id')->first();
            if ($model) {
                $model->user_id = $user->id;
                $model->save();
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
            'age' => ['required', 'integer', 'min:16', 'max:60'],
            'height' => ['required', 'numeric', 'min:120', 'max:215'],
            'category' => ['required', 'string', 'in:experienced,new-face,Experienced,New Face'],
            'experience' => ['required', 'numeric', 'min:0', 'max:40'],
            'skills' => ['required', 'string', 'min:3'],
            'modelType' => ['required', 'array', 'min:1'],
            'modelType.*' => ['string'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:150'],
            'about' => ['nullable', 'string'],
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
            $existingModel = ModelProfile::where('name', $user->name)->whereNull('user_id')->first();
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
                'description' => $validated['about'] ?? null,
                'outstation' => strtolower($validated['outstation']),
                'international' => strtolower($validated['international']),
                'availability' => 'Outstation: '.ucfirst(strtolower($validated['outstation'])).', International: '.ucfirst(strtolower($validated['international'])),
                'specialities' => [
                    'outstation' => strtolower($validated['outstation']),
                    'international' => strtolower($validated['international']),
                ],
                'profile_photo' => $photoPath,
                'portfolio_images' => array_values($portfolio),
                'status' => 'published',
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
        if ($redirect = $this->authorizeRole('fashion-designer')) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();
        $designer = Designer::where('user_id', $user->id)->first();

        if (! $designer) {
            $designer = Designer::where('name', $user->name)->whereNull('user_id')->first();
            if ($designer) {
                $designer->user_id = $user->id;
                $designer->save();
            }
        }

        return view('profiles.fashion-designer', compact('designer', 'user'));
    }

    /**
     * Save the Fashion Designer Profile.
     */
    public function saveDesigner(Request $request): JsonResponse|RedirectResponse
    {
        if ($redirect = $this->authorizeRole('fashion-designer')) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'brand' => ['nullable', 'string', 'max:150'],
            'place' => ['required', 'string', 'max:100'],
            'experience' => ['required', 'numeric', 'min:0', 'max:40'],
            'phone' => ['required', 'string', 'max:25'],
            'email' => ['required', 'email', 'max:150'],
            'about' => ['required', 'string', 'min:20'],
            'atelier' => ['nullable', 'string'],
            'atelier_notes' => ['nullable', 'string'],
            'service' => ['required', 'array', 'min:1'],
            'service.*' => ['string'],
            'outstation' => ['required', 'in:yes,no,Yes,No'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'service.required' => 'Please select at least one design specialisation.',
            'about.min' => 'Please write at least 20 characters about your design focus.',
        ]);

        $existingDesigner = Designer::where('user_id', $user->id)->first();
        if (! $existingDesigner) {
            $existingDesigner = Designer::where('name', $user->name)->whereNull('user_id')->first();
        }

        $photoPath = $existingDesigner?->profile_photo;
        $uploadDir = public_path('uploads/designers');
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'des_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $photoPath = 'uploads/designers/'.$filename;
        }

        $portfolio = $existingDesigner?->portfolio_images ?? [];
        if (! is_array($portfolio)) {
            $portfolio = [];
        }

        if ($request->hasFile('portfolio_image_1')) {
            $f1 = $request->file('portfolio_image_1');
            $fn1 = 'des_port_'.time().'_1_'.Str::random(8).'.'.$f1->getClientOriginalExtension();
            $f1->move($uploadDir, $fn1);
            $portfolio[0] = ['image' => 'uploads/designers/'.$fn1, 'caption' => 'Runway Look 1'];
        }

        if ($request->hasFile('portfolio_image_2')) {
            $f2 = $request->file('portfolio_image_2');
            $fn2 = 'des_port_'.time().'_2_'.Str::random(8).'.'.$f2->getClientOriginalExtension();
            $f2->move($uploadDir, $fn2);
            $portfolio[1] = ['image' => 'uploads/designers/'.$fn2, 'caption' => 'Runway Look 2'];
        }

        if ($request->hasFile('portfolio_images')) {
            foreach ($request->file('portfolio_images') as $file) {
                $pFilename = 'des_port_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
                $file->move($uploadDir, $pFilename);
                $portfolio[] = ['image' => 'uploads/designers/'.$pFilename, 'caption' => 'Runway Look'];
            }
        }

        $designer = Designer::updateOrCreate(
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
                'atelier_notes' => trim((string) ($validated['atelier'] ?? $validated['atelier_notes'] ?? '')),
                'services' => $validated['service'],
                'outstation' => strtolower($validated['outstation']),
                'profile_photo' => $photoPath,
                'portfolio_images' => array_values($portfolio),
                'status' => 'published',
            ]
        );

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

        $message = "Thank you, {$user->name}. Your fashion designer profile has been saved successfully.";

        if ($request->expectsJson() || $request->ajax()) {
            $freshDesigner = $designer->fresh();

            return response()->json([
                'success' => true,
                'message' => $message,
                'profile' => $freshDesigner,
                'photo_url' => $freshDesigner->photo_url,
                'profile_completion' => $user->fresh()->profileCompletion(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display a Creative Professional profile page.
     */
    protected function showCreativeProfile(string $category): View|RedirectResponse
    {
        if ($redirect = $this->authorizeRole($category)) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();
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

        return view("profiles.{$category}", compact('profile', 'user'));
    }

    /**
     * Save a Creative Professional profile.
     */
    protected function handleSaveCreative(Request $request, string $category): JsonResponse|RedirectResponse
    {
        if ($redirect = $this->authorizeRole($category)) {
            return $redirect;
        }

        $user = Auth::guard('web')->user();

        $validated = $request->validate([
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
            'service' => ['required', 'array', 'min:1'],
            'service.*' => ['string'],
            'outstation' => ['required', 'in:yes,no,Yes,No'],
            'brand' => ['nullable', 'string', 'max:100'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_1' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_image_2' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'service.required' => 'Please select at least one specialisation or service.',
            'about.min' => 'Please write at least 20 characters in your bio.',
        ]);

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
            ?? '';

        $roleLabel = UserRole::tryFrom($category)?->label()
            ?? CreativeProfessional::categories()[$category]
            ?? ucfirst(str_replace('-', ' ', $category));

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
                'services' => $validated['service'],
                'outstation' => strtolower($validated['outstation']),
                'profile_photo' => $photoPath,
                'portfolio_images' => array_values($portfolio),
                'status' => 'published',
            ]
        );

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
}
