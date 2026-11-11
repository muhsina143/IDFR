@extends('admin.layouts.app')

@section('title', 'ASDreamStudio — Studio Facilities Provided')
@section('page_title', 'Facilities Provided')

@section('content')
@include('admin.asdreams.studio-rental._nav')

<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio Rental — <em>Facilities Provided</em></h2>
    <p class="module-subtitle">Manage the Studio Rental Facilities heading, description, icons, display order, and active items shown on the ASDreams homepage.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.studio-facilities.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Facility</span>
    </a>
  </div>
</div>

<!-- Editable Section Heading & Description -->
<div class="admin-card" style="margin-bottom: 1.5rem;">
  <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
    <h3 style="font-family: var(--font-serif); font-size: 1.1rem; color: #fff; margin: 0;">Section Heading &amp; Description</h3>
    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.25rem;">Controls the title and badge/note displayed at the top of the Facilities Provided panel.</p>
  </div>

  <form method="POST" action="{{ route('admin.asdreams.studio-facilities.settings') }}">
    @csrf
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; align-items: start;">
      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label" for="facilitiesHeading">Section Heading <span style="color: var(--gold);">*</span></label>
        <input type="text" id="facilitiesHeading" name="heading" value="{{ old('heading', $heading) }}" required maxlength="150" class="form-control" placeholder="Facilities Provided" />
        @error('heading') <span class="form-error">{{ $message }}</span> @enderror
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label" for="facilitiesDescription">Section Description</label>
        <input type="text" id="facilitiesDescription" name="description" value="{{ old('description', $description) }}" maxlength="500" class="form-control" placeholder="All areas are air-conditioned." />
        @error('description') <span class="form-error">{{ $message }}</span> @enderror
      </div>
    </div>

    <div style="margin-top: 1.15rem; display: flex; justify-content: flex-end;">
      <button type="submit" class="btn-gold">Save Heading &amp; Description</button>
    </div>
  </form>
</div>

<!-- Search & Filter Card -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.studio-facilities.index') }}" class="filter-form">
    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search facility name..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status']))
        <a href="{{ route('admin.asdreams.studio-facilities.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Facilities Table -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 75px;">Order</th>
          <th style="width: 75px;">Icon</th>
          <th>Facility Name</th>
          <th style="width: 130px;">Status</th>
          <th style="width: 140px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($facilities as $facility)
          <tr>
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $facility->display_order }}</span>
            </td>
            <td>
              <div style="width: 38px; height: 38px; border-radius: 50%; border: 1px solid rgba(229, 184, 61, 0.35); display: flex; align-items: center; justify-content: center; color: var(--gold); background: rgba(229, 184, 61, 0.06);">
                <svg width="18" height="18" aria-hidden="true" style="stroke: currentColor; fill: none;">
                  <use href="#i-{{ $facility->icon ?: 'check' }}" />
                </svg>
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem;">{{ $facility->name }}</strong>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.studio-facilities.toggle-status', $facility) }}" method="POST">
                @csrf
                <button type="submit" class="status-toggle-btn {{ $facility->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle visibility">
                  <span class="status-dot"></span>
                  <span>{{ $facility->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <a href="{{ route('admin.asdreams.studio-facilities.edit', $facility) }}" class="btn-action btn-action--edit" title="Edit Facility">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmAsdFacilityDelete('{{ $facility->id }}', '{{ addslashes($facility->name) }}')" title="Delete Facility">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteAsdFacilityForm-{{ $facility->id }}" action="{{ route('admin.asdreams.studio-facilities.destroy', $facility) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; padding: 3rem 1rem;">
              <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 1rem;">No studio facilities added yet.</p>
              <a href="{{ route('admin.asdreams.studio-facilities.create') }}" class="btn-gold" style="display: inline-flex;">Add First Facility</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($facilities->hasPages())
    <div style="padding: 1.5rem; border-top: 1px solid var(--border-subtle);">
      {{ $facilities->links() }}
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteAsdFacilityModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeAsdFacilityDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to delete facility <strong id="deleteAsdFacilityName" style="color: var(--gold);"></strong>? It will immediately be removed from the Book Studio for Rent section.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeAsdFacilityDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmAsdFacilityDeleteBtn">Delete Facility</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetAsdFacilityId = null;

  function confirmAsdFacilityDelete(id, name) {
    targetAsdFacilityId = id;
    document.getElementById('deleteAsdFacilityName').textContent = name;
    document.getElementById('deleteAsdFacilityModal').style.display = 'flex';
  }

  function closeAsdFacilityDeleteModal() {
    document.getElementById('deleteAsdFacilityModal').style.display = 'none';
    targetAsdFacilityId = null;
  }

  document.getElementById('confirmAsdFacilityDeleteBtn').addEventListener('click', function () {
    if (targetAsdFacilityId) {
      document.getElementById('deleteAsdFacilityForm-' + targetAsdFacilityId).submit();
    }
  });
</script>
@endsection

