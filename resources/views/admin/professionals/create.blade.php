@extends('admin.layouts.app')

@section('title', 'Add Creative Professional')
@section('header_title', 'Register Creative Specialist')

@section('content')
<div class="module-header">
  <div>
    <a href="{{ route('admin.professionals') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Professionals Directory</span>
    </a>
    <h2 class="module-title">New Creative <em>Professional</em></h2>
    <p class="module-subtitle">Register makeup artists, hairstylists, photographers, videographers, drone pilots, and editors.</p>
  </div>
</div>

<form action="{{ route('admin.professionals.store') }}" method="POST" enctype="multipart/form-data" class="editor-form">
  @csrf

  <div class="form-layout">
    <!-- Left Column: Primary Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Specialist Profile</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="name" class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Ananya Menon" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="category" class="form-label">Professional Category <span class="required">*</span></label>
            <select id="category" name="category" required class="form-control">
              @foreach($categories as $catKey => $catName)
                <option value="{{ $catKey }}" {{ old('category') === $catKey ? 'selected' : '' }}>{{ $catName }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="professional_title" class="form-label">Professional Title / Subtitle</label>
            <input type="text" id="professional_title" name="professional_title" value="{{ old('professional_title') }}" class="form-control" placeholder="e.g. Senior Bridal &amp; Runway Makeup Artist" />
          </div>

          <div class="form-group">
            <label for="experience" class="form-label">Experience</label>
            <input type="text" id="experience" name="experience" value="{{ old('experience') }}" class="form-control" placeholder="e.g. 5 years" />
          </div>
        </div>

        <div class="form-group">
          <label for="location" class="form-label">Location (City, State)</label>
          <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Kozhikode, Kerala" />
        </div>

        <div class="form-group">
          <label for="biography" class="form-label">Professional Bio &amp; Experience Highlights</label>
          <textarea id="biography" name="biography" rows="4" class="form-control" placeholder="Detail their expertise, signature styling techniques, and runway assignments...">{{ old('biography') }}</textarea>
        </div>
      </div>

      <!-- Services & Socials -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Services Offered &amp; Portfolios</h3>

        <div class="form-group">
          <label for="services" class="form-label">Services Offered (Comma Separated)</label>
          <input type="text" id="services" name="services" value="{{ old('services') }}" class="form-control" placeholder="e.g. Bridal makeup, Runway makeup, HD airbrush, Editorial shoots" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="website_url" class="form-label">Website / Portfolio Link</label>
            <input type="url" id="website_url" name="website_url" value="{{ old('website_url') }}" class="form-control" placeholder="https://..." />
          </div>

          <div class="form-group">
            <label for="instagram_url" class="form-label">Instagram Profile URL</label>
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
            <span>Featured Specialist</span>
          </label>
        </div>

        <div class="form-actions-sticky">
          <button type="submit" class="btn-gold btn-block">Save Professional</button>
        </div>
      </div>

      <!-- Media & Photo Upload Card -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Imagery &amp; Portfolio Media</h3>

        <div class="form-group">
          <label for="profile_photo" class="form-label">Portrait Photograph</label>
          <label for="profile_photo" class="upload-dropzone" style="cursor: pointer;">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span id="profile_photo_text">Choose specialist portrait</span>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;" onchange="if(this.files && this.files[0]){ document.getElementById('profile_photo_text').textContent = this.files[0].name; }" />
          </label>
          @error('profile_photo') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label for="portfolio_images" class="form-label">Portfolio Work (Multi-select)</label>
          <label for="portfolio_images" class="upload-dropzone" style="cursor: pointer;">
            <svg viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span id="portfolio_images_text">Upload beauty / camera portfolio</span>
            <input type="file" id="portfolio_images" name="portfolio_images[]" multiple accept="image/*" style="display:none;" onchange="if(this.files && this.files.length){ document.getElementById('portfolio_images_text').textContent = this.files.length + ' image(s) selected'; }" />
          </label>
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
