@extends('admin.layouts.app')

@section('title', 'Add New Sponsor')
@section('header_title', 'Add New Sponsor')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.sponsors.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Sponsors</span>
    </a>
    <h2 class="module-title">Add New <em>Sponsor</em></h2>
    <p class="module-subtitle">Register a brand sponsor or title sponsor. Preview the exact homepage sponsor badge in real time.</p>
  </div>
</div>

<form action="{{ route('admin.sponsors.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Sponsor Information</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="sponsorName">Sponsor Name <span class="required">*</span></label>
            <input type="text" id="sponsorName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Aurum Jewels" oninput="updatePreview()" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="sponsorTier">Sponsorship Tier <span class="required">*</span></label>
            <input type="text" id="sponsorTier" name="tier" class="form-control @error('tier') is-invalid @enderror" value="{{ old('tier') }}" required placeholder="e.g. Title Sponsor · Season 4, Automobile Partner" oninput="updatePreview()" />
            @error('tier') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
          <div class="custom-checkbox custom-checkbox--block">
            <input type="checkbox" id="isTitle" name="is_title" value="1" {{ old('is_title') ? 'checked' : '' }} onchange="updatePreview()" />
            <div>
              <label for="isTitle" style="color: #fff; font-weight: 600; cursor: pointer; display: block;">Designate as Title Sponsor</label>
              <span style="font-size: 0.75rem; color: var(--text-muted);">Title sponsors receive highlighted featured prominence at the top of the homepage sponsors grid.</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="sponsorDesc">Short Description (Optional)</label>
          <textarea id="sponsorDesc" name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Describe sponsorship involvement...">{{ old('description') }}</textarea>
          @error('description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="sponsorWebsite">Website URL (Optional)</label>
            <input type="url" id="sponsorWebsite" name="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url') }}" placeholder="https://example.com" />
            @error('website_url') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="sponsorOrder">Display Order</label>
            <input type="number" id="sponsorOrder" name="display_order" class="form-control" value="{{ old('display_order', $nextOrder) }}" min="0" />
          </div>
        </div>

        <h3 class="card-section-title" style="margin-top: 1.5rem;">Logo &amp; Brand Appearance</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="sponsorLogo">Upload Logo Graphic (Optional)</label>
            <input type="file" id="sponsorLogo" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'sponsorPreviewLogoImg')" />
            <small class="form-hint">Transparent monochrome logo or SVG. Max 5MB.</small>
            @error('logo') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="sponsorImage">Upload Showcase Image (Optional)</label>
            <input type="file" id="sponsorImage" name="image" class="form-control" accept="image/*" />
            <small class="form-hint">Optional campaign photo for sponsor modals/profiles.</small>
          </div>
        </div>

        <!-- Stylized Monogram Logo Settings -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="color: var(--gold); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Stylized Monogram Badge Settings (If no graphic uploaded)</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoStyle">Badge Style</label>
              <select id="logoStyle" name="logo_style" class="form-select" onchange="updatePreview()">
                <option value="crown">Crown</option>
                <option value="arch">Arch</option>
                <option value="drop">Drop</option>
                <option value="weave">Weave</option>
                <option value="hex">Hexagon</option>
                <option value="rings">Rings</option>
                <option value="wave">Wave</option>
                <option value="bars">Bars</option>
                <option value="star">Star</option>
                <option value="circle">Circle</option>
                <option value="diamond">Diamond</option>
                <option value="crest">Crest</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoMark">Initial Mark</label>
              <input type="text" id="logoMark" name="logo_mark" maxlength="3" class="form-control" value="{{ old('logo_mark') }}" placeholder="A" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoWord">Logo Word</label>
              <input type="text" id="logoWord" name="logo_word" class="form-control" value="{{ old('logo_word') }}" placeholder="AURUM" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoSub">Sub Line</label>
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub') }}" placeholder="FINE JEWELS" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} />
          <label for="isActive">Active and Visible on Homepage</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Save Sponsor</button>
          <a href="{{ route('admin.sponsors.index') }}" class="btn-subtle">Cancel</a>
        </div>
      </div>
    </div>

    <!-- Live Preview Sponsor Badge Column -->
    <div class="form-col-side">
      <div class="preview-card-wrap">
        <h3 class="preview-card-title">Live Homepage Sponsor Badge Preview</h3>

        <div id="previewCardWrap" style="background: #111; color: #fff; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); padding: 2rem 1.5rem; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); transition: border-color 0.3s ease;">
          <div id="previewTitleBadge" style="display: none; margin-bottom: 1rem;">
            <span class="badge" style="background: var(--gold); color: #000; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 4px 10px; border-radius: 4px;">Title Sponsor Highlight</span>
          </div>

          <div id="sponsorPreviewLogoWrap" style="height: 60px; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: center;">
            <div id="sponsorPreviewBadge" style="display: inline-flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1rem; letter-spacing: 0.12em; color: var(--gold);">
              <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1.5px solid var(--gold); border-radius: 50%; font-size: 0.9rem;">A</span>
              <div style="text-align: left;">
                <div id="previewWord" style="line-height: 1.1; color: #fff;">AURUM</div>
                <div id="previewSub" style="font-size: 0.65rem; letter-spacing: 0.2em; color: var(--text-muted);">FINE JEWELS</div>
              </div>
            </div>
            <img id="sponsorPreviewLogoImg" src="" alt="Logo" style="display: none; max-height: 50px; max-width: 160px; object-fit: contain;">
          </div>

          <p id="previewTier" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.12em; color: var(--text-muted); margin: 0; font-weight: 500;">Title Sponsor · Season 4</p>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  function updatePreview() {
    const name = document.getElementById('sponsorName').value.trim() || 'Sponsor Name';
    const tier = document.getElementById('sponsorTier').value.trim() || 'Sponsorship Tier';
    const isTitle = document.getElementById('isTitle').checked;

    document.getElementById('previewTier').textContent = tier;

    const wrap = document.getElementById('previewCardWrap');
    const titleBadge = document.getElementById('previewTitleBadge');
    if (isTitle) {
      wrap.style.borderColor = 'var(--gold)';
      titleBadge.style.display = 'block';
    } else {
      wrap.style.borderColor = 'rgba(255,255,255,0.1)';
      titleBadge.style.display = 'none';
    }

    const mark = document.getElementById('logoMark').value.trim() || name.charAt(0).toUpperCase();
    const word = document.getElementById('logoWord').value.trim() || name.toUpperCase();
    const sub = document.getElementById('logoSub').value.trim() || tier.toUpperCase();

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
        document.getElementById('sponsorPreviewBadge').style.display = 'none';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endsection
