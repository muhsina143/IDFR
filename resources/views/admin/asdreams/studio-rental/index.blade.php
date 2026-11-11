@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Studio Rental Management')
@section('page_title', 'Studio Rental Management')

@section('content')
@include('partials.svg-sprite')

<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio Rental <em>Management</em></h2>
    <p class="module-subtitle">Manage Facilities Provided, Equipment Included, Studio Gallery photos, and dynamic Studio Rental pricing from one unified workspace.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('asd.home') }}#studio_rental" target="_blank" class="btn-subtle">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      <span>View Live Studio Rental</span>
    </a>
  </div>
</div>

<!-- Compact Stats Bar -->
<div class="stats-overview-row">
  <div class="stat-pill">
    <span class="stat-pill__label">Facilities Provided</span>
    <span class="stat-pill__val stat-pill__val--gold" id="hubCountFacilities">{{ $facilities->count() }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Equipment Included</span>
    <span class="stat-pill__val" id="hubCountEquipment">{{ $equipments->count() }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Studio Gallery Shots</span>
    <span class="stat-pill__val stat-pill__val--green" id="hubCountGallery">{{ $galleryImages->count() }}</span>
  </div>
  <div class="stat-pill">
    <span class="stat-pill__label">Current Rental Rate</span>
    <span class="stat-pill__val" id="hubStatPrice" style="font-size: 1.35rem; color: var(--gold-light);">
      @if($rentalConfig['price_enabled'])
        {{ $rentalConfig['formatted_price'] }} {{ $rentalConfig['unit_display'] }}
      @else
        Hidden
      @endif
    </span>
  </div>
</div>

<!-- Centralized Section Tabs -->
<div class="category-tabs-bar" id="studioHubTabs" role="tablist" aria-label="Studio Rental Management sections">
  <button type="button" role="tab" class="category-tab {{ $activeTab === 'facilities' ? 'is-active' : '' }}" data-hub-tab="facilities" aria-selected="{{ $activeTab === 'facilities' ? 'true' : 'false' }}">
    1. Facilities Provided
  </button>
  <button type="button" role="tab" class="category-tab {{ $activeTab === 'equipment' ? 'is-active' : '' }}" data-hub-tab="equipment" aria-selected="{{ $activeTab === 'equipment' ? 'true' : 'false' }}">
    2. Equipment Included
  </button>
  <button type="button" role="tab" class="category-tab {{ $activeTab === 'gallery' ? 'is-active' : '' }}" data-hub-tab="gallery" aria-selected="{{ $activeTab === 'gallery' ? 'true' : 'false' }}">
    3. Studio Gallery
  </button>
  <button type="button" role="tab" class="category-tab {{ $activeTab === 'settings' ? 'is-active' : '' }}" data-hub-tab="settings" aria-selected="{{ $activeTab === 'settings' ? 'true' : 'false' }}">
    4. Studio Settings
  </button>
</div>

<!-- AJAX Notification Container -->
<div id="studioHubAlert" class="alert" style="display: none; margin-bottom: 1.25rem;" role="status" aria-live="polite">
  <span id="studioHubAlertText"></span>
  <button type="button" class="alert__close" onclick="document.getElementById('studioHubAlert').style.display='none'">&times;</button>
</div>

<!-- Centralized Tab Panels -->
<div id="studioHubPanel-facilities" class="studio-hub-panel" style="{{ $activeTab === 'facilities' ? '' : 'display: none;' }}">
  @include('admin.asdreams.studio-rental.partials._facilities_tab')
</div>

<div id="studioHubPanel-equipment" class="studio-hub-panel" style="{{ $activeTab === 'equipment' ? '' : 'display: none;' }}">
  @include('admin.asdreams.studio-rental.partials._equipment_tab')
</div>

<div id="studioHubPanel-gallery" class="studio-hub-panel" style="{{ $activeTab === 'gallery' ? '' : 'display: none;' }}">
  @include('admin.asdreams.studio-rental.partials._gallery_tab')
</div>

<div id="studioHubPanel-settings" class="studio-hub-panel" style="{{ $activeTab === 'settings' ? '' : 'display: none;' }}">
  @include('admin.asdreams.studio-rental.partials._settings_tab')
</div>

<!-- Modal 1: Add / Edit Facility -->
<div class="admin-modal" id="facilityFormModal" style="display: none;">
  <div class="admin-modal__backdrop" data-close-modal="facilityFormModal"></div>
  <div class="admin-modal__panel" style="max-width: 560px;">
    <div class="admin-modal__header">
      <h3 id="facilityModalTitle">Add Studio Facility</h3>
      <button type="button" class="modal-close-btn" data-close-modal="facilityFormModal">&times;</button>
    </div>

    <form id="facilityModalForm" method="POST" action="{{ route('admin.asdreams.studio-facilities.store') }}" data-hub-form="facilities">
      @csrf
      <input type="hidden" name="_method" id="facilityFormMethod" value="POST" />
      <input type="hidden" name="from_hub" value="1" />

      <div class="form-group">
        <label class="form-label" for="modalFacilityName">Facility Name <span style="color: var(--gold);">*</span></label>
        <input type="text" id="modalFacilityName" name="name" required maxlength="150" class="form-control" placeholder="e.g. Makeup Room" />
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="modalFacilityOrder">Display Order</label>
          <input type="number" id="modalFacilityOrder" name="display_order" min="0" value="{{ $nextFacilityOrder }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="form-label" for="modalFacilityIcon">Icon</label>
          <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 38px; height: 38px; flex-shrink: 0; border-radius: 50%; border: 1px solid rgba(229, 184, 61, 0.4); display: flex; align-items: center; justify-content: center; color: var(--gold); background: rgba(229, 184, 61, 0.08);">
              <svg width="17" height="17" aria-hidden="true" style="stroke: currentColor; fill: none;">
                <use id="modalFacilityIconUse" href="#i-check" />
              </svg>
            </div>
            <select id="modalFacilityIcon" name="icon" class="form-control" style="flex: 1;">
              @foreach($facilityIcons as $iconKey => $iconLabel)
                <option value="{{ $iconKey }}">{{ $iconLabel }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <input type="hidden" name="is_active" value="0" />
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" id="modalFacilityActive" name="is_active" value="1" checked />
          <span style="color: #fff; font-weight: 500;">Active &amp; Visible on ASDreams Website</span>
        </label>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" data-close-modal="facilityFormModal">Cancel</button>
        <button type="submit" class="btn-gold" id="facilityModalSubmitBtn">Save Facility</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 2: Add / Edit Equipment -->
<div class="admin-modal" id="equipmentFormModal" style="display: none;">
  <div class="admin-modal__backdrop" data-close-modal="equipmentFormModal"></div>
  <div class="admin-modal__panel" style="max-width: 600px;">
    <div class="admin-modal__header">
      <h3 id="equipmentModalTitle">Add Studio Equipment</h3>
      <button type="button" class="modal-close-btn" data-close-modal="equipmentFormModal">&times;</button>
    </div>

    <form id="equipmentModalForm" method="POST" action="{{ route('admin.asdreams.studio-equipment.store') }}" enctype="multipart/form-data" data-hub-form="equipment">
      @csrf
      <input type="hidden" name="_method" id="equipmentFormMethod" value="POST" />
      <input type="hidden" name="from_hub" value="1" />

      <div class="form-group">
        <label class="form-label" for="modalEquipmentName">Equipment Name <span style="color: var(--gold);">*</span></label>
        <input type="text" id="modalEquipmentName" name="name" required maxlength="150" class="form-control" placeholder="e.g. 2 SK 400 Photo Lights" />
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="modalEquipmentOrder">Display Order</label>
          <input type="number" id="modalEquipmentOrder" name="display_order" min="0" value="{{ $nextEquipmentOrder }}" class="form-control" />
        </div>

        <div class="form-group">
          <label class="form-label" for="modalEquipmentIcon">Fallback Icon</label>
          <div style="display: flex; align-items: center; gap: 0.65rem;">
            <div style="width: 38px; height: 38px; flex-shrink: 0; border-radius: 50%; border: 1px solid rgba(229, 184, 61, 0.4); display: flex; align-items: center; justify-content: center; color: var(--gold); background: rgba(229, 184, 61, 0.08);">
              <svg width="17" height="17" aria-hidden="true" style="stroke: currentColor; fill: none;">
                <use id="modalEquipmentIconUse" href="#i-light" />
              </svg>
            </div>
            <select id="modalEquipmentIcon" name="icon" class="form-control" style="flex: 1;">
              @foreach($equipmentIcons as $iconKey => $iconLabel)
                <option value="{{ $iconKey }}">{{ $iconLabel }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="modalEquipmentImage">Equipment Thumbnail (Optional)</label>
        <div id="modalEquipmentPreviewWrap" style="display: none; align-items: center; gap: 0.85rem; margin-bottom: 0.65rem;">
          <div style="width: 56px; height: 56px; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-gold); background: #000;">
            <img id="modalEquipmentPreviewImg" src="" alt="Equipment preview" style="width: 100%; height: 100%; object-fit: cover;" />
          </div>
          <label id="modalEquipmentRemoveWrap" style="display: none; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.8rem; color: var(--text-secondary);">
            <input type="checkbox" id="modalEquipmentRemoveImage" name="remove_image" value="1" />
            <span>Remove custom image</span>
          </label>
        </div>
        <input type="file" id="modalEquipmentImage" name="image" accept="image/*,.svg" class="form-control" />
        <span class="form-hint">JPG, PNG, WebP, or SVG (max 5MB).</span>
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <input type="hidden" name="is_active" value="0" />
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" id="modalEquipmentActive" name="is_active" value="1" checked />
          <span style="color: #fff; font-weight: 500;">Active &amp; Visible on ASDreams Website</span>
        </label>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" data-close-modal="equipmentFormModal">Cancel</button>
        <button type="submit" class="btn-gold" id="equipmentModalSubmitBtn">Save Equipment</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 3: Multi-Image Studio Gallery Upload -->
<div class="admin-modal" id="galleryUploadModal" style="display: none;">
  <div class="admin-modal__backdrop" data-close-modal="galleryUploadModal"></div>
  <div class="admin-modal__panel" style="max-width: 780px; max-height: 90vh; overflow-y: auto;">
    <div class="admin-modal__header">
      <h3>Upload Studio Gallery Images</h3>
      <button type="button" class="modal-close-btn" data-close-modal="galleryUploadModal">&times;</button>
    </div>

    <form id="galleryUploadForm" method="POST" action="{{ route('admin.asdreams.studio-gallery.store') }}" enctype="multipart/form-data" data-hub-form="gallery">
      @csrf
      <input type="hidden" name="from_hub" value="1" />

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
        <div class="form-group">
          <label class="form-label" for="modalGalleryUploadFiles">Select Studio Images <span style="color: var(--gold);">*</span></label>
          <input type="file" id="modalGalleryUploadFiles" name="images[]" multiple required accept="image/jpeg,image/png,image/webp" class="form-control" />
          <span class="form-hint">Select one or multiple JPG, PNG, or WebP images (max 8MB each).</span>
        </div>

        <div class="form-group">
          <label class="form-label" for="modalGalleryStartOrder">Starting Display Order</label>
          <input type="number" id="modalGalleryStartOrder" name="display_order" min="0" value="{{ $nextGalleryOrder }}" class="form-control" />
        </div>
      </div>

      <div class="form-group">
        <input type="hidden" name="is_active" value="0" />
        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
          <input type="checkbox" name="is_active" value="1" checked />
          <span style="color: #fff; font-weight: 500;">Publish uploaded images as Active immediately</span>
        </label>
      </div>

      <div id="modalGalleryPreviewWrap" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
          <strong style="color: var(--gold); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em;">Pre-Upload Previews &amp; Captions</strong>
          <span id="modalGalleryPreviewCount" style="font-size: 0.78rem; color: var(--text-secondary);"></span>
        </div>
        <div id="modalGalleryPreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 1rem;"></div>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" data-close-modal="galleryUploadModal">Cancel</button>
        <button type="submit" class="btn-gold">Upload &amp; Save Images</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 4: Edit Studio Gallery Image -->
<div class="admin-modal" id="galleryEditModal" style="display: none;">
  <div class="admin-modal__backdrop" data-close-modal="galleryEditModal"></div>
  <div class="admin-modal__panel" style="max-width: 680px;">
    <div class="admin-modal__header">
      <h3 id="galleryEditModalTitle">Edit Studio Image</h3>
      <button type="button" class="modal-close-btn" data-close-modal="galleryEditModal">&times;</button>
    </div>

    <form id="galleryEditForm" method="POST" action="" enctype="multipart/form-data" data-hub-form="gallery">
      @csrf
      <input type="hidden" name="_method" value="PUT" />
      <input type="hidden" name="from_hub" value="1" />

      <div style="display: grid; grid-template-columns: 170px 1fr; gap: 1.25rem; align-items: start;">
        <div>
          <label class="form-label">Preview</label>
          <div style="aspect-ratio: 4 / 5; width: 100%; border-radius: 6px; overflow: hidden; border: 1px solid var(--border-gold); background: #000;">
            <img id="modalGalleryEditPreview" src="" alt="Gallery shot" style="width: 100%; height: 100%; object-fit: cover;" />
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="modalGalleryEditTitle">Image Title / Caption <span style="color: var(--gold);">*</span></label>
            <input type="text" id="modalGalleryEditTitle" name="title" required maxlength="150" class="form-control" />
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="modalGalleryEditDescription">Description</label>
            <textarea id="modalGalleryEditDescription" name="description" rows="2" maxlength="500" class="form-control"></textarea>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem; align-items: center;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="modalGalleryEditOrder">Display Order</label>
              <input type="number" id="modalGalleryEditOrder" name="display_order" min="0" class="form-control" />
            </div>

            <div class="form-group" style="margin-bottom: 0; padding-top: 1.25rem;">
              <input type="hidden" name="is_active" value="0" />
              <label style="display: flex; align-items: center; gap: 0.45rem; cursor: pointer;">
                <input type="checkbox" id="modalGalleryEditActive" name="is_active" value="1" />
                <span style="color: #fff; font-weight: 500;">Active &amp; Visible</span>
              </label>
            </div>
          </div>

          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label" for="modalGalleryEditFile">Replace Image (Optional)</label>
            <input type="file" id="modalGalleryEditFile" name="image" accept="image/jpeg,image/png,image/webp" class="form-control" />
          </div>
        </div>
      </div>

      <div class="admin-modal__footer">
        <button type="button" class="btn-subtle" data-close-modal="galleryEditModal">Cancel</button>
        <button type="submit" class="btn-gold">Update Image</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal 5: Delete Confirmation -->
<div class="admin-modal" id="studioRentalDeleteModal" style="display: none;">
  <div class="admin-modal__backdrop" data-close-modal="studioRentalDeleteModal"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete <strong id="studioRentalDeleteLabel" style="color: var(--gold);"></strong>? This action cannot be undone.
    </p>
    <form id="studioRentalDeleteForm" method="POST" action="" data-hub-form="facilities">
      @csrf
      @method('DELETE')
      <input type="hidden" name="from_hub" value="1" />
      <div style="display: flex; gap: 1rem; justify-content: flex-end;">
        <button type="button" class="btn-subtle" data-close-modal="studioRentalDeleteModal">Cancel</button>
        <button type="submit" class="btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
  var csrfMeta = document.querySelector('meta[name="csrf-token"]');
  var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
  var currencies = @json($currencies);
  var units = @json($units);
  var routes = {
    facilityStore: @json(route('admin.asdreams.studio-facilities.store')),
    equipmentStore: @json(route('admin.asdreams.studio-equipment.store')),
    galleryStore: @json(route('admin.asdreams.studio-gallery.store'))
  };

  function showAlert(message, isError) {
    var box = document.getElementById('studioHubAlert');
    var text = document.getElementById('studioHubAlertText');
    if (!box || !text) {
      return;
    }
    box.className = 'alert ' + (isError ? 'alert--error' : 'alert--success');
    text.textContent = message;
    box.style.display = 'flex';
  }

  function switchTab(tabName) {
    document.querySelectorAll('#studioHubTabs [data-hub-tab]').forEach(function (btn) {
      var active = btn.getAttribute('data-hub-tab') === tabName;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-selected', active ? 'true' : 'false');
    });

    document.querySelectorAll('.studio-hub-panel').forEach(function (panel) {
      panel.style.display = (panel.id === 'studioHubPanel-' + tabName) ? '' : 'none';
    });

    if (window.history && window.history.replaceState) {
      var url = new URL(window.location.href);
      url.searchParams.set('tab', tabName);
      window.history.replaceState({}, '', url.toString());
    }
  }

  function openModal(id) {
    var el = document.getElementById(id);
    if (el) {
      el.style.display = 'flex';
    }
  }

  function closeModal(id) {
    var el = document.getElementById(id);
    if (el) {
      el.style.display = 'none';
    }
  }

  function closeAllModals() {
    ['facilityFormModal', 'equipmentFormModal', 'galleryUploadModal', 'galleryEditModal', 'studioRentalDeleteModal'].forEach(closeModal);
  }

  function applyFilter(containerId, query, status) {
    var container = document.getElementById(containerId);
    if (!container) {
      return;
    }
    var term = (query || '').toLowerCase().trim();
    Array.from(container.children).forEach(function (item) {
      var hay = item.getAttribute('data-search');
      if (hay === null) {
        return;
      }
      var itemStatus = item.getAttribute('data-status') || '';
      var matchesTerm = !term || hay.indexOf(term) !== -1;
      var matchesStatus = !status || itemStatus === status;
      item.style.display = (matchesTerm && matchesStatus) ? '' : 'none';
    });
  }

  function updateSettingsLivePreview() {
    var priceEl = document.getElementById('rentalPriceAmount');
    if (!priceEl) {
      return;
    }

    var amount = parseFloat(priceEl.value);
    if (isNaN(amount) || amount < 0) {
      amount = 0;
    }
    var decimals = Math.abs(amount - Math.floor(amount)) > 0.0001 ? 2 : 0;
    var formattedNum = amount.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });

    var currencyCode = (document.getElementById('rentalCurrency') || {}).value || 'INR';
    var currencyMeta = currencies[currencyCode] || { symbol: '₹', space_after: false };
    var formattedPrice = currencyMeta.symbol + (currencyMeta.space_after ? ' ' : '') + formattedNum;

    var unitKey = (document.getElementById('rentalUnit') || {}).value || 'Per Day';
    var unitDisplay = (units[unitKey] && units[unitKey].display) ? units[unitKey].display : '/ Day';

    var enabled = !!(document.getElementById('rentalPriceEnabled') || {}).checked;
    var title = (document.getElementById('rentalTitle') || {}).value || 'Studio Rental';
    var pricingLabel = (document.getElementById('rentalPricingLabel') || {}).value || '';
    var shortDesc = (document.getElementById('rentalShortDescription') || {}).value || '';
    var priceDesc = (document.getElementById('rentalPriceDescription') || {}).value || '';
    var bookingInfo = (document.getElementById('rentalBookingInfo') || {}).value || '';
    var additionalNote = (document.getElementById('rentalAdditionalNote') || {}).value || '';

    var pTitle = document.getElementById('previewTitle');
    var pBlock = document.getElementById('previewPriceBlock');
    var pLabel = document.getElementById('previewPricingLabel');
    var pAmount = document.getElementById('previewFormattedPrice');
    var pUnit = document.getElementById('previewUnit');
    var pShort = document.getElementById('previewShortDesc');
    var pDesc = document.getElementById('previewPriceDesc');
    var pBook = document.getElementById('previewBookingInfo');
    var pAdd = document.getElementById('previewAdditionalNote');
    var pBadge = document.getElementById('previewStatusBadge');

    if (pTitle) pTitle.textContent = title;
    if (pBlock) pBlock.style.display = enabled ? '' : 'none';
    if (pLabel) {
      pLabel.textContent = pricingLabel;
      pLabel.style.display = pricingLabel.trim() ? '' : 'none';
    }
    if (pAmount) pAmount.textContent = formattedPrice;
    if (pUnit) pUnit.textContent = unitDisplay;
    if (pShort) {
      pShort.textContent = shortDesc;
      pShort.style.display = shortDesc.trim() ? '' : 'none';
    }
    if (pDesc) {
      pDesc.textContent = priceDesc;
      pDesc.style.display = (enabled && priceDesc.trim()) ? '' : 'none';
    }
    if (pBook) {
      pBook.textContent = bookingInfo;
      pBook.style.display = bookingInfo.trim() ? '' : 'none';
    }
    if (pAdd) {
      pAdd.textContent = additionalNote;
      pAdd.style.display = (enabled && additionalNote.trim()) ? '' : 'none';
    }
    if (pBadge) {
      pBadge.textContent = enabled ? 'Price Visible' : 'Price Hidden';
      pBadge.style.color = enabled ? '#4ade80' : '#f87171';
      pBadge.style.background = enabled ? 'rgba(34, 197, 94, 0.12)' : 'rgba(239, 68, 68, 0.12)';
    }
  }

  function toTitleFromFilename(name) {
    var clean = name.replace(/\.[^/.]+$/, '').replace(/[_-]+/g, ' ').trim();
    return clean.replace(/\b\w/g, function (c) { return c.toUpperCase(); });
  }

  function renderGalleryUploadPreviews() {
    var input = document.getElementById('modalGalleryUploadFiles');
    var startEl = document.getElementById('modalGalleryStartOrder');
    var wrap = document.getElementById('modalGalleryPreviewWrap');
    var grid = document.getElementById('modalGalleryPreviewGrid');
    var countEl = document.getElementById('modalGalleryPreviewCount');
    if (!input || !wrap || !grid) {
      return;
    }

    var files = Array.from(input.files || []);
    grid.innerHTML = '';

    if (!files.length) {
      wrap.style.display = 'none';
      return;
    }

    wrap.style.display = 'block';
    if (countEl) {
      countEl.textContent = files.length + (files.length === 1 ? ' image selected' : ' images selected');
    }

    var baseOrder = parseInt(startEl ? startEl.value : '1', 10);
    if (isNaN(baseOrder)) baseOrder = 1;

    files.forEach(function (file, idx) {
      var card = document.createElement('div');
      card.style.cssText = 'background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 6px; overflow: hidden;';

      var thumbWrap = document.createElement('div');
      thumbWrap.style.cssText = 'aspect-ratio: 4 / 5; background: #000; position: relative;';

      var img = document.createElement('img');
      img.src = URL.createObjectURL(file);
      img.alt = file.name;
      img.style.cssText = 'width: 100%; height: 100%; object-fit: cover; display: block;';
      thumbWrap.appendChild(img);

      if (typeof DataTransfer !== 'undefined') {
        var rm = document.createElement('button');
        rm.type = 'button';
        rm.textContent = 'Remove';
        rm.style.cssText = 'position: absolute; top: 0.4rem; right: 0.4rem; background: rgba(180,35,24,0.9); color: #fff; border: none; border-radius: 4px; padding: 0.2rem 0.5rem; font-size: 0.7rem; cursor: pointer;';
        rm.addEventListener('click', function () {
          var dt = new DataTransfer();
          files.forEach(function (f, keepIdx) {
            if (keepIdx !== idx) dt.items.add(f);
          });
          input.files = dt.files;
          renderGalleryUploadPreviews();
        });
        thumbWrap.appendChild(rm);
      }

      var fields = document.createElement('div');
      fields.style.cssText = 'padding: 0.65rem; display: flex; flex-direction: column; gap: 0.45rem;';

      var t = document.createElement('input');
      t.type = 'text';
      t.name = 'titles[' + idx + ']';
      t.maxLength = 150;
      t.value = toTitleFromFilename(file.name);
      t.placeholder = 'Image Title';
      t.className = 'form-control';
      t.style.fontSize = '0.8rem';

      var d = document.createElement('input');
      d.type = 'text';
      d.name = 'descriptions[' + idx + ']';
      d.maxLength = 500;
      d.placeholder = 'Description (optional)';
      d.className = 'form-control';
      d.style.fontSize = '0.78rem';

      var o = document.createElement('input');
      o.type = 'number';
      o.name = 'orders[' + idx + ']';
      o.min = '0';
      o.value = String(baseOrder + idx);
      o.placeholder = 'Order';
      o.className = 'form-control';
      o.style.fontSize = '0.78rem';

      fields.appendChild(t);
      fields.appendChild(d);
      fields.appendChild(o);
      card.appendChild(thumbWrap);
      card.appendChild(fields);
      grid.appendChild(card);
    });
  }

  document.addEventListener('click', function (e) {
    var tabBtn = e.target.closest('[data-hub-tab]');
    if (tabBtn) {
      switchTab(tabBtn.getAttribute('data-hub-tab'));
      return;
    }

    var closeBtn = e.target.closest('[data-close-modal]');
    if (closeBtn) {
      closeModal(closeBtn.getAttribute('data-close-modal'));
      return;
    }

    var facCreate = e.target.closest('[data-open-facility-create]');
    if (facCreate) {
      document.getElementById('facilityModalTitle').textContent = 'Add Studio Facility';
      document.getElementById('facilityModalSubmitBtn').textContent = 'Save Facility';
      document.getElementById('facilityModalForm').action = routes.facilityStore;
      document.getElementById('facilityFormMethod').value = 'POST';
      document.getElementById('modalFacilityName').value = '';
      document.getElementById('modalFacilityIcon').value = 'check';
      document.getElementById('modalFacilityIconUse').setAttribute('href', '#i-check');
      document.getElementById('modalFacilityOrder').value = facCreate.getAttribute('data-next-order') || '1';
      document.getElementById('modalFacilityActive').checked = true;
      openModal('facilityFormModal');
      return;
    }

    var facEdit = e.target.closest('[data-open-facility-edit]');
    if (facEdit) {
      document.getElementById('facilityModalTitle').textContent = 'Edit Studio Facility';
      document.getElementById('facilityModalSubmitBtn').textContent = 'Update Facility';
      document.getElementById('facilityModalForm').action = facEdit.getAttribute('data-action');
      document.getElementById('facilityFormMethod').value = 'PUT';
      document.getElementById('modalFacilityName').value = facEdit.getAttribute('data-name') || '';
      var fIcon = facEdit.getAttribute('data-icon') || 'check';
      document.getElementById('modalFacilityIcon').value = fIcon;
      document.getElementById('modalFacilityIconUse').setAttribute('href', '#i-' + fIcon);
      document.getElementById('modalFacilityOrder').value = facEdit.getAttribute('data-order') || '1';
      document.getElementById('modalFacilityActive').checked = facEdit.getAttribute('data-active') === '1';
      openModal('facilityFormModal');
      return;
    }

    var eqCreate = e.target.closest('[data-open-equipment-create]');
    if (eqCreate) {
      document.getElementById('equipmentModalTitle').textContent = 'Add Studio Equipment';
      document.getElementById('equipmentModalSubmitBtn').textContent = 'Save Equipment';
      document.getElementById('equipmentModalForm').action = routes.equipmentStore;
      document.getElementById('equipmentFormMethod').value = 'POST';
      document.getElementById('modalEquipmentName').value = '';
      document.getElementById('modalEquipmentIcon').value = 'light';
      document.getElementById('modalEquipmentIconUse').setAttribute('href', '#i-light');
      document.getElementById('modalEquipmentOrder').value = eqCreate.getAttribute('data-next-order') || '1';
      document.getElementById('modalEquipmentActive').checked = true;
      document.getElementById('modalEquipmentImage').value = '';
      document.getElementById('modalEquipmentPreviewWrap').style.display = 'none';
      document.getElementById('modalEquipmentRemoveWrap').style.display = 'none';
      document.getElementById('modalEquipmentRemoveImage').checked = false;
      openModal('equipmentFormModal');
      return;
    }

    var eqEdit = e.target.closest('[data-open-equipment-edit]');
    if (eqEdit) {
      document.getElementById('equipmentModalTitle').textContent = 'Edit Studio Equipment';
      document.getElementById('equipmentModalSubmitBtn').textContent = 'Update Equipment';
      document.getElementById('equipmentModalForm').action = eqEdit.getAttribute('data-action');
      document.getElementById('equipmentFormMethod').value = 'PUT';
      document.getElementById('modalEquipmentName').value = eqEdit.getAttribute('data-name') || '';
      var eIcon = eqEdit.getAttribute('data-icon') || 'light';
      document.getElementById('modalEquipmentIcon').value = eIcon;
      document.getElementById('modalEquipmentIconUse').setAttribute('href', '#i-' + eIcon);
      document.getElementById('modalEquipmentOrder').value = eqEdit.getAttribute('data-order') || '1';
      document.getElementById('modalEquipmentActive').checked = eqEdit.getAttribute('data-active') === '1';
      document.getElementById('modalEquipmentImage').value = '';
      document.getElementById('modalEquipmentRemoveImage').checked = false;
      var imgUrl = eqEdit.getAttribute('data-image-url') || '';
      if (imgUrl) {
        document.getElementById('modalEquipmentPreviewImg').src = imgUrl;
        document.getElementById('modalEquipmentPreviewWrap').style.display = 'flex';
        document.getElementById('modalEquipmentRemoveWrap').style.display = 'flex';
      } else {
        document.getElementById('modalEquipmentPreviewWrap').style.display = 'none';
        document.getElementById('modalEquipmentRemoveWrap').style.display = 'none';
      }
      openModal('equipmentFormModal');
      return;
    }

    var galUpload = e.target.closest('[data-open-gallery-upload]');
    if (galUpload) {
      document.getElementById('galleryUploadForm').reset();
      document.getElementById('modalGalleryStartOrder').value = galUpload.getAttribute('data-next-order') || '1';
      document.getElementById('modalGalleryPreviewGrid').innerHTML = '';
      document.getElementById('modalGalleryPreviewWrap').style.display = 'none';
      openModal('galleryUploadModal');
      return;
    }

    var galEdit = e.target.closest('[data-open-gallery-edit]');
    if (galEdit) {
      document.getElementById('galleryEditForm').action = galEdit.getAttribute('data-action');
      document.getElementById('modalGalleryEditTitle').value = galEdit.getAttribute('data-title') || '';
      document.getElementById('modalGalleryEditDescription').value = galEdit.getAttribute('data-description') || '';
      document.getElementById('modalGalleryEditOrder').value = galEdit.getAttribute('data-order') || '1';
      document.getElementById('modalGalleryEditActive').checked = galEdit.getAttribute('data-active') === '1';
      document.getElementById('modalGalleryEditPreview').src = galEdit.getAttribute('data-image-url') || '';
      document.getElementById('modalGalleryEditFile').value = '';
      openModal('galleryEditModal');
      return;
    }

    var delBtn = e.target.closest('[data-hub-delete]');
    if (delBtn) {
      var delForm = document.getElementById('studioRentalDeleteForm');
      delForm.action = delBtn.getAttribute('data-action');
      delForm.setAttribute('data-hub-form', delBtn.getAttribute('data-tab') || 'facilities');
      document.getElementById('studioRentalDeleteLabel').textContent = delBtn.getAttribute('data-label') || 'this item';
      openModal('studioRentalDeleteModal');
    }
  });

  document.addEventListener('input', function (e) {
    if (e.target.matches('[data-table-search]')) {
      var tId = e.target.getAttribute('data-table-search');
      var sel = document.querySelector('[data-table-status="' + tId + '"]');
      applyFilter(tId, e.target.value, sel ? sel.value : '');
    } else if (e.target.matches('[data-grid-search]')) {
      var gId = e.target.getAttribute('data-grid-search');
      var gSel = document.querySelector('[data-grid-status="' + gId + '"]');
      applyFilter(gId, e.target.value, gSel ? gSel.value : '');
    } else if (e.target.id === 'modalGalleryStartOrder') {
      var baseOrder = parseInt(e.target.value, 10);
      if (!isNaN(baseOrder)) {
        document.querySelectorAll('#modalGalleryPreviewGrid input[name^="orders["]').forEach(function (el, idx) {
          el.value = String(baseOrder + idx);
        });
      }
    } else if (e.target.matches('[data-preview-input]')) {
      updateSettingsLivePreview();
    }
  });

  document.addEventListener('change', function (e) {
    if (e.target.matches('[data-table-status]')) {
      var tId = e.target.getAttribute('data-table-status');
      var inp = document.querySelector('[data-table-search="' + tId + '"]');
      applyFilter(tId, inp ? inp.value : '', e.target.value);
    } else if (e.target.matches('[data-grid-status]')) {
      var gId = e.target.getAttribute('data-grid-status');
      var gInp = document.querySelector('[data-grid-search="' + gId + '"]');
      applyFilter(gId, gInp ? gInp.value : '', e.target.value);
    } else if (e.target.id === 'modalFacilityIcon') {
      document.getElementById('modalFacilityIconUse').setAttribute('href', '#i-' + e.target.value);
    } else if (e.target.id === 'modalEquipmentIcon') {
      document.getElementById('modalEquipmentIconUse').setAttribute('href', '#i-' + e.target.value);
    } else if (e.target.id === 'modalEquipmentImage') {
      var eqFile = e.target.files && e.target.files[0];
      if (eqFile) {
        document.getElementById('modalEquipmentPreviewImg').src = URL.createObjectURL(eqFile);
        document.getElementById('modalEquipmentPreviewWrap').style.display = 'flex';
      }
    } else if (e.target.id === 'modalGalleryUploadFiles') {
      renderGalleryUploadPreviews();
    } else if (e.target.id === 'modalGalleryEditFile') {
      var gFile = e.target.files && e.target.files[0];
      if (gFile) {
        document.getElementById('modalGalleryEditPreview').src = URL.createObjectURL(gFile);
      }
    } else if (e.target.matches('[data-preview-input]')) {
      updateSettingsLivePreview();
    }
  });

  // Unified AJAX Form Handler (avoids unnecessary page reloads while supporting all CRUD + File Uploads)
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('form[data-hub-form]');
    if (!form) {
      return;
    }
    e.preventDefault();

    var tab = form.getAttribute('data-hub-form') || 'facilities';
    var submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
    }

    fetch(form.action, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken
      },
      body: new FormData(form)
    })
      .then(function (res) {
        return res.json().then(function (payload) {
          return { ok: res.ok, status: res.status, payload: payload };
        });
      })
      .then(function (result) {
        if (!result.ok || !result.payload || !result.payload.success) {
          var errMsg = (result.payload && result.payload.message) ? result.payload.message : 'Unable to save changes. Please review your input.';
          if (result.payload && result.payload.errors) {
            var firstField = Object.keys(result.payload.errors)[0];
            if (firstField && result.payload.errors[firstField][0]) {
              errMsg = result.payload.errors[firstField][0];
            }
          }
          showAlert(errMsg, true);
          return;
        }

        var data = result.payload;
        var panel = document.getElementById('studioHubPanel-' + (data.tab || tab));
        if (panel && data.tabHtml) {
          panel.innerHTML = data.tabHtml;
        }

        if (data.counts) {
          var cFac = document.getElementById('hubCountFacilities');
          var cEq = document.getElementById('hubCountEquipment');
          var cGal = document.getElementById('hubCountGallery');
          if (cFac) cFac.textContent = data.counts.facilities;
          if (cEq) cEq.textContent = data.counts.equipment;
          if (cGal) cGal.textContent = data.counts.gallery;
        }

        if (data.rentalConfig) {
          var statPrice = document.getElementById('hubStatPrice');
          if (statPrice) {
            statPrice.textContent = data.rentalConfig.price_enabled
              ? (data.rentalConfig.formatted_price + ' ' + data.rentalConfig.unit_display)
              : 'Hidden';
          }
          updateSettingsLivePreview();
        }

        closeAllModals();
        showAlert(data.message || 'Changes saved successfully.', false);
      })
      .catch(function () {
        showAlert('Network error occurred while saving. Please try again.', true);
      })
      .finally(function () {
        if (submitBtn) {
          submitBtn.disabled = false;
        }
      });
  });
})();
</script>
@endsection

