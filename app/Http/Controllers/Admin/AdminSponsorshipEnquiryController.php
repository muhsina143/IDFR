<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Season;
use App\Models\SponsorshipEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSponsorshipEnquiryController extends Controller
{
    /**
     * Display a listing of sponsorship enquiries with search, filtering, and stats.
     */
    public function index(Request $request): View
    {
        $query = SponsorshipEnquiry::with('season');

        // Search by name/company, email, phone, sponsorship type, message, season title
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('sponsorship_type', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('season_title', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        $status = $request->input('status', 'all');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by Sponsorship Type
        $type = $request->input('type', 'all');
        if ($type && $type !== 'all') {
            $query->where('sponsorship_type', $type);
        }

        // Filter by Season
        $seasonId = $request->input('season_id', 'all');
        if ($seasonId && $seasonId !== 'all') {
            $query->where('season_id', $seasonId);
        }

        // Always show latest enquiries first
        $enquiries = $query->latest()->paginate(15)->withQueryString();

        // Calculate Overview Metrics
        $totalCount = SponsorshipEnquiry::count();
        $newCount = SponsorshipEnquiry::where('status', SponsorshipEnquiry::STATUS_NEW)->count();
        $contactedCount = SponsorshipEnquiry::where('status', SponsorshipEnquiry::STATUS_CONTACTED)->count();
        $completedCount = SponsorshipEnquiry::where('status', SponsorshipEnquiry::STATUS_COMPLETED)->count();

        $availableTypes = SponsorshipEnquiry::availableTypes();
        $seasons = Season::orderByDesc('number')->get();

        return view('admin.sponsorship-enquiries.index', compact(
            'enquiries',
            'totalCount',
            'newCount',
            'contactedCount',
            'completedCount',
            'status',
            'type',
            'seasonId',
            'availableTypes',
            'seasons'
        ));
    }

    /**
     * Display the specified sponsorship enquiry in full detail.
     */
    public function show(SponsorshipEnquiry $enquiry): View
    {
        return view('admin.sponsorship-enquiries.show', compact('enquiry'));
    }

    /**
     * Update enquiry status or administrator notes.
     */
    public function update(Request $request, SponsorshipEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,completed'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $enquiry->update($validated);

        AdminActivity::log(
            'sponsorship_enquiry_updated',
            "Updated sponsorship enquiry #{$enquiry->id} from '{$enquiry->name}' (Status: {$enquiry->statusLabel()})",
            ['enquiry_id' => $enquiry->id]
        );

        return redirect()->back()
            ->with('success', 'Enquiry status updated to '.$enquiry->statusLabel().'.');
    }

    /**
     * Delete an enquiry with confirmation.
     */
    public function destroy(SponsorshipEnquiry $enquiry): RedirectResponse
    {
        $name = $enquiry->name;
        $id = $enquiry->id;

        $enquiry->delete();

        AdminActivity::log(
            'sponsorship_enquiry_deleted',
            "Deleted sponsorship enquiry #{$id} from '{$name}'",
            ['enquiry_id' => $id]
        );

        return redirect()->route('admin.sponsorship-enquiries.index')
            ->with('success', "Sponsorship enquiry from '{$name}' was deleted.");
    }
}
