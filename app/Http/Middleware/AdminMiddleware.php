<?php

namespace App\Http\Middleware;

use App\Models\AdminActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Must be authenticated via the dedicated admin guard
        if (! Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated administrative access.'], 401);
            }

            return redirect()->guest(route('admin.login'))->with('error', 'Authentication required to access the admin portal.');
        }

        $user = Auth::guard('admin')->user();

        // 2. Must possess administrator privileges
        if (! $user || ! $user->isAdmin()) {
            AdminActivity::log(
                'unauthorized_access_attempt',
                $user,
                $user?->email,
                'Attempted to access admin route: '.$request->path(),
                $request
            );

            Auth::guard('admin')->logout();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied: Administrator privileges required.'], 403);
            }

            return redirect()->route('admin.login')->with('error', 'Access denied: Your account does not have administrator privileges.');
        }

        // Set the active guard for this request to 'admin'
        Auth::shouldUse('admin');

        return $next($request);
    }
}
