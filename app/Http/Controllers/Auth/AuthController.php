<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\CreativeProfessional;
use App\Models\Designer;
use App\Models\ModelProfile;
use App\Models\ProfessionalCategory;
use App\Models\User;
use App\Services\OtpService;
use App\Services\RegistrationPaymentPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct(
        protected OtpService $otpService,
        protected RegistrationPaymentPolicy $paymentPolicy
    ) {}

    /**
     * Handle user registration with single-role selection and email OTP dispatch.
     * Safely updates unverified accounts on retry without creating duplicate rows.
     */
    public function register(\App\Http\Requests\Auth\RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $email = strtolower(trim($validated['email']));
        $existingUser = User::where('email', $email)->first();

        if ($existingUser && $existingUser->isEmailVerified()) {
            throw ValidationException::withMessages([
                'email' => ['An account with this email address already exists.'],
            ]);
        }

        if ($existingUser) {
            // Update unverified user registration details (safe retry, no duplicate account created)
            $existingUser->update([
                'name' => trim($validated['name']),
                'phone' => trim($validated['phone']),
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);
            $user = $existingUser;
        } else {
            $user = User::create([
                'name' => trim($validated['name']),
                'email' => $email,
                'phone' => trim($validated['phone']),
                'role' => $validated['role'],
                'password' => Hash::make($validated['password']),
                'is_admin' => false,
                'email_verified_at' => null,
                'payment_status' => 'exempt',
            ]);
        }

        if (! empty($validated['model_type'])) {
            session(['registration_model_type' => $validated['model_type']]);
        }

        $this->ensureProfileExists($user, $validated['model_type'] ?? null);

        $otpResult = $this->otpService->sendOtp($user);

        if (! $otpResult['email_sent']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'email' => $user->email,
                    'email_sent' => false,
                    'can_retry' => true,
                    'message' => $otpResult['message'],
                ], 502);
            }

            return redirect()->back()->withErrors([
                'email' => $otpResult['message'],
            ])->withInput();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'email' => $user->email,
                'email_sent' => true,
                'message' => $otpResult['message'],
                'resend_cooldown' => OtpService::RESEND_COOLDOWN_SECONDS,
            ], 201);
        }

        return redirect()->back()->with([
            'status' => $otpResult['message'],
            'pending_verification_email' => $user->email,
        ]);
    }

    /**
     * Verify the 6-digit email OTP.
     */
    public function verifyOtp(\App\Http\Requests\Auth\VerifyOtpRequest $request): JsonResponse|RedirectResponse
    {
        $throttleKey = 'verify-otp:'.Str::transliterate(Str::lower($request->input('email', '')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = "Too many verification requests. Please try again in {$seconds} seconds.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 429);
            }

            return redirect()->back()->withErrors(['otp' => $message]);
        }

        RateLimiter::hit($throttleKey, 60);

        $validated = $request->validated();

        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        if (! $user) {
            $message = 'No registered account found with this email address.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->withErrors(['email' => $message]);
        }

        $result = $this->otpService->verifyOtp($user, $validated['otp']);

        if (! $result['success']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'status' => $result['status'],
                    'message' => $result['message'],
                    'remaining_attempts' => $result['remaining_attempts'] ?? null,
                ], 422);
            }

            return redirect()->back()->withErrors(['otp' => $result['message']]);
        }

        RateLimiter::clear($throttleKey);

        // Log public user into web guard upon successful verification
        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        if (! empty($validated['model_type'])) {
            session(['registration_model_type' => $validated['model_type']]);
        }

        $this->ensureProfileExists($user, $validated['model_type'] ?? null);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $result['status'],
                'message' => $result['message'],
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initial' => strtoupper(substr($user->name, 0, 1)),
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->roleLabel(),
                    'profile_url' => $user->profileUrl(),
                    'profile_completion' => $user->profileCompletion(),
                ],
            ]);
        }

        return redirect()->intended('/')->with('status', $result['message']);
    }

    /**
     * Resend a fresh 6-digit email OTP respecting the 60-second cooldown.
     */
    public function resendOtp(\App\Http\Requests\Auth\ResendOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'No account found with this email address.'], 422);
        }

        if ($user->isEmailVerified()) {
            return response()->json(['success' => true, 'message' => 'Email is already verified. You can now log in.'], 200);
        }

        $result = $this->otpService->sendOtp($user);

        if (! $result['success']) {
            $status = ($result['cooldown_remaining'] ?? 0) > 0 ? 429 : 502;

            return response()->json([
                'success' => false,
                'email_sent' => false,
                'message' => $result['message'],
                'cooldown_remaining' => $result['cooldown_remaining'] ?? 0,
            ], $status);
        }

        if (! ($result['email_sent'] ?? false)) {
            return response()->json([
                'success' => false,
                'email_sent' => false,
                'message' => $result['message'],
                'cooldown_remaining' => 0,
            ], 502);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'email_sent' => true,
            'resend_cooldown' => OtpService::RESEND_COOLDOWN_SECONDS,
        ]);
    }

    /**
     * Handle public user authentication.
     * Prevents login if the user has not verified their email,
     * and redirects admin accounts to the dedicated admin portal.
     */
    public function login(\App\Http\Requests\Auth\LoginRequest $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validated();

        $email = strtolower(trim($credentials['email']));
        $throttleKey = 'user-login:'.Str::transliterate($email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = "Too many login attempts. Please try again in {$seconds} seconds.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 429);
            }

            return redirect()->back()->withErrors(['email' => $message]);
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            $message = 'Invalid email address or password.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->withErrors(['email' => $message]);
        }

        // Dedicated separation: administrative-only accounts must use the admin portal
        if ($user->isAdmin() && ($user->role === 'admin' || empty($user->role))) {
            $msg = 'Administrator accounts must log in via the Executive Admin Portal at /admin/login.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return redirect()->back()->withErrors(['email' => $msg]);
        }

        // Check account active status - Deactivated accounts cannot log in
        if (! $user->isActive()) {
            $reason = $user->deactivation_reason ?: 'Your account has been deactivated by an administrator. Please contact IDFR support.';
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'account_deactivated' => true,
                    'message' => $reason,
                ], 403);
            }

            return redirect()->back()->withErrors(['email' => $reason]);
        }

        // Check email verification status - Unverified users cannot log in
        if (! $user->isEmailVerified()) {
            $cooldown = $this->otpService->resendCooldownRemaining($user);
            $otpRes = null;
            if ($cooldown === 0) {
                $otpRes = $this->otpService->sendOtp($user);
            }

            $message = ($otpRes && ! $otpRes['email_sent'])
                ? 'Please verify your email address before logging in. Note: email delivery failed; please verify mail settings.'
                : 'Please verify your email address before logging in. A verification code has been sent to your inbox.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'requires_verification' => true,
                    'email' => $user->email,
                    'message' => $message,
                    'resend_cooldown' => $this->otpService->resendCooldownRemaining($user),
                ], 403);
            }

            return redirect()->back()->with([
                'requires_verification' => true,
                'email' => $user->email,
                'error' => $message,
            ]);
        }

        RateLimiter::clear($throttleKey);
        Auth::guard('web')->login($user, (bool) $request->boolean('remember'));
        $request->session()->regenerate();
        if (! empty($validated['model_type'])) {
            session(['registration_model_type' => $validated['model_type']]);
        }

        $this->ensureProfileExists($user, $validated['model_type'] ?? null);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Welcome back, '.$user->name.'!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'initial' => strtoupper(substr($user->name, 0, 1)),
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->roleLabel(),
                    'profile_url' => $user->profileUrl(),
                    'profile_completion' => $user->profileCompletion(),
                    'is_admin' => $user->isAdmin(),
                ],
            ]);
        }

        return redirect()->intended('/')->with('status', 'Welcome back, '.$user->name.'!');
    }

    /**
     * Handle public user logout.
     * Only clears the web guard session, preserving any concurrent admin session.
     */
    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->forget(Auth::guard('web')->getName());
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
        }

        return redirect('/')->with('status', 'Logged out successfully.');
    }

    /**
     * Get currently authenticated public user status.
     * Evaluates only the web guard so admin logins never leak into the public site.
     */
    public function me(Request $request): JsonResponse
    {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return response()->json([
                'authenticated' => false,
                'user' => null,
            ]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'initial' => strtoupper(substr($user->name, 0, 1)),
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'role_label' => $user->roleLabel(),
                'profile_url' => $user->profileUrl(),
                'profile_completion' => $user->profileCompletion(),
                'is_admin' => $user->isAdmin(),
                'email_verified' => $user->isEmailVerified(),
                'payment_status' => $user->payment_status,
            ],
        ]);
    }

    /**
     * Redirect the user to the Google OAuth consent screen.
     */
    public function redirectToGoogle(): \Symfony\Component\HttpFoundation\RedirectResponse|RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google OAuth.
     * Public website authentication only. Rejects administrator accounts.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            Log::warning('Google OAuth callback failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('idfr.home')->with('error', 'Unable to authenticate with Google. Please try again.');
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));

        if (empty($email)) {
            return redirect()->route('idfr.home')->with('error', 'Google did not provide a valid email address.');
        }

        $googleId = (string) $googleUser->getId();
        $avatar = (string) $googleUser->getAvatar();

        // 1. Locate existing registered user by email or matching google_id
        $user = User::where('email', $email)->first();
        if (! $user && ! empty($googleId)) {
            $user = User::where('google_id', $googleId)->first();
        }

        // 2. Reject Unregistered Users: Only already registered users are allowed to log in via Google
        if (! $user) {
            return redirect()->route('idfr.home', ['auth' => 'login'])->with('error', 'You are not registered yet. Please register first to access your account.');
        }

        // 3. Strict Administrator Isolation: admin accounts must never log in via public Google OAuth
        if ($user->isAdmin()) {
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Administrator accounts must log in via the Executive Admin Portal.',
            ]);
        }

        // 4. Deactivated accounts check
        if (! $user->isActive()) {
            $reason = $user->deactivation_reason ?: 'Your account has been deactivated. Please contact IDFR support.';

            return redirect()->route('idfr.home', ['auth' => 'login'])->with('error', $reason);
        }

        // 5. Existing user: Link Google ID and update avatar/verification
        $needsSave = false;

        if (empty($user->google_id) && ! empty($googleId)) {
            $user->google_id = $googleId;
            $needsSave = true;
        }

        if (empty($user->avatar) && ! empty($avatar)) {
            $user->avatar = $avatar;
            $needsSave = true;
        }

        if (! $user->isEmailVerified()) {
            $user->email_verified_at = now();
            $needsSave = true;
        }

        if ($needsSave) {
            $user->save();
        }

        $this->ensureProfileExists($user);

        // 6. Authenticate strictly into the public web guard
        Auth::guard('web')->login($user, true);
        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended', route('idfr.home'));
        if (str_contains($intended, '/admin')) {
            $intended = route('idfr.home');
        }

        return redirect()->to($intended)->with('status', 'Welcome back, '.$user->name.'!');
    }

    /**
     * Ensure dedicated role profile exists for the user.
     * All newly created member profiles default to 'pending' approval status.
     */
    protected function ensureProfileExists(User $user, ?string $modelType = null): void
    {
        if (empty($user->role) || in_array($user->role, ['admin', 'user'], true)) {
            return;
        }

        $chosenType = $modelType ?: session('registration_model_type');
        $initialType = ! empty($chosenType) ? [ModelProfile::formatType($chosenType)] : ['Commercial Model'];

        if ($user->role === 'model') {
            $model = ModelProfile::where('user_id', $user->id)->first();
            if (! $model) {
                $model = ModelProfile::where('email', $user->email)->whereNull('user_id')->first()
                    ?? (! empty($user->name) ? ModelProfile::where('name', $user->name)->whereNull('user_id')->first() : null);

                if ($model) {
                    $model->user_id = $user->id;
                    if (empty($model->email)) {
                        $model->email = $user->email;
                    }
                    if (empty($model->phone) && ! empty($user->phone)) {
                        $model->phone = $user->phone;
                    }
                    if (! empty($chosenType)) {
                        $model->types = $initialType;
                    }
                    $model->save();
                } else {
                    ModelProfile::create([
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
                        'types' => $initialType,
                        'skills' => ['Runway walk', 'Editorial posing'],
                        'outstation' => 'yes',
                        'international' => 'no',
                        'status' => 'pending',
                    ]);
                }
            } elseif (! empty($chosenType) && (empty($model->types) || $model->types === ['Commercial'])) {
                $model->types = $initialType;
                $model->save();
            }
        } elseif ($user->role === 'fashion-designer') {
            $designer = Designer::where('user_id', $user->id)->first();
            if (! $designer) {
                $designer = Designer::where('email', $user->email)->whereNull('user_id')->first()
                    ?? (! empty($user->name) ? Designer::where('name', $user->name)->whereNull('user_id')->first() : null);

                if ($designer) {
                    $designer->user_id = $user->id;
                    if (empty($designer->email)) {
                        $designer->email = $user->email;
                    }
                    if (empty($designer->phone) && ! empty($user->phone)) {
                        $designer->phone = $user->phone;
                    }
                    $designer->save();
                } else {
                    $designer = Designer::create([
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'brand_name' => $user->name.' Atelier',
                        'email' => $user->email,
                        'phone' => $user->phone ?? '',
                        'location' => '',
                        'experience' => '1',
                        'biography' => '',
                        'status' => 'pending',
                    ]);
                }
            }

            $creative = CreativeProfessional::where('user_id', $user->id)->where('category', 'fashion-designer')->first();
            if (! $creative) {
                CreativeProfessional::create([
                    'user_id' => $user->id,
                    'category' => 'fashion-designer',
                    'name' => $user->name,
                    'brand' => $designer->brand_name ?? ($user->name.' Atelier'),
                    'professional_title' => 'Fashion Designer',
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'location' => $designer->location ?? '',
                    'status' => $designer->status ?? 'pending',
                ]);
            }
        } else {
            // Other Creative Professional categories
            $categorySlug = ($user->role === 'party-event-mnagement') ? 'party-event-management' : $user->role;
            $creative = CreativeProfessional::where('user_id', $user->id)->where('category', $categorySlug)->first();

            if (! $creative) {
                $creative = CreativeProfessional::where('category', $categorySlug)->where('email', $user->email)->whereNull('user_id')->first()
                    ?? (! empty($user->name) ? CreativeProfessional::where('category', $categorySlug)->where('name', $user->name)->whereNull('user_id')->first() : null);

                if ($creative) {
                    $creative->user_id = $user->id;
                    if (empty($creative->email)) {
                        $creative->email = $user->email;
                    }
                    if (empty($creative->phone) && ! empty($user->phone)) {
                        $creative->phone = $user->phone;
                    }
                    $creative->save();
                } else {
                    CreativeProfessional::create([
                        'user_id' => $user->id,
                        'category' => $categorySlug,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '',
                        'professional_title' => $user->roleLabel(),
                        'location' => '',
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }
}
