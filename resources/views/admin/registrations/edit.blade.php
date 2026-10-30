@extends('admin.layouts.app')

@section('title', 'Edit Member: ' . $user->name . ' | IDFR Executive Portal')
@section('page_title', 'Edit Registered Account')

@section('styles')
<style>
  .edit-form-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.75rem;
    align-items: start;
  }
  @media (max-width: 992px) {
    .edit-form-grid {
      grid-template-columns: 1fr;
    }
  }

  .form-section-title {
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--gold);
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border-subtle);
  }

  .form-group {
    margin-bottom: 1.25rem;
  }

  .form-label {
    display: block;
    font-size: 0.82rem;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
    font-weight: 500;
  }

  .form-label .required {
    color: #f87171;
    margin-left: 2px;
  }

  .form-control {
    width: 100%;
    padding: 0.65rem 0.9rem;
    background: rgba(0, 0, 0, 0.4);
    border: 1px solid var(--border-subtle);
    border-radius: 6px;
    color: var(--text-primary);
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
  }

  .form-control:focus {
    border-color: var(--gold);
    box-shadow: 0 0 0 2px rgba(229, 184, 61, 0.2);
  }

  .form-help {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 0.35rem;
  }

  .error-message {
    color: #f87171;
    font-size: 0.75rem;
    margin-top: 0.35rem;
  }

  .privacy-card {
    background: rgba(59, 130, 246, 0.05);
    border: 1px solid rgba(59, 130, 246, 0.25);
    border-radius: 8px;
    padding: 1.25rem;
    font-size: 0.82rem;
    color: #93c5fd;
    line-height: 1.5;
  }
</style>
@endsection

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Edit Account: <em>{{ $user->name }}</em></h2>
    <p class="module-subtitle">Update member identity, role classification, email verification status, and account activity state.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('admin.registrations.show', $user) }}" class="btn-subtle" style="text-decoration: none;">
      &larr; Back to Member Details
    </a>
  </div>
</div>

<form action="{{ route('admin.registrations.update', $user) }}" method="POST">
  @csrf
  @method('PUT')

  <div class="edit-form-grid">
    <!-- Main Form Column -->
    <div class="admin-card">
      <div class="form-section-title">Account &amp; Identity Information</div>

      <div class="form-group">
        <label for="name" class="form-label">Full Name <span class="required">*</span></label>
        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required />
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required />
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
        <div class="form-help">Must be a valid, unique email address for this member.</div>
      </div>

      <div class="form-group">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+91 9876543210" />
        @error('phone')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-section-title" style="margin-top: 2rem;">Role &amp; Commercial Settings</div>

      <div class="form-group">
        <label for="role" class="form-label">Assigned Role <span class="required">*</span></label>
        <select id="role" name="role" class="form-control" required>
          @foreach($roles as $r)
            <option value="{{ $r->value }}" {{ old('role', $user->role) === $r->value ? 'selected' : '' }}>
              {{ $r->label() }}
            </option>
          @endforeach
        </select>
        @error('role')
          <div class="error-message">{{ $message }}</div>
        @enderror
        <div class="form-help">Changing role alters the associated profile schema and dashboard capabilities.</div>
      </div>

      <div class="form-group">
        <label for="payment_status" class="form-label">Commercial / Registration Payment Status <span class="required">*</span></label>
        <select id="payment_status" name="payment_status" class="form-control" required>
          <option value="exempt" {{ old('payment_status', $user->payment_status) === 'exempt' ? 'selected' : '' }}>Exempt (Default complimentary)</option>
          <option value="paid" {{ old('payment_status', $user->payment_status) === 'paid' ? 'selected' : '' }}>Paid</option>
          <option value="waived" {{ old('payment_status', $user->payment_status) === 'waived' ? 'selected' : '' }}>Waived / VIP</option>
          <option value="pending" {{ old('payment_status', $user->payment_status) === 'pending' ? 'selected' : '' }}>Pending Payment</option>
        </select>
        @error('payment_status')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-section-title" style="margin-top: 2rem;">Verification &amp; Security Controls</div>

      <div class="form-group">
        <label class="form-label" style="margin-bottom: 0.6rem;">Email Verification Status</label>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
          <label style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; color: #fff; cursor: pointer;">
            <input type="radio" name="email_verified" value="1" {{ old('email_verified', $user->isEmailVerified() ? '1' : '0') === '1' ? 'checked' : '' }} />
            <span style="color: #4ade80; font-weight: 600;">Verified</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; color: #fff; cursor: pointer;">
            <input type="radio" name="email_verified" value="0" {{ old('email_verified', $user->isEmailVerified() ? '1' : '0') === '0' ? 'checked' : '' }} />
            <span style="color: var(--gold); font-weight: 600;">Unverified (Require OTP)</span>
          </label>
        </div>
        <div class="form-help">Administrators can directly verify emails if the user confirmed through manual offline channels.</div>
      </div>

      <div class="form-group" style="margin-top: 1.5rem;">
        <label class="form-label" style="margin-bottom: 0.6rem;">Account State <span class="required">*</span></label>
        <div style="display: flex; gap: 1.5rem; align-items: center;">
          <label style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; color: #fff; cursor: pointer;">
            <input type="radio" name="is_active" value="1" {{ old('is_active', $user->isActive() ? '1' : '0') === '1' ? 'checked' : '' }} onchange="document.getElementById('deactivationReasonGroup').style.display = 'none';" />
            <span style="color: #4ade80; font-weight: 600;">Active</span>
          </label>
          <label style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.88rem; color: #fff; cursor: pointer;">
            <input type="radio" name="is_active" value="0" {{ old('is_active', $user->isActive() ? '1' : '0') === '0' ? 'checked' : '' }} onchange="document.getElementById('deactivationReasonGroup').style.display = 'block';" />
            <span style="color: #f87171; font-weight: 600;">Deactivated</span>
          </label>
        </div>
        @error('is_active')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group" id="deactivationReasonGroup" style="display: {{ old('is_active', $user->isActive() ? '1' : '0') === '0' ? 'block' : 'none' }}; margin-top: 1rem;">
        <label for="deactivation_reason" class="form-label">Deactivation Reason / Policy Note</label>
        <input type="text" id="deactivation_reason" name="deactivation_reason" class="form-control" value="{{ old('deactivation_reason', $user->deactivation_reason) }}" placeholder="e.g., Incomplete documentation or compliance review." />
        @error('deactivation_reason')
          <div class="error-message">{{ $message }}</div>
        @enderror
        <div class="form-help">This reason is logged in administrative telemetry and presented if the user attempts to sign in.</div>
      </div>

      <div style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-subtle);">
        <button type="submit" class="btn-subtle" style="background: var(--gold); color: #000; font-weight: 600; padding: 0.65rem 1.5rem; cursor: pointer;">
          Save Member Changes
        </button>
        <a href="{{ route('admin.registrations.show', $user) }}" class="btn-reset" style="text-decoration: none; padding: 0.65rem 1rem;">
          Cancel
        </a>
      </div>
    </div>

    <!-- Side Column: Privacy & Guidelines -->
    <div>
      <div class="privacy-card" style="margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 0.5rem; color: #fff; font-weight: 600;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          Sensitive Data Isolation
        </div>
        <p style="margin: 0; color: #bfdbfe;">
          This administrative view intentionally excludes password hashes, one-time passwords (OTP), and remember tokens. Administrative updates are authenticated and recorded in the audit trail.
        </p>
      </div>

      <div class="admin-card">
        <span class="form-section-title" style="display: block; margin-bottom: 0.75rem;">Account Guidelines</span>
        <ul style="font-size: 0.8rem; color: var(--text-secondary); line-height: 1.6; padding-left: 1.2rem; margin: 0;">
          <li>Deactivating an account immediately revokes login privileges across the IDFR portal.</li>
          <li>Marking an account as verified enables immediate authentication without requiring OTP verification.</li>
          <li>Role changes take effect upon the user's subsequent navigation or login.</li>
        </ul>
      </div>
    </div>
  </div>
</form>
@endsection
