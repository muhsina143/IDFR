<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminClientController extends Controller
{
    /**
     * Display a listing of clients & testimonials.
     */
    public function index(Request $request): View
    {
        $query = Client::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('industry', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('testimonial_quote', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('is_active', $request->boolean('status'));
        }

        if ($request->has('featured') && $request->input('featured') !== '') {
            $query->where('is_featured', $request->boolean('featured'));
        }

        $sort = $request->input('sort', 'order');
        if ($sort === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            $query->orderByDesc('is_featured')->orderBy('display_order', 'asc')->orderBy('id', 'asc');
        }

        $clients = $query->paginate(15)->withQueryString();

        return view('admin.clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client/testimonial.
     */
    public function create(): View
    {
        $nextOrder = (Client::max('display_order') ?? 0) + 1;

        return view('admin.clients.create', compact('nextOrder'));
    }

    /**
     * Store a newly created client/testimonial in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('author_role') && $request->filled('author_title')) {
            $request->merge(['author_role' => $request->input('author_title')]);
        }
        if (! $request->filled('testimonial_quote') && $request->filled('testimonial_text')) {
            $request->merge(['testimonial_quote' => $request->input('testimonial_text')]);
        }
        if (! $request->filled('industry') && $request->filled('category')) {
            $request->merge(['industry' => $request->input('category')]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:5120'],
            'logo_style' => ['nullable', 'string', 'max:50'],
            'logo_mark' => ['nullable', 'string', 'max:20'],
            'logo_word' => ['nullable', 'string', 'max:100'],
            'logo_sub' => ['nullable', 'string', 'max:100'],
            'testimonial_quote' => ['nullable', 'string', 'max:2000'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_role' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'include_in_strip' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'client_bg_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/clients';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $imagePath = $dir.'/'.$fileName;
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = 'client_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/clients/logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $logoPath = $dir.'/'.$fileName;
        }

        $isFeatured = $request->boolean('is_featured');
        if ($isFeatured) {
            Client::where('is_featured', true)->update(['is_featured' => false]);
        }

        $client = Client::create([
            'name' => $validated['name'],
            'industry' => $validated['industry'] ?? null,
            'image_path' => $imagePath,
            'logo_path' => $logoPath,
            'logo_style' => $validated['logo_style'] ?? 'arch',
            'logo_mark' => $validated['logo_mark'] ?? substr($validated['name'], 0, 1),
            'logo_word' => $validated['logo_word'] ?? strtoupper($validated['name']),
            'logo_sub' => $validated['logo_sub'] ?? strtoupper($validated['industry'] ?? 'CLIENT'),
            'testimonial_quote' => $validated['testimonial_quote'] ?? null,
            'author_name' => $validated['author_name'] ?? null,
            'author_role' => $validated['author_role'] ?? null,
            'company_name' => $validated['company_name'] ?? $validated['name'],
            'website_url' => $validated['website_url'] ?? null,
            'is_featured' => $isFeatured,
            'include_in_strip' => $request->has('include_in_strip') ? $request->boolean('include_in_strip') : true,
            'display_order' => $validated['display_order'] ?? ((Client::max('display_order') ?? 0) + 1),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
        ]);

        AdminActivity::log(
            'client_created',
            auth()->user(),
            auth()->user()->email,
            "Created client / testimonial: {$client->name}",
            $request
        );

        return redirect()->route('admin.clients.index')
            ->with('success', "Client '{$client->name}' added successfully.");
    }

    /**
     * Show the form for editing the specified client.
     */
    public function edit(Client $client): View
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified client in storage.
     */
    public function update(Request $request, Client $client): RedirectResponse
    {
        if (! $request->filled('author_role') && $request->filled('author_title')) {
            $request->merge(['author_role' => $request->input('author_title')]);
        }
        if (! $request->filled('testimonial_quote') && $request->filled('testimonial_text')) {
            $request->merge(['testimonial_quote' => $request->input('testimonial_text')]);
        }
        if (! $request->filled('industry') && $request->filled('category')) {
            $request->merge(['industry' => $request->input('category')]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,svg,avif', 'max:5120'],
            'logo_style' => ['nullable', 'string', 'max:50'],
            'logo_mark' => ['nullable', 'string', 'max:20'],
            'logo_word' => ['nullable', 'string', 'max:100'],
            'logo_sub' => ['nullable', 'string', 'max:100'],
            'testimonial_quote' => ['nullable', 'string', 'max:2000'],
            'author_name' => ['nullable', 'string', 'max:150'],
            'author_role' => ['nullable', 'string', 'max:150'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'include_in_strip' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($client->image_path && str_starts_with($client->image_path, 'uploads/') && File::exists(public_path($client->image_path))) {
                @unlink(public_path($client->image_path));
            }
            $file = $request->file('image');
            $fileName = 'client_bg_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/clients';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $client->image_path = $dir.'/'.$fileName;
        }

        if ($request->hasFile('logo')) {
            if ($client->logo_path && str_starts_with($client->logo_path, 'uploads/') && File::exists(public_path($client->logo_path))) {
                @unlink(public_path($client->logo_path));
            }
            $file = $request->file('logo');
            $fileName = 'client_logo_'.time().'_'.Str::random(8).'.'.$file->getClientOriginalExtension();
            $dir = 'uploads/clients/logos';
            if (! File::exists(public_path($dir))) {
                File::makeDirectory(public_path($dir), 0755, true);
            }
            $file->move(public_path($dir), $fileName);
            $client->logo_path = $dir.'/'.$fileName;
        }

        $isFeatured = $request->boolean('is_featured');
        if ($isFeatured && ! $client->is_featured) {
            Client::where('id', '!=', $client->id)->where('is_featured', true)->update(['is_featured' => false]);
        }

        $client->name = $validated['name'];
        $client->industry = $validated['industry'] ?? null;
        $client->logo_style = $validated['logo_style'] ?? $client->logo_style;
        $client->logo_mark = $validated['logo_mark'] ?? $client->logo_mark;
        $client->logo_word = $validated['logo_word'] ?? $client->logo_word;
        $client->logo_sub = $validated['logo_sub'] ?? $client->logo_sub;
        $client->testimonial_quote = $validated['testimonial_quote'] ?? null;
        $client->author_name = $validated['author_name'] ?? null;
        $client->author_role = $validated['author_role'] ?? null;
        $client->company_name = $validated['company_name'] ?? $validated['name'];
        $client->website_url = $validated['website_url'] ?? null;
        $client->is_featured = $isFeatured;
        $client->include_in_strip = $request->has('include_in_strip') ? $request->boolean('include_in_strip') : $client->include_in_strip;
        $client->display_order = $validated['display_order'] ?? $client->display_order;
        $client->is_active = $request->has('is_active') ? $request->boolean('is_active') : $client->is_active;
        $client->save();

        AdminActivity::log(
            'client_updated',
            auth()->user(),
            auth()->user()->email,
            "Updated client / testimonial: {$client->name}",
            $request
        );

        return redirect()->route('admin.clients.index')
            ->with('success', "Client '{$client->name}' updated successfully.");
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Request $request, Client $client): RedirectResponse
    {
        $name = $client->name;

        if ($client->image_path && str_starts_with($client->image_path, 'uploads/') && File::exists(public_path($client->image_path))) {
            @unlink(public_path($client->image_path));
        }

        if ($client->logo_path && str_starts_with($client->logo_path, 'uploads/') && File::exists(public_path($client->logo_path))) {
            @unlink(public_path($client->logo_path));
        }

        $wasFeatured = $client->is_featured;
        $client->delete();

        // If the featured client was deleted, designate the next available client as featured
        if ($wasFeatured) {
            $next = Client::where('is_active', true)->orderBy('display_order')->first();
            if ($next) {
                $next->is_featured = true;
                $next->save();
            }
        }

        AdminActivity::log(
            'client_deleted',
            auth()->user(),
            auth()->user()->email,
            "Deleted client / testimonial: {$name}",
            $request
        );

        return redirect()->route('admin.clients.index')
            ->with('success', "Client '{$name}' deleted successfully.");
    }

    /**
     * Set a client as the single featured testimonial.
     */
    public function setFeatured(Request $request, Client $client): JsonResponse|RedirectResponse
    {
        $client->makeFeatured();

        AdminActivity::log(
            'client_featured_set',
            auth()->user(),
            auth()->user()->email,
            "Set {$client->name} as the single featured testimonial",
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$client->name} is now the featured testimonial on the homepage.",
            ]);
        }

        return back()->with('success', "{$client->name} is now the featured testimonial on the homepage.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Client $client): JsonResponse|RedirectResponse
    {
        $client->is_active = ! $client->is_active;
        $client->save();

        AdminActivity::log(
            'client_status_toggled',
            auth()->user(),
            auth()->user()->email,
            "Toggled active status for client: {$client->name} to ".($client->is_active ? 'Active' : 'Inactive'),
            $request
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $client->is_active,
                'message' => 'Client status updated to '.($client->is_active ? 'Active' : 'Inactive').'.',
            ]);
        }

        return back()->with('success', 'Client status updated to '.($client->is_active ? 'Active' : 'Inactive').'.');
    }

    /**
     * Reorder clients display order.
     */
    public function reorder(Request $request): JsonResponse|RedirectResponse
    {
        $orders = $request->input('orders', []);
        foreach ($orders as $item) {
            if (isset($item['id'], $item['display_order'])) {
                Client::where('id', $item['id'])->update(['display_order' => (int) $item['display_order']]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Clients reordered successfully.']);
        }

        return back()->with('success', 'Clients reordered successfully.');
    }
}
