@extends('admin.layouts.app')

@section('title', 'Edit Model — ' . $model->name)
@section('header_title', 'Edit Model Profile')

@section('content')
<div class="module-header">
  <div>
    <a href="{{ route('admin.models') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Models Directory</span>
    </a>
    <h2 class="module-title">Edit <em>{{ $model->name }}</em></h2>
    <p class="module-subtitle">Update profile details, portfolio imagery, stats, and runway season assignments.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('idfr.models') }}" target="_blank" class="btn-subtle" title="View Public Portal">
      <span>Public Preview ↗</span>
    </a>
  </div>
</div>

<form action="{{ route('admin.models.update', $model) }}" method="POST" enctype="multipart/form-data" class="editor-form">
  @csrf
  @method('PUT')

  <div class="form-layout">
    <!-- Left Column: Primary Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Essential Information</h3>

        <div class="form-group">
          <label for="name" class="form-label">Full Name <span class="required">*</span></label>
          <input type="text" id="name" name="name" value="{{ old('name', $model->name) }}" required class="form-control @error('name') is-invalid @enderror" />
          @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="gender" class="form-label">Category / Gender <span class="required">*</span></label>
            <select id="gender" name="gender" required class="form-control">
              <option value="Female" {{ old('gender', $model->gender) === 'Female' ? 'selected' : '' }}>Female</option>
              <option value="Male" {{ old('gender', $model->gender) === 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Kids" {{ old('gender', $model->gender) === 'Kids' ? 'selected' : '' }}>Kids</option>
              <option value="Other" {{ old('gender', $model->gender) === 'Other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="level" class="form-label">Experience Tier <span class="required">*</span></label>
            <select id="level" name="level" required class="form-control">
              <option value="New Face" {{ old('level', $model->level) === 'New Face' ? 'selected' : '' }}>New Face</option>
              <option value="Experienced" {{ old('level', $model->level) === 'Experienced' ? 'selected' : '' }}>Experienced</option>
              <option value="Kids" {{ old('level', $model->level) === 'Kids' ? 'selected' : '' }}>Kids</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="location" class="form-label">Location (City / State)</label>
            <input type="text" id="location" name="location" value="{{ old('location', $model->location) }}" class="form-control" />
          </div>

          <div class="form-group">
            <label for="experience" class="form-label">Experience</label>
            <input type="text" id="experience" name="experience" value="{{ old('experience', $model->experience) }}" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="age" class="form-label">Age</label>
            <input type="number" id="age" name="age" value="{{ old('age', $model->age) }}" min="3" max="80" class="form-control" />
          </div>

          <div class="form-group">
            <label for="height" class="form-label">Height</label>
            <input type="text" id="height" name="height" value="{{ old('height', $model->height) }}" class="form-control" />
          </div>
        </div>

        <div class="form-group">
          <label for="availability" class="form-label">Booking Availability</label>
          <input type="text" id="availability" name="availability" value="{{ old('availability', $model->availability) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label for="description" class="form-label">Biography & Editorial Bio</label>
          <textarea id="description" name="description" rows="4" class="form-control">{{ old('description', $model->description) }}</textarea>
        </div>
      </div>

      <!-- Measurements & Skills -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Specialisations & Attributes</h3>

        <div class="form-group">
          <label class="form-label">Model Work Types</label>
          <div class="checkbox-group">
            @php $currentTypes = (array) old('types', $model->types ?? []); @endphp
            <label class="custom-checkbox">
              <input type="checkbox" name="types[]" value="Runway" {{ in_array('Runway', $currentTypes) ? 'checked' : '' }}>
              <span>Runway</span>
            </label>
            <label class="custom-checkbox">
              <input type="checkbox" name="types[]" value="Editorial" {{ in_array('Editorial', $currentTypes) ? 'checked' : '' }}>
              <span>Editorial</span>
            </label>
            <label class="custom-checkbox">
              <input type="checkbox" name="types[]" value="Commercial" {{ in_array('Commercial', $currentTypes) ? 'checked' : '' }}>
              <span>Commercial</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="skills" class="form-label">Key Skills (Comma Separated)</label>
          <input type="text" id="skills" name="skills" value="{{ old('skills', is_array($model->skills) ? implode(', ', $model->skills) : '') }}" class="form-control" />
        </div>

        <div class="form-group">
          <label for="specialities" class="form-label">Specialities (Comma Separated)</label>
          <input type="text" id="specialities" name="specialities" value="{{ old('specialities', is_array($model->specialities) ? implode(', ', $model->specialities) : '') }}" class="form-control" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="instagram_url" class="form-label">Instagram Profile URL</label>
            <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $model->instagram_url) }}" class="form-control" />
          </div>

          <div class="form-group">
            <label for="portfolio_url" class="form-label">Portfolio / Agency Link</label>
            <input type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $model->portfolio_url) }}" class="form-control" />
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Settings & Photos -->
    <div class="form-col-side">
      <!-- Publishing Status Card -->
      <div class="admin-card">
        <h3 class="card-section-title">Publication & Season</h3>

        <div class="form-group">
          <label for="status" class="form-label">Status <span class="required">*</span></label>
          <select id="status" name="status" class="form-control">
            <option value="published" {{ old('status', $model->status) === 'published' ? 'selected' : '' }}>Published (Live on Public Portal)</option>
            <option value="draft" {{ old('status', $model->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
            <option value="archived" {{ old('status', $model->status) === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Associated Runway Seasons</label>
          <div class="season-selector-list">
            @php $currentSeasons = (array) old('seasons', $model->seasons->pluck('id')->toArray()); @endphp
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
          <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $model->display_order) }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $model->is_featured) ? 'checked' : '' }}>
            <span>Mark as Featured Talent</span>
          </label>
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_kid" value="1" {{ old('is_kid', $model->is_kid) ? 'checked' : '' }}>
            <span>Kids Talent Roster</span>
          </label>
        </div>

        <div class="form-actions-sticky">
          <button type="submit" class="btn-gold btn-block">Save Changes</button>
        </div>
      </div>

      <!-- Media & Photo Upload Card -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Media & Photographs</h3>

        <div class="form-group">
          <label class="form-label">Current Profile Photograph</label>
          <div class="current-photo-container">
            <img id="profile_photo_preview" src="{{ $model->photo_url }}" alt="{{ $model->name }}" />
          </div>

          <label for="profile_photo" class="form-label" style="margin-top:0.75rem;">Replace Photo</label>
          <label for="profile_photo" class="upload-dropzone" style="cursor: pointer;">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span id="profile_photo_text">Upload New Photo</span>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;" onchange="if(this.files && this.files[0]){ document.getElementById('profile_photo_preview').src = URL.createObjectURL(this.files[0]); document.getElementById('profile_photo_text').textContent = this.files[0].name; }" />
          </label>
        </div>

        <!-- Current Portfolio Grid -->
        @if(!empty($model->portfolio_images))
          <div class="form-group">
            <label class="form-label">Current Portfolio Looks ({{ count($model->portfolio_images) }})</label>
            <div class="portfolio-thumb-grid">
              @foreach($model->portfolio_images as $pImg)
                <div class="portfolio-thumb-item">
                  <img src="{{ str_starts_with($pImg, 'http') ? $pImg : asset(ltrim($pImg, '/')) }}" alt="" />
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <div class="form-group">
          <label for="portfolio_images" class="form-label">Add More Portfolio Images</label>
          <label for="portfolio_images" class="upload-dropzone" style="cursor: pointer;">
            <svg viewBox="0 0 24 24"><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span id="portfolio_images_text">Upload additional images</span>
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
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--text-muted);
    font-size: 0.8rem;
    text-decoration: none;
    margin-bottom: 0.5rem;
    transition: color 0.2s ease;
  }
  .back-link:hover { color: var(--gold); }
  .back-link svg { width: 14px; height: 14px; stroke: currentColor; stroke-width: 2; fill: none; }

  .form-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
  }
  @media (max-width: 992px) {
    .form-layout { grid-template-columns: 1fr; }
  }

  .card-section-title {
    font-family: var(--font-serif);
    font-size: 1.15rem;
    color: var(--gold);
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-subtle);
  }

  .form-group {
    margin-bottom: 1.25rem;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  @media (max-width: 600px) {
    .form-row { grid-template-columns: 1fr; }
  }

  .form-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
  }
  .required { color: #ef4444; }

  .form-control {
    width: 100%;
    background: #080808;
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    padding: 0.65rem 0.85rem;
    color: #fff;
    font-size: 0.9rem;
    font-family: inherit;
    transition: border-color 0.2s ease;
  }
  .form-control:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 1px var(--gold);
  }
  .form-error {
    display: block;
    color: #ef4444;
    font-size: 0.75rem;
    margin-top: 0.25rem;
  }

  .checkbox-group {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .custom-checkbox {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.85rem;
    color: var(--text-primary);
  }
  .custom-checkbox input[type="checkbox"] {
    accent-color: var(--gold);
    width: 16px;
    height: 16px;
  }
  .custom-checkbox--block {
    display: flex;
    margin-bottom: 0.5rem;
    padding: 0.35rem 0.5rem;
    border-radius: 4px;
    background: #080808;
    border: 1px solid var(--border-subtle);
  }

  .season-selector-list {
    max-height: 160px;
    overflow-y: auto;
    padding-right: 0.5rem;
  }

  .upload-dropzone {
    border: 2px dashed var(--border-subtle);
    border-radius: 6px;
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    background: #080808;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.85rem;
    transition: all 0.2s ease;
  }
  .upload-dropzone:hover {
    border-color: var(--gold);
  }
  .upload-dropzone svg {
    width: 22px;
    height: 22px;
    stroke: var(--text-muted);
    stroke-width: 1.8;
    fill: none;
  }

  .current-photo-container {
    width: 110px;
    height: 145px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid var(--border-gold);
    margin-bottom: 0.5rem;
  }
  .current-photo-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .portfolio-thumb-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
  }
  .portfolio-thumb-item {
    aspect-ratio: 3/4;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid var(--border-subtle);
  }
  .portfolio-thumb-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .btn-block {
    width: 100%;
    justify-content: center;
    padding: 0.85rem;
    font-size: 0.95rem;
  }
</style>
@endsection
