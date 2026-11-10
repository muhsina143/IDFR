<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsShootBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsStudioBookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsdreamsShootBooking::studio()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('start_date', '>=', $request->date_from)
                    ->orWhereDate('shoot_date', '>=', $request->date_from)
                    ->orWhere(function ($sq) use ($request) {
                        $sq->whereNull('start_date')->whereNull('shoot_date')->whereDate('created_at', '>=', $request->date_from);
                    });
            });
        }

        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('end_date', '<=', $request->date_to)
                    ->orWhereDate('shoot_date', '<=', $request->date_to)
                    ->orWhere(function ($sq) use ($request) {
                        $sq->whereNull('end_date')->whereNull('shoot_date')->whereDate('created_at', '<=', $request->date_to);
                    });
            });
        }

        $bookings = $query->paginate(20)->withQueryString();

        return view('admin.asdreams.studio-bookings.index', compact('bookings'));
    }

    public function show(AsdreamsShootBooking $booking): View
    {
        return view('admin.asdreams.studio-bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, AsdreamsShootBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,confirmed,completed,cancelled',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);

        return back()->with('success', 'Studio booking updated successfully.');
    }

    public function destroy(AsdreamsShootBooking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('admin.asdreams.studio-bookings.index')
            ->with('success', 'Studio booking deleted successfully.');
    }
}
