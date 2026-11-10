@extends('admin.layouts.app')

@section('title', 'Gallery & Portfolio Management')
@section('page_title', 'Gallery Portfolio')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Gallery &amp; Portfolio</em></h2>
    <p class="module-subtitle">Manage photography categories (Weddings, Fashion, Events, Portraits, Commercial) and high-resolution portfolio images.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.gallery.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Upload New Photo</span>
    </a>
  </div>
</div>

<div style="display: grid; grid-template-columns: 320px 1fr; gap: 1.5rem; align-items: start;">
  <!-- Category Management Sidebar -->
  <div class="admin-card">
    <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 1rem;">Gallery Categories</h3>

    <ul style="list-style: none; padding: 0; margin: 0 0 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
      @foreach($categories as $cat)
        <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 4px;">
          <div>
            <strong style="color: #fff; font-size: 0.88rem; display: block;">{{ $cat->name }}</strong>
            <span style="color: var(--text-muted); font-size: 0.75rem;">Tab: {{ $cat->tab_label }} ({{ $cat->images()->count() }} photos)</span>
          </div>
          <form action="{{ route('admin.asdreams.gallery.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Delete category {{ $cat->name }} and all its photos?');" style="margin: 0;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action btn-action--delete" title="Delete Category" style="width: 28px; height: 28px;">
              <svg viewBox="0 0 24 24" width="12" height="12"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
            </button>
          </form>
        </li>
      @endforeach
    </ul>

    <!-- Quick Add Category Form -->
    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
      <h4 style="font-size: 0.88rem; color: var(--gold); margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">+ Add New Category</h4>
      <form action="{{ route('admin.asdreams.gallery.categories.store') }}" method="POST">
        @csrf
        <div class="form-group" style="margin-bottom: 0.75rem;">
          <input type="text" name="name" required placeholder="Category name (e.g. Editorial)" class="form-control" style="font-size: 0.85rem;" />
        </div>
        <div class="form-group" style="margin-bottom: 0.75rem;">
          <input type="text" name="tab_label" placeholder="Short tab label (e.g. Editorial)" class="form-control" style="font-size: 0.85rem;" />
        </div>
        <div class="form-group" style="margin-bottom: 0.75rem;">
          <input type="number" name="order" value="{{ $categories->count() + 1 }}" min="1" placeholder="Order" class="form-control" style="font-size: 0.85rem;" />
        </div>
        <button type="submit" class="btn-subtle" style="width: 100%; justify-content: center; font-size: 0.82rem;">Create Category</button>
      </form>
    </div>
  </div>

  <!-- Images Content & Table -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Filters -->
    <div class="admin-card filter-card">
      <form method="GET" action="{{ route('admin.asdreams.gallery.index') }}" class="filter-form">
        <div class="filter-group filter-group--search" style="flex: 2;">
          <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search image title..." class="filter-input" />
        </div>

        <div class="filter-group">
          <select name="category_id" class="filter-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="filter-group">
          <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published Only</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hidden Only</option>
          </select>
        </div>

        <div class="filter-actions">
          <button type="submit" class="btn-subtle">Filter</button>
          @if(request()->hasAny(['search', 'category_id', 'status']))
            <a href="{{ route('admin.asdreams.gallery.index') }}" class="btn-reset">Reset</a>
          @endif
        </div>
      </form>
    </div>

    <!-- Images Grid / Table -->
    <div class="admin-card">
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="width: 40px;">Order</th>
              <th style="width: 80px;">Thumbnail</th>
              <th>Image Title</th>
              <th>Category</th>
              <th>Dimensions</th>
              <th>Status</th>
              <th style="text-align: right; width: 140px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($images as $img)
              <tr>
                <td style="color: var(--text-muted); font-weight: 600;">#{{ $img->order }}</td>
                <td>
                  <div style="width: 60px; height: 50px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
                    <img src="{{ $img->image_url }}" alt="{{ $img->title }}" style="width: 100%; height: 100%; object-fit: cover;" />
                  </div>
                </td>
                <td>
                  <strong style="color: #fff; font-size: 0.92rem; display: block;">{{ $img->title }}</strong>
                </td>
                <td>
                  <span class="badge" style="background: rgba(245, 200, 66, 0.12); color: var(--gold); border: 1px solid var(--gold-a35);">
                    {{ $img->category->name ?? 'Unassigned' }}
                  </span>
                </td>
                <td>
                  <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $img->width }} × {{ $img->height }}</span>
                </td>
                <td>
                  <form action="{{ route('admin.asdreams.gallery.toggle-status', $img) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">
                      @if($img->is_published)
                        <span class="status-badge status-badge--active">Published</span>
                      @else
                        <span class="status-badge status-badge--inactive">Hidden</span>
                      @endif
                    </button>
                  </form>
                </td>
                <td style="text-align: right;">
                  <div class="table-actions" style="justify-content: flex-end;">
                    <a href="{{ route('admin.asdreams.gallery.edit', $img) }}" class="btn-action" title="Edit Image">
                      <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </a>
                    <form action="{{ route('admin.asdreams.gallery.destroy', $img) }}" method="POST" onsubmit="return confirm('Delete this gallery photo?');" style="display: inline-block;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn-action btn-action--delete" title="Delete Photo">
                        <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                  No gallery images found matching your filter.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($images->hasPages())
        <div style="margin-top: 1.5rem;">
          {{ $images->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
