<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\ContactEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminEnquiryController extends Controller
{
    /**
     * Display a listing of inbound contact and sponsorship enquiries.
     */
    public function index(Request $request): View
    {
        $query = ContactEnquiry::query();

        // Search by sender name, phone, email, subject or message
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by Status
        $status = $request->input('status', 'all');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $enquiries = $query->latest()->paginate(15)->withQueryString();

        $totalCount = ContactEnquiry::count();
        $unreadCount = ContactEnquiry::where('status', 'unread')->count();
        $respondedCount = ContactEnquiry::where('status', 'responded')->count();

        return view('admin.enquiries.index', compact('enquiries', 'totalCount', 'unreadCount', 'respondedCount', 'status'));
    }

    /**
     * Display a single enquiry, marking it as read if unread.
     */
    public function show(ContactEnquiry $enquiry): View
    {
        if ($enquiry->status === 'unread') {
            $enquiry->update(['status' => 'read']);
        }

        return view('admin.enquiries.show', compact('enquiry'));
    }

    /**
     * Update enquiry status or administrator notes.
     */
    public function update(Request $request, ContactEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:unread,read,responded,archived'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $enquiry->update($validated);

        AdminActivity::log(
            'enquiry_updated',
            "Updated enquiry from '{$enquiry->name}' (Status: {$enquiry->status})",
            ['enquiry_id' => $enquiry->id]
        );

        return redirect()->back()
            ->with('success', 'Enquiry status updated to '.ucfirst($enquiry->status).'.');
    }

    /**
     * Delete an enquiry with confirmation.
     */
    public function destroy(ContactEnquiry $enquiry): RedirectResponse
    {
        $name = $enquiry->name;
        $enquiry->delete();

        AdminActivity::log(
            'enquiry_deleted',
            "Deleted enquiry from '{$name}'",
            ['enquiry_id' => $enquiry->id]
        );

        return redirect()->route('admin.enquiries')
            ->with('success', "Enquiry from '{$name}' was deleted.");
    }
}
