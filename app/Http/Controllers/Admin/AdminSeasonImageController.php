<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Season;
use App\Models\SeasonImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSeasonImageController extends Controller
{
    /**
     * Display the Season Image Manager interface.
     */
    public function index(Request $request): View
    {
        $seasons = Season::orderBy('number', 'desc')->get();

        $selectedSeasonId = $request->query('season_id');
        if ($selectedSeasonId) {
            $currentSeason = Season::find($selectedSeasonId) ?? $seasons->first();
        } else {
            $currentSeason = Season::where('status', 'published')->orderBy('number', 'desc')->first() ?? $seasons->first();
        }

        $images = $currentSeason
            ? $currentSeason->images()->paginate(24)->withQueryString()
            : collect();

        $totalImagesCount = $currentSeason ? $currentSeason->images()->count() : 0;
        $activeImagesCount = $currentSeason ? $currentSeason->activeImages()->count() : 0;

        return view('admin.seasons.images', compact(
            'seasons',
            'currentSeason',
            'images',
            'totalImagesCount',
            'activeImagesCount'
        ));
    }

    /**
     * Display images for a specific season route parameter.
     */
    public function seasonImages(Season $season): View
    {
        $seasons = Season::orderBy('number', 'desc')->get();
        $currentSeason = $season;

        $images = $currentSeason->images()->paginate(24)->withQueryString();
        $totalImagesCount = $currentSeason->images()->count();
        $activeImagesCount = $currentSeason->activeImages()->count();

        return view('admin.seasons.images', compact(
            'seasons',
            'currentSeason',
            'images',
            'totalImagesCount',
            'activeImagesCount'
        ));
    }

    /**
     * Handle single or multi-file uploads for a season.
     */
    public function upload(Request $request, Season $season): RedirectResponse|JsonResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'designer_name' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string', 'max:255'],
            'look_number_prefix' => ['nullable', 'string', 'max:30'],
        ]);

        $uploadDir = public_path('uploads/seasons/images');
        if (! File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        $files = $request->file('images');
        $uploadedRecords = [];
        $maxOrder = (int) ($season->images()->max('display_order') ?? 0);
        $prefix = $request->input('look_number_prefix', 'Look');

        DB::transaction(function () use ($files, $season, $request, $uploadDir, &$uploadedRecords, &$maxOrder, $prefix) {
            foreach ($files as $file) {
                if (! $file->isValid()) {
                    continue;
                }

                $maxOrder++;
                $extension = strtolower($file->getClientOriginalExtension());
                $safeName = 'hero_'.$season->id.'_'.time().'_'.Str::random(10).'.'.$extension;
                $file->move($uploadDir, $safeName);

                $relativePath = 'uploads/seasons/images/'.$safeName;
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $title = ucwords(str_replace(['-', '_'], ' ', $originalName));
                $lookNumber = sprintf('%s %02d', $prefix ?: 'Look', $maxOrder);

                $imageRecord = SeasonImage::create([
                    'season_id' => $season->id,
                    'image_path' => $relativePath,
                    'image_title' => $title,
                    'caption' => $request->input('caption') ?: $title,
                    'designer_name' => $request->input('designer_name') ?: 'IDFR Runway',
                    'look_number' => $lookNumber,
                    'alt_text' => $title.' on IDFR Runway',
                    'display_order' => $maxOrder,
                    'is_active' => true,
                ]);

                $uploadedRecords[] = $imageRecord;
            }
        });

        $count = count($uploadedRecords);

        AdminActivity::log(
            'season_images_uploaded',
            auth()->user(),
            auth()->user()->email,
            "Uploaded {$count} hero look image(s) to {$season->title}",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "Successfully uploaded {$count} look image(s) for {$season->title}.",
            ]);
        }

        return redirect()->route('admin.seasons.images', $season)
            ->with('success', "Successfully uploaded {$count} look image(s) for {$season->title}.");
    }

    /**
     * Update an individual season image metadata.
     */
    public function update(Request $request, SeasonImage $image): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'look_number' => ['nullable', 'string', 'max:50'],
            'image_title' => ['nullable', 'string', 'max:150'],
            'caption' => ['nullable', 'string', 'max:255'],
            'designer_name' => ['nullable', 'string', 'max:150'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'display_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'replacement_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ]);

        if ($request->hasFile('replacement_image')) {
            $file = $request->file('replacement_image');
            $uploadDir = public_path('uploads/seasons/images');
            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }

            // Remove old uploaded file if located in uploads/
            if (str_starts_with($image->image_path, 'uploads/') && File::exists(public_path($image->image_path))) {
                File::delete(public_path($image->image_path));
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $safeName = 'hero_'.$image->season_id.'_'.time().'_'.Str::random(10).'.'.$extension;
            $file->move($uploadDir, $safeName);
            $validated['image_path'] = 'uploads/seasons/images/'.$safeName;
        }

        $image->update($validated);

        AdminActivity::log(
            'season_image_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated look '{$image->look_number}' for {$image->season->title}",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Look '{$image->look_number}' updated successfully.",
                'image' => $image,
            ]);
        }

        return redirect()->route('admin.seasons.images', $image->season_id)
            ->with('success', "Look '{$image->look_number}' updated successfully.");
    }

    /**
     * Delete an individual image safely.
     */
    public function destroy(Request $request, SeasonImage $image): RedirectResponse|JsonResponse
    {
        $seasonId = $image->season_id;
        $lookName = $image->look_number ?: $image->image_title ?: 'Hero Image #'.$image->id;
        $seasonTitle = $image->season ? $image->season->title : 'Season';

        // Delete physical file only if in uploads/ folder
        if (str_starts_with($image->image_path, 'uploads/') && File::exists(public_path($image->image_path))) {
            File::delete(public_path($image->image_path));
        }

        $image->delete();

        AdminActivity::log(
            'season_image_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted look '{$lookName}' from {$seasonTitle}",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Image '{$lookName}' deleted successfully.",
            ]);
        }

        return redirect()->route('admin.seasons.images', $seasonId)
            ->with('success', "Image '{$lookName}' deleted successfully.");
    }

    /**
     * Set a specific image as the first slide in the hero carousel.
     */
    public function setFirst(Request $request, SeasonImage $image): RedirectResponse|JsonResponse
    {
        DB::transaction(function () use ($image) {
            // Push all other images for this season down by 1
            SeasonImage::where('season_id', $image->season_id)
                ->where('id', '!=', $image->id)
                ->increment('display_order', 1);

            // Set chosen image as order 1
            $image->update(['display_order' => 1]);
        });

        AdminActivity::log(
            'season_image_set_first',
            auth()->user(),
            auth()->user()->email,
            "Set look '{$image->look_number}' as first hero slide for {$image->season->title}",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Image '{$image->look_number}' set as first look.",
            ]);
        }

        return redirect()->route('admin.seasons.images', $image->season_id)
            ->with('success', "Image '{$image->look_number}' set as the first homepage hero slide.");
    }

    /**
     * Toggle active / inactive status of an image.
     */
    public function toggleStatus(Request $request, SeasonImage $image): RedirectResponse|JsonResponse
    {
        $image->is_active = ! $image->is_active;
        $image->save();

        $statusText = $image->is_active ? 'Activated' : 'Deactivated';

        AdminActivity::log(
            'season_image_status_toggled',
            auth()->user(),
            auth()->user()->email,
            "{$statusText} look '{$image->look_number}' for {$image->season->title}",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $image->is_active,
                'message' => "Look '{$image->look_number}' is now ".strtolower($statusText).'.',
            ]);
        }

        return redirect()->route('admin.seasons.images', $image->season_id)
            ->with('success', "Look '{$image->look_number}' is now ".strtolower($statusText).'.');
    }

    /**
     * Bulk reorder images for a season.
     */
    public function reorder(Request $request, Season $season): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'integer', 'exists:season_images,id'],
            'orders.*.order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $season) {
            foreach ($validated['orders'] as $item) {
                SeasonImage::where('id', $item['id'])
                    ->where('season_id', $season->id)
                    ->update(['display_order' => $item['order']]);
            }
        });

        AdminActivity::log(
            'season_images_reordered',
            auth()->user(),
            auth()->user()->email,
            "Reordered images for {$season->title}",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Display orders updated successfully.',
            ]);
        }

        return redirect()->route('admin.seasons.images', $season)
            ->with('success', 'Images reordered successfully.');
    }
}
