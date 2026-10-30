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
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
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
}
