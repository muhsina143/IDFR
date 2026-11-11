@php
  $isEdit = isset($facility);
  $selectedIcon = old('icon', $facility->icon ?? 'check');
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
  <div class="form-group">
    <label class="form-label" for="facilityName">Facility Name <span style="color: var(--gold);">*</span></label>
    <input type="text" id="facilityName" name="name" value="{{ old('name', $facility->name ?? '') }}" required maxlength="150" class="form-control" placeholder="e.g. Makeup Room" />
    @error('name') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="displayOrder">Display Order</label>
    <input type="number" id="displayOrder" name="display_order" value="{{ old('display_order', $facility->display_order ?? $nextOrder ?? 1) }}" min="0" class="form-control" />
    @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="facilityIcon">Icon</label>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <div id="facilityIconPreview" style="width: 40px; height: 40px; flex-shrink: 0; border-radius: 50%; border: 1px solid rgba(229, 184, 61, 0.4); display: flex; align-items: center; justify-content: center; color: var(--gold); background: rgba(229, 184, 61, 0.08);">
        <svg width="18" height="18" aria-hidden="true" style="stroke: currentColor; fill: none;">
          <use id="facilityIconUse" href="#i-{{ $selectedIcon }}" />
        </svg>
      </div>
      <select id="facilityIcon" name="icon" class="form-control" style="flex: 1;">
        @foreach($icons as $iconKey => $iconLabel)
          <option value="{{ $iconKey }}" {{ $selectedIcon === $iconKey ? 'selected' : '' }}>{{ $iconLabel }}</option>
        @endforeach
      </select>
    </div>
    @error('icon') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="display: flex; align-items: center; padding-top: 1.35rem;">
    <input type="hidden" name="is_active" value="0" />
    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $isEdit ? ($facility->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }} />
      <span style="font-weight: 500; color: #fff;">Active &amp; Visible on ASDreams Website</span>
    </label>
  </div>
</div>

@section('scripts')
<script>
  (function () {
    var select = document.getElementById('facilityIcon');
    var useEl = document.getElementById('facilityIconUse');
    if (select && useEl) {
      select.addEventListener('change', function () {
        useEl.setAttribute('href', '#i-' + this.value);
      });
    }
  })();
</script>
@endsection

