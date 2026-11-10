@extends('admin.layouts.app')

@section('title', 'Add New Creative Brand')
@section('page_title', 'Create Brand')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.brands.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Brands</span>
    </a>
    <h2 class="module-title">Add Creative <em>Brand</em></h2>
    <p class="module-subtitle">Add a partner brand or creative house to the homepage showcase and "Our Brands" modal.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 850px;">
  <form action="{{ route('admin.asdreams.brands.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="name">Brand Name <span style="color: var(--gold);">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control" placeholder="e.g. Indian Designer Fashion Runway" />
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="tagline">Tagline / Sub-heading</label>
        <input type="text" id="tagline" name="tagline" value="{{ old('tagline') }}" class="form-control" placeholder="e.g. Fashion Runway & Showcase" />
        <span class="form-hint">Displayed under the brand name.</span>
        @error('tagline') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="css_variant">Card Layout Variant <span style="color: var(--gold);">*</span></label>
        <select id="css_variant" name="css_variant" class="form-control">
          <option value="standard" {{ old('css_variant') == 'standard' ? 'selected' : '' }}>Standard (Logo + Name + Tagline)</option>
          <option value="wide" {{ old('css_variant') == 'wide' ? 'selected' : '' }}>Wide / IDFR (Embedded full logo, arrow pinned below)</option>
          <option value="tall" {{ old('css_variant') == 'tall' ? 'selected' : '' }}>Tall / Sketch (Vertical logo, e.g. Athul's Academy)</option>
          <option value="lakme" {{ old('css_variant') == 'lakme' ? 'selected' : '' }}>Lakmé / Beauty (Portrait image + Tagline)</option>
        </select>
        <span class="form-hint">Determines sizing and text formatting for the popup card.</span>
        @error('css_variant') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="description">Short Description / Overview</label>
        <textarea id="description" name="description" rows="3" class="form-control" placeholder="Brief background of this brand house...">{{ old('description') }}</textarea>
        @error('description') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="logo">Brand Logo <span style="color: var(--gold);">*</span></label>
        <input type="file" id="logo" name="logo" required accept="image/*" class="form-control" />
        <span class="form-hint">PNG with transparent background or SVG recommended.</span>
        @error('logo') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="cover_image">Cover Image (for Homepage Showcase)</label>
        <input type="file" id="cover_image" name="cover_image" accept="image/*" class="form-control" />
        <span class="form-hint">High-res vertical photo (4:5 or 2:3 aspect ratio).</span>
        @error('cover_image') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="link_url">Target URL / Link</label>
        <input type="text" id="link_url" name="link_url" value="{{ old('link_url', '#') }}" class="form-control" placeholder="e.g. / or /asdreams/contact?brand=athuls" />
        <span class="form-hint">Internal path or external website URL.</span>
        @error('link_url') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" id="order" name="order" value="{{ old('order', 1) }}" min="1" class="form-control" />
        @error('order') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="tags_raw">Pill Tags (Comma separated)</label>
        <input type="text" id="tags_raw" name="tags_raw" value="{{ old('tags_raw') }}" class="form-control" placeholder="e.g. Runway, Grooming, Auditions, Mentorship" />
        <span class="form-hint">Separate multiple tags with commas.</span>
        @error('tags_raw') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} />
          <span style="font-weight: 500; color: #fff;">Active &amp; Visible across ASDreamStudio</span>
        </label>
      </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Create Brand</button>
      <a href="{{ route('admin.asdreams.brands.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

