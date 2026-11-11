<!-- Gallery Toolbar + Upload Trigger -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
  <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 240px; flex-wrap: wrap;">
    <input type="text" class="form-control" style="max-width: 300px;" placeholder="Search gallery caption or description..." data-grid-search="studioGalleryHubGrid" />
    <select class="form-control" style="max-width: 170px;" data-grid-status="studioGalleryHubGrid">
      <option value="">All Statuses</option>
      <option value="active">Active Only</option>
      <option value="inactive">Inactive Only</option>
    </select>
  </div>

  <button type="button" class="btn-gold" data-open-gallery-upload data-next-order="{{ $nextGalleryOrder }}">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    <span>Upload Studio Images</span>
  </button>
</div>

<!-- Gallery Cards Grid -->
<div class="admin-card">
  @if($galleryImages->isEmpty())
    <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
      No Studio Gallery images uploaded yet. Click <strong>Upload Studio Images</strong> above to add studio photos.
    </div>
  @else
    <div id="studioGalleryHubGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(225px, 1fr)); gap: 1.25rem;">
      @foreach($galleryImages as $img)
        <div data-search="{{ strtolower($img->title.' '.$img->description) }}"
             data-status="{{ $img->is_active ? 'active' : 'inactive' }}"
             style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
          <div style="position: relative; aspect-ratio: 4 / 5; background: #000; overflow: hidden;">
            <img src="{{ $img->image_url }}" alt="{{ $img->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
            <span class="badge" style="position: absolute; top: 0.65rem; left: 0.65rem; background: rgba(5, 5, 5, 0.85); border: 1px solid var(--border-gold); color: var(--gold); font-weight: 700;">#{{ $img->display_order }}</span>
          </div>

          <div style="padding: 0.95rem; display: flex; flex-direction: column; gap: 0.45rem; flex: 1;">
            <strong style="color: #fff; font-size: 0.94rem; line-height: 1.3;">{{ $img->title }}</strong>
            @if($img->description)
              <p style="font-size: 0.78rem; color: var(--text-secondary); line-height: 1.45; margin: 0;">{{ Str::limit($img->description, 90) }}</p>
            @else
              <p style="font-size: 0.76rem; color: var(--text-muted); margin: 0;">No caption description set.</p>
            @endif

            <div style="margin-top: auto; padding-top: 0.75rem; border-top: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between; gap: 0.45rem;">
              <form action="{{ route('admin.asdreams.studio-gallery.toggle-status', $img) }}" method="POST" data-hub-form="gallery">
                @csrf
                <input type="hidden" name="from_hub" value="1" />
                <button type="submit" class="status-toggle-btn {{ $img->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle visibility">
                  <span class="status-dot"></span>
                  <span>{{ $img->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>

              <div style="display: inline-flex; gap: 0.4rem;">
                <button type="button"
                  class="btn-action btn-action--edit"
                  title="Edit / Replace Image"
                  data-open-gallery-edit
                  data-action="{{ route('admin.asdreams.studio-gallery.update', $img) }}"
                  data-title="{{ $img->title }}"
                  data-description="{{ $img->description ?? '' }}"
                  data-order="{{ $img->display_order }}"
                  data-active="{{ $img->is_active ? '1' : '0' }}"
                  data-image-url="{{ $img->image_url }}">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
                <button type="button"
                  class="btn-action btn-action--delete"
                  title="Delete Image"
                  data-hub-delete
                  data-tab="gallery"
                  data-action="{{ route('admin.asdreams.studio-gallery.destroy', $img) }}"
                  data-label="gallery image &quot;{{ $img->title }}&quot;">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

