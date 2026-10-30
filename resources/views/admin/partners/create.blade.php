@extends('admin.layouts.app')

@section('title', 'Add New Partner')
@section('header_title', 'Add New Partner')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.partners.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Partners</span>
    </a>
    <h2 class="module-title">Add New <em>Partner</em></h2>
    <p class="module-subtitle">Register a collaborating atelier, studio, or production house. Preview the exact homepage card in real time.</p>
  </div>
</div>

<form action="{{ route('admin.partners.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Partner Information</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="partnerName">Partner Name <span class="required">*</span></label>
            <input type="text" id="partnerName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Lumière Studios" oninput="updatePreview()" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="partnerType">Category / Type <span class="required">*</span></label>
            <input type="text" id="partnerType" name="type" class="form-control @error('type') is-invalid @enderror" value="{{ old('type') }}" required placeholder="e.g. Photography Partner, Couture Partner" oninput="updatePreview()" />
            @error('type') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="partnerDesc">Short Description / Tagline</label>
          <textarea id="partnerDesc" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the partner's role or collaboration details..." oninput="updatePreview()">{{ old('description') }}</textarea>
          @error('description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="partnerWebsite">Website URL (Optional)</label>
            <input type="url" id="partnerWebsite" name="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url') }}" placeholder="https://example.com" />
            @error('website_url') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="partnerOrder">Display Order</label>
            <input type="number" id="partnerOrder" name="display_order" class="form-control" value="{{ old('display_order', $nextOrder) }}" min="0" />
          </div>
        </div>

        <h3 class="card-section-title" style="margin-top: 1.5rem;">Card Imagery &amp; Brand Logo</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="partnerImage">Upload Card Media Photo</label>
            <input type="file" id="partnerImage" name="image" class="form-control" accept="image/*" onchange="previewFile(this, 'cardPreviewImg')" />
            <small class="form-hint">Recommended: 600×375px or 16:9 ratio. JPG, PNG, WEBP max 5MB.</small>
            @error('image') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="partnerLogo">Upload Logo Graphic (Optional)</label>
            <input type="file" id="partnerLogo" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'cardPreviewLogoImg')" />
            <small class="form-hint">Monochrome logo image or leave empty for stylized badge.</small>
            @error('logo') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <!-- Stylized Monogram Logo Settings (Fallback or Modern badge) -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="color: var(--gold); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Stylized Monogram Badge Settings (If no graphic uploaded)</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoStyle">Badge Style</label>
              <select id="logoStyle" name="logo_style" class="form-select" onchange="updatePreview()">
                <option value="circle">Circle</option>
                <option value="diamond">Diamond</option>
                <option value="crest">Crest</option>
                <option value="rings">Rings</option>
                <option value="bars">Bars</option>
                <option value="star">Star</option>
                <option value="arch">Arch</option>
                <option value="crown">Crown</option>
                <option value="hex">Hexagon</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoMark">Initial Mark</label>
              <input type="text" id="logoMark" name="logo_mark" maxlength="3" class="form-control" value="{{ old('logo_mark') }}" placeholder="L" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoWord">Logo Word</label>
              <input type="text" id="logoWord" name="logo_word" class="form-control" value="{{ old('logo_word') }}" placeholder="LUMIÈRE" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoSub">Sub Line</label>
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub') }}" placeholder="STUDIOS" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} />
          <label for="isActive">Active and Visible on Homepage</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Save Partner</button>
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
            <img id="cardPreviewImg" src="{{ asset('images/partners/partner-01.jpg') }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <!-- Card Body -->
          <div style="padding: 1.5rem;">
            <div id="cardPreviewLogoWrap" style="height: 40px; margin-bottom: 1rem; display: flex; align-items: center;">
              <div id="cardPreviewBadge" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.1em; color: #111;">
                <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border: 1.5px solid #111; border-radius: 50%; font-size: 0.8rem;">L</span>
                <div>
                  <div id="previewWord" style="line-height: 1.1;">LUMIÈRE</div>
                  <div id="previewSub" style="font-size: 0.6rem; letter-spacing: 0.18em; opacity: 0.6;">STUDIOS</div>
                </div>
              </div>
              <img id="cardPreviewLogoImg" src="" alt="Logo" style="display: none; max-height: 40px; max-width: 140px; object-fit: contain;">
            </div>

            <p id="previewType" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: #9c8052; font-weight: 600; margin: 0 0 0.35rem;">Photography Partner</p>
            <h4 id="previewName" style="font-size: 1.25rem; font-family: var(--font-serif); margin: 0 0 0.5rem; color: #111;">Lumière Studios</h4>
            <p id="previewDesc" style="font-size: 0.85rem; line-height: 1.5; color: #555; margin: 0;">Official portfolio and campaign photography for IDFR models.</p>
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
