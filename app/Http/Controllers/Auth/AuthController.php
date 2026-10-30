<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\RegistrationPaymentPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\s\-]{8,20}$/'],
            'role' => ['required', 'string', Rule::in(UserRole::registrationValues())],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'role.in' => 'Please select a valid registration role.',
            'terms.accepted' => 'Please accept the terms and conditions to continue.',
            'phone.regex' => 'Please enter a valid phone number (8 to 20 digits).',
        ]);

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
    public function verifyOtp(Request $request): JsonResponse|RedirectResponse
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

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

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
    public function resendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

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
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

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
}
