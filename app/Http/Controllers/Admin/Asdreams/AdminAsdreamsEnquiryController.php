<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsContactEnquiry;
use App\Models\Asdreams\AsdreamsOffer;
use App\Models\Asdreams\AsdreamsOfferEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $type = $request->query('type', 'offers');
        $offers = AsdreamsOffer::published()->get();

        if ($type === 'contact') {
            $query = AsdreamsContactEnquiry::latest();

            if ($request->filled('brand')) {
                $query->where('brand', $request->brand);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $enquiries = $query->paginate(20)->withQueryString();
        } else {
            $query = AsdreamsOfferEnquiry::with(['offer', 'banner'])->latest();

            if ($request->filled('offer_id')) {
                $query->where('offer_id', $request->offer_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('offer_title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $enquiries = $query->paginate(20)->withQueryString();
        }

        $newOffersCount = AsdreamsOfferEnquiry::where('status', 'new')->count();
        $newContactCount = AsdreamsContactEnquiry::where('status', 'new')->count();

        return view('admin.asdreams.enquiries.index', compact(
            'enquiries',
            'type',
            'offers',
            'newOffersCount',
            'newContactCount'
        ));
    }

    public function showOffer(AsdreamsOfferEnquiry $enquiry): View
    {
        return view('admin.asdreams.enquiries.show-offer', compact('enquiry'));
    }

    public function updateOfferStatus(Request $request, AsdreamsOfferEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,completed,archived',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $enquiry->update($validated);

        return back()->with('success', 'Offer enquiry updated successfully.');
    }

    public function destroyOffer(AsdreamsOfferEnquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()->route('admin.asdreams.enquiries.index', ['type' => 'offers'])
            ->with('success', 'Offer enquiry removed.');
    }

    public function showContact(AsdreamsContactEnquiry $enquiry): View
    {
        return view('admin.asdreams.enquiries.show-contact', compact('enquiry'));
    }

    public function updateContactStatus(Request $request, AsdreamsContactEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,completed,archived',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $enquiry->update($validated);

        return back()->with('success', 'Contact enquiry updated successfully.');
    }

    public function destroyContact(AsdreamsContactEnquiry $enquiry): RedirectResponse
    {
        $enquiry->delete();

        return redirect()->route('admin.asdreams.enquiries.index', ['type' => 'contact'])
            ->with('success', 'Contact enquiry removed.');
    }
}
