@php
  $isEdit = isset($movingLogo);
@endphp

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
  <div class="form-group">
    <label class="form-label" for="logoName">Logo Name <span style="color: var(--gold);">*</span></label>
    <input type="text" id="logoName" name="name" value="{{ old('name', $movingLogo->name ?? '') }}" required class="form-control" placeholder="e.g. Maison Kerala" />
    @error('name') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="logoDisplayOrder">Display Order</label>
    <input type="number" id="logoDisplayOrder" name="display_order" value="{{ old('display_order', $movingLogo->display_order ?? $nextOrder ?? 1) }}" min="0" class="form-control" />
    @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2;">
    <label class="form-label" for="movingLogoUpload">Logo Image Upload @unless($isEdit)<span style="color: var(--gold);">*</span>@endunless</label>
    @if($isEdit)
      <div style="margin-bottom: 0.75rem; width: 150px; height: 58px; border-radius: 4px; background: #000; border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; padding: 6px;">
        @if($movingLogo->logo_url)
          <img src="{{ $movingLogo->logo_url }}" alt="{{ $movingLogo->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" />
        @else
          <span style="font-size: 0.75rem; color: var(--gold); text-align: center; font-weight: 600;">{{ $movingLogo->logo_word ?: $movingLogo->name }}</span>
        @endif
      </div>
    @endif
    <input type="file" id="movingLogoUpload" name="logo" {{ $isEdit ? '' : 'required' }} accept="image/*,.svg" class="form-control" />
    <span class="form-hint">{{ $isEdit ? 'Upload a new image only to replace the current logo. ' : '' }}Transparent PNG, SVG, JPG, or WebP (max 5MB).</span>
    @error('logo') <span class="form-error">{{ $message }}</span> @enderror
  </div>

  <div class="form-group" style="grid-column: span 2;">
    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active', $isEdit ? ($movingLogo->is_active ? '1' : '0') : '1') == '1' ? 'checked' : '' }} />
      <span style="font-weight: 500; color: #fff;">Active &amp; Visible in Moving Logo Strip</span>
    </label>
  </div>
</div>

