@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Studio Gallery')
@section('page_title', 'Studio Gallery')

@section('content')
@include('admin.asdreams.studio-rental._nav')

<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio Rental — <em>Studio Gallery</em></h2>
    <p class="module-subtitle">Upload multiple studio photos, edit captions and descriptions, reorder slides, or toggle visibility in the Book Studio for Rent gallery.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.studio-gallery.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Upload Studio Images</span>
    </a>
  </div>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.studio-gallery.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or description..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.asdreams.studio-gallery.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Gallery Images Grid -->
<div class="admin-card">
  @if($images->isEmpty())
    <div style="text-align: center; padding: 3.5rem 1rem;">
      <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1.25rem;">No Studio Gallery images found.</p>
      <a href="{{ route('admin.asdreams.studio-gallery.create') }}" class="btn-gold" style="display: inline-flex;">Upload First Studio Image</a>
    </div>
  @else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem;">
      @foreach($images as $img)
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
          <div style="position: relative; aspect-ratio: 4 / 5; background: #000; overflow: hidden;">
            <img src="{{ $img->image_url }}" alt="{{ $img->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
            <span class="badge" style="position: absolute; top: 0.65rem; left: 0.65rem; background: rgba(5, 5, 5, 0.85); border: 1px solid var(--border-gold); color: var(--gold); font-weight: 700;">#{{ $img->display_order }}</span>
          </div>

          <div style="padding: 1rem; display: flex; flex-direction: column; gap: 0.55rem; flex: 1;">
            <strong style="color: #fff; font-size: 0.95rem; line-height: 1.3;">{{ $img->title }}</strong>
            @if($img->description)
              <p style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.45; margin: 0;">{{ Str::limit($img->description, 95) }}</p>
            @else
              <p style="font-size: 0.78rem; color: var(--text-muted); margin: 0;">No description set.</p>
            @endif

            <div style="margin-top: auto; padding-top: 0.85rem; border-top: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
              <form action="{{ route('admin.asdreams.studio-gallery.toggle-status', $img) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $img->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle visibility">
                  <span class="status-dot"></span>
                  <span>{{ $img->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>

              <div style="display: inline-flex; gap: 0.4rem;">
                <a href="{{ route('admin.asdreams.studio-gallery.edit', $img) }}" class="btn-action btn-action--edit" title="Edit / Replace Image">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmAsdGalleryDelete('{{ $img->id }}', '{{ addslashes($img->title) }}')" title="Delete Image">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteAsdGalleryForm-{{ $img->id }}" action="{{ route('admin.asdreams.studio-gallery.destroy', $img) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    @if($images->hasPages())
      <div style="padding-top: 1.5rem; margin-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
        {{ $images->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteAsdGalleryModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeAsdGalleryDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete <strong id="deleteAsdGalleryTitle" style="color: var(--gold);"></strong> from the Studio Gallery?
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeAsdGalleryDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmAsdGalleryDeleteBtn">Delete Image</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetAsdGalleryId = null;

  function confirmAsdGalleryDelete(id, title) {
    targetAsdGalleryId = id;
    document.getElementById('deleteAsdGalleryTitle').textContent = title;
    document.getElementById('deleteAsdGalleryModal').style.display = 'flex';
  }

  function closeAsdGalleryDeleteModal() {
    document.getElementById('deleteAsdGalleryModal').style.display = 'none';
    targetAsdGalleryId = null;
  }

  document.getElementById('confirmAsdGalleryDeleteBtn').addEventListener('click', function () {
    if (targetAsdGalleryId) {
      document.getElementById('deleteAsdGalleryForm-' + targetAsdGalleryId).submit();
    }
  });
</script>
@endsection

