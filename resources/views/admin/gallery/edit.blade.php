@extends('admin.layouts.app')

@section('title', 'Edit Runway Photograph')
@section('header_title', 'Gallery & Media Manager')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Edit Runway <em>Photograph</em></h2>
    <p class="module-subtitle">Update metadata, replace image file, or adjust season assignment for '{{ $gallery->title }}'.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.gallery') }}" class="btn-subtle">
      ← Back to Gallery
    </a>
  </div>
</div>

<form action="{{ route('admin.gallery.update', $gallery->id) }}" method="POST" enctype="multipart/form-data" class="form-layout">
  @csrf
  @method('PUT')

  <div class="form-grid">
    <!-- Left Column: Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Photo Metadata</h3>

        <div class="form-group">
          <label for="title" class="form-label">Look Caption / Title <span class="required">*</span></label>
          <input type="text" id="title" name="title" value="{{ old('title', $gallery->title) }}" required class="form-control" />
          @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label for="alt_text" class="form-label">Alt Text (Accessibility & SEO)</label>
          <input type="text" id="alt_text" name="alt_text" value="{{ old('alt_text', $gallery->alt_text) }}" class="form-control" />
          @error('alt_text') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="width" class="form-label">Pixel Width</label>
            <input type="number" id="width" name="width" value="{{ old('width', $gallery->width) }}" class="form-control" />
          </div>

          <div class="form-group">
            <label for="height" class="form-label">Pixel Height</label>
            <input type="number" id="height" name="height" value="{{ old('height', $gallery->height) }}" class="form-control" />
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Image Preview & Status -->
    <div class="form-col-side">
      <div class="admin-card">
        <h3 class="card-section-title">Current Photograph</h3>

        <div class="current-image-wrapper">
          <img id="currentImgPreview" src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="current-image" />
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label">Replace Image (Optional)</label>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control" onchange="previewNewImage(this)" />
          <small class="form-hint">Leave blank to keep existing photograph.</small>
          @error('image') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Season &amp; Publication</h3>

        <div class="form-group">
          <label for="season_id" class="form-label">Runway Season</label>
          <select id="season_id" name="season_id" class="form-control">
            @foreach($seasons as $s)
              <option value="{{ $s->id }}" {{ old('season_id', $gallery->season_id) == $s->id ? 'selected' : '' }}>{{ $s->title }} ({{ $s->year }})</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="status" class="form-label">Status <span class="required">*</span></label>
          <select id="status" name="status" class="form-control">
            <option value="published" {{ old('status', $gallery->status) === 'published' ? 'selected' : '' }}>Published (Live on Portal)</option>
            <option value="draft" {{ old('status', $gallery->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
            <option value="archived" {{ old('status', $gallery->status) === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label for="display_order" class="form-label">Display Order</label>
          <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $gallery->display_order) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $gallery->is_featured) ? 'checked' : '' }}>
            <span>Highlight as Featured Look</span>
          </label>
        </div>

        <button type="submit" class="btn-gold btn-block" style="margin-top: 1.5rem;">
          Update Photograph
        </button>
      </div>
    </div>
  </div>
</form>

<script>
  function previewNewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('currentImgPreview').src = e.target.result;

        const img = new Image();
        img.src = e.target.result;
        img.onload = function() {
          const wInput = document.getElementById('width');
          const hInput = document.getElementById('height');
          if (wInput && hInput) {
            wInput.value = this.width;
            hInput.value = this.height;
          }
        };
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>

<style>
  .module-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
  }
  .module-title {
    font-size: 1.75rem;
    font-weight: 300;
    color: var(--text-primary);
  }
  .module-title em {
    font-style: normal;
    color: var(--gold);
    font-weight: 600;
  }
  .module-subtitle {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
  }
  .form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
  }
  .card-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gold);
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(229, 184, 61, 0.2);
  }
  .form-group {
    margin-bottom: 1.25rem;
  }
  .form-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
  }
  .required {
    color: var(--gold);
  }
  .form-control {
    width: 100%;
    padding: 0.65rem 0.85rem;
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.9rem;
  }
  .form-control:focus {
    border-color: var(--gold);
    outline: none;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  .form-hint {
    display: block;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 0.3rem;
  }
  .form-error {
    color: #f87171;
    font-size: 0.8rem;
    margin-top: 0.3rem;
    display: block;
  }
  .current-image-wrapper {
    width: 100%;
    height: 220px;
    background: #000;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .current-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  .btn-block {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0.75rem;
  }
  .custom-checkbox {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.85rem;
    color: var(--text-primary);
    cursor: pointer;
  }
  .custom-checkbox input {
    accent-color: var(--gold);
    width: 16px;
    height: 16px;
  }
  @media (max-width: 900px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endsection
