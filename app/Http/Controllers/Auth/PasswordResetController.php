<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Send a password reset link to the given user email.
     */
    public function sendResetLinkEmail(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
        ], [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        $email = strtolower(trim($validated['email']));
        $throttleKey = 'forgot-password:'.Str::transliterate($email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $message = "Too many password reset requests. Please try again in {$seconds} seconds.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 429);
            }

            return back()->withErrors(['email' => $message])->withInput();
        }

        RateLimiter::hit($throttleKey, 60);

        // Safe generic message to prevent account enumeration
        $genericSuccessMessage = 'If an account exists with this email address, we have sent a password reset link.';

        $user = User::where('email', $email)->first();

        // 1. If user doesn't exist, return safe generic success response
        if (! $user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $genericSuccessMessage,
                ], 200);
            }

            return back()->with('status', $genericSuccessMessage);
        }

        // 2. Strict Administrator Isolation: administrative accounts must not use public reset flow
        if ($user->isAdmin() && ($user->role === 'admin' || empty($user->role))) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $genericSuccessMessage,
                ], 200);
            }

            return back()->with('status', $genericSuccessMessage);
        }

        // 3. Generate secure reset token via Laravel Password Broker
        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // 4. Send the branded reset email using configured SMTP service
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $resetUrl));
        } catch (\Throwable $e) {
            Log::error('Password reset email sending failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            $errorMsg = 'Failed to deliver the password reset email. Please try again later or contact IDFR support.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 500);
            }

            return back()->withErrors(['email' => $errorMsg])->withInput();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'A password reset link has been sent to your email address.',
            ], 200);
        }

        return back()->with('status', 'A password reset link has been sent to your email address.');
    }

    /**
     * Display the password reset view.
     */
    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ]);

        $credentials = $request->only('email', 'password', 'password_confirmation', 'token');
        $credentials['email'] = strtolower(trim($credentials['email']));

        $status = Password::broker()->reset(
            $credentials,
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $successMsg = 'Your password has been reset successfully. You can now log in with your new password.';

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMsg,
                ], 200);
            }

            return redirect()->route('password.reset', ['token' => 'completed'])
                ->with('status', $successMsg);
        }

        $errorMessage = match ($status) {
            Password::INVALID_USER => "We can't find a registered user with that email address.",
            Password::INVALID_TOKEN => 'This password reset link is invalid or has already been used.',
            default => 'This password reset token is invalid or has expired.',
        };

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 422);
        }

        return back()->withErrors(['email' => $errorMessage])->withInput($request->only('email'));
    }
}
