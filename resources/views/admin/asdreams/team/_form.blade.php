@php
  $member = $teamMember ?? $team ?? null;
  $isEdit = $member !== null;
  $selectedType = old('member_type', $member->member_type ?? \App\Models\Asdreams\AsdreamsTeamMember::TYPE_TEAM_MEMBER);
  $isActiveChecked = old('is_active', $isEdit ? ($member->is_active ? '1' : '0') : '1') == '1';
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
  <div class="form-group">
    <label class="form-label" for="member_type">Member Type <span style="color: var(--gold);">*</span></label>
    <select id="member_type" name="member_type" required class="form-control">
      @foreach($memberTypes as $typeValue => $typeLabel)
        <option value="{{ $typeValue }}" {{ $selectedType === $typeValue ? 'selected' : '' }}>
          {{ $typeLabel }}
        </option>
      @endforeach
    </select>
    <span class="form-hint">Determines highlighted placement on the public Team page.</span>
    @error('member_type') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="name">Full Name <span style="color: var(--gold);">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $member->name ?? '') }}" required class="form-control" placeholder="e.g. Athul S." />
    @error('name') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="designation">Designation / Role</label>
    <input type="text" id="designation" name="designation" value="{{ old('designation', $member->designation ?? '') }}" class="form-control" placeholder="e.g. Founder &amp; Creative Director" />
    @error('designation') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="display_order">Display Order</label>
    <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $member->display_order ?? ($nextOrder ?? 1)) }}" min="0" class="form-control" />
    <span class="form-hint">Lower numbers appear first within each section.</span>
    @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2;">
    <label class="form-label" for="bio">Short Bio / Quote</label>
    <textarea id="bio" name="bio" rows="3" class="form-control" placeholder="Brief quote, artistic background, or vision shown on the Team page...">{{ old('bio', $member->bio ?? '') }}</textarea>
    @error('bio') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2;">
    <label class="form-label" for="photo">Portrait Photo</label>
    <div style="display: flex; align-items: flex-start; gap: 1.25rem; flex-wrap: wrap;">
      <div id="photoPreviewWrap" style="width: 96px; height: 124px; border-radius: 6px; overflow: hidden; background: #080808; border: 1px solid var(--border-gold); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        @if($isEdit && $member->photo_url)
          <img id="photoPreviewImg" src="{{ $member->photo_url }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" />
        @else
          <img id="photoPreviewImg" src="{{ asset('images/founders/tt6.jpg') }}" alt="Portrait preview" style="width: 100%; height: 100%; object-fit: cover; display: block; opacity: 0.45;" />
        @endif
      </div>
      <div style="flex: 1; min-width: 220px;">
        <input type="file" id="photo" name="photo" accept="image/*" class="form-control" />
        <span class="form-hint" style="display: block; margin-top: 0.45rem;">
          {{ $isEdit ? 'Upload a new portrait only to replace the current image. ' : 'High quality vertical portrait photo (3:4 or 4:5, max 5MB). Leave empty to use studio default portrait.' }}
        </span>
        @error('photo') <span class="form-error">{{ $message }}</span> @enderror
      </div>
    </div>
  </div>

  <div class="form-group">
    <label class="form-label" for="instagram">Instagram Profile URL</label>
    <input type="text" id="instagram" name="instagram" value="{{ old('instagram', $member->instagram ?? '') }}" class="form-control" placeholder="https://instagram.com/..." />
    @error('instagram') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="linkedin">LinkedIn Profile URL</label>
    <input type="text" id="linkedin" name="linkedin" value="{{ old('linkedin', $member->linkedin ?? '') }}" class="form-control" placeholder="https://linkedin.com/in/..." />
    @error('linkedin') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2; margin-bottom: 0;">
    <input type="hidden" name="is_active" value="0" />
    <label class="custom-checkbox--block" style="cursor: pointer; margin-bottom: 0;">
      <input type="checkbox" id="is_active" name="is_active" value="1" {{ $isActiveChecked ? 'checked' : '' }} />
      <span>
        <span class="checkbox-title">Active &amp; Visible on Team Page</span>
        <span class="checkbox-desc">Only active members appear publicly under Founders, Co-Founders, or Team Members.</span>
      </span>
    </label>
  </div>
</div>

@section('scripts')
<script>
  (function () {
    const input = document.getElementById('photo');
    const img = document.getElementById('photoPreviewImg');
    if (!input || !img) return;

    input.addEventListener('change', function () {
      const file = this.files && this.files[0];
      if (!file) return;
      img.src = URL.createObjectURL(file);
      img.style.opacity = '1';
    });
  })();
</script>
@endsection

