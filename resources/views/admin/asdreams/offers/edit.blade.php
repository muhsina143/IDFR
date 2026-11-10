@extends('admin.layouts.app')

@section('title', 'Edit ASDreamStudio Offer')
@section('page_title', 'Edit Offer')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.offers.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Offers</span>
    </a>
    <h2 class="module-title">Edit Offer: <em>{{ $offer->title }}</em></h2>
    <p class="module-subtitle">Update package copy, inclusions, featured photo, and enquiry settings.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('asd.offers.show', $offer->slug) }}" target="_blank" class="btn-subtle">
      <svg viewBox="0 0 24 24" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      <span>View Live</span>
    </a>
  </div>
</div>

<form action="{{ route('admin.asdreams.offers.update', $offer) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Details -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Package Header &amp; Titles</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="title">Package Tab Title <span style="color: var(--gold);">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $offer->title) }}" required class="form-control" />
            <span class="form-hint">Shown in the Offer Details switcher tabs and banners.</span>
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="slug">URL Slug</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $offer->slug) }}" class="form-control" />
            <span class="form-hint">Path: /asdreams/offers/{{ $offer->slug }}</span>
            @error('slug') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="category_label">Category Label / Eyebrow</label>
            <input type="text" id="category_label" name="category_label" value="{{ old('category_label', $offer->category_label) }}" class="form-control" />
            @error('category_label') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="heading_main">Main Heading <span style="color: var(--gold);">*</span></label>
            <input type="text" id="heading_main" name="heading_main" value="{{ old('heading_main', $offer->heading_main) }}" required class="form-control" />
            @error('heading_main') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="heading_highlight">Highlighted / Italic Heading</label>
            <input type="text" id="heading_highlight" name="heading_highlight" value="{{ old('heading_highlight', $offer->heading_highlight) }}" class="form-control" />
            <span class="form-hint">Rendered in cursive italic gold font.</span>
            @error('heading_highlight') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="price">Package Price <span class="form-hint" style="display: inline;">(Optional)</span></label>
            <input type="text" id="price" name="price" value="{{ old('price', $offer->price) }}" class="form-control" placeholder="e.g. ₹25,000 or Starts at ₹15,000" />
            @error('price') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="validity">Offer Validity <span class="form-hint" style="display: inline;">(Optional)</span></label>
            <input type="text" id="validity" name="validity" value="{{ old('validity', $offer->validity) }}" class="form-control" placeholder="e.g. Valid until 31 Dec 2026 or Limited slots" />
            @error('validity') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="lead_text">Lead Summary Paragraph</label>
            <textarea id="lead_text" name="lead_text" rows="3" class="form-control">{{ old('lead_text', $offer->lead_text) }}</textarea>
            @error('lead_text') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="full_description">Detailed Scope &amp; Deliverables Description</label>
            <textarea id="full_description" name="full_description" rows="5" class="form-control">{{ old('full_description', $offer->full_description) }}</textarea>
            @error('full_description') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>

      <!-- Package Features & Inclusions -->
      <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h3 style="font-size: 1.1rem; color: #fff; margin: 0;">Inclusions &amp; Features</h3>
          <button type="button" class="btn-subtle" id="btnAddFeature" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
            + Add Feature
          </button>
        </div>
        <p class="form-hint" style="margin-bottom: 1rem;">Bullet items displayed in the "What’s Included" section of the offer page.</p>

        <div id="featuresList" style="display: flex; flex-direction: column; gap: 0.75rem;">
          @php
            $currentFeatures = old('features');
            if ($currentFeatures === null) {
              $currentFeatures = $offer->features->pluck('feature_text')->toArray();
            }
            if (empty($currentFeatures)) {
              $currentFeatures = ['Package feature item'];
            }
          @endphp
          @foreach($currentFeatures as $idx => $feat)
            <div class="feature-row" style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--gold); font-size: 0.9rem;">✦</span>
              <input type="text" name="features[]" value="{{ $feat }}" class="form-control" placeholder="Feature description..." />
              <button type="button" class="btn-action btn-action--delete btnRemoveFeature" title="Remove Feature">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Client Enquiry Form Copy -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Client Enquiry Box Copy</h3>

        <div class="form-group">
          <label class="form-label" for="enquiry_placeholder">Enquiry Textarea Placeholder</label>
          <input type="text" id="enquiry_placeholder" name="enquiry_placeholder" value="{{ old('enquiry_placeholder', $offer->enquiry_placeholder) }}" class="form-control" />
          @error('enquiry_placeholder') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="notes">Fine Print / Footnote</label>
          <textarea id="notes" name="notes" rows="2" class="form-control">{{ old('notes', $offer->notes) }}</textarea>
          @error('notes') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="terms">Terms &amp; Conditions <span class="form-hint" style="display: inline;">(Optional)</span></label>
          <textarea id="terms" name="terms" rows="2" class="form-control" placeholder="e.g. 50% advance to confirm the slot. Rescheduling permitted up to 48 hours prior.">{{ old('terms', $offer->terms) }}</textarea>
          @error('terms') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="important_notes">Important Notes <span class="form-hint" style="display: inline;">(Optional)</span></label>
          <textarea id="important_notes" name="important_notes" rows="2" class="form-control" placeholder="e.g. Wardrobe styling assistance and makeup artist can be arranged upon request.">{{ old('important_notes', $offer->important_notes) }}</textarea>
          @error('important_notes') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>
    </div>

    <!-- Sidebar Controls -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Publishing &amp; Media</h3>

        <div class="form-group">
          <label class="form-label" for="status">Offer Status <span style="color: var(--gold);">*</span></label>
          <select id="status" name="status" class="form-control">
            <option value="published" {{ old('status', $offer->status) == 'published' ? 'selected' : '' }}>Published (Live on Website)</option>
            <option value="draft" {{ old('status', $offer->status) == 'draft' ? 'selected' : '' }}>Draft (Hidden)</option>
          </select>
          @error('status') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="order">Display Order</label>
          <input type="number" id="order" name="order" value="{{ old('order', $offer->order) }}" min="1" class="form-control" />
          <span class="form-hint">Controls tab order (1 = first tab).</span>
          @error('order') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1.25rem;">
          <label class="form-label" for="image">Featured Image</label>
          @if($offer->image_url)
            <div style="margin-bottom: 0.75rem; border-radius: 4px; overflow: hidden; background: #000;">
              <img src="{{ $offer->image_url }}" alt="Preview" style="max-height: 120px; width: 100%; object-fit: cover; display: block;" />
            </div>
          @endif
          <input type="file" id="image" name="image" accept="image/*" class="form-control" />
          <span class="form-hint">Leave blank to retain current image.</span>
          @error('image') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">SEO Meta Tags</h3>

        <div class="form-group">
          <label class="form-label" for="meta_title">Meta Title</label>
          <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $offer->meta_title) }}" class="form-control" />
          @error('meta_title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="meta_description">Meta Description</label>
          <textarea id="meta_description" name="meta_description" rows="3" class="form-control">{{ old('meta_description', $offer->meta_description) }}</textarea>
          @error('meta_description') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="admin-card" style="background: rgba(245, 200, 66, 0.04); border-color: var(--gold-a35);">
        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center; padding: 0.85rem;">
          Update Offer
        </button>
        <a href="{{ route('admin.asdreams.offers.index') }}" class="btn-subtle" style="width: 100%; justify-content: center; margin-top: 0.75rem; text-align: center;">
          Cancel
        </a>
      </div>
    </div>
  </div>
</form>

<script>
  document.getElementById('btnAddFeature')?.addEventListener('click', function() {
    const container = document.getElementById('featuresList');
    const row = document.createElement('div');
    row.className = 'feature-row';
    row.style = 'display: flex; gap: 0.5rem; align-items: center;';
    row.innerHTML = `
      <span style="color: var(--gold); font-size: 0.9rem;">✦</span>
      <input type="text" name="features[]" class="form-control" placeholder="Feature description..." />
      <button type="button" class="btn-action btn-action--delete btnRemoveFeature" title="Remove Feature">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    `;
    container.appendChild(row);
  });

  document.getElementById('featuresList')?.addEventListener('click', function(e) {
    if (e.target.closest('.btnRemoveFeature')) {
      const rows = document.querySelectorAll('.feature-row');
      if (rows.length > 1) {
        e.target.closest('.feature-row').remove();
      } else {
        alert('You must keep at least one feature row.');
      }
    }
  });
</script>
@endsection

