<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Admin\Asdreams\Concerns\HandlesAsdreamsImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsTeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminAsdreamsTeamController extends Controller
{
    use HandlesAsdreamsImageUploads;

    /**
     * Display the paginated list of team members, founders, and co-founders.
     */
    public function index(Request $request): View
    {
        $query = AsdreamsTeamMember::ordered();

        if ($request->filled('type') && array_key_exists($request->type, AsdreamsTeamMember::MEMBER_TYPES)) {
            $query->where('member_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        $rawCounts = AsdreamsTeamMember::selectRaw('member_type, COUNT(*) as total')
            ->groupBy('member_type')
            ->pluck('total', 'member_type');

        $typeCounts = [
            'all' => (int) $rawCounts->sum(),
            AsdreamsTeamMember::TYPE_FOUNDER => (int) ($rawCounts[AsdreamsTeamMember::TYPE_FOUNDER] ?? 0),
            AsdreamsTeamMember::TYPE_CO_FOUNDER => (int) ($rawCounts[AsdreamsTeamMember::TYPE_CO_FOUNDER] ?? 0),
            AsdreamsTeamMember::TYPE_TEAM_MEMBER => (int) ($rawCounts[AsdreamsTeamMember::TYPE_TEAM_MEMBER] ?? 0),
        ];

        $team = $query->paginate(15)->withQueryString();
        $memberTypes = AsdreamsTeamMember::MEMBER_TYPES;

        return view('admin.asdreams.team.index', compact('team', 'typeCounts', 'memberTypes'));
    }

    /**
     * Show the form for creating a new team member.
     */
    public function create(): View
    {
        $memberTypes = AsdreamsTeamMember::MEMBER_TYPES;
        $nextOrder = ((int) AsdreamsTeamMember::max('display_order')) + 1;

        return view('admin.asdreams.team.create', compact('memberTypes', 'nextOrder'));
    }

    /**
     * Store a newly created team member in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTeamMember($request);

        $photoPath = 'images/founders/tt6.jpg';
        if ($request->hasFile('photo')) {
            $photoPath = $this->uploadAsdreamsFile($request->file('photo'), 'team', 'team');
        }

        $socialLinks = $this->extractSocialLinks($validated);
        $nextOrder = ((int) AsdreamsTeamMember::max('display_order')) + 1;
        $displayOrder = (int) ($validated['display_order'] ?? $request->input('order', $nextOrder));
        $isActive = $request->has('is_active')
            ? $request->boolean('is_active')
            : ($request->has('is_published') ? $request->boolean('is_published') : true);

        AsdreamsTeamMember::create([
            'member_type' => $validated['member_type'],
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'photo_path' => $photoPath,
            'social_links' => $socialLinks,
            'display_order' => $displayOrder,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.asdreams.team.index')
            ->with('success', 'Team member added successfully.');
    }

    /**
     * Show the form for editing an existing team member.
     */
    public function edit(AsdreamsTeamMember $team): View
    {
        return view('admin.asdreams.team.edit', [
            'team' => $team,
            'teamMember' => $team,
            'memberTypes' => AsdreamsTeamMember::MEMBER_TYPES,
        ]);
    }

    /**
     * Update the specified team member in storage.
     */
    public function update(Request $request, AsdreamsTeamMember $team): RedirectResponse
    {
        $validated = $this->validateTeamMember($request);

        if ($request->hasFile('photo')) {
            $team->photo_path = $this->uploadAsdreamsFile(
                $request->file('photo'),
                'team',
                'team',
                $team->photo_path
            );
        }

        $socialLinks = $this->extractSocialLinks($validated, $team->social_links ?? []);
        $displayOrder = (int) ($validated['display_order'] ?? $request->input('order', $team->display_order));
        $isActive = $request->has('is_active')
            ? $request->boolean('is_active')
            : ($request->has('is_published') ? $request->boolean('is_published') : $team->is_active);

        $team->update([
            'member_type' => $validated['member_type'],
            'name' => $validated['name'],
            'designation' => $validated['designation'] ?? null,
            'bio' => $validated['bio'] ?? null,
            'photo_path' => $team->photo_path,
            'social_links' => $socialLinks,
            'display_order' => $displayOrder,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.asdreams.team.index')
            ->with('success', 'Team member updated successfully.');
    }

    /**
     * Remove the specified team member from storage.
     */
    public function destroy(AsdreamsTeamMember $team): RedirectResponse
    {
        $this->deleteAsdreamsUploadedFile($team->photo_path);
        $team->delete();

        return redirect()->route('admin.asdreams.team.index')
            ->with('success', 'Team member removed.');
    }

    /**
     * Toggle active/inactive visibility for the specified team member.
     */
    public function toggleStatus(AsdreamsTeamMember $team): RedirectResponse
    {
        $team->is_active = ! $team->is_active;
        $team->save();

        return back()->with('success', 'Team member status updated.');
    }

    /**
     * Persist drag-and-drop display ordering for team members.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'distinct', 'exists:asdreams_team_members,id'],
            'offset' => ['nullable', 'integer', 'min:0'],
        ]);

        $offset = (int) ($validated['offset'] ?? 0);
        $updated = [];

        DB::transaction(function () use ($validated, $offset, &$updated): void {
            foreach (array_values($validated['order']) as $index => $memberId) {
                $position = $offset + $index + 1;
                $member = AsdreamsTeamMember::find((int) $memberId);
                if ($member) {
                    $member->update(['display_order' => $position]);
                    $updated[(int) $memberId] = $position;
                }
            }
        });

        return response()->json([
            'success' => true,
            'orders' => $updated,
        ]);
    }

    /**
     * Validate store and update requests for team members.
     *
     * @return array<string, mixed>
     */
    private function validateTeamMember(Request $request): array
    {
        return $request->validate([
            'member_type' => ['required', Rule::in(array_keys(AsdreamsTeamMember::MEMBER_TYPES))],
            'name' => ['required', 'string', 'max:150'],
            'designation' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * Normalize optional social links from validated request data.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<string, mixed>  $existing
     * @return array<string, string>
     */
    private function extractSocialLinks(array $validated, array $existing = []): array
    {
        $socialLinks = $existing;

        foreach (['instagram', 'linkedin'] as $platform) {
            if (array_key_exists($platform, $validated)) {
                $value = trim((string) ($validated[$platform] ?? ''));
                if ($value !== '') {
                    $socialLinks[$platform] = $value;
                } else {
                    unset($socialLinks[$platform]);
                }
            }
        }

        return $socialLinks;
    }
}
