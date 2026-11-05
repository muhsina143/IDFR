@extends('admin.layouts.app')

@section('title', 'Registration Details #' . $registration->registration_number . ' | IDFR Admin')
@section('header_title', 'Model Registration Details')

@section('styles')
<style>
  .reg-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  .reg-badge--paid { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .reg-badge--unpaid { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
  .reg-badge--pending { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
  .reg-badge--approved { background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); }
  .reg-badge--rejected { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
  .detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
  }
  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  .detail-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-muted, #94a3b8);
    font-weight: 600;
  }
  .detail-value {
    font-size: 1.05rem;
    color: #ffffff;
    font-weight: 500;
  }
  .pay-summary-box {
    background: linear-gradient(135deg, rgba(229, 184, 61, 0.12) 0%, rgba(20, 20, 24, 0.95) 100%);
    border: 1px solid var(--border-gold, #d4af37);
    border-radius: 10px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
      <a href="{{ route('admin.season-registrations.index') }}" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;">
        &larr; Back to Registrations
      </a>
      <span style="font-family: monospace; font-size: 0.9rem; color: var(--gold); font-weight: 700;">
        #{{ $registration->registration_number }}
      </span>
    </div>
    <h2 class="module-title">{{ $registration->full_name }} <em>({{ $registration->season ? $registration->season->title : 'Season '.$registration->season_id }})</em></h2>
    <p class="module-subtitle">Application submitted on {{ $registration->created_at->format('d M Y, h:i A') }}</p>
  </div>
  <div class="module-header__actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <a href="{{ route('admin.season-registrations.edit', $registration) }}" class="btn-gold" style="display: inline-flex; align-items: center; gap: 0.5rem;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
      <span>Edit Details</span>
    </a>
    <form method="POST" action="{{ route('admin.season-registrations.destroy', $registration) }}" onsubmit="return confirm('Are you sure you want to permanently delete this registration?');" style="display: inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-subtle" style="color: #f87171; border-color: rgba(239,68,68,0.3); padding: 0.65rem 1rem;">
        Delete Record
      </button>
    </form>
  </div>
</div>

@if(session('success'))
  <div class="alert alert--success" style="padding: 1rem 1.25rem; background: rgba(34, 197, 94, 0.15); border: 1px solid #22c55e; border-radius: 6px; color: #4ade80; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
    <span>{{ session('success') }}</span>
  </div>
@endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start; margin-bottom: 2rem;">
  
  <!-- Left Column: Participant Information -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    
    <!-- Personal Profile Card -->
    <div class="admin-card" style="padding: 1.5rem;">
      <h3 style="font-family: var(--font-serif); font-size: 1.25rem; color: #fff; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem;">
        Participant Profile
      </h3>

      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label">Full Name</span>
          <span class="detail-value">{{ $registration->full_name }}</span>
        </div>

        <div class="detail-item">
          <span class="detail-label">WhatsApp Number</span>
          <span class="detail-value">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $registration->whatsapp_number) }}" target="_blank" rel="noopener" style="color: #4ade80; text-decoration: none;">
              {{ $registration->whatsapp_number }}
            </a>
          </span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Email Address</span>
          <span class="detail-value">{{ $registration->email ?: 'Not provided' }}</span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Age &amp; Gender</span>
          <span class="detail-value">{{ $registration->age }} Years · {{ ucfirst($registration->gender) }}</span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Registration Category</span>
          <span class="detail-value">
            @if($registration->category === 'kids')
              <span class="reg-badge" style="background: rgba(168, 85, 247, 0.15); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.3);">Kids / Minor (&lt;18)</span>
            @else
              <span class="reg-badge" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">Adult (18+)</span>
            @endif
          </span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Citizenship / Nationality</span>
          <span class="detail-value">{{ $registration->citizenship }}</span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Runway Season</span>
          <span class="detail-value" style="color: var(--gold);">
            {{ $registration->season ? $registration->season->title : 'Season '.$registration->season_id }}
            @if($registration->season && $registration->season->location)
              ({{ $registration->season->location }})
            @endif
          </span>
        </div>

        <div class="detail-item">
          <span class="detail-label">Registration Number</span>
          <span class="detail-value" style="font-family: monospace;">{{ $registration->registration_number }}</span>
        </div>
      </div>

      @if($registration->additional_info)
        <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
          <span class="detail-label">Additional Info / Portfolio</span>
          <p style="color: #e2e8f0; margin-top: 0.35rem; line-height: 1.5; white-space: pre-wrap;">{{ $registration->additional_info }}</p>
        </div>
      @endif

      @if($registration->admin_notes)
        <div style="margin-top: 1rem; padding: 0.85rem 1rem; background: rgba(229, 184, 61, 0.08); border-left: 3px solid var(--gold); border-radius: 4px;">
          <span class="detail-label" style="color: var(--gold);">Admin Internal Notes</span>
          <p style="color: #cbd5e1; margin-top: 0.25rem; font-size: 0.9rem; line-height: 1.45; white-space: pre-wrap;">{{ $registration->admin_notes }}</p>
        </div>
      @endif
    </div>

    <!-- Parent / Guardian Details Card (if applicable) -->
    @if($registration->isKid() || $registration->parent_name)
      <div class="admin-card" style="padding: 1.5rem; border-color: rgba(168, 85, 247, 0.4);">
        <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #c084fc; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
          <span>Parent / Legal Guardian Details (Minor)</span>
        </h3>

        <div class="detail-grid">
          <div class="detail-item">
            <span class="detail-label">Guardian Name</span>
            <span class="detail-value">{{ $registration->parent_name ?: '—' }}</span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Guardian Contact</span>
            <span class="detail-value">
              @if($registration->parent_contact)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $registration->parent_contact) }}" target="_blank" rel="noopener" style="color: #4ade80; text-decoration: none;">
                  {{ $registration->parent_contact }}
                </a>
              @else
                —
              @endif
            </span>
          </div>

          <div class="detail-item">
            <span class="detail-label">Relationship to Minor</span>
            <span class="detail-value">{{ $registration->parent_relationship ?: 'Parent / Guardian' }}</span>
          </div>
        </div>
      </div>
    @endif

  </div>

  <!-- Right Column: Payment & Status Summary -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    
    <!-- Status & Balance Box -->
    <div class="pay-summary-box">
      <div style="display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--gold); font-weight: 700;">Financial Overview</span>
        @if($registration->payment_status === 'paid')
          <span class="reg-badge reg-badge--paid">Fully Paid</span>
        @elseif($registration->payment_status === 'unpaid')
          <span class="reg-badge reg-badge--unpaid">Unpaid</span>
        @else
          <span class="reg-badge reg-badge--pending">{{ ucfirst($registration->payment_status) }}</span>
        @endif
      </div>

      <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.75rem;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span style="color: var(--text-muted); font-size: 0.9rem;">Registration Fee:</span>
          <strong style="color: #fff; font-size: 1rem;">₹{{ number_format($registration->registration_fee, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
          <span style="color: var(--text-muted); font-size: 0.9rem;">Amount Paid:</span>
          <strong style="color: #4ade80; font-size: 1rem;">₹{{ number_format($registration->amount_paid, 2) }}</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding-top: 0.5rem; border-top: 1px dashed rgba(255,255,255,0.15);">
          <span style="color: var(--gold); font-weight: 600; font-size: 0.95rem;">Outstanding Balance:</span>
          <strong style="color: {{ $registration->remaining_balance > 0 ? '#f87171' : '#4ade80' }}; font-size: 1.15rem;">
            ₹{{ number_format($registration->remaining_balance, 2) }}
          </strong>
        </div>
      </div>

      <div style="border-top: 1px solid rgba(255,255,255,0.08); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.85rem; color: var(--text-muted);">Registration Status:</span>
        @if($registration->registration_status === 'approved')
          <span class="reg-badge reg-badge--approved">Approved</span>
        @elseif($registration->registration_status === 'pending')
          <span class="reg-badge reg-badge--pending">Pending</span>
        @elseif($registration->registration_status === 'rejected')
          <span class="reg-badge reg-badge--rejected">Rejected</span>
        @else
          <span class="reg-badge reg-badge--cancelled">{{ ucfirst($registration->registration_status) }}</span>
        @endif
      </div>
    </div>

    <!-- Record Manual Payment Card -->
    <div class="admin-card" style="padding: 1.5rem;">
      <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #fff; margin-bottom: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
        <span>Record Manual Payment</span>
      </h3>

      <form method="POST" action="{{ route('admin.season-registrations.payments.store', $registration) }}">
        @csrf

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
            Amount Received (INR) <span style="color: #f87171;">*</span>
          </label>
          <input type="number" step="0.01" min="1" max="1000000" name="amount" value="{{ old('amount', $registration->remaining_balance > 0 ? $registration->remaining_balance : $registration->registration_fee) }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff; font-size: 1rem;" />
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
            Payment Method <span style="color: #f87171;">*</span>
          </label>
          <select name="payment_method" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
            <option value="upi" selected>UPI (GPay / PhonePe / Paytm)</option>
            <option value="bank_transfer">Bank Transfer (NEFT/RTGS/IMPS)</option>
            <option value="cash">Cash Payment</option>
            <option value="card">Card (Debit / Credit)</option>
            <option value="cheque">Cheque</option>
            <option value="other">Other Method</option>
          </select>
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
            Transaction Reference / UTR
          </label>
          <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref / Bank UTR / Receipt #" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
            Payment Date <span style="color: #f87171;">*</span>
          </label>
          <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
        </div>

        <div style="margin-bottom: 1rem;">
          <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
            Status <span style="color: #f87171;">*</span>
          </label>
          <select name="payment_status" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
            <option value="paid" selected>Paid / Verified</option>
            <option value="pending">Pending Verification</option>
          </select>
        </div>

        <div style="margin-bottom: 1.25rem;">
          <label style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
            Payment Notes (Optional)
          </label>
          <textarea name="notes" rows="2" placeholder="e.g. Paid in cash at casting office / Received via account transfer" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;"></textarea>
        </div>

        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center;">
          Record Payment
        </button>
      </form>
    </div>

  </div>
</div>

<!-- Bottom Section: Recorded Payments History Table -->
<div class="admin-card">
  <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center;">
    <div>
      <h3 style="margin: 0; font-size: 1.15rem; color: #fff; font-family: var(--font-serif);">Payment History</h3>
      <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--text-muted);">Audit log of manual payments recorded against this registration record.</p>
    </div>
    <span class="reg-badge" style="background: rgba(255,255,255,0.05); color: #fff;">
      {{ $registration->payments->count() }} Transaction(s)
    </span>
  </div>

  @if($registration->payments->isEmpty())
    <div style="padding: 2.5rem 1.5rem; text-align: center; color: var(--text-muted);">
      <p style="margin: 0;">No payments have been recorded yet for this participant.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Payment Ref</th>
            <th>Date</th>
            <th>Method</th>
            <th>Transaction Ref</th>
            <th>Amount (INR)</th>
            <th>Status</th>
            <th>Recorded By</th>
            <th>Notes</th>
            <th style="text-align: right;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($registration->payments as $pay)
            <tr>
              <td>
                <span style="font-family: monospace; font-weight: 700; color: var(--gold);">
                  {{ $pay->payment_reference }}
                </span>
              </td>
              <td style="white-space: nowrap;">{{ $pay->payment_date ? $pay->payment_date->format('d M Y') : $pay->created_at->format('d M Y') }}</td>
              <td><span style="text-transform: uppercase; font-size: 0.78rem; font-weight: 600;">{{ str_replace('_', ' ', $pay->payment_method) }}</span></td>
              <td style="font-family: monospace; font-size: 0.85rem;">{{ $pay->transaction_reference ?: '—' }}</td>
              <td style="font-weight: 700; color: #4ade80;">₹{{ number_format($pay->amount, 2) }}</td>
              <td>
                @if($pay->payment_status === 'paid')
                  <span class="reg-badge reg-badge--paid">Paid</span>
                @else
                  <span class="reg-badge reg-badge--pending">{{ ucfirst($pay->payment_status) }}</span>
                @endif
              </td>
              <td style="font-size: 0.82rem; color: var(--text-muted);">
                {{ $pay->recordedBy ? $pay->recordedBy->name : 'System / Admin' }}
              </td>
              <td style="font-size: 0.82rem; color: #cbd5e1; max-width: 200px;">
                {{ $pay->notes ?: '—' }}
              </td>
              <td style="text-align: right;">
                <form method="POST" action="{{ route('admin.season-payments.destroy', $pay) }}" onsubmit="return confirm('Remove payment record {{ $pay->payment_reference }} (₹{{ number_format($pay->amount, 0) }}) and recalculate balance?');" style="display: inline;">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-subtle" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; color: #f87171;">
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection

