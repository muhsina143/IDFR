@extends('admin.layouts.app')

@section('title', 'Season Hero Image Manager')
@section('header_title', 'Season Hero Image Manager')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.35rem;">
      <a href="{{ route('admin.seasons') }}" class="btn-back" title="Back to Runway Seasons">
        <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      </a>
      <h2 class="module-title">Season <em>Hero Images</em></h2>
    </div>
    <p class="module-subtitle">Manage multiple runway hero looks, designer attributions, look sequences, and homepage carousel presentation for <strong>{{ $currentSeason ? $currentSeason->title : 'Selected Season' }}</strong>.</p>
  </div>

  <div class="module-header__actions">
    <!-- Season Selector Dropdown -->
    <div class="season-selector-wrapper">
      <label for="seasonSelector" class="selector-label">Runway Season:</label>
      <select id="seasonSelector" class="form-control season-selector" onchange="window.location.href='/admin/seasons/' + this.value + '/images'">
        @foreach($seasons as $s)
          <option value="{{ $s->id }}" {{ $currentSeason && $currentSeason->id === $s->id ? 'selected' : '' }}>
            {{ $s->title }} ({{ $s->number }}) — {{ $s->status === 'published' ? 'Published' : ucfirst($s->status) }}
          </option>
        @endforeach
      </select>
    </div>

    <button type="button" class="btn-gold" onclick="scrollToUpload()">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Upload New Looks</span>
    </button>
  </div>
</div>

@if(session('success'))
  <div class="alert alert--success">
    <span>{{ session('success') }}</span>
    <button type="button" class="alert__close" onclick="this.parentElement.remove()">&times;</button>
  </div>
@endif

@if($errors->any())
  <div class="alert alert--error">
    <div>
      <strong>Upload Errors:</strong>
      <ul style="margin-top:0.35rem; padding-left:1.25rem;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    <button type="button" class="alert__close" onclick="this.parentElement.remove()">&times;</button>
  </div>
@endif

<!-- Season Quick Stats -->
@if($currentSeason)
<div class="stats-overview">
  <div class="stat-box">
    <span class="stat-box__label">Current Edition</span>
    <span class="stat-box__value">{{ $currentSeason->title }}</span>
    <span class="stat-box__sub">{{ $currentSeason->year ?? '2026' }} · {{ $currentSeason->location ?? 'Kerala' }}</span>
  </div>
  <div class="stat-box">
    <span class="stat-box__label">Total Looks in Database</span>
    <span class="stat-box__value">{{ $totalImagesCount }}</span>
    <span class="stat-box__sub">All registered hero records</span>
  </div>
  <div class="stat-box">
    <span class="stat-box__label">Active in Homepage Hero</span>
    <span class="stat-box__value" style="color:#4ade80;">{{ $activeImagesCount }}</span>
    <span class="stat-box__sub">Live in the homepage slider</span>
  </div>
  <div class="stat-box">
    <span class="stat-box__label">Homepage Preview</span>
    <div style="margin-top:0.35rem;">
      <a href="{{ route('idfr.home') }}" target="_blank" class="btn-subtle btn-sm" style="display:inline-flex; align-items:center; gap:0.4rem;">
        <span>View Live Site</span>
        <svg viewBox="0 0 24 24" style="width:12px;height:12px;stroke:currentColor;stroke-width:2;fill:none;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      </a>
    </div>
  </div>
</div>
@endif

<!-- Multiple Image Upload Card -->
@if($currentSeason)
<div class="admin-card upload-section" id="uploadCard">
  <div class="admin-card__header">
    <h3 class="card-title">
      <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
      <span>Upload Runway Looks to {{ $currentSeason->title }}</span>
    </h3>
    <span class="badge-gold">Supports Multiple Files</span>
  </div>

  <form id="multiUploadForm" action="{{ route('admin.seasons.images.upload', $currentSeason) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="upload-dropzone" id="dropzone" onclick="document.getElementById('fileInput').click()">
      <input type="file" id="fileInput" name="images[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp" style="display:none;" onchange="handleFileSelect(this)" />
      <div class="dropzone-icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
      </div>
      <h4 class="dropzone-title">Click to browse or drag & drop runway images here</h4>
      <p class="dropzone-hint">Upload 1, 5, 10, or 20+ images simultaneously. Supported formats: JPG, PNG, WEBP (Max 10MB per file). High-resolution runway portraits recommended (1100×1650 or similar).</p>
      <button type="button" class="btn-subtle" style="margin-top:0.75rem;" onclick="event.stopPropagation(); document.getElementById('fileInput').click()">Choose Files</button>
    </div>

    <!-- Preview Container Before Upload -->
    <div id="uploadPreviewSection" style="display:none; margin-top:1.5rem;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
        <h5 style="color:var(--gold); font-size:0.95rem;">Files Ready to Upload (<span id="previewCount">0</span> selected)</h5>
        <button type="button" class="btn-subtle btn-sm" onclick="clearSelectedFiles()">Clear Selection</button>
      </div>
      <div class="preview-grid" id="previewGrid"></div>
    </div>

    <!-- Optional Batch Metadata -->
    <div class="form-row" style="margin-top:1.25rem;">
      <div class="form-group">
        <label class="form-label">Look Number Prefix</label>
        <input type="text" name="look_number_prefix" class="form-control" value="Look" placeholder="e.g. Look, Couture Look, Showstopper" />
        <span class="field-hint">Auto-numbers sequentially: Look 01, Look 02, etc.</span>
      </div>
      <div class="form-group">
        <label class="form-label">Designer / Atelier Name (Optional batch default)</label>
        <input type="text" name="designer_name" class="form-control" placeholder="e.g. Aarav Khanna, IDFR Runway Couture" />
        <span class="field-hint">Can be edited individually after upload.</span>
      </div>
      <div class="form-group">
        <label class="form-label">Theme / Collection Title (Optional batch default)</label>
        <input type="text" name="caption" class="form-control" placeholder="e.g. Neon Noir, Golden Hour Tailoring" />
        <span class="field-hint">Used as look subtitle on the homepage slider.</span>
      </div>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:1.25rem; padding-top:1rem; border-top:1px solid var(--border-subtle);">
      <button type="submit" class="btn-gold" id="btnUploadSubmit" disabled>
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
        <span id="btnUploadText">Upload Selected Images</span>
      </button>
    </div>
  </form>
</div>
@endif

<!-- Existing Images Section -->
<div class="admin-card" style="margin-top:2rem;">
  <div class="admin-card__header" style="flex-wrap:wrap; gap:1rem;">
    <div>
      <h3 class="card-title">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
        <span>Runway Lookbook Gallery — {{ $currentSeason ? $currentSeason->title : '' }}</span>
      </h3>
      <p class="module-subtitle" style="margin-top:0.25rem;">Images are presented on the homepage hero in ascending Display Order (lowest order displays first).</p>
    </div>

    @if($images->isNotEmpty())
    <div style="display:flex; gap:0.75rem; align-items:center;">
      <button type="button" class="btn-subtle btn-sm" onclick="saveDisplayOrders()">
        <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:currentColor;fill:none;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
        <span>Save Reordered Sequence</span>
      </button>
    </div>
    @endif
  </div>

  @if($images->isEmpty())
    <div class="empty-state">
      <div class="empty-state__icon">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
      </div>
      <h4>No Images Found For {{ $currentSeason ? $currentSeason->title : 'This Season' }}</h4>
      <p>Upload runway model photos above to build this season's dynamic homepage hero slider.</p>
      <button type="button" class="btn-gold" style="margin-top:1rem;" onclick="scrollToUpload()">Upload Images Now</button>
    </div>
  @else
    <form id="reorderForm" action="{{ route('admin.seasons.images.reorder', $currentSeason) }}" method="POST">
      @csrf
      <div class="gallery-grid" id="imagesGalleryGrid">
        @foreach($images as $index => $img)
          <div class="image-card {{ !$img->is_active ? 'image-card--inactive' : '' }}" data-id="{{ $img->id }}" id="imgCard_{{ $img->id }}">
            <div class="image-card__thumb-wrapper">
              <img src="{{ $img->image_url }}" alt="{{ $img->alt_text ?: $img->caption }}" class="image-card__thumb" loading="lazy" />
              
              <div class="image-card__badges">
                <span class="badge-look">{{ $img->look_number ?: 'Look ' . sprintf('%02d', $img->display_order) }}</span>
                @if($img->display_order === 1 || ($index === 0 && !request()->has('page')))
                  <span class="badge-first" title="This look appears first when the homepage loads">1st Slide</span>
                @endif
                <span class="badge-status {{ $img->is_active ? 'badge-status--active' : 'badge-status--inactive' }}">
                  {{ $img->is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>

              <div class="image-card__overlay-actions">
                <button type="button" class="overlay-btn" title="Edit look details" onclick='openEditModal(@json($img))'>
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
                <form action="{{ route('admin.season-images.toggle-status', $img) }}" method="POST" style="display:inline;">
                  @csrf
                  <button type="submit" class="overlay-btn" title="{{ $img->is_active ? 'Deactivate (hide from homepage)' : 'Activate (show on homepage)' }}">
                    @if($img->is_active)
                      <svg viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                    @else
                      <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    @endif
                  </button>
                </form>
              </div>
            </div>

            <div class="image-card__body">
              <h4 class="image-card__title">{{ $img->caption ?: ($img->image_title ?: 'Untitled Look') }}</h4>
              <p class="image-card__designer">
                <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>{{ $img->designer_name ?: 'IDFR Runway' }}</span>
              </p>

              <div class="image-card__order-row">
                <label class="order-label" for="order_input_{{ $img->id }}">Order:</label>
                <input type="number" id="order_input_{{ $img->id }}" class="order-input" name="order[{{ $img->id }}]" value="{{ $img->display_order }}" min="0" />
                
                @if($img->display_order !== 1)
                  <form action="{{ route('admin.season-images.set-first', $img) }}" method="POST" style="margin-left:auto;">
                    @csrf
                    <button type="submit" class="btn-set-first" title="Make this image appear first on homepage">Make 1st</button>
                  </form>
                @endif
              </div>
            </div>

            <div class="image-card__footer">
              <button type="button" class="btn-subtle btn-sm" style="flex:1;" onclick='openEditModal(@json($img))'>Edit Look</button>
              
              <button type="button" class="action-btn action-btn--danger" title="Delete image" onclick="confirmDelete({{ $img->id }}, '{{ addslashes($img->look_number ?: $img->caption ?: 'this look') }}')">
                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </form>

    <!-- Pagination -->
    @if($images instanceof \Illuminate\Pagination\LengthAwarePaginator && $images->hasPages())
      <div class="pagination-wrapper" style="margin-top:2rem; display:flex; justify-content:center;">
        {{ $images->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Edit Look Modal -->
<div id="editModal" class="admin-modal" style="display:none;">
  <div class="admin-modal__backdrop" onclick="closeEditModal()"></div>
  <div class="admin-modal__dialog">
    <div class="admin-modal__header">
      <h3 id="editModalTitle">Edit Runway Look Details</h3>
      <button type="button" class="modal-close-btn" onclick="closeEditModal()">&times;</button>
    </div>

    <form id="editForm" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="admin-modal__body">
        <div style="display:flex; gap:1.25rem; align-items:flex-start; margin-bottom:1.25rem; padding-bottom:1.25rem; border-bottom:1px solid var(--border-subtle);">
          <div style="width:100px; height:140px; border-radius:6px; overflow:hidden; border:1px solid var(--border-gold); flex-shrink:0;">
            <img id="editModalImgPreview" src="" style="width:100%; height:100%; object-fit:cover;" />
          </div>
          <div style="flex:1;">
            <label class="form-label">Replace Image File (Optional)</label>
            <input type="file" name="replacement_image" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control" onchange="previewReplacementImg(this)" />
            <span class="field-hint">Upload a replacement if you need to swap out this specific look.</span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Look Number <span class="required">*</span></label>
            <input type="text" id="editLookNumber" name="look_number" required class="form-control" placeholder="e.g. Look 01" />
          </div>
          <div class="form-group">
            <label class="form-label">Designer / Atelier Name <span class="required">*</span></label>
            <input type="text" id="editDesignerName" name="designer_name" required class="form-control" placeholder="e.g. Aarav Khanna" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Look Title / Caption <span class="required">*</span></label>
          <input type="text" id="editCaption" name="caption" required class="form-control" placeholder="e.g. Neon Noir" />
          <span class="field-hint">Featured prominently in the hero slider caption box.</span>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Display Order <span class="required">*</span></label>
            <input type="number" id="editDisplayOrder" name="display_order" min="0" required class="form-control" />
            <span class="field-hint">1 = First look on the homepage.</span>
          </div>
          <div class="form-group">
            <label class="form-label">Hero Slider Status <span class="required">*</span></label>
            <select id="editIsActive" name="is_active" class="form-control">
              <option value="1">Active (Visible in Carousel)</option>
              <option value="0">Inactive (Hidden)</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Accessibility Alt Text</label>
          <input type="text" id="editAltText" name="alt_text" class="form-control" placeholder="Describe the model attire and styling..." />
        </div>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-gold" id="btnEditSave">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="admin-modal" style="display:none;">
  <div class="admin-modal__backdrop" onclick="closeDeleteModal()"></div>
  <div class="admin-modal__dialog" style="max-width:440px;">
    <div class="admin-modal__header">
      <h3 style="color:#ef4444;">Confirm Deletion</h3>
      <button type="button" class="modal-close-btn" onclick="closeDeleteModal()">&times;</button>
    </div>
    <form id="deleteForm" method="POST">
      @csrf
      @method('DELETE')
      <div class="admin-modal__body">
        <p style="color:var(--text-secondary); font-size:0.95rem; line-height:1.6;">
          Are you sure you want to permanently remove <strong id="deleteTargetName" style="color:#fff;">this image</strong> from this season's hero sequence?
        </p>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.75rem;">
          This will remove the database record and delete any custom uploaded file. Other existing season images will remain unaffected.
        </p>
      </div>
      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" onclick="closeDeleteModal()">Cancel</button>
        <button type="submit" class="btn-gold" style="background:#ef4444; color:#fff;">Delete Image</button>
      </div>
    </form>
  </div>
</div>

@endsection

@section('styles')
<style>
  .module-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.75rem; gap: 1rem; flex-wrap: wrap; }
  .module-title { font-family: var(--font-serif); font-size: 1.85rem; color: var(--text-primary); font-weight: 700; }
  .module-title em { font-style: italic; color: var(--gold); font-weight: 300; }
  .module-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem; }
  .btn-gold { display: inline-flex; align-items: center; gap: 0.5rem; background: var(--gold); color: #000; padding: 0.65rem 1.25rem; border-radius: 6px; font-weight: 600; font-size: 0.85rem; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s ease; }
  .btn-gold:hover { background: #f0c54e; }
  .btn-gold:disabled { opacity: 0.5; cursor: not-allowed; }
  .btn-gold svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2.2; fill: none; }
  .btn-subtle { background: #1a1a1a; color: #fff; border: 1px solid var(--border-subtle); padding: 0.55rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; transition: all 0.2s; }
  .btn-subtle:hover { background: #262626; border-color: var(--border-gold); }
  .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
  .btn-back { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; background: #161616; border: 1px solid var(--border-subtle); color: var(--gold); transition: all 0.2s; }
  .btn-back:hover { background: var(--bg-card); border-color: var(--gold); }
  .btn-back svg { width: 16px; height: 16px; stroke: currentColor; stroke-width: 2; fill: none; }

  .season-selector-wrapper { display: flex; align-items: center; gap: 0.5rem; }
  .selector-label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
  .season-selector { min-width: 260px; background: #111; border-color: var(--border-gold); color: var(--gold); font-weight: 600; }

  .stats-overview { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
  .stat-box { background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; display: flex; flex-direction: column; transition: border-color 0.2s; }
  .stat-box:hover { border-color: rgba(229, 184, 61, 0.3); }
  .stat-box__label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.08em; }
  .stat-box__value { font-family: var(--font-serif); font-size: 1.75rem; color: var(--gold); font-weight: 700; margin: 0.3rem 0; line-height: 1.1; }
  .stat-box__sub { font-size: 0.75rem; color: var(--text-secondary); }

  /* Upload Dropzone */
  .upload-section { border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.5rem; background: var(--bg-card); }
  .admin-card__header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid var(--border-subtle); }
  .card-title { display: flex; align-items: center; gap: 0.65rem; font-family: var(--font-serif); font-size: 1.25rem; color: var(--text-primary); font-weight: 600; }
  .card-title svg { width: 20px; height: 20px; stroke: var(--gold); stroke-width: 2; fill: none; }
  .badge-gold { background: rgba(229, 184, 61, 0.15); color: var(--gold); border: 1px solid var(--border-gold); padding: 0.25rem 0.65rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }

  .upload-dropzone {
    border: 2px dashed rgba(229, 184, 61, 0.35);
    background: #080808;
    border-radius: 8px;
    padding: 2.5rem 1.5rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
  }
  .upload-dropzone:hover, .upload-dropzone.dragover {
    border-color: var(--gold);
    background: rgba(229, 184, 61, 0.03);
  }
  .dropzone-icon {
    width: 52px;
    height: 52px;
    margin: 0 auto 0.85rem;
    background: rgba(229, 184, 61, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--gold);
  }
  .dropzone-icon svg { width: 26px; height: 26px; stroke: currentColor; stroke-width: 1.8; fill: none; }
  .dropzone-title { font-size: 1.05rem; color: #fff; font-weight: 600; margin-bottom: 0.35rem; }
  .dropzone-hint { font-size: 0.8rem; color: var(--text-muted); max-width: 580px; margin: 0 auto; line-height: 1.5; }

  /* Upload Previews */
  .preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 0.75rem;
    max-height: 260px;
    overflow-y: auto;
    padding: 0.5rem;
    background: #080808;
    border-radius: 6px;
    border: 1px solid var(--border-subtle);
  }
  .preview-item {
    position: relative;
    aspect-ratio: 2/3;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid var(--border-subtle);
  }
  .preview-item img { width: 100%; height: 100%; object-fit: cover; }
  .preview-item__name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.8);
    color: #fff;
    font-size: 0.65rem;
    padding: 0.2rem 0.35rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Gallery Grid */
  .gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1.25rem;
  }
  .image-card {
    background: #0a0a0a;
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease, border-color 0.2s ease;
    position: relative;
  }
  .image-card:hover {
    border-color: rgba(229, 184, 61, 0.4);
    transform: translateY(-2px);
  }
  .image-card--inactive {
    opacity: 0.65;
    border-style: dashed;
  }
  .image-card__thumb-wrapper {
    position: relative;
    aspect-ratio: 2/3;
    overflow: hidden;
    background: #000;
  }
  .image-card__thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  .image-card:hover .image-card__thumb {
    transform: scale(1.03);
  }
  .image-card__badges {
    position: absolute;
    top: 0.65rem;
    left: 0.65rem;
    right: 0.65rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.35rem;
    pointer-events: none;
    z-index: 2;
  }
  .badge-look {
    background: rgba(0, 0, 0, 0.8);
    color: var(--gold);
    border: 1px solid var(--border-gold);
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
  }
  .badge-first {
    background: var(--gold);
    color: #000;
    padding: 0.2rem 0.55rem;
    border-radius: 4px;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
  }
  .badge-status {
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
  }
  .badge-status--active { background: rgba(34, 197, 94, 0.85); color: #fff; }
  .badge-status--inactive { background: rgba(239, 68, 68, 0.85); color: #fff; }

  .image-card__overlay-actions {
    position: absolute;
    bottom: 0.65rem;
    right: 0.65rem;
    display: flex;
    gap: 0.35rem;
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 3;
  }
  .image-card:hover .image-card__overlay-actions { opacity: 1; }
  .overlay-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: rgba(0, 0, 0, 0.85);
    border: 1px solid var(--border-subtle);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
  }
  .overlay-btn:hover { background: var(--gold); color: #000; border-color: var(--gold); }
  .overlay-btn svg { width: 15px; height: 15px; stroke: currentColor; stroke-width: 2; fill: none; }

  .image-card__body {
    padding: 0.9rem;
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  .image-card__title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0.25rem;
  }
  .image-card__designer {
    font-size: 0.75rem;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    margin-bottom: 0.75rem;
  }
  .image-card__designer svg { width: 12px; height: 12px; stroke: var(--gold); stroke-width: 2; fill: none; }

  .image-card__order-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: auto;
    padding-top: 0.65rem;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
  }
  .order-label { font-size: 0.75rem; color: var(--text-muted); font-weight: 600; }
  .order-input {
    width: 60px;
    background: #141414;
    border: 1px solid var(--border-subtle);
    border-radius: 4px;
    padding: 0.25rem 0.45rem;
    color: var(--gold);
    font-weight: 700;
    font-size: 0.85rem;
    text-align: center;
  }
  .order-input:focus { outline: none; border-color: var(--gold); }
  .btn-set-first {
    background: none;
    border: 1px solid var(--border-gold);
    color: var(--gold);
    padding: 0.25rem 0.55rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-set-first:hover { background: var(--gold); color: #000; }

  .image-card__footer {
    padding: 0.75rem 0.9rem;
    background: #080808;
    border-top: 1px solid var(--border-subtle);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .action-btn { width: 32px; height: 32px; border-radius: 6px; background: #1a1a1a; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); cursor: pointer; transition: all 0.2s; }
  .action-btn svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2; fill: none; }
  .action-btn--danger:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; border-color: #ef4444; }

  /* Empty State */
  .empty-state { text-align: center; padding: 4rem 1.5rem; }
  .empty-state__icon { width: 64px; height: 64px; margin: 0 auto 1rem; border-radius: 50%; background: #141414; display: flex; align-items: center; justify-content: center; color: var(--gold); }
  .empty-state__icon svg { width: 32px; height: 32px; stroke: currentColor; stroke-width: 1.8; fill: none; }
  .empty-state h4 { font-family: var(--font-serif); font-size: 1.35rem; color: #fff; margin-bottom: 0.4rem; }
  .empty-state p { color: var(--text-muted); font-size: 0.85rem; max-width: 420px; margin: 0 auto; }

  /* Modals */
  .admin-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; }
  .admin-modal__backdrop { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px); }
  .admin-modal__dialog { position: relative; background: var(--bg-card); border: 1px solid var(--border-gold); border-radius: 8px; width: 90%; max-width: 540px; z-index: 10; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.8); }
  .admin-modal__header { display: flex; justify-content: space-between; align-items: center; padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); }
  .admin-modal__header h3 { font-family: var(--font-serif); color: var(--gold); font-size: 1.25rem; }
  .modal-close-btn { background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer; }
  .admin-modal__body { padding: 1.5rem; max-height: calc(85vh - 120px); overflow-y: auto; }
  .admin-modal__footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-subtle); background: rgba(0, 0, 0, 0.3); }

  .form-group { margin-bottom: 1rem; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .form-label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.35rem; font-weight: 500; }
  .form-control { width: 100%; background: #080808; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.6rem 0.85rem; color: #fff; font-size: 0.85rem; }
  .form-control:focus { outline: none; border-color: var(--gold); }
  .field-hint { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.25rem; display: block; }
  .required { color: #ef4444; }
</style>
@endsection

@section('scripts')
<script>
  let selectedFiles = [];

  function scrollToUpload() {
    const el = document.getElementById('uploadCard');
    if (el) {
      el.scrollIntoView({ behavior: 'smooth' });
    }
  }

  // Drag & drop handlers
  const dropzone = document.getElementById('dropzone');
  if (dropzone) {
    ['dragenter', 'dragover'].forEach(name => {
      dropzone.addEventListener(name, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.add('dragover');
      });
    });

    ['dragleave', 'drop'].forEach(name => {
      dropzone.addEventListener(name, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('dragover');
      });
    });

    dropzone.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      if (dt.files && dt.files.length) {
        document.getElementById('fileInput').files = dt.files;
        handleFileSelect(document.getElementById('fileInput'));
      }
    });
  }

  function handleFileSelect(input) {
    const files = Array.from(input.files);
    selectedFiles = files;
    const grid = document.getElementById('previewGrid');
    const previewSection = document.getElementById('uploadPreviewSection');
    const btnSubmit = document.getElementById('btnUploadSubmit');
    const btnText = document.getElementById('btnUploadText');
    const countEl = document.getElementById('previewCount');

    grid.innerHTML = '';
    if (files.length === 0) {
      previewSection.style.display = 'none';
      btnSubmit.disabled = true;
      btnText.textContent = 'Upload Selected Images';
      return;
    }

    countEl.textContent = files.length;
    previewSection.style.display = 'block';
    btnSubmit.disabled = false;
    btnText.textContent = `Upload ${files.length} Image${files.length > 1 ? 's' : ''}`;

    files.forEach(file => {
      if (!file.type.startsWith('image/')) return;
      const reader = new FileReader();
      reader.onload = (e) => {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.innerHTML = `
          <img src="${e.target.result}" alt="${file.name}" />
          <div class="preview-item__name">${file.name}</div>
        `;
        grid.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  }

  function clearSelectedFiles() {
    document.getElementById('fileInput').value = '';
    handleFileSelect(document.getElementById('fileInput'));
  }

  // Edit Modal
  function openEditModal(image) {
    document.getElementById('editModalTitle').textContent = `Edit Look: ${image.look_number || image.caption || ('#' + image.id)}`;
    document.getElementById('editForm').action = `/admin/season-images/${image.id}`;
    document.getElementById('editLookNumber').value = image.look_number || '';
    document.getElementById('editDesignerName').value = image.designer_name || '';
    document.getElementById('editCaption').value = image.caption || image.image_title || '';
    document.getElementById('editDisplayOrder').value = image.display_order;
    document.getElementById('editIsActive').value = image.is_active ? '1' : '0';
    document.getElementById('editAltText').value = image.alt_text || '';
    
    const url = image.image_path.startsWith('http') ? image.image_path : ('/' + image.image_path.replace(/^\//, ''));
    document.getElementById('editModalImgPreview').src = url;

    document.getElementById('editModal').style.display = 'flex';
  }

  function previewReplacementImg(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = (e) => {
        document.getElementById('editModalImgPreview').src = e.target.result;
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  // Delete Confirmation Modal
  let pendingDeleteId = null;
  function confirmDelete(id, name) {
    pendingDeleteId = id;
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = `/admin/season-images/${id}`;
    document.getElementById('deleteModal').style.display = 'flex';
  }

  function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
  }

  // Quick Bulk Save Display Orders
  function saveDisplayOrders() {
    const inputs = document.querySelectorAll('.order-input');
    const orders = [];
    inputs.forEach(input => {
      const match = input.id.match(/order_input_(\d+)/);
      if (match) {
        orders.push({
          id: parseInt(match[1]),
          order: parseInt(input.value) || 0
        });
      }
    });

    const token = document.querySelector('input[name="_token"]').value;
    const seasonId = "{{ $currentSeason ? $currentSeason->id : '' }}";

    fetch(`/admin/seasons/${seasonId}/images/reorder`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ orders: orders })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Error saving sequence orders.');
      }
    })
    .catch(err => {
      console.error(err);
      alert('An error occurred while saving display orders.');
    });
  }
</script>
@endsection
