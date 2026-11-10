@extends('admin.layouts.app')

@section('title', 'Add New Team Member')
@section('page_title', 'Create Team Member')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.team.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Team</span>
    </a>
    <h2 class="module-title">Add Team <em>Member</em></h2>
    <p class="module-subtitle">Add a photographer, cinematographer, or creative artist to the team roster.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 750px;">
  <form action="{{ route('admin.asdreams.team.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group">
        <label class="form-label" for="name">Full Name <span style="color: var(--gold);">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required class="form-control" placeholder="e.g. Athul S." />
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="designation">Designation / Role <span style="color: var(--gold);">*</span></label>
        <input type="text" id="designation" name="designation" value="{{ old('designation') }}" required class="form-control" placeholder="e.g. Founder &amp; Creative Director" />
        @error('designation') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="bio">Short Biography / Artistic Background</label>
        <textarea id="bio" name="bio" rows="3" class="form-control" placeholder="Brief background of their creative experience and vision...">{{ old('bio') }}</textarea>
        @error('bio') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="photo">Portrait Photo <span style="color: var(--gold);">*</span></label>
        <input type="file" id="photo" name="photo" required accept="image/*" class="form-control" />
        <span class="form-hint">High quality vertical portrait photo (aspect ratio 4:5 or 2:3, max 5MB).</span>
        @error('photo') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="instagram">Instagram Profile URL</label>
        <input type="text" id="instagram" name="instagram" value="{{ old('instagram') }}" class="form-control" placeholder="https://instagram.com/..." />
        @error('instagram') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="linkedin">LinkedIn Profile URL</label>
        <input type="text" id="linkedin" name="linkedin" value="{{ old('linkedin') }}" class="form-control" placeholder="https://linkedin.com/in/..." />
        @error('linkedin') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" id="order" name="order" value="{{ old('order', 1) }}" min="1" class="form-control" />
        @error('order') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_published" value="1" {{ old('is_published', '1') == '1' ? 'checked' : '' }} />
          <span style="font-weight: 500; color: #fff;">Visible on Team Page</span>
        </label>
      </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Add Team Member</button>
      <a href="{{ route('admin.asdreams.team.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

