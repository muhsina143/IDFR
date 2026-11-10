@extends('admin.layouts.app')

@section('title', 'Edit Creative Brand')
@section('page_title', 'Edit Brand')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.brands.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Brands</span>
    </a>
    <h2 class="module-title">Edit Brand: <em>{{ $brand->name }}</em></h2>
    <p class="module-subtitle">Update logos, taglines, layout variant, and target links.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 850px;">
  <form action="{{ route('admin.asdreams.brands.update', $brand) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="name">Brand Name <span style="color: var(--gold);">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $brand->name) }}" required class="form-control" />
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="tagline">Tagline / Sub-heading</label>
        <input type="text" id="tagline" name="tagline" value="{{ old('tagline', $brand->tagline) }}" class="form-control" />
        <span class="form-hint">Displayed under the brand name.</span>
        @error('tagline') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="css_variant">Card Layout Variant <span style="color: var(--gold);">*</span></label>
        <select id="css_variant" name="css_variant" class="form-control">
          <option value="standard" {{ old('css_variant', $brand->css_variant) == 'standard' ? 'selected' : '' }}>Standard (Logo + Name + Tagline)</option>
          <option value="wide" {{ old('css_variant', $brand->css_variant) == 'wide' ? 'selected' : '' }}>Wide / IDFR (Embedded full logo, arrow pinned below)</option>
          <option value="tall" {{ old('css_variant', $brand->css_variant) == 'tall' ? 'selected' : '' }}>Tall / Sketch (Vertical logo, e.g. Athul's Academy)</option>
          <option value="lakme" {{ old('css_variant', $brand->css_variant) == 'lakme' ? 'selected' : '' }}>Lakmé / Beauty (Portrait image + Tagline)</option>
        </select>
        <span class="form-hint">Determines sizing and text formatting for the popup card.</span>
        @error('css_variant') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="description">Short Description / Overview</label>
        <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $brand->description) }}</textarea>
        @error('description') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="logo">Change Brand Logo</label>
        @if($brand->logo_url)
          <div style="margin-bottom: 0.75rem; padding: 0.5rem; background: #000; border: 1px solid var(--border-subtle); border-radius: 4px; display: inline-block;">
            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" style="max-height: 55px; max-width: 140px; object-fit: contain; display: block;" />
          </div>
        @endif
        <input type="file" id="logo" name="logo" accept="image/*" class="form-control" />
        <span class="form-hint">Leave blank to retain current logo.</span>
        @error('logo') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="cover_image">Change Cover Image (Homepage Showcase)</label>
        @if($brand->cover_image_url)
          <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; display: inline-block;">
            <img src="{{ $brand->cover_image_url }}" alt="Cover" style="height: 55px; width: 45px; object-fit: cover; display: block;" />
          </div>
        @endif
        <input type="file" id="cover_image" name="cover_image" accept="image/*" class="form-control" />
        <span class="form-hint">Leave blank to retain current cover image.</span>
        @error('cover_image') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="link_url">Target URL / Link</label>
        <input type="text" id="link_url" name="link_url" value="{{ old('link_url', $brand->link_url) }}" class="form-control" />
        <span class="form-hint">Internal path or external website URL.</span>
        @error('link_url') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" id="order" name="order" value="{{ old('order', $brand->order) }}" min="1" class="form-control" />
        @error('order') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="tags_raw">Pill Tags (Comma separated)</label>
        <input type="text" id="tags_raw" name="tags_raw" value="{{ old('tags_raw', is_array($brand->tags) ? implode(', ', $brand->tags) : '') }}" class="form-control" />
        <span class="form-hint">Separate multiple tags with commas.</span>
        @error('tags_raw') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $brand->is_active ? '1' : '0') == '1' ? 'checked' : '' }} />
          <span style="font-weight: 500; color: #fff;">Active &amp; Visible across ASDreamStudio</span>
        </label>
      </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Brand</button>
      <a href="{{ route('admin.asdreams.brands.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

