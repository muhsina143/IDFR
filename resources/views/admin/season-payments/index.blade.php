@extends('admin.layouts.app')

@section('title', 'Payment Management | IDFR Admin')
@section('header_title', 'Season Registration Payments')

@section('styles')
<style>
  .pay-badge {
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
  .pay-badge--paid { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .pay-badge--pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
  .pay-badge--failed { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
  .pay-badge--refunded { background: rgba(148, 163, 184, 0.15); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.3); }
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
    <h2 class="module-title">Payment <em>Management</em></h2>
    <p class="module-subtitle">Complete ledger of recorded manual payments, UPI/bank receipts, and transaction history for model registrations.</p>
  </div>
</div>

@if(session('success'))
  <div class="alert alert--success" style="padding: 1rem 1.25rem; background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 6px; color: #4ade80; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span>{{ session('success') }}</span>
  </div>
@endif

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
  <div class="admin-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(34,197,94,0.12); display: flex; align-items: center; justify-content: center; color: #4ade80;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
    </div>
    <div>
      <div style="font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2;">₹{{ number_format($totalCollected, 0) }}</div>
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Collected Revenue</div>
    </div>
  </div>

  <div class="admin-card" style="padding: 1.5rem; display: flex; align-items: center; gap: 1.25rem;">
    <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(229,184,61,0.12); display: flex; align-items: center; justify-content: center; color: var(--gold);">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
    </div>
    <div>
      <div style="font-size: 1.75rem; font-weight: 700; color: #fff; line-height: 1.2;">{{ number_format($totalPaymentsCount) }}</div>
      <div style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Payments Recorded</div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="admin-card filter-card" style="margin-bottom: 1.5rem;">
  <form method="GET" action="{{ route('admin.season-payments.index') }}" class="filter-form" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
    
    <div class="filter-group filter-group--search" style="flex: 1; min-width: 240px;">
      <svg class="search-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payment ref, UTR, model name, phone..." class="filter-input" />
    </div>

    <div class="filter-group">
      <select name="season_id" class="filter-select" onchange="this.form.submit()">
        <option value="">All Seasons</option>
        @foreach($seasons as $s)
          <option value="{{ $s->id }}" {{ request('season_id') == $s->id ? 'selected' : '' }}>{{ $s->title }}</option>
        @endforeach
      </select>
    </div>

    <div class="filter-group">
      <select name="payment_method" class="filter-select" onchange="this.form.submit()">
        <option value="">All Payment Methods</option>
        <option value="upi" {{ request('payment_method') === 'upi' ? 'selected' : '' }}>UPI</option>
        <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
        <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Card</option>
        <option value="cheque" {{ request('payment_method') === 'cheque' ? 'selected' : '' }}>Cheque</option>
        <option value="other" {{ request('payment_method') === 'other' ? 'selected' : '' }}>Other</option>
      </select>
    </div>

    <div class="filter-group">
      <select name="status" class="filter-select" onchange="this.form.submit()">
        <option value="">All Statuses</option>
        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
      </select>
    </div>

    <div class="filter-actions" style="display: flex; gap: 0.5rem;">
      <button type="submit" class="btn-subtle" style="padding: 0.5rem 1rem;">Filter</button>
      @if(request()->anyFilled(['search', 'season_id', 'payment_method', 'status']))
        <a href="{{ route('admin.season-payments.index') }}" class="btn-reset" style="padding: 0.5rem 0.85rem;">Reset</a>
      @endif
    </div>
  </form>
</div>

<!-- Payments Ledger Table -->
<div class="admin-card">
  @if($payments->isEmpty())
    <div class="empty-state" style="padding: 3.5rem 1.5rem; text-align: center;">
      <h3 style="color: #fff; font-family: var(--font-serif); margin-bottom: 0.5rem;">No Payments Found</h3>
      <p style="color: var(--text-muted); font-size: 0.9rem;">No manual payment entries match the criteria.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Payment Ref</th>
            <th>Model Participant</th>
            <th>Season</th>
            <th>Date</th>
            <th>Method</th>
            <th>Transaction Ref</th>
            <th>Amount (INR)</th>
            <th>Status</th>
            <th>Recorded By</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $pay)
            @php $reg = $pay->registration; @endphp
            <tr>
              <td>
                <span style="font-family: monospace; font-weight: 700; color: var(--gold);">
                  {{ $pay->payment_reference }}
                </span>
              </td>
              <td>
                @if($reg)
                  <a href="{{ route('admin.season-registrations.show', $reg) }}" style="font-weight: 600; color: #fff; text-decoration: none;">
                    {{ $reg->full_name }}
                  </a>
                  <div style="font-size: 0.78rem; font-family: monospace; color: var(--text-muted);">
                    #{{ $reg->registration_number }} · {{ $reg->whatsapp_number }}
                  </div>
                @else
                  <span style="color: var(--text-muted);">Deleted Participant</span>
                @endif
              </td>
              <td>
                <span style="color: #e2e8f0; font-size: 0.85rem;">
                  {{ $reg && $reg->season ? $reg->season->title : '—' }}
                </span>
              </td>
              <td style="white-space: nowrap;">
                {{ $pay->payment_date ? $pay->payment_date->format('d M Y') : $pay->created_at->format('d M Y') }}
              </td>
              <td>
                <span style="text-transform: uppercase; font-size: 0.75rem; font-weight: 600; color: #cbd5e1;">
                  {{ str_replace('_', ' ', $pay->payment_method) }}
                </span>
              </td>
              <td style="font-family: monospace; font-size: 0.82rem;">
                {{ $pay->transaction_reference ?: '—' }}
              </td>
              <td style="font-weight: 700; color: #4ade80;">
                ₹{{ number_format($pay->amount, 2) }}
              </td>
              <td>
                @if($pay->payment_status === 'paid')
                  <span class="pay-badge pay-badge--paid">Paid</span>
                @elseif($pay->payment_status === 'pending')
                  <span class="pay-badge pay-badge--pending">Pending</span>
                @else
                  <span class="pay-badge pay-badge--failed">{{ ucfirst($pay->payment_status) }}</span>
                @endif
              </td>
              <td style="font-size: 0.82rem; color: var(--text-muted);">
                {{ $pay->recordedBy ? $pay->recordedBy->name : 'System' }}
              </td>
              <td style="text-align: right; white-space: nowrap;">
                @if($reg)
                  <a href="{{ route('admin.season-registrations.show', $reg) }}" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.78rem;">
                    View
                  </a>
                @endif
                <form method="POST" action="{{ route('admin.season-payments.destroy', $pay) }}" onsubmit="return confirm('Delete payment record {{ $pay->payment_reference }} (₹{{ number_format($pay->amount, 0) }})?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.78rem; color: #f87171;">
                    Del
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($payments->hasPages())
      <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-subtle);">
        {{ $payments->links() }}
      </div>
    @endif
  @endif
</div>
@endsection

