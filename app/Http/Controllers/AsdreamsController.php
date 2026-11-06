<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsdreamsController extends Controller
{
    /**
     * Display the ASDreamStudio home page.
     */
    public function home(): View
    {
        return view('asdreams.home');
    }

    /**
     * Display the ASDreamStudio About page.
     */
    public function about(): View
    {
        return view('asdreams.about');
    }

    /**
     * Display the ASDreamStudio Gallery page.
     */
    public function gallery(): View
    {
        return view('asdreams.gallery');
    }

    /**
     * Display the ASDreamStudio Contact page.
     */
    public function contact(): View
    {
        return view('asdreams.contact');
    }

    /**
     * Display the ASDreamStudio Team Members page.
     */
    public function team(): View
    {
        return view('asdreams.team');
    }

    /**
     * Handle shoot booking submission.
     */
    public function handleShootBooking(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'shoot_type' => 'nullable|string',
            'shoot_date' => 'nullable|date',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Shoot booking request received! Our studio will contact you shortly.',
            ]);
        }

        return back()->with('success', 'Shoot booking request received! Our studio will contact you shortly.');
    }

    /**
     * Handle studio rental booking submission.
     */
    public function handleStudioBooking(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'days' => 'nullable|integer',
            'purpose' => 'nullable|string|max:255',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your studio booking request has been submitted successfully! Our representative will contact you within 5 hours to discuss your requirements and confirm availability.',
            ]);
        }

        return back()->with('success', 'Your studio booking request has been submitted successfully! Our representative will contact you within 5 hours.');
    }
}
