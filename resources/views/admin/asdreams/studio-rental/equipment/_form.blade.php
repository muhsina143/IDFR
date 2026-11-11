@php
  $isEdit = isset($equipment);
  $selectedIcon = old('icon', $equipment->icon ?? 'light');
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
  <div class="form-group">
    <label class="form-label" for="equipmentName">Equipment Name <span style="color: var(--gold);">*</span></label>
    <input type="text" id="equipmentName" name="name" value="{{ old('name', $equipment->name ?? '') }}" required maxlength="150" class="form-control" placeholder="e.g. 2 SK 400 Photo Lights" />
    @error('name') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="displayOrder">Display Order</label>
    <input type="number" id="displayOrder" name="display_order" value="{{ old('display_order', $equipment->display_order ?? $nextOrder ?? 1) }}" min="0" class="form-control" />
    @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="equipmentIcon">Default Icon (when no custom image is set)</label>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <div style="width: 40px; height: 40px; flex-shrink: 0; border-radius: 50%; border: 1px solid rgba(229, 184, 61, 0.4); display: flex; align-items: center; justify-content: center; color: var(--gold); background: rgba(229, 184, 61, 0.08);">
        <svg width="18" height="18" aria-hidden="true" style="stroke: currentColor; fill: none;">
          <use id="equipmentIconUse" href="#i-{{ $selectedIcon }}" />
        </svg>
      </div>
      <select id="equipmentIcon" name="icon" class="form-control" style="flex: 1;">
        @foreach($icons as $iconKey => $iconLabel)
          <option value="{{ $iconKey }}" {{ $selectedIcon === $iconKey ? 'selected' : '' }}>{{ $iconLabel }}</option>
        @endforeach
      </select>
    </div>
    @error('icon') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="equipmentImage">Equipment Image (Optional)</label>

    <div id="equipmentImagePreviewWrap" style="margin-bottom: 0.75rem; display: {{ ($isEdit && $equipment->image_url) ? 'flex' : 'none' }}; align-items: center; gap: 1rem;">
      <div style="width: 68px; height: 68px; border-radius: 6px; overflow: hidden; background: #000; border: 1px solid var(--border-gold);">
        <img id="equipmentImagePreview" src="{{ $isEdit && $equipment->image_url ? $equipment->image_url : '' }}" alt="Equipment preview" style="width: 100%; height: 100%; object-fit: cover;" />
      </div>
      @if($isEdit && $equipment->image_url)
        <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer; font-size: 0.82rem; color: var(--text-secondary);">
          <input type="checkbox" name="remove_image" value="1" />
          <span>Remove uploaded image (use icon)</span>
        </label>
      @endif
    </div>

    <input type="file" id="equipmentImage" name="image" accept="image/*,.svg" class="form-control" />
    <span class="form-hint">Optional thumbnail replacing the default SVG icon. JPG, PNG, WebP, or SVG (max 5MB).</span>
    @error('image') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2;">
    <input type="hidden" name="is_active" value="0" />
    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $isEdit ? ($equipment->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }} />
      <span style="font-weight: 500; color: #fff;">Active &amp; Visible on ASDreams Website</span>
    </label>
  </div>
</div>

@section('scripts')
<script>
  (function () {
    var select = document.getElementById('equipmentIcon');
    var useEl = document.getElementById('equipmentIconUse');
    if (select && useEl) {
      select.addEventListener('change', function () {
        useEl.setAttribute('href', '#i-' + this.value);
      });
    }

    var fileInput = document.getElementById('equipmentImage');
    var previewWrap = document.getElementById('equipmentImagePreviewWrap');
    var previewImg = document.getElementById('equipmentImagePreview');
    if (fileInput && previewWrap && previewImg) {
      fileInput.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (!file) {
          return;
        }
        var url = URL.createObjectURL(file);
        previewImg.src = url;
        previewWrap.style.display = 'flex';
      });
    }
  })();
</script>
@endsection

