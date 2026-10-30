@extends('admin.layouts.app')

@section('title', 'Edit Creative Professional — ' . $professional->name)
@section('header_title', 'Edit Specialist Profile')

@section('content')
<div class="module-header">
  <div>
    <a href="{{ route('admin.professionals') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Professionals Directory</span>
    </a>
    <h2 class="module-title">Edit <em>{{ $professional->name }}</em></h2>
    <p class="module-subtitle">Update specialist portfolio, category classification, services, and runway seasons.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('idfr.professionals') }}#creative-{{ $professional->category }}" target="_blank" class="btn-subtle" title="View on Public Website">
      <span>Public Category ↗</span>
    </a>
  </div>
</div>

<form action="{{ route('admin.professionals.update', $professional) }}" method="POST" enctype="multipart/form-data" class="editor-form">
  @csrf
  @method('PUT')

  <div class="form-layout">
    <!-- Left Column: Primary Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Specialist Profile</h3>

        <div class="form-row">
          <div class="form-group">
            <label for="name" class="form-label">Full Name <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $professional->name) }}" required class="form-control @error('name') is-invalid @enderror" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label for="category" class="form-label">Professional Category <span class="required">*</span></label>
            <select id="category" name="category" required class="form-control">
              @foreach($categories as $catKey => $catName)
                <option value="{{ $catKey }}" {{ old('category', $professional->category) === $catKey ? 'selected' : '' }}>{{ $catName }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="professional_title" class="form-label">Professional Title / Subtitle</label>
            <input type="text" id="professional_title" name="professional_title" value="{{ old('professional_title', $professional->professional_title) }}" class="form-control" />
          </div>

          <div class="form-group">
            <label for="experience" class="form-label">Experience</label>
            <input type="text" id="experience" name="experience" value="{{ old('experience', $professional->experience) }}" class="form-control" />
          </div>
        </div>

        <div class="form-group">
          <label for="location" class="form-label">Location (City, State)</label>
          <input type="text" id="location" name="location" value="{{ old('location', $professional->location) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label for="biography" class="form-label">Professional Bio &amp; Experience Highlights</label>
          <textarea id="biography" name="biography" rows="4" class="form-control">{{ old('biography', $professional->biography) }}</textarea>
        </div>
      </div>

      <!-- Services & Socials -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Services Offered &amp; Portfolios</h3>

        <div class="form-group">
          <label for="services" class="form-label">Services Offered (Comma Separated)</label>
          <input type="text" id="services" name="services" value="{{ old('services', is_array($professional->services) ? implode(', ', $professional->services) : '') }}" class="form-control" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="website_url" class="form-label">Website / Portfolio Link</label>
            <input type="url" id="website_url" name="website_url" value="{{ old('website_url', $professional->website_url) }}" class="form-control" />
          </div>

          <div class="form-group">
            <label for="instagram_url" class="form-label">Instagram Profile URL</label>
            <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $professional->instagram_url) }}" class="form-control" />
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
            <option value="published" {{ old('status', $professional->status) === 'published' ? 'selected' : '' }}>Published (Live on Portal)</option>
            <option value="draft" {{ old('status', $professional->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
            <option value="archived" {{ old('status', $professional->status) === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Associated Runway Seasons</label>
          <div class="season-selector-list">
            @php $currentSeasons = (array) old('seasons', $professional->seasons->pluck('id')->toArray()); @endphp
            @foreach($seasons as $s)
              <label class="custom-checkbox custom-checkbox--block">
                <input type="checkbox" name="seasons[]" value="{{ $s->id }}" {{ in_array($s->id, $currentSeasons) ? 'checked' : '' }}>
                <span>{{ $s->title }} ({{ $s->year }})</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="form-group">
          <label for="display_order" class="form-label">Display Order</label>
          <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $professional->display_order) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $professional->is_featured) ? 'checked' : '' }}>
            <span>Featured Specialist</span>
          </label>
        </div>

        <div class="form-actions-sticky">
          <button type="submit" class="btn-gold btn-block">Save Changes</button>
        </div>
      </div>

      <!-- Media & Photo Upload Card -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Imagery &amp; Portfolio Media</h3>

        <div class="form-group">
          <label class="form-label">Current Portrait</label>
          <div class="current-photo-container">
            <img id="profile_photo_preview" src="{{ $professional->photo_url }}" alt="{{ $professional->name }}" />
          </div>
          <label for="profile_photo" class="upload-dropzone" style="cursor: pointer;">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span id="profile_photo_text">Replace Photo</span>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;" onchange="if(this.files && this.files[0]){ document.getElementById('profile_photo_preview').src = URL.createObjectURL(this.files[0]); document.getElementById('profile_photo_text').textContent = this.files[0].name; }" />
          </label>
        </div>

        <!-- Current Portfolio Grid -->
        @if(!empty($professional->portfolio_images))
          <div class="form-group">
            <label class="form-label">Current Portfolio Work ({{ count($professional->portfolio_images) }})</label>
            <div class="portfolio-thumb-grid">
              @foreach($professional->portfolio_images as $pItem)
                @php $imgSrc = is_array($pItem) ? ($pItem['image'] ?? '') : $pItem; @endphp
                @if($imgSrc)
                  <div class="portfolio-thumb-item" title="{{ is_array($pItem) ? ($pItem['caption'] ?? '') : '' }}">
                    <img src="{{ str_starts_with($imgSrc, 'http') ? $imgSrc : asset(ltrim($imgSrc, '/')) }}" alt="" />
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        @endif

        <div class="form-group">
          <label for="portfolio_images" class="form-label">Add More Portfolio Images</label>
          <label for="portfolio_images" class="upload-dropzone" style="cursor: pointer;">
            <svg viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span id="portfolio_images_text">Upload additional looks</span>
            <input type="file" id="portfolio_images" name="portfolio_images[]" multiple accept="image/*" style="display:none;" onchange="if(this.files && this.files.length){ document.getElementById('portfolio_images_text').textContent = this.files.length + ' look(s) selected'; }" />
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
  .upload-dropzone { border: 2px dashed var(--border-subtle); border-radius: 6px; padding: 1rem; text-align: center; cursor: pointer; background: #080808; display: flex; flex-direction: column; align-items: center; gap: 0.35rem; font-size: 0.85rem; }
  .upload-dropzone:hover { border-color: var(--gold); }
  .upload-dropzone svg { width: 22px; height: 22px; stroke: var(--text-muted); stroke-width: 1.8; fill: none; }
  .current-photo-container { width: 110px; height: 145px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-gold); margin-bottom: 0.5rem; }
  .current-photo-container img { width: 100%; height: 100%; object-fit: cover; }
  .portfolio-thumb-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
  .portfolio-thumb-item { aspect-ratio: 3/4; border-radius: 4px; overflow: hidden; border: 1px solid var(--border-subtle); }
  .portfolio-thumb-item img { width: 100%; height: 100%; object-fit: cover; }
  .btn-block { width: 100%; justify-content: center; padding: 0.85rem; font-size: 0.95rem; }
</style>
@endsection
