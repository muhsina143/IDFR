@extends('admin.layouts.app')

@section('title', 'Add New Hero Banner Slide')
@section('page_title', 'Create Banner Slide')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.banners.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Banners</span>
    </a>
    <h2 class="module-title">Add Hero <em>Banner Slide</em></h2>
    <p class="module-subtitle">Configure the homepage hero slide and its corresponding dynamic Offer Details page all in one place.</p>
  </div>
</div>

<form action="{{ route('admin.asdreams.banners.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div style="display: flex; flex-direction: column; gap: 2rem; max-width: 980px;">
    
    <!-- 1. HOMEPAGE BANNER SLIDER SETTINGS -->
    <div class="admin-card">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: #fff; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span style="display: inline-flex; width: 26px; height: 26px; border-radius: 50%; background: rgba(212,175,55,0.2); color: var(--gold); align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">1</span>
            Homepage Banner Slider Settings
          </h3>
          <p class="form-hint" style="margin: 0.25rem 0 0;">Configures the slide backdrop image, category tag, main title, and button in the homepage banner slider.</p>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label" for="title">Banner Heading / Main Title <span style="color: var(--gold);">*</span></label>
          <input type="text" id="title" name="title" value="{{ old('title') }}" required class="form-control" placeholder="e.g. Behind the Viewfinder or Lakme Bridal Studio" />
          @error('title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="highlight_title">Highlighted / Italic Heading Text</label>
          <input type="text" id="highlight_title" name="highlight_title" value="{{ old('highlight_title') }}" class="form-control" placeholder="e.g. Viewfinder or Bridal" />
          <span class="form-hint">Text rendered in gold script/italic font within the banner.</span>
          @error('highlight_title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="label">Top Category Label</label>
          <input type="text" id="label" name="label" value="{{ old('label') }}" class="form-control" placeholder="e.g. PORTRAIT SESSION · KOZHIKODE" />
          <span class="form-hint">Small uppercase eyebrow text above the heading.</span>
          @error('label') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label" for="description">Banner Subtitle / Short Description</label>
          <textarea id="description" name="description" rows="2" class="form-control" placeholder="e.g. Stories, emotions and timeless moments captured through our lens.">{{ old('description') }}</textarea>
          @error('description') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="image">Banner Background Image <span style="color: var(--gold);">*</span></label>
          <input type="file" id="image" name="image" required accept="image/*" class="form-control" />
          <span class="form-hint">High-resolution photography (1100×900px or larger, JPG/PNG/WEBP, max 5MB).</span>
          @error('image') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="alt_text">Image Alt Text (SEO)</label>
          <input type="text" id="alt_text" name="alt_text" value="{{ old('alt_text') }}" class="form-control" placeholder="e.g. Portrait session setup at studio" />
          @error('alt_text') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="button_text">Button CTA Text</label>
          <input type="text" id="button_text" name="button_text" value="{{ old('button_text', 'Offer Details') }}" class="form-control" />
          @error('button_text') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="order">Display Order in Slider</label>
          <input type="number" id="order" name="order" value="{{ old('order', 1) }}" min="1" class="form-control" />
          @error('order') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="grid-column: span 2; display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 4px;">
          <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--gold);" />
          <label for="is_active" style="color: #fff; font-size: 0.9rem; cursor: pointer; margin: 0;">Enable this banner slide in the homepage slider</label>
        </div>
      </div>
    </div>

    <!-- 2. OFFER DETAILS PAGE CONTENT (THE MOST IMPORTANT REQUIREMENT) -->
    <div class="admin-card" style="border: 1px solid rgba(212, 175, 55, 0.35); background: linear-gradient(180deg, rgba(212, 175, 55, 0.04) 0%, rgba(0,0,0,0) 100%);">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; border-bottom: 1px solid rgba(212, 175, 55, 0.25); padding-bottom: 0.75rem;">
        <div>
          <h3 style="font-size: 1.15rem; color: var(--gold); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
            <span style="display: inline-flex; width: 26px; height: 26px; border-radius: 50%; background: var(--gold); color: #000; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700;">2</span>
            Offer Details Page Content
          </h3>
          <p class="form-hint" style="margin: 0.25rem 0 0; color: var(--text-light);">
            When visitors click the "Offer Details" button on this banner, they will open this dedicated dynamic offer page.
          </p>
        </div>
        <div>
          <button type="button" class="btn-subtle" id="btnSyncFromBanner" style="font-size: 0.78rem; padding: 0.35rem 0.75rem;" title="Copy banner title, category and subtitle into offer fields">
            ↻ Copy From Banner
          </button>
        </div>
      </div>

      <!-- Optional: Link Existing Offer or Create New -->
      <div class="form-group" style="margin-bottom: 1.5rem; background: rgba(0,0,0,0.3); padding: 0.85rem 1rem; border-radius: 4px; border: 1px solid var(--border-subtle);">
        <label class="form-label" for="linked_offer_id" style="color: var(--gold);">Link Existing Offer (Optional)</label>
        <select id="linked_offer_id" name="linked_offer_id" class="form-control">
          <option value="">-- Auto-create a new dedicated offer for this banner (Recommended) --</option>
          @foreach($offers as $offer)
            <option value="{{ $offer->id }}" {{ old('linked_offer_id') == $offer->id ? 'selected' : '' }}>
              {{ $offer->title }} (/asdreams/offers/{{ $offer->slug }})
            </option>
          @endforeach
        </select>
        <span class="form-hint">Leave blank to automatically create a dedicated, unique Offer Details page for this banner using the fields below.</span>
        @error('linked_offer_id') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <!-- A. Offer Details Page Information -->
      <h4 style="color: #fff; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.08em; margin: 1.25rem 0 0.85rem; padding-bottom: 0.35rem; border-bottom: 1px dashed rgba(255,255,255,0.1);">
        A. Offer Details Page Information
      </h4>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label" for="offer_title">Offer Page Title</label>
          <input type="text" id="offer_title" name="offer_title" value="{{ old('offer_title') }}" class="form-control" placeholder="e.g. Behind the Viewfinder (defaults to banner title if left blank)" />
          @error('offer_title') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="offer_category_label">Category Label / Eyebrow</label>
          <input type="text" id="offer_category_label" name="offer_category_label" value="{{ old('offer_category_label') }}" class="form-control" placeholder="e.g. STUDIO ARCHIVE or PORTRAIT SESSION · KOZHIKODE" />
          @error('offer_category_label') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="offer_price">Offer Price <span class="form-hint" style="display: inline;">(Optional)</span></label>
          <input type="text" id="offer_price" name="offer_price" value="{{ old('offer_price') }}" class="form-control" placeholder="e.g. ₹25,000 or Starts at ₹15,000" />
          @error('offer_price') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="offer_validity">Offer Validity <span class="form-hint" style="display: inline;">(Optional)</span></label>
          <input type="text" id="offer_validity" name="offer_validity" value="{{ old('offer_validity') }}" class="form-control" placeholder="e.g. Valid until 31 Dec 2026 or Limited slots" />
          @error('offer_validity') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="offer_image">Offer Specific Image <span class="form-hint" style="display: inline;">(Optional)</span></label>
          <input type="file" id="offer_image" name="offer_image" accept="image/*" class="form-control" />
          <span class="form-hint">Defaults to the banner image if not uploaded separately.</span>
          @error('offer_image') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label" for="offer_lead_text">Subtitle / Short Description</label>
          <textarea id="offer_lead_text" name="offer_lead_text" rows="2" class="form-control" placeholder="e.g. A directed portrait session for people who want photographs that feel quiet, close and true.">{{ old('offer_lead_text') }}</textarea>
          @error('offer_lead_text') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label" for="offer_full_description">Main Description / Scope &amp; Direction <span class="form-hint" style="display: inline;">(Optional)</span></label>
          <textarea id="offer_full_description" name="offer_full_description" rows="4" class="form-control" placeholder="Extended details about this photography experience, creative vision, deliverable gallery, framing options...">{{ old('offer_full_description') }}</textarea>
          @error('offer_full_description') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <!-- B. Offer Features / Inclusions -->
      <div style="margin-top: 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.35rem; border-bottom: 1px dashed rgba(255,255,255,0.1);">
          <h4 style="color: #fff; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.08em; margin: 0;">
            B. Offer Features / Bullet Points
          </h4>
          <button type="button" class="btn-gold" id="btnAddFeature" style="font-size: 0.78rem; padding: 0.35rem 0.85rem;">
            + Add Feature Point
          </button>
        </div>
        <p class="form-hint" style="margin-bottom: 1rem;">
          These appear as premium bullet points with gold dashes (—) on the Offer Details page. You can add, edit, reorder (▲/▼), and delete points as needed.
        </p>

        <div id="featuresContainer" style="display: flex; flex-direction: column; gap: 0.75rem;">
          @php
            $defaultFeatures = old('offer_features', [
              '90-minute session in the Kozhikode studio or at a nearby outdoor location',
              'Lighting and posing directed for individuals, couples or small families',
              'Wardrobe guidance before the shoot, plus on-set styling support',
              '25 retouched photographs delivered in a private online gallery',
              'One 12×18 fine-art print, ready to frame'
            ]);
          @endphp
          @foreach($defaultFeatures as $fIndex => $fText)
            <div class="feature-item" style="display: flex; gap: 0.5rem; align-items: center; background: rgba(255,255,255,0.02); padding: 0.4rem 0.6rem; border-radius: 4px; border: 1px solid var(--border-subtle);">
              <span style="color: var(--gold); font-size: 1rem; width: 15px; text-align: center;">—</span>
              <input type="text" name="offer_features[]" value="{{ $fText }}" class="form-control" placeholder="Enter feature point description..." />
              <button type="button" class="btn-subtle btnMoveUp" title="Move Up" style="padding: 0.35rem 0.5rem; font-size: 0.75rem;">▲</button>
              <button type="button" class="btn-subtle btnMoveDown" title="Move Down" style="padding: 0.35rem 0.5rem; font-size: 0.75rem;">▼</button>
              <button type="button" class="btn-table-action btn-table-action--delete btnRemoveFeature" title="Delete Point" style="padding: 0.4rem;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            </div>
          @endforeach
        </div>
      </div>

      <!-- C. Additional Content -->
      <div style="margin-top: 1.75rem;">
        <h4 style="color: #fff; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 0.85rem; padding-bottom: 0.35rem; border-bottom: 1px dashed rgba(255,255,255,0.1);">
          C. Additional Content &amp; Notes
        </h4>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
          <div class="form-group">
            <label class="form-label" for="offer_notes">Additional Information / Highlights Note</label>
            <textarea id="offer_notes" name="offer_notes" rows="2" class="form-control" placeholder="e.g. Sessions are scheduled Tuesday to Sunday. A printed proof set can be added once the gallery is delivered.">{{ old('offer_notes', 'Sessions are scheduled Tuesday to Sunday. A printed proof set can be added once the gallery is delivered.') }}</textarea>
            <span class="form-hint">Displayed in the highlighted gold border note box on the Offer Details page.</span>
            @error('offer_notes') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="offer_terms">Terms and Conditions <span class="form-hint" style="display: inline;">(Optional)</span></label>
            <textarea id="offer_terms" name="offer_terms" rows="2" class="form-control" placeholder="e.g. 50% advance to confirm the slot. Rescheduling allowed up to 48 hours prior to the session.">{{ old('offer_terms') }}</textarea>
            @error('offer_terms') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="offer_important_notes">Important Notes <span class="form-hint" style="display: inline;">(Optional)</span></label>
            <textarea id="offer_important_notes" name="offer_important_notes" rows="2" class="form-control" placeholder="e.g. Wardrobe styling assistance and makeup artist can be arranged upon request.">{{ old('offer_important_notes') }}</textarea>
            @error('offer_important_notes') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>

    </div>

    <!-- SUBMIT ACTIONS -->
    <div style="display: flex; justify-content: flex-end; gap: 1rem; padding: 1.5rem 0; border-top: 1px solid var(--border-subtle);">
      <a href="{{ route('admin.asdreams.banners.index') }}" class="btn-subtle">Cancel</a>
      <button type="submit" class="btn-gold" style="padding: 0.75rem 2rem; font-size: 0.95rem;">
        <span>Create Banner &amp; Offer Details</span>
      </button>
    </div>

  </div>
</form>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // 1. Sync button from banner to offer fields
    const btnSync = document.getElementById('btnSyncFromBanner');
    if (btnSync) {
      btnSync.addEventListener('click', function () {
        const title = document.getElementById('title').value;
        const label = document.getElementById('label').value;
        const desc = document.getElementById('description').value;

        if (title && !document.getElementById('offer_title').value) {
          document.getElementById('offer_title').value = title;
        }
        if (label && !document.getElementById('offer_category_label').value) {
          document.getElementById('offer_category_label').value = label;
        }
        if (desc && !document.getElementById('offer_lead_text').value) {
          document.getElementById('offer_lead_text').value = desc;
        }
      });
    }

    // 2. Add / Remove / Reorder Features
    const container = document.getElementById('featuresContainer');
    const btnAdd = document.getElementById('btnAddFeature');

    function createFeatureRow(text) {
      const row = document.createElement('div');
      row.className = 'feature-item';
      row.style.cssText = 'display: flex; gap: 0.5rem; align-items: center; background: rgba(255,255,255,0.02); padding: 0.4rem 0.6rem; border-radius: 4px; border: 1px solid var(--border-subtle);';
      row.innerHTML = `
        <span style="color: var(--gold); font-size: 1rem; width: 15px; text-align: center;">—</span>
        <input type="text" name="offer_features[]" value="${text.replace(/"/g, '&quot;')}" class="form-control" placeholder="Enter feature point description..." />
        <button type="button" class="btn-subtle btnMoveUp" title="Move Up" style="padding: 0.35rem 0.5rem; font-size: 0.75rem;">▲</button>
        <button type="button" class="btn-subtle btnMoveDown" title="Move Down" style="padding: 0.35rem 0.5rem; font-size: 0.75rem;">▼</button>
        <button type="button" class="btn-table-action btn-table-action--delete btnRemoveFeature" title="Delete Point" style="padding: 0.4rem;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      `;
      return row;
    }

    if (btnAdd && container) {
      btnAdd.addEventListener('click', function () {
        const newRow = createFeatureRow('');
        container.appendChild(newRow);
        const input = newRow.querySelector('input');
        if (input) input.focus();
      });

      container.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btnRemoveFeature');
        if (removeBtn) {
          const row = removeBtn.closest('.feature-item');
          if (container.querySelectorAll('.feature-item').length > 1) {
            row.remove();
          } else {
            const input = row.querySelector('input');
            if (input) input.value = '';
          }
          return;
        }

        const upBtn = e.target.closest('.btnMoveUp');
        if (upBtn) {
          const row = upBtn.closest('.feature-item');
          const prev = row.previousElementSibling;
          if (prev) {
            container.insertBefore(row, prev);
          }
          return;
        }

        const downBtn = e.target.closest('.btnMoveDown');
        if (downBtn) {
          const row = downBtn.closest('.feature-item');
          const next = row.nextElementSibling;
          if (next) {
            container.insertBefore(next, row);
          }
          return;
        }
      });
    }
  });
</script>
@endsection
