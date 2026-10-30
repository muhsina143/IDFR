<?php

namespace App\Services;

use App\Mail\EmailVerificationOtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public const EXPIRY_MINUTES = 5;

    public const RESEND_COOLDOWN_SECONDS = 60;

    public const MAX_ATTEMPTS = 5;

    /**
     * Check how many seconds remain before a new OTP can be requested.
     */
    public function resendCooldownRemaining(User $user): int
    {
        if (! $user->email_otp_sent_at) {
            return 0;
        }

        $cooldownEnds = $user->email_otp_sent_at->copy()->addSeconds(self::RESEND_COOLDOWN_SECONDS);

        if (now()->greaterThanOrEqualTo($cooldownEnds)) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($cooldownEnds));
    }

    /**
     * Generate, hash, store, and send an email OTP to the user.
     *
     * @return array{success: bool, email_sent: bool, message: string, cooldown_remaining?: int}
     */
    public function sendOtp(User $user): array
    {
        $cooldown = $this->resendCooldownRemaining($user);
        if ($cooldown > 0) {
            return [
                'success' => false,
                'email_sent' => false,
                'cooldown_remaining' => $cooldown,
                'message' => "Please wait {$cooldown} seconds before requesting a new OTP.",
            ];
        }

        // Generate 6-digit cryptographically secure OTP
        $otp = sprintf('%06d', random_int(0, 999999));

        $user->email_otp_hash = Hash::make($otp);
        $user->email_otp_expires_at = now()->addMinutes(self::EXPIRY_MINUTES);
        $user->email_otp_sent_at = now();
        $user->email_otp_attempts = 0;
        $user->save();

        $emailSent = false;
        $errorMessage = null;
        try {
            Mail::to($user->email)->send(new EmailVerificationOtpMail($otp, $user->name));
            $emailSent = true;
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error('OTP email delivery failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            // Invalidate sent_at so the user is not locked into a resend cooldown
            $user->email_otp_sent_at = null;
            $user->save();
        }

        if (! $emailSent) {
            return [
                'success' => false,
                'email_sent' => false,
                'message' => 'Unable to deliver verification email. Please check your mail configuration or try again.',
                'error_detail' => config('app.debug') ? $errorMessage : null,
            ];
        }

        return [
            'success' => true,
            'email_sent' => true,
            'message' => 'A 6-digit verification code has been sent to your email.',
        ];
    }

    /**
     * Verify the provided OTP code against the user's stored hash.
     *
     * @return array{success: bool, status: string, message: string, remaining_attempts?: int}
     */
    public function verifyOtp(User $user, string $otp): array
    {
        if ($user->isEmailVerified()) {
            return [
                'success' => true,
                'status' => 'already_verified',
                'message' => 'Email is already verified. You can now log in.',
            ];
        }

        if ($user->email_otp_attempts >= self::MAX_ATTEMPTS) {
            return [
                'success' => false,
                'status' => 'max_attempts_exceeded',
                'message' => 'Too many failed verification attempts. Please request a new OTP.',
            ];
        }

        if (! $user->email_otp_expires_at || now()->isAfter($user->email_otp_expires_at)) {
            return [
                'success' => false,
                'status' => 'expired',
                'message' => 'The verification code has expired. Please request a new one.',
            ];
        }

        if (! $user->email_otp_hash || ! Hash::check($otp, $user->email_otp_hash)) {
            $user->increment('email_otp_attempts');
            $remaining = max(0, self::MAX_ATTEMPTS - $user->email_otp_attempts);

            return [
                'success' => false,
                'status' => 'invalid',
                'remaining_attempts' => $remaining,
                'message' => $remaining > 0
                    ? "Invalid verification code. Please try again. ({$remaining} attempts remaining)"
                    : 'Too many failed attempts. Please request a new OTP.',
            ];
        }

        // Verification successful: invalidate OTP and mark verified
        $user->forceFill([
            'email_verified_at' => now(),
            'email_otp_hash' => null,
            'email_otp_expires_at' => null,
            'email_otp_attempts' => 0,
        ])->save();

        return [
            'success' => true,
            'status' => 'verified',
            'message' => 'Email verified successfully! Welcome to IDFR.',
        ];
    }
}
