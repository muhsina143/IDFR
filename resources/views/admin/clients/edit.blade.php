@extends('admin.layouts.app')

@section('title', 'Edit Client: ' . $client->name)
@section('header_title', 'Edit Client Testimonial')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.clients.index') }}" class="back-link">
      <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
      <span>Back to Clients</span>
    </a>
    <h2 class="module-title">Edit Client: <em>{{ $client->name }}</em></h2>
    <p class="module-subtitle">Update testimonial quote, author attribution, replace campaign image or logo, and manage featured status.</p>
  </div>
</div>

<form action="{{ route('admin.clients.update', $client) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div class="form-layout">
    <!-- Form Fields Column -->
    <div class="form-col-main">
      <div class="admin-card">
        <h3 class="card-section-title">Company &amp; Testimonial Content</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="clientName">Company / Client Name <span class="required">*</span></label>
            <input type="text" id="clientName" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $client->name) }}" required oninput="updatePreview()" />
            @error('name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="clientIndustry">Industry / Business Category</label>
            <input type="text" id="clientIndustry" name="industry" class="form-control @error('industry') is-invalid @enderror" value="{{ old('industry', $client->industry) }}" oninput="updatePreview()" />
            @error('industry') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="clientQuote">Testimonial Quote</label>
          <textarea id="clientQuote" name="testimonial_quote" rows="3" class="form-control @error('testimonial_quote') is-invalid @enderror" oninput="updatePreview()">{{ old('testimonial_quote', $client->testimonial_quote) }}</textarea>
          @error('testimonial_quote') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="authorName">Author / Person Name</label>
            <input type="text" id="authorName" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name', $client->author_name) }}" oninput="updatePreview()" />
            @error('author_name') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="authorRole">Designation / Role</label>
            <input type="text" id="authorRole" name="author_role" class="form-control @error('author_role') is-invalid @enderror" value="{{ old('author_role', $client->author_role) }}" oninput="updatePreview()" />
            @error('author_role') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="clientWebsite">Website URL (Optional)</label>
            <input type="url" id="clientWebsite" name="website_url" class="form-control @error('website_url') is-invalid @enderror" value="{{ old('website_url', $client->website_url) }}" placeholder="https://example.com" />
            @error('website_url') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="clientOrder">Display Order</label>
            <input type="number" id="clientOrder" name="display_order" class="form-control" value="{{ old('display_order', $client->display_order) }}" min="0" />
          </div>
        </div>

        <!-- Featured Testimonial Option -->
        <div class="form-group" style="margin-bottom: 1.25rem;">
          <div class="custom-checkbox custom-checkbox--block">
            <input type="checkbox" id="isFeatured" name="is_featured" value="1" {{ old('is_featured', $client->is_featured) ? 'checked' : '' }} onchange="updatePreview()" />
            <div>
              <label for="isFeatured" style="color: #fff; font-weight: 600; cursor: pointer; display: block;">Set as Featured Homepage Testimonial</label>
              <span style="font-size: 0.75rem; color: var(--text-muted);">Enforces the single-featured rule: this client will automatically become the initial featured testimonial in the #clientFeature area.</span>
            </div>
          </div>
        </div>

        <h3 class="card-section-title" style="margin-top: 1.5rem;">Imagery &amp; Brand Logo</h3>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label" for="clientImage">Replace Campaign / Featured Image</label>
            <input type="file" id="clientImage" name="image" class="form-control" accept="image/*" onchange="previewFile(this, 'previewFeaturedImg')" />
            <small class="form-hint">Leave empty to keep current image.</small>
            @error('image') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="clientLogo">Replace Client Logo</label>
            <input type="file" id="clientLogo" name="logo" class="form-control" accept="image/*,.svg" onchange="previewFile(this, 'previewClientLogoImg')" />
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
                <option value="arch" {{ old('logo_style', $client->logo_style) === 'arch' ? 'selected' : '' }}>Arch</option>
                <option value="diamond" {{ old('logo_style', $client->logo_style) === 'diamond' ? 'selected' : '' }}>Diamond</option>
                <option value="circle" {{ old('logo_style', $client->logo_style) === 'circle' ? 'selected' : '' }}>Circle</option>
                <option value="bars" {{ old('logo_style', $client->logo_style) === 'bars' ? 'selected' : '' }}>Bars</option>
                <option value="hex" {{ old('logo_style', $client->logo_style) === 'hex' ? 'selected' : '' }}>Hexagon</option>
                <option value="weave" {{ old('logo_style', $client->logo_style) === 'weave' ? 'selected' : '' }}>Weave</option>
                <option value="star" {{ old('logo_style', $client->logo_style) === 'star' ? 'selected' : '' }}>Star</option>
                <option value="drop" {{ old('logo_style', $client->logo_style) === 'drop' ? 'selected' : '' }}>Drop</option>
                <option value="crown" {{ old('logo_style', $client->logo_style) === 'crown' ? 'selected' : '' }}>Crown</option>
                <option value="crest" {{ old('logo_style', $client->logo_style) === 'crest' ? 'selected' : '' }}>Crest</option>
                <option value="rings" {{ old('logo_style', $client->logo_style) === 'rings' ? 'selected' : '' }}>Rings</option>
                <option value="wave" {{ old('logo_style', $client->logo_style) === 'wave' ? 'selected' : '' }}>Wave</option>
              </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoMark">Initial Mark</label>
              <input type="text" id="logoMark" name="logo_mark" maxlength="3" class="form-control" value="{{ old('logo_mark', $client->logo_mark) }}" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoWord">Logo Word</label>
              <input type="text" id="logoWord" name="logo_word" class="form-control" value="{{ old('logo_word', $client->logo_word) }}" oninput="updatePreview()" />
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" style="font-size: 0.8rem;" for="logoSub">Sub Line</label>
              <input type="text" id="logoSub" name="logo_sub" class="form-control" value="{{ old('logo_sub', $client->logo_sub) }}" oninput="updatePreview()" />
            </div>
          </div>
        </div>

        <div class="form-group custom-checkbox" style="margin-bottom: 1.5rem;">
          <input type="checkbox" id="isActive" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }} />
          <label for="isActive">Active and Visible on Homepage</label>
        </div>

        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
          <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem;">Update Client Testimonial</button>
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
            <img id="previewFeaturedImg" src="{{ $client->image_url }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
          <!-- Body -->
          <div style="padding: 1.5rem; background: #fff; color: #111;">
            <div id="previewLogoContainer" style="height: 40px; margin-bottom: 1rem; display: flex; align-items: center;">
              <div id="previewClientBadge" style="{{ $client->logo_url ? 'display: none;' : 'display: inline-flex;' }} align-items: center; gap: 8px; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.1em; color: #111;">
                <span id="previewMark" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border: 1.5px solid #111; border-radius: 50%; font-size: 0.8rem;">{{ $client->logo_mark ?: substr($client->name, 0, 1) }}</span>
                <div>
                  <div id="previewWord" style="line-height: 1.1;">{{ $client->logo_word ?: strtoupper($client->name) }}</div>
                  <div id="previewSub" style="font-size: 0.6rem; letter-spacing: 0.18em; opacity: 0.6;">{{ $client->logo_sub ?: strtoupper($client->industry ?: 'CLIENT') }}</div>
                </div>
              </div>
              <img id="previewClientLogoImg" src="{{ $client->logo_url ?? '' }}" alt="Logo" style="{{ $client->logo_url ? 'display: block;' : 'display: none;' }} max-height: 40px; max-width: 140px; object-fit: contain;">
            </div>

            <blockquote id="previewQuote" style="margin: 0 0 1rem; font-size: 0.95rem; line-height: 1.5; font-style: italic; color: #333;">
              “{{ $client->testimonial_quote ?: 'IDFR helped us cast our entire campaign with total professionalism.' }}”
            </blockquote>

            <p id="previewPerson" style="margin: 0; font-size: 0.85rem; color: #666;">
              <strong id="previewAuthor" style="color: #111;">{{ $client->author_name ?: 'Author Name' }}</strong> — <span id="previewRole">{{ $client->author_role ?: 'Role' }}</span>, <span id="previewCompany">{{ $client->name }}</span>
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
    const quote = document.getElementById('clientQuote').value.trim() || 'Testimonial quote text...';
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
