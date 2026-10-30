@extends('admin.layouts.app')

@section('title', 'Add New Model Profile')
@section('header_title', 'Create Model Profile')

@section('content')
<div class="module-header">
  <div>
    <a href="{{ route('admin.models') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Models Directory</span>
    </a>
    <h2 class="module-title">New Model <em>Profile</em></h2>
    <p class="module-subtitle">Register runway models, new faces, or commercial talent with measurements and portfolio media.</p>
  </div>
</div>

<form action="{{ route('admin.models.store') }}" method="POST" enctype="multipart/form-data" class="editor-form">
  @csrf

  <div class="form-layout">
    <!-- Left Column: Primary Details -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Essential Information</h3>

        <div class="form-group">
          <label for="name" class="form-label">Full Name <span class="required">*</span></label>
          <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Diya Pillai" />
          @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="gender" class="form-label">Category / Gender <span class="required">*</span></label>
            <select id="gender" name="gender" required class="form-control">
              <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
              <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
              <option value="Kids" {{ old('gender') === 'Kids' ? 'selected' : '' }}>Kids</option>
              <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="level" class="form-label">Experience Tier <span class="required">*</span></label>
            <select id="level" name="level" required class="form-control">
              <option value="New Face" {{ old('level') === 'New Face' ? 'selected' : '' }}>New Face</option>
              <option value="Experienced" {{ old('level') === 'Experienced' ? 'selected' : '' }}>Experienced</option>
              <option value="Kids" {{ old('level') === 'Kids' ? 'selected' : '' }}>Kids</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="location" class="form-label">Location (City / State)</label>
            <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Kochi, Kerala" />
          </div>

          <div class="form-group">
            <label for="experience" class="form-label">Experience</label>
            <input type="text" id="experience" name="experience" value="{{ old('experience') }}" class="form-control" placeholder="e.g. 2 Years" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="age" class="form-label">Age</label>
            <input type="number" id="age" name="age" value="{{ old('age') }}" min="3" max="80" class="form-control" placeholder="e.g. 22" />
          </div>

          <div class="form-group">
            <label for="height" class="form-label">Height</label>
            <input type="text" id="height" name="height" value="{{ old('height') }}" class="form-control" placeholder="e.g. 5'8&quot;" />
          </div>
        </div>

        <div class="form-group">
          <label for="availability" class="form-label">Booking Availability</label>
          <input type="text" id="availability" name="availability" value="{{ old('availability', 'Available for assignments') }}" class="form-control" placeholder="e.g. Available for assignments" />
        </div>

        <div class="form-group">
          <label for="description" class="form-label">Biography & Editorial Bio</label>
          <textarea id="description" name="description" rows="4" class="form-control" placeholder="Detail the model's runway presence, past campaigns, and portfolio style...">{{ old('description') }}</textarea>
        </div>
      </div>

      <!-- Measurements & Skills -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Specialisations & Attributes</h3>

        <div class="form-group">
          <label class="form-label">Model Work Types</label>
          <div class="checkbox-group">
            @php $oldTypes = (array) old('types', ['Runway', 'Editorial']); @endphp
            <label class="custom-checkbox">
              <input type="checkbox" name="types[]" value="Runway" {{ in_array('Runway', $oldTypes) ? 'checked' : '' }}>
              <span>Runway</span>
            </label>
            <label class="custom-checkbox">
              <input type="checkbox" name="types[]" value="Editorial" {{ in_array('Editorial', $oldTypes) ? 'checked' : '' }}>
              <span>Editorial</span>
            </label>
            <label class="custom-checkbox">
              <input type="checkbox" name="types[]" value="Commercial" {{ in_array('Commercial', $oldTypes) ? 'checked' : '' }}>
              <span>Commercial</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="skills" class="form-label">Key Skills (Comma Separated)</label>
          <input type="text" id="skills" name="skills" value="{{ old('skills') }}" class="form-control" placeholder="e.g. Runway walk, Editorial posing, Acting, Expression work" />
        </div>

        <div class="form-group">
          <label for="specialities" class="form-label">Specialities (Comma Separated)</label>
          <input type="text" id="specialities" name="specialities" value="{{ old('specialities') }}" class="form-control" placeholder="e.g. Bridal runway, Ethnic wear, Handloom lookbooks" />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="instagram_url" class="form-label">Instagram Profile URL</label>
            <input type="url" id="instagram_url" name="instagram_url" value="{{ old('instagram_url') }}" class="form-control" placeholder="https://instagram.com/..." />
          </div>

          <div class="form-group">
            <label for="portfolio_url" class="form-label">Portfolio / Agency Link</label>
            <input type="url" id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="form-control" placeholder="https://..." />
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
            <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Published (Live on Public Portal)</option>
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
          <small class="text-muted">Lower numbers appear first on the public grid.</small>
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
            <span>Mark as Featured Talent</span>
          </label>
        </div>

        <div class="form-group">
          <label class="custom-checkbox">
            <input type="checkbox" name="is_kid" value="1" {{ old('is_kid') ? 'checked' : '' }}>
            <span>Kids Talent Roster</span>
          </label>
        </div>

        <div class="form-actions-sticky">
          <button type="submit" class="btn-gold btn-block">Save Model Profile</button>
        </div>
      </div>

      <!-- Media & Photo Upload Card -->
      <div class="admin-card" style="margin-top: 1.5rem;">
        <h3 class="card-section-title">Media & Photographs</h3>

        <div class="form-group">
          <label for="profile_photo" class="form-label">Main Profile Photograph</label>
          <div class="upload-dropzone" onclick="document.getElementById('profile_photo').click()">
            <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span>Choose photo or drag file here</span>
            <small class="text-muted">JPG, PNG, WebP · Max 5MB</small>
            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" style="display:none;" onchange="previewUpload(this, '#mainPhotoPreview')" />
          </div>
          <div id="mainPhotoPreview" class="photo-preview-box" style="display:none;"></div>
          @error('profile_photo') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label for="portfolio_images" class="form-label">Additional Portfolio Looks (Multi-select)</label>
          <div class="upload-dropzone" onclick="document.getElementById('portfolio_images').click()">
            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
            <span>Select runway / editorial looks</span>
            <input type="file" id="portfolio_images" name="portfolio_images[]" multiple accept="image/*" style="display:none;" />
          </div>
          <small class="text-muted">Select multiple images for the runway lookbook slider.</small>
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
    padding: 1.5rem 1rem;
    text-align: center;
    cursor: pointer;
    background: #080808;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.35rem;
    transition: all 0.2s ease;
  }
  .upload-dropzone:hover {
    border-color: var(--gold);
    background: rgba(229, 184, 61, 0.02);
  }
  .upload-dropzone svg {
    width: 28px;
    height: 28px;
    stroke: var(--text-muted);
    stroke-width: 1.5;
    fill: none;
  }
  .photo-preview-box {
    margin-top: 0.75rem;
    width: 100px;
    height: 130px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid var(--gold);
  }
  .photo-preview-box img {
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
<script>
  function previewUpload(input, targetSelector) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const box = document.querySelector(targetSelector);
        box.innerHTML = `<img src="${e.target.result}" alt="Preview" />`;
        box.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endsection
