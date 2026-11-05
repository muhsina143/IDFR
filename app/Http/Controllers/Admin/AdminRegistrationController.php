<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\CreativeProfessional;
use App\Models\ProfessionalCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminRegistrationController extends Controller
{
    /**
     * Display a listing of registered users with search and filters.
     */
    public function index(Request $request): View
    {
        $query = User::query()->where('is_admin', false)->where('role', '!=', 'admin');

        // 1. Search by name, email, or phone
        if ($request->filled('search')) {
            $term = trim((string) $request->input('search'));
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        // 2. Filter by Role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // 3. Filter by Email Verification Status
        if ($request->filled('verified')) {
            if ($request->input('verified') === '1') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->input('verified') === '0') {
                $query->whereNull('email_verified_at');
            }
        }

        // 4. Filter by Account Status (Active vs Deactivated)
        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where(function ($q) {
                    $q->where('is_active', true)->orWhereNull('is_active');
                });
            } elseif ($request->input('status') === 'deactivated') {
                $query->where('is_active', false);
            }
        }

        // 5. Filter by Profile Approval Status (Pending, Published, Rejected)
        if ($request->filled('approval_status')) {
            $st = $request->input('approval_status');
            $query->where(function ($q) use ($st) {
                $q->whereHas('modelProfile', fn ($mq) => $mq->where('status', $st))
                    ->orWhereHas('creativeProfile', fn ($cq) => $cq->where('status', $st))
                    ->orWhereHas('designerProfile', fn ($dq) => $dq->where('status', $st));
            });
        }

        // 6. Aggregate metrics for header stats
        $stats = [
            'total_users' => User::where('is_admin', false)->where('role', '!=', 'admin')->count(),
            'verified_users' => User::where('is_admin', false)->where('role', '!=', 'admin')->whereNotNull('email_verified_at')->count(),
            'models' => User::where('role', UserRole::Model->value)->count(),
            'designers' => User::where('role', UserRole::FashionDesigner->value)->count(),
            'creative_pros' => User::where('is_admin', false)->whereNotIn('role', ['admin', 'model', 'fashion-designer'])->count(),
            'deactivated' => User::where('is_active', false)->count(),
            'pending_approval' => User::where('is_admin', false)->where('role', '!=', 'admin')->where(function ($q) {
                $q->whereHas('modelProfile', fn ($mq) => $mq->where('status', 'pending'))
                    ->orWhereHas('creativeProfile', fn ($cq) => $cq->where('status', 'pending'))
                    ->orWhereHas('designerProfile', fn ($dq) => $dq->where('status', 'pending'));
            })->count(),
        ];

        // 7. Paginate results
        $users = $query->latest()->paginate(15)->withQueryString();

        $roles = UserRole::cases();

        return view('admin.registrations.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Display the specified user and role-specific profile data.
     */
    public function show(User $user): View
    {
        // 1. Calculate profile completion details
        $completion = $user->profileCompletion();

        // 2. Load role-specific profile data
        $roleProfile = $user->getProfileRecord();
        $roleType = $user->role;

        // Load dynamic form fields if creative category
        $categoryModel = null;
        $formFields = collect();
        if ($roleProfile instanceof CreativeProfessional) {
            $categoryModel = ProfessionalCategory::where('slug', $roleProfile->category)->first();
            $formFields = $categoryModel?->getActiveFormFields() ?? collect();
        }

        // 3. Load recent administrative audit telemetry for this user
        $auditActivities = AdminActivity::where('user_id', $user->id)
            ->orWhere('description', 'like', "%user #{$user->id}%")
            ->latest()
            ->take(8)
            ->get();

        return view('admin.registrations.show', compact(
            'user',
            'completion',
            'roleProfile',
            'roleType',
            'categoryModel',
            'formFields',
            'auditActivities'
        ));
    }

    /**
     * Update the profile approval status (Accept / Reject / Pending / Draft / Archived).
     */
    public function updateApprovalStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:published,approved,pending,rejected,draft,archived'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = ($validated['status'] === 'approved') ? 'published' : $validated['status'];
        $roleProfile = $user->getProfileRecord();

        if ($roleProfile) {
            $roleProfile->status = $newStatus;
            $roleProfile->save();
        }

        // Also sync any sibling profiles
        if ($user->role === 'fashion-designer') {
            if ($user->designerProfile) {
                $user->designerProfile->update(['status' => $newStatus]);
            }
            if ($user->creativeProfile) {
                $user->creativeProfile->update(['status' => $newStatus]);
            }
        } elseif ($user->role === 'model') {
            if ($user->modelProfile) {
                $user->modelProfile->update(['status' => $newStatus]);
            }
        } else {
            if ($user->creativeProfile) {
                $user->creativeProfile->update(['status' => $newStatus]);
            }
        }

        if ($newStatus === 'rejected' && ! empty($validated['rejection_reason'])) {
            $user->update(['deactivation_reason' => $validated['rejection_reason']]);
        }

        $adminUser = Auth::guard('admin')->user() ?? Auth::user();

        if (class_exists(AdminActivity::class)) {
            AdminActivity::create([
                'user_id' => $adminUser?->id,
                'activity' => 'profile_approval_updated',
                'description' => "Profile for member {$user->name} (#{$user->id}, role: {$user->role}) status updated to {$newStatus}".(! empty($validated['rejection_reason']) ? " (Reason: {$validated['rejection_reason']})" : ''),
                'ip_address' => $request->ip(),
            ]);
        }

        $msg = match ($newStatus) {
            'published' => "Profile for {$user->name} has been accepted and is now published on the main website.",
            'rejected' => "Profile for {$user->name} has been rejected and remains hidden from the main website.",
            'pending' => "Profile for {$user->name} has been set to Pending Approval.",
            default => "Profile status updated to {$newStatus}.",
        };

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Show the form for editing the user details.
     */
    public function edit(User $user): View
    {
        $roles = UserRole::cases();

        return view('admin.registrations.edit', compact('user', 'roles'));
    }

    /**
     * Update the user's account details and status.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'in:'.implode(',', UserRole::registrationValues())],
            'payment_status' => ['required', 'string', 'in:exempt,paid,waived,pending'],
            'is_active' => ['required', 'boolean'],
            'deactivation_reason' => ['nullable', 'string', 'max:255'],
            'email_verified' => ['nullable', 'boolean'],
        ]);

        $oldActive = $user->isActive();
        $newActive = (bool) $validated['is_active'];

        $user->name = trim($validated['name']);
        $user->email = strtolower(trim($validated['email']));
        $user->phone = ! empty($validated['phone']) ? trim($validated['phone']) : null;
        $user->role = $validated['role'];
        $user->payment_status = $validated['payment_status'];
        $user->is_active = $newActive;

        if ($oldActive && ! $newActive) {
            $user->deactivated_at = now();
            $user->deactivation_reason = $validated['deactivation_reason'] ?: 'Deactivated by administrator.';
        } elseif (! $oldActive && $newActive) {
            $user->deactivated_at = null;
            $user->deactivation_reason = null;
        }

        if (isset($validated['email_verified'])) {
            if ($validated['email_verified'] && ! $user->isEmailVerified()) {
                $user->email_verified_at = now();
            } elseif (! $validated['email_verified']) {
                $user->email_verified_at = null;
            }
        }

        $user->save();

        if (class_exists(AdminActivity::class) && Auth::guard('admin')->check()) {
            AdminActivity::create([
                'user_id' => Auth::guard('admin')->id(),
                'activity' => 'user_updated',
                'description' => "Updated user profile #{$user->id} ({$user->name}, {$user->email}).",
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('admin.registrations.show', $user)
            ->with('success', "User account for {$user->name} updated successfully.");
    }

    /**
     * Toggle active/deactivated status for a user.
     */
    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::guard('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $newState = ! $user->isActive();
        $reason = $request->input('reason', $newState ? null : 'Account deactivated by administrative action.');

        $user->is_active = $newState;
        $user->deactivated_at = $newState ? null : now();
        $user->deactivation_reason = $newState ? null : $reason;
        $user->save();

        $actionWord = $newState ? 'activated' : 'deactivated';

        if (class_exists(AdminActivity::class) && Auth::guard('admin')->check()) {
            AdminActivity::create([
                'user_id' => Auth::guard('admin')->id(),
                'activity' => $newState ? 'user_activated' : 'user_deactivated',
                'description' => ucfirst($actionWord)." user account #{$user->id} ({$user->name}).",
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->back()->with('success', "User account for {$user->name} has been {$actionWord}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::guard('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own administrative account.');
        }

        $userName = $user->name;
        $userId = $user->id;

        $user->delete();

        if (class_exists(AdminActivity::class) && Auth::guard('admin')->check()) {
            AdminActivity::create([
                'user_id' => Auth::guard('admin')->id(),
                'activity' => 'user_deleted',
                'description' => "Deleted user account #{$userId} ({$userName}).",
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('admin.registrations.index')
            ->with('success', "User account for {$userName} was permanently removed.");
    }
}
