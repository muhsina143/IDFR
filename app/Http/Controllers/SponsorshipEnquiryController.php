<?php

namespace App\Http\Controllers;

use App\Mail\AdminSponsorshipEnquiryMail;
use App\Models\AdminActivity;
use App\Models\Season;
use App\Models\SponsorshipEnquiry;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SponsorshipEnquiryController extends Controller
{
    /**
     * Store an inbound sponsorship enquiry from the public modal.
     */
    public function store(\App\Http\Requests\Idfr\SponsorshipEnquiryRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        // Resolve season details (defaulting to the current active runway season)
        $season = null;
        if (! empty($validated['season_id'])) {
            $season = Season::find($validated['season_id']);
        }
        if (! $season) {
            $season = Season::current();
        }

        $seasonTitle = $season?->title ?? 'Season 8';

        $enquiry = SponsorshipEnquiry::create([
            'season_id' => $season?->id,
            'season_title' => $seasonTitle,
            'name' => trim($validated['name']),
            'email' => trim(strtolower($validated['email'])),
            'phone' => trim($validated['phone']),
            'sponsorship_type' => trim($validated['sponsorship_type']),
            'message' => ! empty($validated['message']) ? trim($validated['message']) : null,
            'status' => SponsorshipEnquiry::STATUS_NEW,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        // Log admin activity
        AdminActivity::log(
            'sponsorship_enquiry_submitted',
            "New sponsorship enquiry received from '{$enquiry->name}' for {$enquiry->sponsorship_type} ({$seasonTitle})",
            ['enquiry_id' => $enquiry->id]
        );

        // Attempt sending email notification to the official IDFR admin
        try {
            $adminEmail = User::where('is_admin', true)->whereNotNull('email')->value('email')
                ?? config('mail.from.address')
                ?? 'info@idfr.example';

            if (! empty($adminEmail)) {
                Mail::to($adminEmail)->send(new AdminSponsorshipEnquiryMail($enquiry));
            }
        } catch (\Throwable $e) {
            Log::warning('Sponsorship enquiry admin email notification failed: '.$e->getMessage(), [
                'enquiry_id' => $enquiry->id,
            ]);
        }

        $successMessage = "Thank you, {$enquiry->name}. Your sponsorship enquiry for {$seasonTitle} has been received. Our executive team will review your details and contact you shortly.";

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'enquiry' => [
                    'id' => $enquiry->id,
                    'name' => $enquiry->name,
                    'email' => $enquiry->email,
                    'phone' => $enquiry->phone,
                    'sponsorship_type' => $enquiry->sponsorship_type,
                    'season_title' => $enquiry->season_title,
                    'status' => $enquiry->status,
                ],
            ], 201);
        }

        return redirect()->back()->with('success', $successMessage);
    }
}
