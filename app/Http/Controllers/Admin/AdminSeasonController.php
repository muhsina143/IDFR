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
            'hero_heading' => ['nullable', 'string', 'max:150'],
            'hero_highlighted' => ['nullable', 'string', 'max:150'],
            'year' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:100'],
            'dates' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'is_current' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.number' => ['nullable', 'string', 'max:10'],
            'schedule.*.meta' => ['nullable', 'string', 'max:100'],
            'schedule.*.title' => ['nullable', 'string', 'max:150'],
            'schedule.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->has('schedule') && is_array($request->input('schedule'))) {
            $scheduleData = [];
            foreach ($request->input('schedule') as $idx => $event) {
                if (! is_array($event)) {
                    continue;
                }
                $title = trim($event['title'] ?? '');
                $meta = trim($event['meta'] ?? '');
                $desc = trim($event['description'] ?? '');
                $num = trim($event['number'] ?? '');
                if ($title !== '' || $meta !== '' || $desc !== '') {
                    $scheduleData[] = [
                        'number' => $num !== '' ? str_pad($num, 2, '0', STR_PAD_LEFT) : str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                        'meta' => $meta,
                        'title' => $title !== '' ? $title : ('Night '.($idx + 1)),
                        'description' => $desc,
                    ];
                }
            }
            $validated['schedule'] = ! empty($scheduleData) ? $scheduleData : null;
        }

        if ($request->hasFile('hero_image')) {
            $file = $request->file('hero_image');
            $filename = 'hero_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seasons'), $filename);
            $validated['hero_image'] = 'uploads/seasons/'.$filename;
        }

        $season = Season::create($validated);

        if ($request->boolean('is_current')) {
            $season->makeCurrent();
        }

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
            'hero_heading' => ['nullable', 'string', 'max:150'],
            'hero_highlighted' => ['nullable', 'string', 'max:150'],
            'year' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:100'],
            'dates' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'string', 'in:published,draft,archived'],
            'is_current' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'schedule' => ['nullable', 'array'],
            'schedule.*.number' => ['nullable', 'string', 'max:10'],
            'schedule.*.meta' => ['nullable', 'string', 'max:100'],
            'schedule.*.title' => ['nullable', 'string', 'max:150'],
            'schedule.*.description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($request->has('schedule') && is_array($request->input('schedule'))) {
            $scheduleData = [];
            foreach ($request->input('schedule') as $idx => $event) {
                if (! is_array($event)) {
                    continue;
                }
                $title = trim($event['title'] ?? '');
                $meta = trim($event['meta'] ?? '');
                $desc = trim($event['description'] ?? '');
                $num = trim($event['number'] ?? '');
                if ($title !== '' || $meta !== '' || $desc !== '') {
                    $scheduleData[] = [
                        'number' => $num !== '' ? str_pad($num, 2, '0', STR_PAD_LEFT) : str_pad((string) ($idx + 1), 2, '0', STR_PAD_LEFT),
                        'meta' => $meta,
                        'title' => $title !== '' ? $title : ('Night '.($idx + 1)),
                        'description' => $desc,
                    ];
                }
            }
            $validated['schedule'] = ! empty($scheduleData) ? $scheduleData : null;
        }

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

        if ($request->has('is_current')) {
            if ($request->boolean('is_current')) {
                $season->makeCurrent();
            } else {
                $season->update(['is_current' => false]);
            }
        }

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
     * Set the given season as the single active season across the entire application.
     */
    public function activate(Request $request, Season $season): RedirectResponse
    {
        $season->makeCurrent();

        AdminActivity::log(
            'season_activated',
            auth()->user(),
            auth()->user()->email,
            "Activated runway season: {$season->title}",
            $request
        );

        return redirect()->route('admin.seasons')
            ->with('success', "Season '{$season->title}' is now set as the active runway season across the entire website.");
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
