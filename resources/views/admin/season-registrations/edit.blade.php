@extends('admin.layouts.app')

@section('title', 'Edit Registration #' . $registration->registration_number . ' | IDFR Admin')
@section('header_title', 'Edit Model Registration')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.35rem;">
      <a href="{{ route('admin.season-registrations.show', $registration) }}" class="btn-subtle" style="padding: 0.35rem 0.65rem; font-size: 0.8rem;">
        &larr; Back to Details
      </a>
      <span style="font-family: monospace; font-size: 0.9rem; color: var(--gold); font-weight: 700;">
        #{{ $registration->registration_number }}
      </span>
    </div>
    <h2 class="module-title">Edit Registration: <em>{{ $registration->full_name }}</em></h2>
    <p class="module-subtitle">Update participant information, category, guardian details, and casting status.</p>
  </div>
</div>

@if($errors->any())
  <div class="alert alert--danger" style="padding: 1rem 1.25rem; background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; border-radius: 6px; color: #f87171; margin-bottom: 1.5rem;">
    <ul style="margin: 0; padding-left: 1.25rem;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="admin-card" style="padding: 2rem; max-width: 900px;">
  <form method="POST" action="{{ route('admin.season-registrations.update', $registration) }}">
    @csrf
    @method('PUT')

    <h3 style="font-family: var(--font-serif); font-size: 1.2rem; color: #fff; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem;">
      Participant Information
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Full Name <span style="color: #f87171;">*</span>
        </label>
        <input type="text" name="full_name" value="{{ old('full_name', $registration->full_name) }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          WhatsApp Number <span style="color: #f87171;">*</span>
        </label>
        <input type="tel" name="whatsapp_number" value="{{ old('whatsapp_number', $registration->whatsapp_number) }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Email Address
        </label>
        <input type="email" name="email" value="{{ old('email', $registration->email) }}" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Age <span style="color: #f87171;">*</span>
        </label>
        <input type="number" min="3" max="99" name="age" value="{{ old('age', $registration->age) }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Gender <span style="color: #f87171;">*</span>
        </label>
        <select name="gender" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
          <option value="female" {{ old('gender', $registration->gender) === 'female' ? 'selected' : '' }}>Female</option>
          <option value="male" {{ old('gender', $registration->gender) === 'male' ? 'selected' : '' }}>Male</option>
          <option value="other" {{ old('gender', $registration->gender) === 'other' ? 'selected' : '' }}>Other</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Category <span style="color: #f87171;">*</span>
        </label>
        <select name="category" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
          <option value="adult" {{ old('category', $registration->category) === 'adult' ? 'selected' : '' }}>Adult</option>
          <option value="kids" {{ old('category', $registration->category) === 'kids' ? 'selected' : '' }}>Kids / Minor</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Citizenship / Nationality <span style="color: #f87171;">*</span>
        </label>
        <input type="text" name="citizenship" value="{{ old('citizenship', $registration->citizenship) }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Runway Season <span style="color: #f87171;">*</span>
        </label>
        <select name="season_id" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
          @foreach($seasons as $s)
            <option value="{{ $s->id }}" {{ old('season_id', $registration->season_id) == $s->id ? 'selected' : '' }}>
              {{ $s->title }} (Season {{ $s->number }})
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <h3 style="font-family: var(--font-serif); font-size: 1.2rem; color: #fff; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem; margin-top: 2rem;">
      Parent / Legal Guardian Details (Minors &lt; 18)
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Guardian Name
        </label>
        <input type="text" name="parent_name" value="{{ old('parent_name', $registration->parent_name) }}" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Guardian Contact / WhatsApp
        </label>
        <input type="tel" name="parent_contact" value="{{ old('parent_contact', $registration->parent_contact) }}" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Relationship
        </label>
        <input type="text" name="parent_relationship" value="{{ old('parent_relationship', $registration->parent_relationship) }}" placeholder="e.g. Mother, Father, Guardian" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>
    </div>

    <h3 style="font-family: var(--font-serif); font-size: 1.2rem; color: #fff; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.5rem; margin-top: 2rem;">
      Status &amp; Financial Settings
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Registration Fee (INR) <span style="color: #f87171;">*</span>
        </label>
        <input type="number" step="0.01" min="0" name="registration_fee" value="{{ old('registration_fee', $registration->registration_fee) }}" required style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;" />
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Registration / Casting Status <span style="color: #f87171;">*</span>
        </label>
        <select name="registration_status" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
          <option value="pending" {{ old('registration_status', $registration->registration_status) === 'pending' ? 'selected' : '' }}>Pending Review</option>
          <option value="approved" {{ old('registration_status', $registration->registration_status) === 'approved' ? 'selected' : '' }}>Approved / Casting Confirmed</option>
          <option value="rejected" {{ old('registration_status', $registration->registration_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
          <option value="cancelled" {{ old('registration_status', $registration->registration_status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
      </div>

      <div>
        <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
          Payment Status <span style="color: #f87171;">*</span>
        </label>
        <select name="payment_status" required style="width: 100%; padding: 0.65rem 0.85rem; background: #141416; border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">
          <option value="unpaid" {{ old('payment_status', $registration->payment_status) === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
          <option value="pending" {{ old('payment_status', $registration->payment_status) === 'pending' ? 'selected' : '' }}>Pending / Verification</option>
          <option value="paid" {{ old('payment_status', $registration->payment_status) === 'paid' ? 'selected' : '' }}>Paid in Full</option>
          <option value="failed" {{ old('payment_status', $registration->payment_status) === 'failed' ? 'selected' : '' }}>Failed</option>
          <option value="refunded" {{ old('payment_status', $registration->payment_status) === 'refunded' ? 'selected' : '' }}>Refunded</option>
        </select>
      </div>
    </div>

    <div style="margin-bottom: 1.25rem;">
      <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600; margin-bottom: 0.35rem;">
        Additional Info / Portfolio Link
      </label>
      <textarea name="additional_info" rows="3" style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">{{ old('additional_info', $registration->additional_info) }}</textarea>
    </div>

    <div style="margin-bottom: 2rem;">
      <label style="display: block; font-size: 0.8rem; text-transform: uppercase; color: var(--gold); font-weight: 600; margin-bottom: 0.35rem;">
        Admin Internal Notes
      </label>
      <textarea name="admin_notes" rows="3" placeholder="Private internal notes regarding casting, audition status, measurements, or follow-ups." style="width: 100%; padding: 0.65rem 0.85rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-subtle); border-radius: 6px; color: #fff;">{{ old('admin_notes', $registration->admin_notes) }}</textarea>
    </div>

    <div style="display: flex; gap: 1rem; align-items: center;">
      <button type="submit" class="btn-gold" style="padding: 0.75rem 1.75rem;">
        Update Registration
      </button>
      <a href="{{ route('admin.season-registrations.show', $registration) }}" class="btn-subtle" style="padding: 0.75rem 1.25rem;">
        Cancel
      </a>
    </div>
  </form>
</div>
@endsection

