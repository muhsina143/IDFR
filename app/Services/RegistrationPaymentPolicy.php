<?php

namespace App\Services;

use App\Models\User;

/**
 * RegistrationPaymentPolicy
 *
 * Provides a clean separation of concerns for future payment gateway integration.
 * Currently, all roles are exempt and payment is completely inactive.
 * When payment is introduced in a future phase, role requirements can be configured
 * here without rebuilding the registration or OTP verification workflow.
 */
class RegistrationPaymentPolicy
{
    /**
     * Determine if a user's role requires payment.
     * In Phase 2, this unconditionally returns false for all roles.
     */
    public function requiresPayment(User $user): bool
    {
        // Future Phase: Return true for roles requiring paid registration or subscription fees
        // e.g. return in_array($user->role, config('services.payment.paid_roles', []), true);
        return false;
    }

    /**
     * Check if user is eligible to access their full role features based on payment status.
     */
    public function isEligible(User $user): bool
    {
        if (! $this->requiresPayment($user)) {
            return true;
        }

        return in_array($user->payment_status, ['paid', 'exempt', 'waived'], true);
    }

    /**
     * Get future payment configuration for a specific role.
     *
     * @return array{required: bool, amount: int, currency: string}
     */
    public function getRolePaymentConfig(string $role): array
    {
        return [
            'required' => false,
            'amount' => 0,
            'currency' => 'INR',
        ];
    }
}
