<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutStatistic;
use App\Models\AdminActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAboutStatisticController extends Controller
{
    /**
     * Display a listing of about statistics.
     */
    public function index(Request $request): View
    {
        $query = AboutStatistic::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%")
                    ->orWhere('prefix', 'like', "%{$search}%")
                    ->orWhere('suffix', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        $sort = $request->input('sort', 'order');
        if ($sort === 'title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
        }

        $statistics = $query->paginate(15)->withQueryString();

        $totalCount = AboutStatistic::count();
        $activeCount = AboutStatistic::where('is_active', true)->count();
        $inactiveCount = AboutStatistic::where('is_active', false)->count();

        return view('admin.about-statistics.index', compact('statistics', 'totalCount', 'activeCount', 'inactiveCount'));
    }

    /**
     * Show the form for creating a new statistic.
     */
    public function create(): View
    {
        $nextOrder = (AboutStatistic::max('display_order') ?? 0) + 1;

        return view('admin.about-statistics.create', compact('nextOrder'));
    }

    /**
     * Store a newly created statistic in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:150'],
            'prefix' => ['nullable', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = true;
        if ($request->has('is_active')) {
            $isActive = $request->boolean('is_active');
        } elseif ($request->filled('status')) {
            $isActive = ($request->input('status') === 'active');
        }

        $status = $isActive ? 'active' : 'inactive';

        $statistic = AboutStatistic::create([
            'value' => trim($validated['value']),
            'title' => trim($validated['title']),
            'prefix' => $validated['prefix'] !== null ? trim($validated['prefix']) : null,
            'suffix' => $validated['suffix'] !== null ? trim($validated['suffix']) : null,
            'display_order' => $validated['display_order'] ?? ((AboutStatistic::max('display_order') ?? 0) + 1),
            'status' => $status,
            'is_active' => $isActive,
        ]);

        AdminActivity::log(
            'about_statistic_created',
            auth()->user(),
            auth()->user()?->email,
            "Created about statistic: {$statistic->title} ({$statistic->full_display})",
            $request
        );

        return redirect()->route('admin.about-statistics.index')
            ->with('success', "Statistic '{$statistic->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified statistic.
     */
    public function edit(AboutStatistic $aboutStatistic): View
    {
        return view('admin.about-statistics.edit', [
            'statistic' => $aboutStatistic,
        ]);
    }

    /**
     * Update the specified statistic in storage.
     */
    public function update(Request $request, AboutStatistic $aboutStatistic): RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:150'],
            'prefix' => ['nullable', 'string', 'max:20'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $isActive = $aboutStatistic->is_active;
        if ($request->has('is_active')) {
            $isActive = $request->boolean('is_active');
        } elseif ($request->filled('status')) {
            $isActive = ($request->input('status') === 'active');
        }

        $aboutStatistic->value = trim($validated['value']);
        $aboutStatistic->title = trim($validated['title']);
        $aboutStatistic->prefix = $validated['prefix'] !== null ? trim($validated['prefix']) : null;
        $aboutStatistic->suffix = $validated['suffix'] !== null ? trim($validated['suffix']) : null;
        $aboutStatistic->display_order = $validated['display_order'] ?? $aboutStatistic->display_order;
        $aboutStatistic->is_active = $isActive;
        $aboutStatistic->status = $isActive ? 'active' : 'inactive';
        $aboutStatistic->save();

        AdminActivity::log(
            'about_statistic_updated',
            auth()->user(),
            auth()->user()?->email,
            "Updated about statistic: {$aboutStatistic->title} ({$aboutStatistic->full_display})",
            $request
        );

        return redirect()->route('admin.about-statistics.index')
            ->with('success', "Statistic '{$aboutStatistic->title}' updated successfully.");
    }

    /**
     * Remove the specified statistic from storage.
     */
    public function destroy(Request $request, AboutStatistic $aboutStatistic): RedirectResponse
    {
        $title = $aboutStatistic->title;
        $display = $aboutStatistic->full_display;

        $aboutStatistic->delete();

        AdminActivity::log(
            'about_statistic_deleted',
            auth()->user(),
            auth()->user()?->email,
            "Deleted about statistic: {$title} ({$display})",
            $request
        );

        return redirect()->route('admin.about-statistics.index')
            ->with('success', "Statistic '{$title}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, AboutStatistic $aboutStatistic): JsonResponse|RedirectResponse
    {
        $aboutStatistic->is_active = ! $aboutStatistic->is_active;
        $aboutStatistic->status = $aboutStatistic->is_active ? 'active' : 'inactive';
        $aboutStatistic->save();

        AdminActivity::log(
            'about_statistic_status_toggled',
            auth()->user(),
            auth()->user()?->email,
            "Toggled status for statistic '{$aboutStatistic->title}' to ".($aboutStatistic->is_active ? 'Active' : 'Inactive'),
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $aboutStatistic->is_active,
                'status' => $aboutStatistic->status,
                'message' => "Statistic '{$aboutStatistic->title}' is now ".($aboutStatistic->is_active ? 'Active' : 'Inactive').'.',
            ]);
        }

        return back()->with('success', "Statistic '{$aboutStatistic->title}' is now ".($aboutStatistic->is_active ? 'Active' : 'Inactive').'.');
    }

    /**
     * Reorder statistics display order.
     */
    public function reorder(Request $request): JsonResponse|RedirectResponse
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            if (isset($item['id'], $item['display_order'])) {
                AboutStatistic::where('id', $item['id'])->update([
                    'display_order' => (int) $item['display_order'],
                ]);
            }
        }

        AdminActivity::log(
            'about_statistics_reordered',
            auth()->user(),
            auth()->user()?->email,
            'Reordered about statistics display sequence',
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Statistics reordered successfully.',
            ]);
        }

        return back()->with('success', 'Statistics reordered successfully.');
    }
}
