@extends('admin.layouts.app')

@section('title', 'Edit Team Member')
@section('page_title', 'Edit Team Member')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.team.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Team</span>
    </a>
    <h2 class="module-title">Edit Team Member: <em>{{ $teamMember->name }}</em></h2>
    <p class="module-subtitle">Update name, designation, bio, portrait photo, and social profile links.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 750px;">
  <form action="{{ route('admin.asdreams.team.update', $teamMember) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
      <div class="form-group">
        <label class="form-label" for="name">Full Name <span style="color: var(--gold);">*</span></label>
        <input type="text" id="name" name="name" value="{{ old('name', $teamMember->name) }}" required class="form-control" />
        @error('name') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="designation">Designation / Role <span style="color: var(--gold);">*</span></label>
        <input type="text" id="designation" name="designation" value="{{ old('designation', $teamMember->designation) }}" required class="form-control" />
        @error('designation') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="bio">Short Biography / Artistic Background</label>
        <textarea id="bio" name="bio" rows="3" class="form-control">{{ old('bio', $teamMember->bio) }}</textarea>
        @error('bio') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="grid-column: span 2;">
        <label class="form-label" for="photo">Change Portrait Photo</label>
        @if($teamMember->photo_url)
          <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; background: #000; display: inline-block;">
            <img src="{{ $teamMember->photo_url }}" alt="{{ $teamMember->name }}" style="max-height: 120px; width: 90px; object-fit: cover; display: block;" />
          </div>
        @endif
        <input type="file" id="photo" name="photo" accept="image/*" class="form-control" />
        <span class="form-hint">Leave blank to retain current photo.</span>
        @error('photo') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="instagram">Instagram Profile URL</label>
        <input type="text" id="instagram" name="instagram" value="{{ old('instagram', $teamMember->instagram) }}" class="form-control" />
        @error('instagram') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="linkedin">LinkedIn Profile URL</label>
        <input type="text" id="linkedin" name="linkedin" value="{{ old('linkedin', $teamMember->linkedin) }}" class="form-control" />
        @error('linkedin') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" id="order" name="order" value="{{ old('order', $teamMember->order) }}" min="1" class="form-control" />
        @error('order') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="display: flex; align-items: flex-end; padding-bottom: 0.5rem;">
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_published" value="1" {{ old('is_published', $teamMember->is_published ? '1' : '0') == '1' ? 'checked' : '' }} />
          <span style="font-weight: 500; color: #fff;">Visible on Team Page</span>
        </label>
      </div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Update Member</button>
      <a href="{{ route('admin.asdreams.team.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

