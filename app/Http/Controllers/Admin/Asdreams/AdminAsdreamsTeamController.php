<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsTeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AdminAsdreamsTeamController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsdreamsTeamMember::orderBy('order', 'asc');

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        $team = $query->paginate(15)->withQueryString();

        return view('admin.asdreams.team.index', compact('team'));
    }

    public function create(): View
    {
        return view('admin.asdreams.team.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'required|string|max:150',
            'bio' => 'nullable|string|max:1000',
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        $photoPath = 'images/founders/tt6.jpg';
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'team_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/team');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $photoPath = 'uploads/asdreams/team/'.$filename;
        }

        $socialLinks = [];
        if (! empty($validated['instagram'])) {
            $socialLinks['instagram'] = $validated['instagram'];
        }
        if (! empty($validated['linkedin'])) {
            $socialLinks['linkedin'] = $validated['linkedin'];
        }

        AsdreamsTeamMember::create([
            'name' => $validated['name'],
            'designation' => $validated['designation'],
            'bio' => $validated['bio'] ?? null,
            'photo_path' => $photoPath,
            'social_links' => $socialLinks,
            'order' => $validated['order'] ?? (AsdreamsTeamMember::max('order') + 1),
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.asdreams.team.index')
            ->with('success', 'Team member added successfully.');
    }

    public function edit(AsdreamsTeamMember $team): View
    {
        return view('admin.asdreams.team.edit', compact('team'));
    }

    public function update(Request $request, AsdreamsTeamMember $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'designation' => 'required|string|max:150',
            'bio' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'instagram' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'team_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/asdreams/team');
            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true, true);
            }
            $file->move($destination, $filename);
            $team->photo_path = 'uploads/asdreams/team/'.$filename;
        }

        $socialLinks = $team->social_links ?? [];
        if (isset($validated['instagram'])) {
            $socialLinks['instagram'] = $validated['instagram'];
        }
        if (isset($validated['linkedin'])) {
            $socialLinks['linkedin'] = $validated['linkedin'];
        }

        $team->update([
            'name' => $validated['name'],
            'designation' => $validated['designation'],
            'bio' => $validated['bio'] ?? null,
            'social_links' => $socialLinks,
            'order' => $validated['order'] ?? $team->order,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.asdreams.team.index')
            ->with('success', 'Team member updated successfully.');
    }

    public function destroy(AsdreamsTeamMember $team): RedirectResponse
    {
        $team->delete();

        return redirect()->route('admin.asdreams.team.index')
            ->with('success', 'Team member removed.');
    }

    public function toggleStatus(AsdreamsTeamMember $team): RedirectResponse
    {
        $team->is_published = ! $team->is_published;
        $team->save();

        return back()->with('success', 'Team member status updated.');
    }
}
