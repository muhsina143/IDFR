@extends('admin.layouts.app')

@section('title', 'Team & Photographers Management')
@section('page_title', 'Studio Team')

@section('content')
@php
  $activeType = request('type');
  $tabs = [
    '' => ['label' => 'All', 'count' => $typeCounts['all'] ?? $team->total()],
    \App\Models\Asdreams\AsdreamsTeamMember::TYPE_FOUNDER => ['label' => 'Founders', 'count' => $typeCounts[\App\Models\Asdreams\AsdreamsTeamMember::TYPE_FOUNDER] ?? 0],
    \App\Models\Asdreams\AsdreamsTeamMember::TYPE_CO_FOUNDER => ['label' => 'Co-Founders', 'count' => $typeCounts[\App\Models\Asdreams\AsdreamsTeamMember::TYPE_CO_FOUNDER] ?? 0],
    \App\Models\Asdreams\AsdreamsTeamMember::TYPE_TEAM_MEMBER => ['label' => 'Team Members', 'count' => $typeCounts[\App\Models\Asdreams\AsdreamsTeamMember::TYPE_TEAM_MEMBER] ?? 0],
  ];
@endphp

<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Team &amp; Photographers</em></h2>
    <p class="module-subtitle">Manage Founders, Co-Founders, creative directors, photographers, cinematographers, and stylists shown on the Team page.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.asdreams.team.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add Team Member</span>
    </a>
  </div>
</div>

<!-- Type Filter Tabs -->
<div class="category-tabs-bar" role="tablist" aria-label="Filter team members by type">
  @foreach($tabs as $tabValue => $tabMeta)
    @php
      $tabQuery = array_filter(array_merge(
        request()->except('type', 'page'),
        $tabValue !== '' ? ['type' => $tabValue] : []
      ), fn ($v) => $v !== null && $v !== '');
      $isTabActive = $tabValue === '' ? empty($activeType) : $activeType === $tabValue;
    @endphp
    <a href="{{ route('admin.asdreams.team.index', $tabQuery) }}"
       class="category-tab {{ $isTabActive ? 'is-active' : '' }}">
      {{ $tabMeta['label'] }} ({{ $tabMeta['count'] }})
    </a>
  @endforeach
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.asdreams.team.index') }}" class="filter-form">
    @if($activeType)
      <input type="hidden" name="type" value="{{ $activeType }}" />
    @endif

    <div class="filter-group filter-group--search" style="flex: 2;">
      <svg class="search-icon" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, designation, or bio..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active Only</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Hidden Only</option>
      </select>
    </div>

    <div class="filter-actions">
      <button type="submit" class="btn-subtle">Filter</button>
      @if(request()->hasAny(['search', 'status', 'type']))
        <a href="{{ route('admin.asdreams.team.index') }}" class="btn-reset">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Team Table -->
<div class="admin-card">
  <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap;">
    <span style="font-size: 0.78rem; color: var(--text-muted);">
      Drag rows using the handle or edit numeric Display Order to arrange cards.
    </span>
    <span id="reorderStatus" style="display: none; font-size: 0.78rem; color: #4ade80; font-weight: 600;"></span>
  </div>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 85px;">Order</th>
          <th style="width: 80px;">Portrait</th>
          <th>Full Name &amp; Bio</th>
          <th style="width: 140px;">Type</th>
          <th>Designation</th>
          <th>Socials</th>
          <th style="width: 110px;">Status</th>
          <th style="text-align: right; width: 130px;">Actions</th>
        </tr>
      </thead>
      <tbody id="teamTableBody"
             data-reorder-url="{{ route('admin.asdreams.team.reorder') }}"
             data-offset="{{ ($team->currentPage() - 1) * $team->perPage() }}">
        @forelse($team as $member)
          @php
            $typeBadgeStyle = match ($member->member_type) {
              \App\Models\Asdreams\AsdreamsTeamMember::TYPE_FOUNDER => 'background: var(--gold); color: #050505; border: 1px solid var(--gold); font-weight: 700;',
              \App\Models\Asdreams\AsdreamsTeamMember::TYPE_CO_FOUNDER => 'background: rgba(229, 184, 61, 0.14); color: var(--gold-light); border: 1px solid rgba(229, 184, 61, 0.5); font-weight: 600;',
              default => 'background: rgba(163, 163, 163, 0.12); color: #d4d4d4; border: 1px solid rgba(163, 163, 163, 0.3); font-weight: 500;',
            };
          @endphp
          <tr data-member-id="{{ $member->id }}" draggable="true" style="cursor: grab;">
            <td>
              <div style="display: inline-flex; align-items: center; gap: 0.45rem;">
                <svg class="drag-handle" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-muted); flex-shrink: 0;" title="Drag to reorder">
                  <line x1="8" y1="6" x2="16" y2="6"></line>
                  <line x1="8" y1="12" x2="16" y2="12"></line>
                  <line x1="8" y1="18" x2="16" y2="18"></line>
                </svg>
                <span class="js-order-label" style="color: var(--gold); font-weight: 700;">#{{ $member->display_order }}</span>
              </div>
            </td>
            <td>
              <div style="width: 55px; height: 70px; border-radius: 4px; overflow: hidden; background: #000; border: 1px solid var(--border-subtle);">
                <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover;" />
              </div>
            </td>
            <td>
              <strong style="color: #fff; font-size: 0.95rem; display: block;">{{ $member->name }}</strong>
              @if($member->bio)
                <span style="color: var(--text-muted); font-size: 0.78rem; display: block; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                  {{ $member->bio }}
                </span>
              @endif
            </td>
            <td>
              <span class="status-badge" style="{{ $typeBadgeStyle }}">
                {{ $member->member_type_label }}
              </span>
            </td>
            <td>
              @if($member->designation)
                <span class="badge-tag badge-tag--gold">{{ $member->designation }}</span>
              @else
                <span style="color: var(--text-muted); font-size: 0.8rem;">—</span>
              @endif
            </td>
            <td>
              <div style="display: flex; gap: 0.5rem; font-size: 0.78rem; color: var(--text-muted);">
                @if($member->instagram)
                  <a href="{{ $member->instagram }}" target="_blank" rel="noopener" style="color: var(--gold);" title="Instagram">IG</a>
                @endif
                @if($member->linkedin)
                  <a href="{{ $member->linkedin }}" target="_blank" rel="noopener" style="color: #8bb4e7;" title="LinkedIn">IN</a>
                @endif
                @if(! $member->instagram && ! $member->linkedin)
                  <span>—</span>
                @endif
              </div>
            </td>
            <td>
              <form action="{{ route('admin.asdreams.team.toggle-status', $member) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;" title="Click to toggle visibility">
                  @if($member->is_active)
                    <span class="status-badge status-badge--active">Active</span>
                  @else
                    <span class="status-badge status-badge--archived">Hidden</span>
                  @endif
                </button>
              </form>
            </td>
            <td style="text-align: right;">
              <div class="table-actions" style="display: inline-flex; gap: 0.45rem; justify-content: flex-end;">
                <a href="{{ route('admin.asdreams.team.edit', $member) }}" class="btn-action" title="Edit Member">
                  <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                </a>
                <button type="button" class="btn-action btn-action--delete" onclick="confirmTeamDelete('{{ $member->id }}', '{{ addslashes($member->name) }}')" title="Delete Member">
                  <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
                <form id="deleteTeamForm-{{ $member->id }}" action="{{ route('admin.asdreams.team.destroy', $member) }}" method="POST" style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
              <p style="margin-bottom: 1rem;">No team members found for this filter.</p>
              <a href="{{ route('admin.asdreams.team.create') }}" class="btn-gold" style="display: inline-flex;">Add Team Member</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($team->hasPages())
    <div style="margin-top: 1.5rem;">
      {{ $team->links() }}
    </div>
  @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="admin-modal" id="deleteTeamModal" style="display: none;">
  <div class="admin-modal__backdrop" onclick="closeTeamDeleteModal()"></div>
  <div class="admin-modal__panel" style="max-width: 440px;">
    <h3 style="color: #fff; font-family: var(--font-serif); font-size: 1.35rem; margin-bottom: 0.75rem;">Confirm Delete</h3>
    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
      Are you sure you want to remove <strong id="deleteTeamName" style="color: var(--gold);"></strong> from the studio team?
    </p>
    <div style="display: flex; gap: 1rem; justify-content: flex-end;">
      <button type="button" class="btn-subtle" onclick="closeTeamDeleteModal()">Cancel</button>
      <button type="button" class="btn-danger" id="confirmTeamDeleteBtn">Delete Member</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  let targetTeamDeleteId = null;

  function confirmTeamDelete(id, name) {
    targetTeamDeleteId = id;
    document.getElementById('deleteTeamName').textContent = name;
    document.getElementById('deleteTeamModal').style.display = 'flex';
  }

  function closeTeamDeleteModal() {
    document.getElementById('deleteTeamModal').style.display = 'none';
    targetTeamDeleteId = null;
  }

  document.getElementById('confirmTeamDeleteBtn')?.addEventListener('click', function () {
    if (targetTeamDeleteId) {
      document.getElementById('deleteTeamForm-' + targetTeamDeleteId)?.submit();
    }
  });

  (function () {
    const tbody = document.getElementById('teamTableBody');
    const statusEl = document.getElementById('reorderStatus');
    if (!tbody) return;

    const reorderUrl = tbody.dataset.reorderUrl;
    const offset = parseInt(tbody.dataset.offset || '0', 10);
    let draggingRow = null;

    tbody.addEventListener('dragstart', function (e) {
      const row = e.target.closest('tr[data-member-id]');
      if (!row || e.target.closest('a, button, form, input, select')) {
        e.preventDefault();
        return;
      }
      draggingRow = row;
      row.style.opacity = '0.45';
      e.dataTransfer.effectAllowed = 'move';
      e.dataTransfer.setData('text/plain', row.dataset.memberId || '');
    });

    tbody.addEventListener('dragover', function (e) {
      if (!draggingRow) return;
      e.preventDefault();
      const targetRow = e.target.closest('tr[data-member-id]');
      if (!targetRow || targetRow === draggingRow) return;
      const rect = targetRow.getBoundingClientRect();
      const after = (e.clientY - rect.top) > rect.height / 2;
      tbody.insertBefore(draggingRow, after ? targetRow.nextSibling : targetRow);
    });

    tbody.addEventListener('dragend', async function () {
      if (!draggingRow) return;
      draggingRow.style.opacity = '1';
      draggingRow = null;

      const rows = Array.from(tbody.querySelectorAll('tr[data-member-id]'));
      if (rows.length < 2) return;

      const order = rows.map(function (r, idx) {
        const label = r.querySelector('.js-order-label');
        if (label) {
          label.textContent = '#' + (offset + idx + 1);
        }
        return parseInt(r.dataset.memberId, 10);
      });

      try {
        const res = await fetch(reorderUrl, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
          body: JSON.stringify({ order: order, offset: offset }),
        });

        if (res.ok && statusEl) {
          statusEl.textContent = 'Display order saved';
          statusEl.style.color = '#4ade80';
          statusEl.style.display = 'inline';
          setTimeout(function () { statusEl.style.display = 'none'; }, 2400);
        }
      } catch (err) {
        if (statusEl) {
          statusEl.textContent = 'Could not save order';
          statusEl.style.color = '#f87171';
          statusEl.style.display = 'inline';
        }
      }
    });
  })();
</script>
@endsection
