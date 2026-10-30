<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'phone',
    'password',
    'is_admin',
    'role',
    'email_verified_at',
    'payment_status',
    'is_active',
    'deactivated_at',
    'deactivation_reason',
    'email_otp_hash',
    'email_otp_expires_at',
    'email_otp_sent_at',
    'email_otp_attempts',
])]
#[Hidden(['password', 'remember_token', 'email_otp_hash'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'deactivated_at' => 'datetime',
            'email_otp_expires_at' => 'datetime',
            'email_otp_sent_at' => 'datetime',
            'email_otp_attempts' => 'integer',
        ];
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    /**
     * Check if user is an administrator.
     */
    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin || $this->role === 'admin');
    }

    /**
     * Check if the user has completed email OTP verification.
     */
    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Get human-readable role label.
     */
    public function roleLabel(): string
    {
        return UserRole::tryFrom($this->role)?->label() ?? ucfirst($this->role);
    }

    /**
     * Check if user meets payment eligibility rules.
     * Ready for future payment phases.
     */
    public function isPaymentEligible(): bool
    {
        return in_array($this->payment_status, ['exempt', 'paid', 'waived'], true);
    }

    /**
     * Get the UserRole enum instance for this user.
     */
    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFrom((string) $this->role);
    }

    /**
     * Get the destination URL for this user's role profile.
     */
    public function profileUrl(): string
    {
        $roleEnum = $this->roleEnum();
        if ($roleEnum) {
            return route($roleEnum->profileRouteName());
        }

        return route('idfr.home');
    }

    /**
     * Calculate the user's profile completion percentage and status.
     *
     * @return array{percentage: int, label: string, status: string, is_complete: bool}
     */
    public function profileCompletion(): array
    {
        $percentage = 0;

        // 1. Basic registration fields (Name & Role) (20%)
        $hasName = ! empty(trim((string) $this->name));
        if ($hasName) {
            $percentage += 20;
        }

        // 2. Email verification (25%)
        $hasVerifiedEmail = $this->isEmailVerified();
        if ($hasVerifiedEmail) {
            $percentage += 25;
        }

        // 3. Contact information (Phone number) (20%)
        $hasPhone = ! empty(trim((string) $this->phone));
        if ($hasPhone) {
            $percentage += 20;
        }

        // 4. Role selection (20%)
        $hasRole = ! empty(trim((string) $this->role));
        if ($hasRole) {
            $percentage += 20;
        }

        // 5. Extended role profile presence / portfolio submission (15%)
        $hasExtendedProfile = false;
        if (! empty($this->role)) {
            if ($this->role === 'model') {
                $hasExtendedProfile = ModelProfile::where('user_id', $this->id)->exists()
                    || ModelProfile::where('name', $this->name)->exists();
            } elseif ($this->role === 'fashion-designer') {
                $hasExtendedProfile = Designer::where('user_id', $this->id)->exists()
                    || Designer::where('name', $this->name)->exists();
            } else {
                $hasExtendedProfile = CreativeProfessional::where('user_id', $this->id)->exists()
                    || CreativeProfessional::where('name', $this->name)->exists();
            }
        }

        if ($hasExtendedProfile) {
            $percentage += 15;
        }

        $percentage = min(100, $percentage);
        $isComplete = $percentage >= 100;
        $label = $isComplete ? 'Complete (100%)' : "Incomplete ({$percentage}%)";

        return [
            'percentage' => $percentage,
            'label' => $label,
            'status' => $isComplete ? 'complete' : 'incomplete',
            'is_complete' => $isComplete,
            'steps' => [
                'name' => $hasName,
                'email_verified' => $hasVerifiedEmail,
                'phone' => $hasPhone,
                'role' => $hasRole,
                'extended_profile' => $hasExtendedProfile,
            ],
        ];
    }

    /**
     * User's model profile relationship.
     */
    public function modelProfile(): HasOne
    {
        return $this->hasOne(ModelProfile::class, 'user_id');
    }

    /**
     * User's designer profile relationship.
     */
    public function designerProfile(): HasOne
    {
        return $this->hasOne(Designer::class, 'user_id');
    }

    /**
     * User's creative professional profile relationship.
     */
    public function creativeProfile(): HasOne
    {
        return $this->hasOne(CreativeProfessional::class, 'user_id');
    }

    /**
     * Admin activities relationship.
     */
    public function adminActivities(): HasMany
    {
        return $this->hasMany(AdminActivity::class);
    }
}
