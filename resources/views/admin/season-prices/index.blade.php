@extends('admin.layouts.app')

@section('title', 'Season Registration Prices | IDFR Admin')
@section('header_title', 'Season Registration Prices')

@section('styles')
<style>
  .price-card-hero {
    background: linear-gradient(135deg, rgba(229, 184, 61, 0.1) 0%, rgba(17, 17, 17, 0.6) 100%);
    border: 1px solid var(--border-gold, #d4af37);
    border-radius: 12px;
    padding: 1.75rem 2rem;
    margin-bottom: 2rem;
  }
  .active-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .active-pill--on { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .active-pill--off { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
      <a href="{{ route('admin.season-registrations.index') }}" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;">
        &larr; Model Registrations
      </a>
    </div>
    <h2 class="module-title">Season Registration <em>Pricing</em></h2>
    <p class="module-subtitle">Configure model participation fees per runway season. Dynamic pricing automatically updates the registration page (/tickets) and applicant records.</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert--success" style="padding: 1rem 1.25rem; background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 6px; color: #4ade80; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span>{{ session('success') }}</span>
  </div>
@endif

@if($errors->any())
  <div class="alert alert--danger" style="padding: 1rem 1.25rem; background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; border-radius: 6px; color: #f87171; margin-bottom: 1.5rem;">
    <ul style="margin: 0; padding-left: 1.25rem;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start; margin-bottom: 2rem;">
  
  <!-- Left Column: Configured Prices Table -->
  <div class="admin-card">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h3 style="margin: 0; font-size: 1.15rem; color: #fff; font-family: var(--font-serif);">Runway Seasons &amp; Fees</h3>
        <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--text-muted);">Active registration fee configured per season (Default fallback: ₹9,999).</p>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Season</th>
            <th>Location &amp; Dates</th>
            <th>Configured Fee</th>
            <th>Currency</th>
            <th>Status</th>
            <th>Notes</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($seasons as $s)
            @php $cfg = $s->registrationPrice; @endphp
            <tr>
              <td>
                <div style="font-weight: 700; color: #fff;">{{ $s->title }}</div>
                <div style="font-size: 0.78rem; color: var(--gold); font-family: monospace;">Season #{{ $s->number }}</div>
              </td>
              <td>
                <div style="color: #e2e8f0; font-size: 0.88rem;">{{ $s->location ?: '—' }}</div>
                <div style="font-size: 0.78rem; color: var(--text-muted);">{{ $s->dates ?: 'Dates TBA' }}</div>
              </td>
              <td>
                <span style="font-size: 1.15rem; font-weight: 700; color: var(--gold);">
                  {{ $s->formattedRegistrationFee() }}
                </span>
                @if(!$cfg)
                  <div style="font-size: 0.72rem; color: var(--text-muted);">(System Default)</div>
                @endif
              </td>
              <td style="font-family: monospace; font-size: 0.85rem; color: #cbd5e1;">
                {{ $cfg ? $cfg->currency : 'INR' }}
              </td>
              <td>
                @if(!$cfg || $cfg->is_active)
                  <span class="active-pill active-pill--on">Active</span>
                @else
                  <span class="active-pill active-pill--off">Inactive</span>
                @endif
              </td>
              <td style="font-size: 0.82rem; color: var(--text-muted); max-width: 160px;">
                {{ $cfg?->notes ?: '—' }}
              </td>
              <td style="text-align: right; white-space: nowrap;">
                @if($cfg)
                  <button type="button" class="btn-subtle" onclick="editPriceModal({{ $cfg->id }}, {{ $s->id }}, '{{ addslashes($s->title) }}', {{ $cfg->price }}, '{{ $cfg->currency }}', {{ $cfg->is_active ? 'true' : 'false' }}, '{{ addslashes($cfg->notes ?? '') }}')" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
                    Edit
                  </button>
                  <form method="POST" action="{{ route('admin.season-prices.destroy', $cfg) }}" onsubmit="return confirm('Reset fee for {{ addslashes($s->title) }} to system default (₹9,999)?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #f87171;">
                      Reset
                    </button>
                  </form>
                @else
                  <button type="button" class="btn-gold" onclick="setSeasonPrice({{ $s->id }}, '{{ addslashes($s->title) }}')" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                    Set Fee
                  </button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right Column: Add / Configure Price Card -->
  <div class="admin-card" style="padding: 1.5rem;" id="priceFormCard">
    <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #fff; margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;" id="formCardTitle">
      Set Season Fee
    </h3>

    <form method="POST" action="{{ route('admin.season-prices.store') }}" id="priceForm">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Select Runway Season <span style="color: #f87171;">*</span>
        </label>
        <select name="season_id" id="formSeasonId" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
          @foreach($seasons as $s)
            <option value="{{ $s->id }}">{{ $s->title }} (Season {{ $s->number }})</option>
          @endforeach
        </select>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Registration Price (INR) <span style="color: #f87171;">*</span>
        </label>
        <input type="number" step="0.01" min="0" max="1000000" name="price" id="formPrice" value="9999.00" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff; font-size: 1.05rem;" />
        <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">e.g. 9999 for ₹9,999 registration fee</span>
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Currency
        </label>
        <input type="text" name="currency" id="formCurrency" value="INR" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div style="margin-bottom: 1rem;">
        <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Internal Price Notes (Optional)
        </label>
        <input type="text" name="notes" id="formNotes" placeholder="e.g. Standard Model entry fee Season 8" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
        <input type="checkbox" name="is_active" id="formIsActive" value="1" checked style="width: 16px; height: 16px; accent-color: var(--gold);" />
        <label for="formIsActive" style="font-size: 0.85rem; color: #fff; cursor: pointer;">
          Active Fee (Display on /tickets page)
        </label>
      </div>

      <button type="submit" class="btn-gold" id="btnSubmitPrice" style="width: 100%; justify-content: center;">
        Save Registration Fee
      </button>

      <button type="button" class="btn-subtle" id="btnCancelEdit" onclick="resetFormToCreate()" style="width: 100%; justify-content: center; margin-top: 0.5rem; display: none;">
        Cancel Edit
      </button>
    </form>
  </div>

</div>

<script>
function setSeasonPrice(seasonId, seasonTitle) {
  resetFormToCreate();
  const select = document.getElementById('formSeasonId');
  if (select) select.value = seasonId;
  document.getElementById('formPrice').focus();
}

function editPriceModal(priceId, seasonId, seasonTitle, price, currency, isActive, notes) {
  document.getElementById('formCardTitle').textContent = 'Edit Fee: ' + seasonTitle;
  const form = document.getElementById('priceForm');
  form.action = '/admin/season-prices/' + priceId;
  document.getElementById('formMethod').value = 'PUT';
  document.getElementById('formSeasonId').value = seasonId;
  document.getElementById('formPrice').value = parseFloat(price).toFixed(2);
  document.getElementById('formCurrency').value = currency || 'INR';
  document.getElementById('formIsActive').checked = Boolean(isActive);
  document.getElementById('formNotes').value = notes || '';
  document.getElementById('btnSubmitPrice').textContent = 'Update Registration Fee';
  document.getElementById('btnCancelEdit').style.display = 'inline-flex';
  document.getElementById('priceFormCard').scrollIntoView({ behavior: 'smooth' });
}

function resetFormToCreate() {
  document.getElementById('formCardTitle').textContent = 'Set Season Fee';
  const form = document.getElementById('priceForm');
  form.action = "{{ route('admin.season-prices.store') }}";
  document.getElementById('formMethod').value = 'POST';
  document.getElementById('formPrice').value = '9999.00';
  document.getElementById('formCurrency').value = 'INR';
  document.getElementById('formIsActive').checked = true;
  document.getElementById('formNotes').value = '';
  document.getElementById('btnSubmitPrice').textContent = 'Save Registration Fee';
  document.getElementById('btnCancelEdit').style.display = 'none';
}
</script>
@endsection

