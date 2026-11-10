@extends('admin.layouts.app')

@section('title', 'Edit Gallery Photo')
@section('page_title', 'Edit Photo')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.gallery.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Gallery</span>
    </a>
    <h2 class="module-title">Edit Gallery <em>Photo</em></h2>
    <p class="module-subtitle">Update title, category, dimensions, or replace image file.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 750px;">
  <form action="{{ route('admin.asdreams.gallery.update', $image) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="category_id">Portfolio Category <span style="color: var(--gold);">*</span></label>
        <select id="category_id" name="category_id" required class="form-control">
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $image->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
        @error('category_id') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="title">Photo Title / Caption <span style="color: var(--gold);">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $image->title) }}" required class="form-control" />
        <span class="form-hint">Shown in the full-screen lightbox when clicked.</span>
        @error('title') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="image">Replace Photograph File</label>
        @if($image->image_url)
          <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; background: #000; display: inline-block;">
            <img src="{{ $image->image_url }}" alt="{{ $image->title }}" style="max-height: 120px; max-width: 180px; object-fit: contain; display: block;" />
          </div>
        @endif
        <input type="file" id="image" name="image" accept="image/*" class="form-control" />
        <span class="form-hint">Leave blank to retain current image.</span>
        @error('image') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="width">Natural Width (px)</label>
        <input type="number" id="width" name="width" value="{{ old('width', $image->width) }}" class="form-control" />
        @error('width') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="height">Natural Height (px)</label>
        <input type="number" id="height" name="height" value="{{ old('height', $image->height) }}" class="form-control" />
        @error('height') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" id="order" name="order" value="{{ old('order', $image->order) }}" min="1" class="form-control" />
        @error('order') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_published" value="1" {{ old('is_published', $image->is_published ? '1' : '0') == '1' ? 'checked' : '' }} />
          <span style="font-weight: 500; color: #fff;">Publish Live</span>
        </label>
      </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Photo</button>
      <a href="{{ route('admin.asdreams.gallery.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection
