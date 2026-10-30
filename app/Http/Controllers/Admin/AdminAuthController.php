<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Process an admin login attempt with rate limiting and security checks.
     */
    public function login(Request $request): RedirectResponse
    {
        // 1. Validation
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $throttleKey = Str::transliterate(Str::lower($email).'|'.$request->ip());

        // 2. Check Rate Limiter (Max 5 attempts per 60 seconds)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            AdminActivity::log(
                'rate_limited',
                null,
                $email,
                "Admin login rate limit triggered. Blocked for {$seconds} seconds.",
                $request
            );

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => "Too many failed login attempts. Please try again in {$seconds} seconds.",
                ]);
        }

        $remember = $request->boolean('remember');

        // 3. Attempt Authentication via admin guard
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $credentials['password']], $remember)) {
            $user = Auth::guard('admin')->user();

            // 4. Check if user is an Administrator
            if (! $user->isAdmin()) {
                AdminActivity::log(
                    'login_unauthorized_role',
                    $user,
                    $email,
                    'User attempted admin login without admin privileges.',
                    $request
                );

                Auth::guard('admin')->logout();

                RateLimiter::hit($throttleKey, 60);

                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors([
                        'email' => 'Access denied: This account does not possess administrator privileges.',
                    ]);
            }

            // 5. Success: clear throttle, regenerate session, log activity
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            AdminActivity::log(
                'login_success',
                $user,
                $email,
                'Administrator authenticated successfully.',
                $request
            );

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Welcome back to the IDFR Executive Portal.');
        }

        // 6. Failed Authentication: increment throttle, log attempt
        RateLimiter::hit($throttleKey, 60);

        AdminActivity::log(
            'login_failed',
            null,
            $email,
            'Failed administrative login attempt with invalid credentials.',
            $request
        );

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'The provided credentials do not match our administrative records.',
            ]);
    }

    /**
     * Log out the administrator.
     * Only clears the admin guard session, preserving any concurrent public user session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::guard('admin')->user();

        if ($user) {
            AdminActivity::log(
                'logout',
                $user,
                $user->email,
                'Administrator logged out.',
                $request
            );
        }

        Auth::guard('admin')->logout();
        $request->session()->forget(Auth::guard('admin')->getName());
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'You have been logged out securely.');
    }
}
