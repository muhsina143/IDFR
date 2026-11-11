<!-- Facilities Section Heading & Description -->
<div class="admin-card" style="margin-bottom: 1.5rem;">
  <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; margin-bottom: 1.15rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
    <div>
      <h3 style="font-family: var(--font-serif); font-size: 1.1rem; color: #fff; margin: 0;">Facilities Section Header</h3>
      <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem;">Customize the heading and badge text shown on the Facilities Provided panel.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('admin.asdreams.studio-facilities.settings') }}" data-hub-form="facilities">
    @csrf
    <input type="hidden" name="from_hub" value="1" />
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; align-items: end;">
      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label" for="hubFacilitiesHeading">Section Heading <span style="color: var(--gold);">*</span></label>
        <input type="text" id="hubFacilitiesHeading" name="heading" value="{{ $facilitiesHeading }}" required maxlength="150" class="form-control" placeholder="Facilities Provided" />
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label class="form-label" for="hubFacilitiesDescription">Section Description</label>
        <input type="text" id="hubFacilitiesDescription" name="description" value="{{ $facilitiesDescription }}" maxlength="500" class="form-control" placeholder="All areas are air-conditioned." />
      </div>

      <div>
        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center;">Save Header</button>
      </div>
    </div>
  </form>
</div>

<!-- Toolbar + Filter + Add Button -->
<div class="admin-card filter-card" style="margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
  <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 240px; flex-wrap: wrap;">
    <input type="text" class="form-control" style="max-width: 300px;" placeholder="Filter facilities..." data-table-search="facilitiesTableBody" />
    <select class="form-control" style="max-width: 170px;" data-table-status="facilitiesTableBody">
      <option value="">All Statuses</option>
      <option value="active">Active Only</option>
      <option value="inactive">Inactive Only</option>
    </select>
  </div>

  <button type="button" class="btn-gold" data-open-facility-create data-next-order="{{ $nextFacilityOrder }}">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
    <span>Add Facility</span>
  </button>
</div>

<!-- Facilities List -->
<div class="admin-card">
  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 75px;">Order</th>
          <th style="width: 70px;">Icon</th>
          <th>Facility Name</th>
          <th style="width: 135px;">Status</th>
          <th style="width: 130px; text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody id="facilitiesTableBody">
        @forelse($facilities as $facility)
          <tr data-search="{{ strtolower($facility->name) }}" data-status="{{ $facility->is_active ? 'active' : 'inactive' }}">
            <td>
              <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--gold); font-weight: 700;">#{{ $facility->display_order }}</span>
            </td>
            <td>
              <div style="width: 36px; height: 36px; border-radius: 50%; border: 1px solid rgba(229, 184, 61, 0.35); display: flex; align-items: center; justify-content: center; color: var(--gold); background: rgba(229, 184, 61, 0.06);">
                <svg width="17" height="17" aria-hidden="true" style="stroke: currentColor; fill: none;">
                  <use href="#i-{{ $facility->icon ?: 'check' }}" />
                </svg>
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.94rem;">{{ $facility->name }}</strong>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.studio-facilities.toggle-status', $facility) }}" method="POST" data-hub-form="facilities">
                @csrf
                <input type="hidden" name="from_hub" value="1" />
                <button type="submit" class="status-toggle-btn {{ $facility->is_active ? 'is-active' : 'is-inactive' }}" title="Click to toggle visibility">
                  <span class="status-dot"></span>
                  <span>{{ $facility->is_active ? 'Active' : 'Inactive' }}</span>
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div style="display: inline-flex; gap: 0.5rem; justify-content: flex-end;">
                <button type="button"
                  class="btn-action btn-action--edit"
                  title="Edit Facility"
                  data-open-facility-edit
                  data-action="{{ route('admin.asdreams.studio-facilities.update', $facility) }}"
                  data-name="{{ $facility->name }}"
                  data-icon="{{ $facility->icon ?: 'check' }}"
                  data-order="{{ $facility->display_order }}"
                  data-active="{{ $facility->is_active ? '1' : '0' }}">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </button>
                <button type="button"
                  class="btn-action btn-action--delete"
                  title="Delete Facility"
                  data-hub-delete
                  data-tab="facilities"
                  data-action="{{ route('admin.asdreams.studio-facilities.destroy', $facility) }}"
                  data-label="facility &quot;{{ $facility->name }}&quot;">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
              No studio facilities configured yet. Click <strong>Add Facility</strong> above to add one.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

