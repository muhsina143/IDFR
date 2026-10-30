@extends('admin.layouts.app')

@section('title', 'Add Runway Photograph')
@section('header_title', 'Gallery & Media Manager')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Add Runway <em>Photograph</em></h2>
    <p class="module-subtitle">Upload a new high-resolution runway look, backstage moment, or atelier showcase.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.gallery') }}" class="btn-subtle">
      ← Back to Gallery
    </a>
  </div>
</div>

<form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="form-layout">
  @csrf

  <div class="form-grid">
    <!-- Left Column: Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Photo Metadata</h3>

        <div class="form-group">
          <label for="title" class="form-label">Look Caption / Title <span class="required">*</span></label>
          <input type="text" id="title" name="title" value="{{ old('title') }}" required class="form-control" placeholder="e.g. The Opening Walk, Finale Florals, Designer Spotlight" />
          @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label for="alt_text" class="form-label">Alt Text (Accessibility & SEO)</label>
          <input type="text" id="alt_text" name="alt_text" value="{{ old('alt_text') }}" class="form-control" placeholder="Descriptive description for screen readers" />
          @error('alt_text') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="width" class="form-label">Pixel Width (Optional)</label>
            <input type="number" id="width" name="width" value="{{ old('width', 1000) }}" class="form-control" placeholder="e.g. 1000" />
            <small class="form-hint">Auto-detected from file if left unchanged.</small>
          </div>

          <div class="form-group">
            <label for="height" class="form-label">Pixel Height (Optional)</label>
            <input type="number" id="height" name="height" value="{{ old('height', 1500) }}" class="form-control" placeholder="e.g. 1500" />
            <small class="form-hint">Used for the bento grid aspect ratio.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Image Upload & Status -->
    <div class="form-col-side">
      <div class="admin-card">
        <h3 class="card-section-title">Photograph Upload <span class="required">*</span></h3>

        <div class="upload-dropzone" id="dropzone">
          <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp" required class="upload-input" onchange="previewImage(this)" />
          <div class="upload-placeholder" id="uploadPlaceholder">
            <svg viewBox="0 0 24 24" class="upload-icon"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <p class="upload-text">Drag & drop image here or <span>Browse</span></p>
            <p class="upload-hint">JPEG, PNG, WebP (max 10 MB)</p>
          </div>
          <div class="image-preview-box" id="previewBox" style="display: none;">
            <img id="previewImg" src="#" alt="Preview" />
            <button type="button" class="btn-remove-preview" onclick="resetImage()">Change Image</button>
          </div>
        </div>
        @error('image') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Season &amp; Publication</h3>

        <div class="form-group">
          <label for="season_id" class="form-label">Runway Season</label>
          <select id="season_id" name="season_id" class="form-control">
            @foreach($seasons as $s)
              <option value="{{ $s->id }}" {{ old('season_id') == $s->id ? 'selected' : '' }}>{{ $s->title }} ({{ $s->year }})</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="status" class="form-label">Status <span class="required">*</span></label>
          <select id="status" name="status" class="form-control">
            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live on Portal)</option>
            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label for="display_order" class="form-label">Display Order</label>
          <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <span>Highlight as Featured Look</span>
          </label>
        </div>

        <button type="submit" class="btn-gold btn-block" style="margin-top: 1.5rem;">
          Save &amp; Publish Photograph
        </button>
      </div>
    </div>
  </div>
</form>

<script>
  function previewImage(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('previewBox').style.display = 'block';

        // Auto calculate dimensions
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

  function resetImage() {
    const input = document.getElementById('imageInput');
    input.value = '';
    document.getElementById('previewBox').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'flex';
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
  .upload-dropzone {
    position: relative;
    border: 2px dashed rgba(229, 184, 61, 0.3);
    border-radius: 8px;
    padding: 2rem 1rem;
    text-align: center;
    background: #000;
    transition: border-color 0.2s;
  }
  .upload-dropzone:hover {
    border-color: var(--gold);
  }
  .upload-input {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    z-index: 10;
  }
  .upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
  }
  .upload-icon {
    width: 40px;
    height: 40px;
    stroke: var(--gold);
    stroke-width: 1.5;
    fill: none;
  }
  .upload-text {
    font-size: 0.85rem;
    color: var(--text-primary);
  }
  .upload-text span {
    color: var(--gold);
    text-decoration: underline;
  }
  .upload-hint {
    font-size: 0.75rem;
    color: var(--text-muted);
  }
  .image-preview-box {
    position: relative;
    z-index: 15;
  }
  .image-preview-box img {
    max-width: 100%;
    max-height: 250px;
    border-radius: 6px;
    object-fit: contain;
  }
  .btn-remove-preview {
    margin-top: 0.5rem;
    background: #222;
    border: 1px solid var(--border-subtle);
    color: var(--text-primary);
    padding: 0.4rem 0.85rem;
    border-radius: 4px;
    font-size: 0.75rem;
    cursor: pointer;
  }
  .btn-remove-preview:hover {
    background: rgba(229, 184, 61, 0.2);
    color: var(--gold);
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
