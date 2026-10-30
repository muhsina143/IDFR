@extends('admin.layouts.app')

@section('title', 'Edit Partner: ' . $partner->name)
@section('header_title', 'Edit Partner')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.partners.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Partners</span>
    </a>
    <h2 class="module-title">Edit Partner: <em>{{ $partner->name }}</em></h2>
    <p class="module-subtitle">Update partner information, replace media/logo, adjust display ordering, or change active status.</p>
  </div>
</div>

<form action="{{ route('admin.partners.update', $partner) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Partner Information</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="partnerName">Partner Name <span class="required">*</span></label>
            <input type="text" id="partnerName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $partner->name) }}" required oninput="updatePreview()" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="partnerType">Category / Type <span class="required">*</span></label>
            <input type="text" id="partnerType" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type', $partner->type) }}" required oninput="updatePreview()" />
            @error('type') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="partnerDesc">Short Description / Tagline</label>
          <textarea id="partnerDesc" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" oninput="updatePreview()">{{ old('description', $partner->description) }}</textarea>
          @error('description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="partnerWebsite">Website URL (Optional)</label>
            <input type="url" id="partnerWebsite" name="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url', $partner->website_url) }}" placeholder="https://example.com" />
            @error('website_url') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="partnerOrder">Display Order</label>
            <input type="number" id="partnerOrder" name="display_order" class="form-control" value="{{ old('display_order', $partner->display_order) }}" min="0" />
          </div>
        </div>

        <h3 class="card-section-title" style="margin-top: 1.5rem;">Card Imagery &amp; Brand Logo</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="partnerImage">Replace Card Media Photo</label>
            <input type="file" id="partnerImage" name="image" class="form-control" accept="image/*" onchange="previewFile(this, 'cardPreviewImg')" />
            <small class="form-hint">Leave empty to keep existing media photo.</small>
            @error('image') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="partnerLogo">Replace Logo Graphic</label>
            <input type="file" id="partnerLogo" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'cardPreviewLogoImg')" />
            <small class="form-hint">Leave empty to keep current logo.</small>
            @error('logo') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <!-- Stylized Monogram Logo Settings -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="color: var(--gold); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Stylized Monogram Badge Settings</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoStyle">Badge Style</label>
              <select id="logoStyle" name="logo_style" class="form-select" onchange="updatePreview()">
                <option value="circle" {{ old('logo_style', $partner->logo_style) === 'circle' ? 'selected' : '' }}>Circle</option>
                <option value="diamond" {{ old('logo_style', $partner->logo_style) === 'diamond' ? 'selected' : '' }}>Diamond</option>
                <option value="crest" {{ old('logo_style', $partner->logo_style) === 'crest' ? 'selected' : '' }}>Crest</option>
                <option value="rings" {{ old('logo_style', $partner->logo_style) === 'rings' ? 'selected' : '' }}>Rings</option>
                <option value="bars" {{ old('logo_style', $partner->logo_style) === 'bars' ? 'selected' : '' }}>Bars</option>
                <option value="star" {{ old('logo_style', $partner->logo_style) === 'star' ? 'selected' : '' }}>Star</option>
                <option value="arch" {{ old('logo_style', $partner->logo_style) === 'arch' ? 'selected' : '' }}>Arch</option>
                <option value="crown" {{ old('logo_style', $partner->logo_style) === 'crown' ? 'selected' : '' }}>Crown</option>
                <option value="hex" {{ old('logo_style', $partner->logo_style) === 'hex' ? 'selected' : '' }}>Hexagon</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoMark">Initial Mark</label>
              <input type="text" id="logoMark" name="logo_mark" maxlength="3" class="form-control" value="{{ old('logo_mark', $partner->logo_mark) }}" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoWord">Logo Word</label>
              <input type="text" id="logoWord" name="logo_word" class="form-control" value="{{ old('logo_word', $partner->logo_word) }}" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoSub">Sub Line</label>
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub', $partner->logo_sub) }}" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }} />
          <label for="isActive">Active and Visible on Homepage</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Update Partner</button>
          <a href="{{ route('admin.partners.index') }}" class="btn-subtle">Cancel</a>
        </div>
      </div>
    </div>

    <!-- Live Preview Card Column -->
    <div class="form-col-side">
      <div class="preview-card-wrap">
        <h3 class="preview-card-title">Live Homepage Card Preview</h3>

        <div style="background: #fff; color: #111; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
          <!-- Card Media -->
          <div style="height: 190px; background: #222; overflow: hidden; position: relative;">
            <img id="cardPreviewImg" src="{{ $partner->image_url }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <!-- Card Body -->
          <div style="padding: 1.5rem;">
            <div id="cardPreviewLogoWrap" style="height: 40px; margin-bottom: 1rem; display: flex; align-items: center;">
              <div id="cardPreviewBadge" style="{{ $partner->logo_url ? 'display: none;' : 'display: inline-flex;' }} align-items: center; gap: 8px; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.1em; color: #111;">
                <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border: 1.5px solid #111; border-radius: 50%; font-size: 0.8rem;">{{ $partner->logo_mark ?: substr($partner->name, 0, 1) }}</span>
                <div>
                  <div id="previewWord" style="line-height: 1.1;">{{ $partner->logo_word ?: strtoupper($partner->name) }}</div>
                  <div id="previewSub" style="font-size: 0.6rem; letter-spacing: 0.18em; opacity: 0.6;">{{ $partner->logo_sub ?: strtoupper($partner->type) }}</div>
                </div>
              </div>
              <img id="cardPreviewLogoImg" src="{{ $partner->logo_url ?? '' }}" alt="Logo" style="{{ $partner->logo_url ? 'display: block;' : 'display: none;' }} max-height: 40px; max-width: 140px; object-fit: contain;">
            </div>

            <p id="previewType" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #9c8052; font-weight: 600; margin: 0 0 0.35rem;">{{ $partner->type }}</p>
            <h4 id="previewName" style="font-size: 1.25rem; font-family: var(--font-serif); margin: 0 0 0.5rem; color: #111;">{{ $partner->name }}</h4>
            <p id="previewDesc" style="font-size: 0.85rem; line-height: 1.5; color: #555; margin: 0;">{{ $partner->description }}</p>
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
    const name = document.getElementById('partnerName').value.trim() || 'Partner Name';
    const type = document.getElementById('partnerType').value.trim() || 'Category Partner';
    const desc = document.getElementById('partnerDesc').value.trim() || 'Description of collaboration...';
    
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewType').textContent = type;
    document.getElementById('previewDesc').textContent = desc;

    const mark = document.getElementById('logoMark').value.trim() || name.charAt(0).toUpperCase();
    const word = document.getElementById('logoWord').value.trim() || name.toUpperCase();
    const sub = document.getElementById('logoSub').value.trim() || type.toUpperCase();

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
        if (targetImgId === 'cardPreviewLogoImg') {
          target.style.display = 'block';
          document.getElementById('cardPreviewBadge').style.display = 'none';
        }
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endsection
