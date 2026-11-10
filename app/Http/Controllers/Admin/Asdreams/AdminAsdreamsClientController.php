<?php

namespace App\Http\Controllers\Admin\Asdreams;

use App\Http\Controllers\Admin\Asdreams\Concerns\HandlesAsdreamsImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Asdreams\AsdreamsClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAsdreamsClientController extends Controller
{
    use HandlesAsdreamsImageUploads;

    /**
     * Display the paginated list of ASDreams clients.
     */
    public function index(Request $request): View
    {
        $query = AsdreamsClient::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        if ($request->filled('featured') && $request->featured === '1') {
            $query->where('is_featured', true);
        }

        match ($request->get('sort', 'order')) {
            'name' => $query->orderBy('name', 'asc'),
            'newest' => $query->orderBy('id', 'desc'),
            default => $query->orderBy('display_order', 'asc')->orderBy('id', 'asc'),
        };

        $clients = $query->paginate(15)->withQueryString();

        return view('admin.asdreams.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new ASDreams client.
     */
    public function create(): View
    {
        $nextOrder = ((int) AsdreamsClient::max('display_order')) + 1;

        return view('admin.asdreams.clients.create', compact('nextOrder'));
    }

    /**
     * Store a newly created ASDreams client in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateClient($request, isUpdate: false);

        $logoPath = $this->uploadAsdreamsFile($request->file('logo'), 'clients/logos', 'client_logo');
        $featuredImagePath = $this->uploadAsdreamsFile($request->file('featured_image'), 'clients/featured', 'client_featured');

        $client = AsdreamsClient::create([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'logo_path' => $logoPath,
            'featured_image' => $featuredImagePath,
            'description' => $validated['description'],
            'author_name' => $validated['author_name'] ?? null,
            'author_role' => $validated['author_role'] ?? null,
            'display_order' => $validated['display_order'] ?? (((int) AsdreamsClient::max('display_order')) + 1),
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => false,
        ]);

        if ($request->boolean('is_featured')) {
            $client->makeFeatured();
        }

        return redirect()->route('admin.asdreams.clients.index')
            ->with('success', 'Client added to ASDreams Our Clients successfully.');
    }

    /**
     * Show the form for editing an ASDreams client.
     */
    public function edit(AsdreamsClient $client): View
    {
        return view('admin.asdreams.clients.edit', compact('client'));
    }

    /**
     * Update the specified ASDreams client in storage.
     */
    public function update(Request $request, AsdreamsClient $client): RedirectResponse
    {
        $validated = $this->validateClient($request, isUpdate: true);

        if ($request->hasFile('logo')) {
            $client->logo_path = $this->uploadAsdreamsFile(
                $request->file('logo'),
                'clients/logos',
                'client_logo',
                $client->logo_path
            );
        }

        if ($request->hasFile('featured_image')) {
            $client->featured_image = $this->uploadAsdreamsFile(
                $request->file('featured_image'),
                'clients/featured',
                'client_featured',
                $client->featured_image
            );
        }

        $client->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
            'logo_path' => $client->logo_path,
            'featured_image' => $client->featured_image,
            'description' => $validated['description'],
            'author_name' => $validated['author_name'] ?? null,
            'author_role' => $validated['author_role'] ?? null,
            'display_order' => $validated['display_order'] ?? $client->display_order,
            'is_active' => $request->boolean('is_active', false),
        ]);

        if ($request->boolean('is_featured')) {
            $client->makeFeatured();
        } elseif ($client->is_featured) {
            $client->update(['is_featured' => false]);
        }

        return redirect()->route('admin.asdreams.clients.index')
            ->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified ASDreams client from storage.
     */
    public function destroy(AsdreamsClient $client): RedirectResponse
    {
        $this->deleteAsdreamsUploadedFile($client->logo_path);
        $this->deleteAsdreamsUploadedFile($client->featured_image);
        $client->delete();

        return redirect()->route('admin.asdreams.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Toggle active/inactive visibility for the specified ASDreams client.
     */
    public function toggleStatus(AsdreamsClient $client): RedirectResponse
    {
        $client->is_active = ! $client->is_active;
        $client->save();

        return back()->with('success', 'Client status updated.');
    }

    /**
     * Set the specified ASDreams client as the initially featured showcase client.
     */
    public function setFeatured(AsdreamsClient $client): RedirectResponse
    {
        $client->makeFeatured();

        return back()->with('success', "'{$client->name}' is now selected as the featured client.");
    }

    /**
     * Validate client store/update request data.
     *
     * @return array<string, mixed>
     */
    private function validateClient(Request $request, bool $isUpdate): array
    {
        $logoRule = $isUpdate ? 'nullable' : 'required';
        $imageRule = $isUpdate ? 'nullable' : 'required';

        return $request->validate([
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:150',
            'logo' => "{$logoRule}|file|mimes:jpeg,png,jpg,webp,svg|max:5120",
            'featured_image' => "{$imageRule}|image|mimes:jpeg,png,jpg,webp|max:5120",
            'description' => 'required|string|max:2000',
            'author_name' => 'nullable|string|max:120',
            'author_role' => 'nullable|string|max:120',
            'display_order' => 'nullable|integer|min:0',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);
    }
}
