@extends('admin.layouts.app')

@section('title', 'Add New Fashion Designer')
@section('header_title', 'Register Designer')

@section('content')
<div class="module-header">
  <div>
    <a href="{{ route('admin.designers') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Designers Directory</span>
    </a>
    <h2 class="module-title">New Couture <em>Designer</em></h2>
    <p class="module-subtitle">Register a fashion house, couture label, collection title, lookbook assets, and runway seasons.</p>
  </div>
</div>

<form action="{{ route('admin.designers.store') }}" method="POST" enctype="multipart/form-data" class="editor-form">
  @csrf

  <div class="form-layout">
    <!-- Left Column: Primary Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Designer &amp; Brand Profile</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="name" class="form-label">Designer Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Tara Nair" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="brand_name" class="form-label">Brand / Label Name</label>
            <input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name') }}" class="form-control" placeholder="e.g. Tara Nair Couture" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="location" class="form-label">Location (City / State)</label>
            <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Kochi, Kerala" />
          </div>

          <div class="form-group">
            <label for="experience" class="form-label">Industry Experience</label>
            <input type="text" id="experience" name="experience" value="{{ old('experience') }}" class="form-control" placeholder="e.g. 6 years" />
          </div>
        </div>

        <div class="form-group">
          <label for="short_intro" class="form-label">Short Tagline / Introduction</label>
          <input type="text" id="short_intro" name="short_intro" value="{{ old('short_intro') }}" class="form-control" placeholder="e.g. Contemporary womenswear rooted in Kerala handloom..." />
        </div>

        <div class="form-group">
          <label for="biography" class="form-label">Biography &amp; Design Philosophy</label>
          <textarea id="biography" name="biography" rows="4" class="form-control" placeholder="Describe the designer's background, atelier roots, and aesthetic language...">{{ old('biography') }}</textarea>
        </div>
      </div>

      <!-- Collection & Services -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Showcase Collection &amp; Links</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="collection_title" class="form-label">Showcase Collection Title</label>
            <input type="text" id="collection_title" name="collection_title" value="{{ old('collection_title') }}" class="form-control" placeholder="e.g. Heritage Weft 2026" />
          </div>

          <div class="form-group">
            <label for="services" class="form-label">Services / Categories (Comma separated)</label>
            <input type="text" id="services" name="services" value="{{ old('services') }}" class="form-control" placeholder="e.g. Bridal wear, Handloom couture, Runway showcase" />
          </div>
        </div>

        <div class="form-group">
          <label for="collection_description" class="form-label">Collection Overview</label>
          <textarea id="collection_description" name="collection_description" rows="3" class="form-control" placeholder="Details about fabrics, craft techniques, and inspiration behind this collection...">{{ old('collection_description') }}</textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="website_url" class="form-label">Official Website URL</label>
            <input type="url" id="website_url" name="website_url" value="{{ old('website_url') }}" class="form-control" placeholder="https://..." />
          </div>

          <div class="form-group">
            <label for="instagram_url" class="form-label">Instagram Handle / URL</label>
            <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url') }}" class="form-control" placeholder="https://instagram.com/..." />
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Settings & Photos -->
    <div class="form-col-side">
      <div class="admin-card">
        <h3 class="card-section-title">Publication &amp; Season</h3>

        <div class="form-group">
          <label for="status" class="form-label">Status <span class="required">*</span></label>
          <select id="status" name="status" class="form-control">
            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live on Portal)</option>
            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
            <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Associated Runway Seasons</label>
          <div class="season-selector-list">
            @foreach($seasons as $s)
              <label class="custom-checkbox custom-checkbox--block">
                <input type="checkbox" name="seasons[]" value="{{ $s->id }}" {{ in_array($s->id, (array) old('seasons', [])) ? 'checked' : '' }}>
                <span>{{ $s->title }} ({{ $s->year }})</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="form-group">
          <label for="display_order" class="form-label">Display Order</label>
          <input type="number" id="display_order" name="display_order" value="{{ old('display_order', 0) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <span>Featured Brand / Headline Designer</span>
          </label>
        </div>

        <div class="form-actions-sticky">
          <button type="submit" class="btn-gold btn-block">Save Designer</button>
        </div>
      </div>

      <!-- Media & Photo Upload Card -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Imagery &amp; Brand Media</h3>

        <div class="form-group">
          <label for="profile_photo" class="form-label">Designer Profile Photo</label>
          <div class="upload-dropzone" onclick="document.getElementById('profile_photo').click()">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span>Choose portrait photograph</span>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;" />
          </div>
          @error('profile_photo') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label for="brand_logo" class="form-label">Brand Logo (PNG / Vector)</label>
          <div class="upload-dropzone" onclick="document.getElementById('brand_logo').click()">
            <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            <span>Upload brand insignia or mark</span>
            <input type="file" id="brand_logo" name="brand_logo" accept="image/*" style="display:none;" />
          </div>
        </div>

        <div class="form-group">
          <label for="portfolio_images" class="form-label">Collection Lookbook Looks</label>
          <div class="upload-dropzone" onclick="document.getElementById('portfolio_images').click()">
            <svg viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span>Upload runway garments (multi-select)</span>
            <input type="file" id="portfolio_images" name="portfolio_images[]" multiple accept="image/*" style="display:none;" />
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('styles')
<style>
  .back-link { display: inline-flex; align-items: center; gap: 0.4rem; color: var(--text-muted); font-size: 0.8rem; text-decoration: none; margin-bottom: 0.5rem; }
  .back-link:hover { color: var(--gold); }
  .back-link svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2; fill: none; }
  .form-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
  @media (max-width: 992px) { .form-layout { grid-template-columns: 1fr; } }
  .card-section-title { font-family: var(--font-serif); font-size: 1.15rem; color: var(--gold); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle); }
  .form-group { margin-bottom: 1.25rem; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }
  .form-label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.4rem; }
  .required { color: #ef4444; }
  .form-control { width: 100%; background: #080808; border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.65rem 0.85rem; color: #fff; font-size: 0.9rem; font-family: inherit; }
  .form-control:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 1px var(--gold); }
  .form-error { display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; }
  .custom-checkbox { display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.85rem; color: var(--text-primary); }
  .custom-checkbox input[type="checkbox"] { accent-color: var(--gold); width: 16px; height: 16px; }
  .custom-checkbox--block { display: flex; margin-bottom: 0.5rem; padding: 0.35rem 0.5rem; border-radius: 4px; background: #080808; border: 1px solid var(--border-subtle); }
  .season-selector-list { max-height: 160px; overflow-y: auto; padding-right: 0.5rem; }
  .upload-dropzone { border: 2px dashed var(--border-subtle); border-radius: 6px; padding: 1.25rem 1rem; text-align: center; cursor: pointer; background: #080808; display: flex; flex-direction: column; align-items: center; gap: 0.35rem; }
  .upload-dropzone:hover { border-color: var(--gold); }
  .upload-dropzone svg { width: 24px; height: 24px; stroke: var(--text-muted); stroke-width: 1.5; fill: none; }
  .btn-block { width: 100%; justify-content: center; padding: 0.85rem; font-size: 0.95rem; }
</style>
@endsection
