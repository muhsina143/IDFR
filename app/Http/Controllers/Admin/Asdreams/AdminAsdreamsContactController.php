<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsContactSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsContactController extends Controller
{
    public function index(): View
    {
        $contact = AsdreamsContactSetting::current();

        return view('admin.asdreams.contact.index', compact('contact'));
    }

    public function update(Request $request): RedirectResponse
    {
        $contact = AsdreamsContactSetting::current();

        $validated = $request->validate([
            'section_label' => 'nullable|string|max:100',
            'title' => 'required|string|max:150',
            'title_highlight' => 'nullable|string|max:100',
            'intro' => 'nullable|string|max:1000',
            'studio_address' => 'required|string|max:500',
            'maps_url' => 'nullable|string|max:500',
            'studio_phone' => 'nullable|string|max:50',
            'bookings_phone' => 'nullable|string|max:50',
            'general_email' => 'nullable|email|max:120',
            'bookings_email' => 'nullable|email|max:120',
            'note' => 'nullable|string|max:500',
            'hours_days' => 'nullable|array',
            'hours_time' => 'nullable|array',
        ]);

        $workingHours = [];
        if (! empty($validated['hours_days']) && ! empty($validated['hours_time'])) {
            foreach ($validated['hours_days'] as $idx => $days) {
                if (! empty($days) || ! empty($validated['hours_time'][$idx] ?? '')) {
                    $workingHours[] = [
                        'days' => $days,
                        'hours' => $validated['hours_time'][$idx] ?? '',
                    ];
                }
            }
        }

        $contact->update([
            'section_label' => $validated['section_label'] ?? 'Get in Touch',
            'title' => $validated['title'],
            'title_highlight' => $validated['title_highlight'] ?? null,
            'intro' => $validated['intro'] ?? null,
            'studio_address' => $validated['studio_address'],
            'maps_url' => $validated['maps_url'] ?? null,
            'studio_phone' => $validated['studio_phone'] ?? null,
            'bookings_phone' => $validated['bookings_phone'] ?? null,
            'general_email' => $validated['general_email'] ?? null,
            'bookings_email' => $validated['bookings_email'] ?? null,
            'working_hours' => $workingHours,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('admin.asdreams.contact.index')
            ->with('success', 'Contact page information updated successfully.');
    }
}
