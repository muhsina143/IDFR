<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\ProfileEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProfileEnquiryController extends Controller
{
    /**
     * Display a paginated listing of profile booking enquiries with search and filtering.
     */
    public function index(Request $request): View
    {
        $query = ProfileEnquiry::query();

        // Search by sender name, phone, email, professional name, location, brief
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('professional_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('brief', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        $status = $request->input('status', 'all');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by Professional Category
        $category = $request->input('category', 'all');
        if ($category && $category !== 'all') {
            $query->where('professional_category', $category);
        }

        $enquiries = $query->latest()->paginate(15)->withQueryString();

        $totalCount = ProfileEnquiry::count();
        $newCount = ProfileEnquiry::where('status', ProfileEnquiry::STATUS_NEW)->count();
        $inProgressCount = ProfileEnquiry::where('status', ProfileEnquiry::STATUS_IN_PROGRESS)->count();
        $completedCount = ProfileEnquiry::where('status', ProfileEnquiry::STATUS_COMPLETED)->count();

        // Distinct categories for filter dropdown
        $categories = ProfileEnquiry::distinct('professional_category')
            ->pluck('professional_category')
            ->filter()
            ->values();

        return view('admin.profile-enquiries.index', compact(
            'enquiries',
            'totalCount',
            'newCount',
            'inProgressCount',
            'completedCount',
            'status',
            'category',
            'categories'
        ));
    }

    /**
     * Display the full details of a profile booking enquiry.
     */
    public function show(ProfileEnquiry $enquiry): View
    {
        return view('admin.profile-enquiries.show', compact('enquiry'));
    }

    /**
     * Update the enquiry status and administrator notes.
     */
    public function update(Request $request, ProfileEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,in_progress,completed,cancelled'],
            'admin_notes' => ['nullable', 'string', 'max:2500'],
        ]);

        $enquiry->update($validated);

        AdminActivity::log(
            'profile_enquiry_updated',
            auth()->user(),
            auth()->user()?->email,
            "Updated booking enquiry #{$enquiry->id} for {$enquiry->professional_name} (Status: {$enquiry->status})"
        );

        return back()->with('success', "Booking enquiry #{$enquiry->id} has been updated successfully.");
    }

    /**
     * Delete an enquiry from the database.
     */
    public function destroy(ProfileEnquiry $enquiry): RedirectResponse
    {
        $proName = $enquiry->professional_name;
        $id = $enquiry->id;

        $enquiry->delete();

        AdminActivity::log(
            'profile_enquiry_deleted',
            auth()->user(),
            auth()->user()?->email,
            "Deleted booking enquiry #{$id} for {$proName}"
        );

        return redirect()
            ->route('admin.profile-enquiries.index')
            ->with('success', "Booking enquiry #{$id} for {$proName} was deleted successfully.");
    }
}
