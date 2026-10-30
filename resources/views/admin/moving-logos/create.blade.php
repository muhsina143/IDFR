@extends('admin.layouts.app')

@section('title', 'Add Logo to Moving Strip')
@section('header_title', 'Add Logo to Strip')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.moving-logos.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Moving Logos</span>
    </a>
    <h2 class="module-title">Add Logo to <em>Moving Strip</em></h2>
    <p class="module-subtitle">Include an existing client, sponsor, or upload a brand logo to appear in the animated homepage logo marquee.</p>
  </div>
</div>

<form action="{{ route('admin.moving-logos.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Logo Information &amp; Source</h3>

        <!-- Quick Entity Selector -->
        <div style="background: rgba(200, 169, 126, 0.08); padding: 1.25rem; border-radius: 6px; border: 1px solid rgba(200, 169, 126, 0.2); margin-bottom: 1.5rem;">
          <label class="form-label" style="color: var(--gold);" for="entityPicker">Quick Select From Existing Client / Sponsor</label>
          <select id="entityPicker" class="form-select" onchange="autoPopulateEntity(this)">
            <option value="">-- Choose Existing Client / Sponsor or create standalone --</option>
            <optgroup label="Clients">
              @foreach($clients as $c)
                <option value="client-{{ $c->id }}" data-type="client" data-id="{{ $c->id }}" data-name="{{ $c->name }}" data-mark="{{ $c->logo_mark }}" data-word="{{ $c->logo_word }}" data-sub="{{ $c->logo_sub }}" data-style="{{ $c->logo_style }}" data-url="{{ $c->logo_url }}">
                  Client: {{ $c->name }} ({{ $c->industry }})
                </option>
              @endforeach
            </optgroup>
            <optgroup label="Sponsors">
              @foreach($sponsors as $s)
                <option value="sponsor-{{ $s->id }}" data-type="sponsor" data-id="{{ $s->id }}" data-name="{{ $s->name }}" data-mark="{{ $s->logo_mark }}" data-word="{{ $s->logo_word }}" data-sub="{{ $s->logo_sub }}" data-style="{{ $s->logo_style }}" data-url="{{ $s->logo_url }}">
                  Sponsor: {{ $s->name }} ({{ $s->tier }})
                </option>
              @endforeach
            </optgroup>
          </select>
          <small class="form-hint">Selecting an entity links this logo and automatically configures its company name and badge.</small>
        </div>

        <input type="hidden" id="clientId" name="client_id" value="" />
        <input type="hidden" id="sponsorId" name="sponsor_id" value="" />

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="companyName">Company / Brand Name <span class="required">*</span></label>
            <input type="text" id="companyName" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required placeholder="e.g. Maison Kerala" oninput="updatePreview()" />
            @error('company_name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="logoOrder">Display Order</label>
            <input type="number" id="logoOrder" name="display_order" class="form-control" value="{{ old('display_order', $nextOrder) }}" min="0" />
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
          <label class="form-label" for="logoUpload">Upload Custom Logo File (Optional)</label>
          <input type="file" id="logoUpload" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'stripPreviewLogoImg')" />
          <small class="form-hint">Transparent monochrome logo or SVG. Max 5MB.</small>
          @error('logo') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <!-- Stylized Monogram Logo Settings -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="color: var(--gold); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Stylized Monogram Badge Settings (Fallback if no file uploaded)</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoStyle">Badge Style</label>
              <select id="logoStyle" name="logo_style" class="form-select" onchange="updatePreview()">
                <option value="circle">Circle</option>
                <option value="arch">Arch</option>
                <option value="diamond">Diamond</option>
                <option value="crest">Crest</option>
                <option value="rings">Rings</option>
                <option value="bars">Bars</option>
                <option value="star">Star</option>
                <option value="crown">Crown</option>
                <option value="hex">Hexagon</option>
                <option value="wave">Wave</option>
                <option value="drop">Drop</option>
                <option value="weave">Weave</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoMark">Initial Mark</label>
              <input type="text" id="logoMark" name="logo_mark" maxlength="3" class="form-control" value="{{ old('logo_mark') }}" placeholder="M" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoWord">Logo Word</label>
              <input type="text" id="logoWord" name="logo_word" class="form-control" value="{{ old('logo_word') }}" placeholder="MAISON" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoSub">Sub Line</label>
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub') }}" placeholder="" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} />
          <label for="isActive">Active in Moving Strip</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Add Logo to Strip</button>
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
            <div id="stripPreviewBadge" style="display: inline-flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1rem; letter-spacing: 0.12em; color: var(--gold);">
              <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px; border: 1.5px solid var(--gold); border-radius: 50%; font-size: 0.9rem;">M</span>
              <div style="text-align: left;">
                <div id="previewWord" style="line-height: 1.1; color: #fff;">MAISON</div>
                <div id="previewSub" style="font-size: 0.65rem; letter-spacing: 0.2em; color: var(--text-muted);">KERALA</div>
              </div>
            </div>
            <img id="stripPreviewLogoImg" src="" alt="Logo" style="display: none; max-height: 48px; max-width: 150px; object-fit: contain;">
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script>
  function autoPopulateEntity(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
      document.getElementById('clientId').value = '';
      document.getElementById('sponsorId').value = '';
      return;
    }

    const type = opt.dataset.type;
    const id = opt.dataset.id;
    const name = opt.dataset.name;
    const mark = opt.dataset.mark;
    const word = opt.dataset.word;
    const sub = opt.dataset.sub;
    const style = opt.dataset.style;
    const logoUrl = opt.dataset.url;

    if (type === 'client') {
      document.getElementById('clientId').value = id;
      document.getElementById('sponsorId').value = '';
    } else {
      document.getElementById('sponsorId').value = id;
      document.getElementById('clientId').value = '';
    }

    document.getElementById('companyName').value = name;
    if (mark) document.getElementById('logoMark').value = mark;
    if (word) document.getElementById('logoWord').value = word;
    if (sub) document.getElementById('logoSub').value = sub;
    if (style) document.getElementById('logoStyle').value = style;

    if (logoUrl) {
      const img = document.getElementById('stripPreviewLogoImg');
      img.src = logoUrl;
      img.style.display = 'block';
      document.getElementById('stripPreviewBadge').style.display = 'none';
    } else {
      document.getElementById('stripPreviewLogoImg').style.display = 'none';
      document.getElementById('stripPreviewBadge').style.display = 'inline-flex';
    }

    updatePreview();
  }

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
