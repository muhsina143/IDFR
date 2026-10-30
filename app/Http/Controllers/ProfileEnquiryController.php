<?php

namespace App\Http\Controllers;

use App\Mail\EnquiryVerificationOtpMail;
use App\Models\EnquiryOtp;
use App\Models\ProfileEnquiry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ProfileEnquiryController extends Controller
{
    /**
     * Send a 6-digit verification OTP to the visitor's email address.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'name' => ['nullable', 'string', 'max:150'],
        ]);

        $email = strtolower(trim($validated['email']));
        $userName = trim($validated['name'] ?? 'Client') ?: 'Client';

        // Check 60-second resend cooldown
        $latestOtp = EnquiryOtp::where('email', $email)->latest()->first();
        if ($latestOtp && $latestOtp->sent_at) {
            $cooldownEnds = $latestOtp->sent_at->copy()->addSeconds(EnquiryOtp::RESEND_COOLDOWN_SECONDS);
            if (now()->lessThan($cooldownEnds)) {
                $secondsLeft = (int) now()->diffInSeconds($cooldownEnds);

                return response()->json([
                    'success' => false,
                    'cooldown_remaining' => $secondsLeft,
                    'message' => "Please wait {$secondsLeft} seconds before requesting a new verification code.",
                ], 429);
            }
        }

        // Generate 6-digit random code
        $otp = sprintf('%06d', random_int(0, 999999));

        $enquiryOtp = EnquiryOtp::create([
            'email' => $email,
            'otp_hash' => Hash::make($otp),
            'session_id' => $request->session()->getId(),
            'ip_address' => $request->ip(),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(EnquiryOtp::EXPIRY_MINUTES),
            'sent_at' => now(),
        ]);

        try {
            Mail::to($email)->send(new EnquiryVerificationOtpMail($otp, $userName));
        } catch (\Throwable $e) {
            Log::error('Profile enquiry OTP delivery failed', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            // Clear sent_at on failure so user is not blocked by cooldown
            $enquiryOtp->update(['sent_at' => null]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to send verification email. Please check your email address and try again.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "A 6-digit verification code has been sent to {$email}.",
            'cooldown_seconds' => EnquiryOtp::RESEND_COOLDOWN_SECONDS,
            'expires_in_seconds' => EnquiryOtp::EXPIRY_MINUTES * 60,
        ]);
    }

    /**
     * Verify the entered 6-digit OTP code against the latest stored hash.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $email = strtolower(trim($validated['email']));
        $otp = trim($validated['otp']);

        $otpRecord = EnquiryOtp::where('email', $email)
            ->latest()
            ->first();

        if (! $otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No active verification code found for this email. Please request a new code.',
            ], 422);
        }

        if ($otpRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'The verification code has expired. Please click resend to receive a fresh code.',
            ], 422);
        }

        if ($otpRecord->hasExceededAttempts()) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum verification attempts exceeded. Please request a new code.',
            ], 422);
        }

        $otpRecord->increment('attempts');

        if (! Hash::check($otp, $otpRecord->otp_hash)) {
            $remaining = max(0, EnquiryOtp::MAX_ATTEMPTS - $otpRecord->attempts);

            return response()->json([
                'success' => false,
                'attempts_remaining' => $remaining,
                'message' => "Invalid verification code. Please check and try again. ({$remaining} attempts remaining)",
            ], 422);
        }

        $otpRecord->update(['verified_at' => now()]);
        $request->session()->put('verified_enquiry_email_'.md5($email), now()->timestamp);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully. You may now submit your booking enquiry.',
        ]);
    }

    /**
     * Submit and store the verified booking enquiry, dispatching admin & client notifications.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'min:7', 'max:25'],
            'assignment_type' => ['required', 'string', Rule::in(ProfileEnquiry::ASSIGNMENT_TYPES)],
            'preferred_date' => ['nullable', 'date'],
            'location' => ['required', 'string', 'min:2', 'max:200'],
            'brief' => ['required', 'string', 'min:10', 'max:3000'],
            'professional_type' => ['required', 'string', 'in:model,creative_professional,designer'],
            'professional_id' => ['nullable', 'integer'],
            'professional_name' => ['required', 'string', 'max:150'],
            'professional_category' => ['required', 'string', 'max:100'],
        ]);

        $email = strtolower(trim($validated['email']));

        // Verify that the email was OTP-verified
        $sessionKey = 'verified_enquiry_email_'.md5($email);
        $verifiedInSession = $request->session()->has($sessionKey);

        $verifiedInDb = EnquiryOtp::where('email', $email)
            ->whereNotNull('verified_at')
            ->where('verified_at', '>=', now()->subMinutes(30))
            ->exists();

        if (! $verifiedInSession && ! $verifiedInDb) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address using the 6-digit OTP code before submitting.',
            ], 422);
        }

        // Prevent duplicate submissions within 60 seconds
        $existingRecent = ProfileEnquiry::where('email', $email)
            ->where('professional_type', $validated['professional_type'])
            ->where('professional_id', $validated['professional_id'])
            ->where('assignment_type', $validated['assignment_type'])
            ->where('created_at', '>=', now()->subSeconds(60))
            ->first();

        if ($existingRecent) {
            return response()->json([
                'success' => true,
                'message' => 'Your enquiry has been submitted successfully. Our team will contact you soon.',
                'enquiry_id' => $existingRecent->id,
            ]);
        }

        // Store Enquiry
        $enquiry = ProfileEnquiry::create([
            'professional_type' => $validated['professional_type'],
            'professional_id' => $validated['professional_id'],
            'professional_name' => trim($validated['professional_name']),
            'professional_category' => trim($validated['professional_category']),
            'name' => trim($validated['name']),
            'email' => $email,
            'phone' => trim($validated['phone']),
            'assignment_type' => $validated['assignment_type'],
            'preferred_date' => $validated['preferred_date'] ?? null,
            'location' => trim($validated['location']),
            'brief' => trim($validated['brief']),
            'is_email_verified' => true,
            'verified_at' => now(),
            'status' => ProfileEnquiry::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Clear verified session token
        $request->session()->forget($sessionKey);

        return response()->json([
            'success' => true,
            'message' => 'Your enquiry has been submitted successfully. Our team will contact you soon.',
            'enquiry_id' => $enquiry->id,
        ]);
    }
}
