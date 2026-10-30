@extends('admin.layouts.app')

@section('title', 'Edit Moving Logo: ' . $movingLogo->company_name)
@section('header_title', 'Edit Moving Logo')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.moving-logos.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Moving Logos</span>
    </a>
    <h2 class="module-title">Edit Logo: <em>{{ $movingLogo->company_name }}</em></h2>
    <p class="module-subtitle">Update company brand name, replace logo graphic, or adjust strip order.</p>
  </div>
</div>

<form action="{{ route('admin.moving-logos.update', $movingLogo) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Logo Information</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="companyName">Company / Brand Name <span class="required">*</span></label>
            <input type="text" id="companyName" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $movingLogo->company_name) }}" required oninput="updatePreview()" />
            @error('company_name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="logoOrder">Display Order</label>
            <input type="number" id="logoOrder" name="display_order" class="form-control" value="{{ old('display_order', $movingLogo->display_order) }}" min="0" />
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label class="form-label" for="logoUpload">Replace Logo File</label>
          <input type="file" id="logoUpload" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'stripPreviewLogoImg')" />
          <small class="form-hint">Leave empty to keep existing logo graphic.</small>
          @error('logo') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <!-- Stylized Monogram Logo Settings -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="color: var(--gold); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Stylized Monogram Badge Settings</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoStyle">Badge Style</label>
              <select id="logoStyle" name="logo_style" class="form-select" onchange="updatePreview()">
                <option value="circle" {{ old('logo_style', $movingLogo->logo_style) === 'circle' ? 'selected' : '' }}>Circle</option>
                <option value="arch" {{ old('logo_style', $movingLogo->logo_style) === 'arch' ? 'selected' : '' }}>Arch</option>
                <option value="diamond" {{ old('logo_style', $movingLogo->logo_style) === 'diamond' ? 'selected' : '' }}>Diamond</option>
                <option value="crest" {{ old('logo_style', $movingLogo->logo_style) === 'crest' ? 'selected' : '' }}>Crest</option>
                <option value="rings" {{ old('logo_style', $movingLogo->logo_style) === 'rings' ? 'selected' : '' }}>Rings</option>
                <option value="bars" {{ old('logo_style', $movingLogo->logo_style) === 'bars' ? 'selected' : '' }}>Bars</option>
                <option value="star" {{ old('logo_style', $movingLogo->logo_style) === 'star' ? 'selected' : '' }}>Star</option>
                <option value="crown" {{ old('logo_style', $movingLogo->logo_style) === 'crown' ? 'selected' : '' }}>Crown</option>
                <option value="hex" {{ old('logo_style', $movingLogo->logo_style) === 'hex' ? 'selected' : '' }}>Hexagon</option>
                <option value="wave" {{ old('logo_style', $movingLogo->logo_style) === 'wave' ? 'selected' : '' }}>Wave</option>
                <option value="drop" {{ old('logo_style', $movingLogo->logo_style) === 'drop' ? 'selected' : '' }}>Drop</option>
                <option value="weave" {{ old('logo_style', $movingLogo->logo_style) === 'weave' ? 'selected' : '' }}>Weave</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoMark">Initial Mark</label>
              <input type="text" id="logoMark" name="logo_mark" maxlength="3" class="form-control" value="{{ old('logo_mark', $movingLogo->logo_mark) }}" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoWord">Logo Word</label>
              <input type="text" id="logoWord" name="logo_word" class="form-control" value="{{ old('logo_word', $movingLogo->logo_word) }}" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoSub">Sub Line</label>
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub', $movingLogo->logo_sub) }}" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', $movingLogo->is_active) ? 'checked' : '' }} />
          <label for="isActive">Active in Moving Strip</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Update Logo</button>
          <a href="{{ route('admin.moving-logos.index') }}" class="btn-subtle">Cancel</a>
        </div>
      </div>
    </div>

    <!-- Live Preview Column -->
    <div class="form-col-side">
      <div class="preview-card-wrap">
        <h3 class="preview-card-title">Logo Strip Tile Preview</h3>

        <div style="background: #111; color: #fff; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); padding: 2rem 1.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
          <div id="stripPreviewLogoWrap" style="height: 60px; display: flex; align-items: center; justify-content: center;">
            <div id="stripPreviewBadge" style="{{ $movingLogo->logo_url ? 'display: none;' : 'display: inline-flex;' }} align-items: center; gap: 10px; font-weight: 700; font-size: 1rem; letter-spacing: 0.12em; color: var(--gold);">
              <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1.5px solid var(--gold); border-radius: 50%; font-size: 0.9rem;">{{ $movingLogo->logo_mark ?: substr($movingLogo->company_name, 0, 1) }}</span>
              <div style="text-align: left;">
                <div id="previewWord" style="line-height: 1.1; color: #fff;">{{ $movingLogo->logo_word ?: strtoupper($movingLogo->company_name) }}</div>
                <div id="previewSub" style="font-size: 0.65rem; letter-spacing: 0.2em; color: var(--text-muted);">{{ $movingLogo->logo_sub ?: '' }}</div>
              </div>
            </div>
            <img id="stripPreviewLogoImg" src="{{ $movingLogo->logo_url ?? '' }}" alt="Logo" style="{{ $movingLogo->logo_url ? 'display: block;' : 'display: none;' }} max-height: 48px; max-width: 150px; object-fit: contain;">
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  function updatePreview() {
    const name = document.getElementById('companyName').value.trim() || 'Company Name';
    const mark = document.getElementById('logoMark').value.trim() || name.charAt(0).toUpperCase();
    const word = document.getElementById('logoWord').value.trim() || name.toUpperCase();
    const sub = document.getElementById('logoSub').value.trim() || '';

    document.getElementById('previewMark').textContent = mark;
    document.getElementById('previewWord').textContent = word;
    document.getElementById('previewSub').textContent = sub;
  }

  function previewFile(input, targetImgId) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const target = document.getElementById(targetImgId);
        target.src = e.target.result;
        target.style.display = 'block';
        document.getElementById('stripPreviewBadge').style.display = 'none';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endsection
