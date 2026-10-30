@extends('admin.layouts.app')

@section('title', 'Add New Client Testimonial')
@section('header_title', 'Add New Client Testimonial')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.clients.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Clients</span>
    </a>
    <h2 class="module-title">Add New Client &amp; <em>Testimonial</em></h2>
    <p class="module-subtitle">Register a client brand, testimonial quote, author attribution, campaign image, and brand logo.</p>
  </div>
</div>

<form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Company &amp; Testimonial Content</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="clientName">Company / Client Name <span class="required">*</span></label>
            <input type="text" id="clientName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Maison Kerala" oninput="updatePreview()" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="clientIndustry">Industry / Business Category</label>
            <input type="text" id="clientIndustry" name="industry" class="form-control @error('industry') is-invalid @enderror" value="{{ old('industry') }}" placeholder="e.g. Couture House, Bridal Wear, Streetwear" oninput="updatePreview()" />
            @error('industry') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="clientQuote">Testimonial Quote</label>
          <textarea id="clientQuote" name="testimonial_quote" rows="3" class="form-control @error('testimonial_quote') is-invalid @enderror" placeholder="Share the client's testimonial about IDFR models..." oninput="updatePreview()">{{ old('testimonial_quote') }}</textarea>
          @error('testimonial_quote') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="authorName">Author / Person Name</label>
            <input type="text" id="authorName" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name') }}" placeholder="e.g. Neha Varghese" oninput="updatePreview()" />
            @error('author_name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="authorRole">Designation / Role</label>
            <input type="text" id="authorRole" name="author_role" class="form-control @error('author_role') is-invalid @enderror" value="{{ old('author_role') }}" placeholder="e.g. Creative Director, Founder" oninput="updatePreview()" />
            @error('author_role') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="clientWebsite">Website URL (Optional)</label>
            <input type="url" id="clientWebsite" name="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url') }}" placeholder="https://example.com" />
            @error('website_url') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="clientOrder">Display Order</label>
            <input type="number" id="clientOrder" name="display_order" class="form-control" value="{{ old('display_order', $nextOrder) }}" min="0" />
          </div>
        </div>

        <!-- Featured Testimonial Option -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
          <div class="custom-checkbox custom-checkbox--block">
            <input type="checkbox" id="isFeatured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} onchange="updatePreview()" />
            <div>
              <label for="isFeatured" style="color: #fff; font-weight: 600; cursor: pointer; display: block;">Set as Featured Homepage Testimonial</label>
              <span style="font-size: 0.75rem; color: var(--text-muted);">Enforces the single-featured rule: this client will automatically become the initial featured testimonial in the #clientFeature area.</span>
            </div>
          </div>
        </div>

        <h3 class="card-section-title" style="margin-top: 1.5rem;">Imagery &amp; Brand Logo</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="clientImage">Upload Campaign / Featured Image</label>
            <input type="file" id="clientImage" name="image" class="form-control" accept="image/*" onchange="previewFile(this, 'previewFeaturedImg')" />
            <small class="form-hint">Portrait orientation image (1000×1250px). Max 5MB.</small>
            @error('image') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="clientLogo">Upload Client Logo (Optional)</label>
            <input type="file" id="clientLogo" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'previewClientLogoImg')" />
            <small class="form-hint">Transparent monochrome logo or SVG. Max 5MB.</small>
            @error('logo') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <!-- Stylized Monogram Logo Settings -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem;">
          <h4 style="color: var(--gold); font-size: 0.85rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Stylized Monogram Badge Settings (If no graphic uploaded)</h4>
          <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoStyle">Badge Style</label>
              <select id="logoStyle" name="logo_style" class="form-select" onchange="updatePreview()">
                <option value="arch">Arch</option>
                <option value="diamond">Diamond</option>
                <option value="circle">Circle</option>
                <option value="bars">Bars</option>
                <option value="hex">Hexagon</option>
                <option value="weave">Weave</option>
                <option value="star">Star</option>
                <option value="drop">Drop</option>
                <option value="crown">Crown</option>
                <option value="crest">Crest</option>
                <option value="rings">Rings</option>
                <option value="wave">Wave</option>
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
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub') }}" placeholder="KERALA" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} />
          <label for="isActive">Active and Visible on Homepage</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Save Client Testimonial</button>
          <a href="{{ route('admin.clients.index') }}" class="btn-subtle">Cancel</a>
        </div>
      </div>
    </div>

    <!-- Live Preview Column -->
    <div class="form-col-side">
      <div class="preview-card-wrap">
        <h3 class="preview-card-title">Live Homepage Featured Testimonial Preview</h3>

        <div style="background: #111; color: #fff; border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
          <!-- Media -->
          <div style="height: 220px; background: #222; overflow: hidden; position: relative;">
            <img id="previewFeaturedImg" src="{{ asset('images/clients/client-featured.jpg') }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <!-- Body -->
          <div style="padding: 1.5rem; background: #fff; color: #111;">
            <div id="previewLogoContainer" style="height: 40px; margin-bottom: 1rem; display: flex; align-items: center;">
              <div id="previewClientBadge" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.1em; color: #111;">
                <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border: 1.5px solid #111; border-radius: 50%; font-size: 0.8rem;">M</span>
                <div>
                  <div id="previewWord" style="line-height: 1.1;">MAISON</div>
                  <div id="previewSub" style="font-size: 0.6rem; letter-spacing: 0.18em; opacity: 0.6;">KERALA</div>
                </div>
              </div>
              <img id="previewClientLogoImg" src="" alt="Logo" style="display: none; max-height: 40px; max-width: 140px; object-fit: contain;">
            </div>

            <blockquote id="previewQuote" style="margin: 0 0 1rem; font-size: 0.95rem; line-height: 1.5; font-style: italic; color: #333;">
              “IDFR helped us cast our entire festive campaign in a single week. The talent was professional, prepared and camera-ready.”
            </blockquote>

            <p id="previewPerson" style="margin: 0; font-size: 0.85rem; color: #666;">
              <strong id="previewAuthor" style="color: #111;">Neha Varghese</strong> — <span id="previewRole">Creative Director</span>, <span id="previewCompany">Maison Kerala</span>
            </p>
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
    const name = document.getElementById('clientName').value.trim() || 'Maison Kerala';
    const industry = document.getElementById('clientIndustry').value.trim() || 'Couture House';
    const quote = document.getElementById('clientQuote').value.trim() || 'IDFR helped us cast our entire festive campaign in a single week.';
    const author = document.getElementById('authorName').value.trim() || 'Author Name';
    const role = document.getElementById('authorRole').value.trim() || 'Role / Designation';

    document.getElementById('previewQuote').textContent = '“' + quote + '”';
    document.getElementById('previewAuthor').textContent = author;
    document.getElementById('previewRole').textContent = role;
    document.getElementById('previewCompany').textContent = name;

    const mark = document.getElementById('logoMark').value.trim() || name.charAt(0).toUpperCase();
    const word = document.getElementById('logoWord').value.trim() || name.toUpperCase();
    const sub = document.getElementById('logoSub').value.trim() || industry.toUpperCase();

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
        if (targetImgId === 'previewClientLogoImg') {
          target.style.display = 'block';
          document.getElementById('previewClientBadge').style.display = 'none';
        }
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
@endsection
