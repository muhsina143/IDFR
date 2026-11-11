@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Upload Studio Gallery Images')
@section('page_title', 'Upload Studio Images')

@section('content')
@include('admin.asdreams.studio-rental._nav')

<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.studio-gallery.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Studio Gallery</span>
    </a>
    <h2 class="module-title">Upload Studio <em>Gallery Images</em></h2>
    <p class="module-subtitle">Select one or multiple photos to upload. Review previews and customize each image title, description, and display order before saving.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 980px;">
  <form action="{{ route('admin.asdreams.studio-gallery.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; align-items: start;">
      <div class="form-group">
        <label class="form-label" for="studioGalleryImages">Select Studio Images <span style="color: var(--gold);">*</span></label>
        <input type="file" id="studioGalleryImages" name="images[]" multiple required accept="image/jpeg,image/png,image/webp" class="form-control" />
        <span class="form-hint">You can select multiple JPG, PNG, or WebP images at once (max 8MB per image).</span>
        @error('images') <span class="form-error">{{ $message }}</span> @enderror
        @error('images.*') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="startingOrder">Starting Display Order</label>
        <input type="number" id="startingOrder" name="display_order" value="{{ old('display_order', $nextOrder) }}" min="0" class="form-control" />
        @error('display_order') <span class="form-error">{{ $message }}</span> @enderror
      </div>
    </div>

    <div class="form-group" style="margin-top: 0.25rem;">
      <input type="hidden" name="is_active" value="0" />
      <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} />
        <span style="font-weight: 500; color: #fff;">Publish uploaded images as Active immediately</span>
      </label>
    </div>

    <!-- Live Pre-Save Preview Container -->
    <div id="studioGalleryPreviewSection" style="display: none; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-family: var(--font-serif); font-size: 1.05rem; color: var(--gold); margin: 0;">Selected Image Previews</h3>
        <span id="studioGalleryPreviewCount" style="font-size: 0.8rem; color: var(--text-secondary);"></span>
      </div>

      <div id="studioGalleryPreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.15rem;"></div>
    </div>

    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
      <button type="submit" class="btn-gold">Upload &amp; Save Studio Images</button>
      <a href="{{ route('admin.asdreams.studio-gallery.index') }}" class="btn-subtle">Cancel</a>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
  (function () {
    var input = document.getElementById('studioGalleryImages');
    var startingOrderEl = document.getElementById('startingOrder');
    var previewSection = document.getElementById('studioGalleryPreviewSection');
    var previewGrid = document.getElementById('studioGalleryPreviewGrid');
    var countEl = document.getElementById('studioGalleryPreviewCount');

    if (!input || !previewSection || !previewGrid) {
      return;
    }

    function toTitleFromFilename(name) {
      var clean = name.replace(/\.[^/.]+$/, '').replace(/[_-]+/g, ' ').trim();
      return clean.replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    function renderPreviews() {
      var files = Array.from(input.files || []);
      previewGrid.innerHTML = '';

      if (!files.length) {
        previewSection.style.display = 'none';
        return;
      }

      previewSection.style.display = 'block';
      if (countEl) {
        countEl.textContent = files.length + (files.length === 1 ? ' image selected' : ' images selected');
      }

      var baseOrder = parseInt(startingOrderEl ? startingOrderEl.value : '1', 10);
      if (isNaN(baseOrder)) {
        baseOrder = 1;
      }

      files.forEach(function (file, idx) {
        var card = document.createElement('div');
        card.style.cssText = 'background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;';

        var thumbWrap = document.createElement('div');
        thumbWrap.style.cssText = 'aspect-ratio: 4 / 5; background: #000; position: relative; overflow: hidden;';

        var img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.alt = file.name;
        img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
        thumbWrap.appendChild(img);

        if (typeof DataTransfer !== 'undefined') {
          var removeBtn = document.createElement('button');
          removeBtn.type = 'button';
          removeBtn.textContent = 'Remove';
          removeBtn.style.cssText = 'position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(180,35,24,0.9); color: #fff; border: none; border-radius: 4px; padding: 0.25rem 0.55rem; font-size: 0.72rem; cursor: pointer;';
          removeBtn.addEventListener('click', function () {
            var dt = new DataTransfer();
            files.forEach(function (f, keepIdx) {
              if (keepIdx !== idx) {
                dt.items.add(f);
              }
            });
            input.files = dt.files;
            renderPreviews();
          });
          thumbWrap.appendChild(removeBtn);
        }

        var body = document.createElement('div');
        body.style.cssText = 'padding: 0.85rem; display: flex; flex-direction: column; gap: 0.6rem;';

        var titleLabel = document.createElement('label');
        titleLabel.className = 'form-label';
        titleLabel.style.marginBottom = '0.2rem';
        titleLabel.textContent = 'Image Title';

        var titleInput = document.createElement('input');
        titleInput.type = 'text';
        titleInput.name = 'titles[' + idx + ']';
        titleInput.maxLength = 150;
        titleInput.value = toTitleFromFilename(file.name);
        titleInput.className = 'form-control';

        var descLabel = document.createElement('label');
        descLabel.className = 'form-label';
        descLabel.style.marginBottom = '0.2rem';
        descLabel.textContent = 'Description / Lightbox Caption';

        var descInput = document.createElement('input');
        descInput.type = 'text';
        descInput.name = 'descriptions[' + idx + ']';
        descInput.maxLength = 500;
        descInput.placeholder = 'Optional studio gallery caption...';
        descInput.className = 'form-control';

        var orderLabel = document.createElement('label');
        orderLabel.className = 'form-label';
        orderLabel.style.marginBottom = '0.2rem';
        orderLabel.textContent = 'Display Order';

        var orderInput = document.createElement('input');
        orderInput.type = 'number';
        orderInput.name = 'orders[' + idx + ']';
        orderInput.min = '0';
        orderInput.value = String(baseOrder + idx);
        orderInput.className = 'form-control';

        body.appendChild(titleLabel);
        body.appendChild(titleInput);
        body.appendChild(descLabel);
        body.appendChild(descInput);
        body.appendChild(orderLabel);
        body.appendChild(orderInput);

        card.appendChild(thumbWrap);
        card.appendChild(body);
        previewGrid.appendChild(card);
      });
    }

    input.addEventListener('change', renderPreviews);
    if (startingOrderEl) {
      startingOrderEl.addEventListener('input', function () {
        var baseOrder = parseInt(startingOrderEl.value, 10);
        if (isNaN(baseOrder)) {
          return;
        }
        var orderInputs = previewGrid.querySelectorAll('input[name^="orders["]');
        orderInputs.forEach(function (el, idx) {
          el.value = String(baseOrder + idx);
        });
      });
    }
  })();
</script>
@endsection

