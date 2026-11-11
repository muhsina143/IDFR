@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Edit Studio Gallery Image')
@section('page_title', 'Edit Studio Image')

@section('content')
@include('admin.asdreams.studio-rental._nav')

<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.studio-gallery.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Studio Gallery</span>
    </a>
    <h2 class="module-title">Edit <em>{{ $studioGallery->title }}</em></h2>
    <p class="module-subtitle">Update title, lightbox description, display order, visibility, or replace the uploaded studio image.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 860px;">
  <form action="{{ route('admin.asdreams.studio-gallery.update', $studioGallery) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 240px 1fr; gap: 1.5rem; align-items: start;">
      <div>
        <label class="form-label">Current / Replacement Preview</label>
        <div style="aspect-ratio: 4 / 5; width: 100%; border-radius: 8px; overflow: hidden; border: 1px solid var(--border-gold); background: #000;">
          <img id="studioGalleryEditPreview" src="{{ $studioGallery->image_url }}" alt="{{ $studioGallery->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
        </div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 1rem;">
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="galleryTitle">Image Title <span style="color: var(--gold);">*</span></label>
          <input type="text" id="galleryTitle" name="title" value="{{ old('title', $studioGallery->title) }}" required maxlength="150" class="form-control" />
          @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="galleryDescription">Description / Lightbox Caption</label>
          <textarea id="galleryDescription" name="description" rows="3" maxlength="500" class="form-control" placeholder="Shown as alt/caption when enlarged in lightbox...">{{ old('description', $studioGallery->description) }}</textarea>
          @error('description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="galleryOrder">Display Order</label>
            <input type="number" id="galleryOrder" name="display_order" value="{{ old('display_order', $studioGallery->display_order) }}" min="0" class="form-control" />
            @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; padding-top: 1.5rem;">
            <input type="hidden" name="is_active" value="0" />
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
              <input type="checkbox" name="is_active" value="1" {{ old('is_active', $studioGallery->is_active ? '1' : '0') == '1' ? 'checked' : '' }} />
              <span style="font-weight: 500; color: #fff;">Active &amp; Visible</span>
            </label>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label" for="galleryReplaceImage">Replace Uploaded Image (Optional)</label>
          <input type="file" id="galleryReplaceImage" name="image" accept="image/jpeg,image/png,image/webp" class="form-control" />
          <span class="form-hint">Choose a new file only if you want to replace the current image (max 8MB).</span>
          @error('image') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Studio Image</button>
      <a href="{{ route('admin.asdreams.studio-gallery.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
  (function () {
    var input = document.getElementById('galleryReplaceImage');
    var preview = document.getElementById('studioGalleryEditPreview');
    if (input && preview) {
      input.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (file) {
          preview.src = URL.createObjectURL(file);
        }
      });
    }
  })();
</script>
@endsection

