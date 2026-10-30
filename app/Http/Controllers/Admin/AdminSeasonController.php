<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSeasonController extends Controller
{
    /**
     * Display a listing of seasons.
     */
    public function index(): View
    {
        $seasons = Season::withCount(['models', 'designers', 'creativeProfessionals', 'images'])
            ->orderBy('number', 'desc')
            ->get();

        return view('admin.seasons.index', compact('seasons'));
    }

    /**
     * Store a newly created season.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'unique:seasons,number'],
            'title' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:100'],
            'dates' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $filename = 'hero_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seasons'), $filename);
            $validated['hero_image'] = 'uploads/seasons/'.$filename;
        }

        $season = Season::create($validated);

        AdminActivity::log(
            'season_created',
            auth()->user(),
            auth()->user()->email,
            "Created runway season: {$season->title}",
            $request
        );

        return redirect()->route('admin.seasons')
            ->with('success', "Season '{$season->title}' created successfully.");
    }

    /**
     * Update the specified season.
     */
    public function update(Request $request, Season $season): RedirectResponse
    {
        $validated = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'unique:seasons,number,'.$season->id],
            'title' => ['required', 'string', 'max:100'],
            'year' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:100'],
            'dates' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if ($request->hasFile('hero_image')) {
            // Delete old custom hero image if stored locally
            if ($season->hero_image && File::exists(public_path($season->hero_image)) && ! str_starts_with($season->hero_image, 'images/')) {
                File::delete(public_path($season->hero_image));
            }
            $file = $request->file('hero_image');
            $filename = 'hero_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seasons'), $filename);
            $validated['hero_image'] = 'uploads/seasons/'.$filename;
        }

        $season->update($validated);

        AdminActivity::log(
            'season_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated runway season: {$season->title}",
            $request
        );

        return redirect()->route('admin.seasons')
            ->with('success', "Season '{$season->title}' updated successfully.");
    }

    /**
     * Delete a season safely without deleting profiles.
     */
    public function destroy(Request $request, Season $season): RedirectResponse
    {
        $title = $season->title;
        if ($season->hero_image && File::exists(public_path($season->hero_image)) && ! str_starts_with($season->hero_image, 'images/')) {
            File::delete(public_path($season->hero_image));
        }
        // Detach relationships from pivot tables safely
        $season->models()->detach();
        $season->designers()->detach();
        $season->creativeProfessionals()->detach();
        $season->delete();

        AdminActivity::log(
            'season_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted runway season: {$title}",
            $request
        );

        return redirect()->route('admin.seasons')
            ->with('success', "Season '{$title}' removed safely.");
    }
}
