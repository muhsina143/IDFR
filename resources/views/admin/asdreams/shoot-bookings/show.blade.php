@extends('admin.layouts.app')

@section('title', 'Shoot Booking #' . $booking->id)
@section('page_title', 'Shoot Booking Details')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.shoot-bookings.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Shoot Bookings</span>
    </a>
    <h2 class="module-title">Booking: <em>{{ $booking->name }}</em></h2>
    <p class="module-subtitle">Received on {{ $booking->created_at->format('d M Y, h:i A') }}</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  <!-- Booking Info -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Client Details &amp; Shoot Schedule</h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Client Name</label>
          <div style="color: #fff; font-size: 1.05rem; font-weight: 600; margin-top: 0.25rem;">{{ $booking->name }}</div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Phone Number</label>
          <div style="margin-top: 0.25rem;">
            <a href="tel:{{ $booking->phone }}" style="color: var(--gold); font-size: 1.05rem; font-weight: 600;">{{ $booking->phone }}</a>
          </div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Email Address</label>
          <div style="margin-top: 0.25rem;">
            @if($booking->email)
              <a href="mailto:{{ $booking->email }}" style="color: #fff; font-size: 0.95rem;">{{ $booking->email }}</a>
            @else
              <span style="color: var(--text-muted);">Not provided</span>
            @endif
          </div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Requested Shoot Date</label>
          <div style="color: var(--gold); font-size: 1.05rem; font-weight: 600; margin-top: 0.25rem;">
            {{ $booking->shoot_date ? $booking->shoot_date->format('l, d F Y') : 'Flexible / TBD' }}
          </div>
        </div>

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Shoot Type / Purpose</label>
          <div style="color: #fff; font-size: 1rem; font-weight: 500; margin-top: 0.25rem;">
            {{ $booking->subject ?: 'Studio Shoot / Rental' }}
          </div>
        </div>

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Client Message / Details</label>
          <div style="margin-top: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 4px; padding: 1.25rem; color: #eee; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;">{{ $booking->message }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Status & Notes Sidebar -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Booking Status &amp; Crew Notes</h3>

      <form action="{{ route('admin.asdreams.shoot-bookings.update-status', $booking) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label class="form-label" for="status">Booking Status</label>
          <select id="status" name="status" class="form-control">
            <option value="new" {{ old('status', $booking->status) == 'new' ? 'selected' : '' }}>New (Uncontacted)</option>
            <option value="read" {{ old('status', $booking->status) == 'read' ? 'selected' : '' }}>Read</option>
            <option value="contacted" {{ old('status', $booking->status) == 'contacted' ? 'selected' : '' }}>Contacted / Quoting</option>
            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed &amp; Scheduled</option>
            <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Shoot Completed</option>
            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="admin_notes">Internal Crew &amp; Studio Notes</label>
          <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control" placeholder="Photographer assigned, studio slot booked, advance received...">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
        </div>

        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center; margin-top: 1.25rem;">
          Update Booking
        </button>
      </form>
    </div>

    <!-- Quick Actions Card -->
    <div class="admin-card">
      <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 1rem;">Direct Contact</h3>
      <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <a href="tel:{{ $booking->phone }}" class="btn-subtle" style="justify-content: center;">
          📞 Call {{ $booking->phone }}
        </a>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->phone) }}" target="_blank" class="btn-subtle" style="justify-content: center; color: #25D366; border-color: rgba(37,211,102,0.3);">
          💬 WhatsApp Client
        </a>
        @if($booking->email)
          <a href="mailto:{{ $booking->email }}?subject={{ urlencode('Your ASDreamStudio Shoot Booking on ' . ($booking->shoot_date ? $booking->shoot_date->format('d M Y') : 'Date')) }}" class="btn-subtle" style="justify-content: center;">
            ✉️ Email Client
          </a>
        @endif
      </div>
    </div>

    <!-- Delete Booking -->
    <div class="admin-card" style="border-color: rgba(234, 67, 53, 0.25);">
      <h3 style="font-size: 1.05rem; color: #ff8080; margin-bottom: 0.5rem;">Delete Booking</h3>
      <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Permanently remove this enquiry from the database.</p>
      <form action="{{ route('admin.asdreams.shoot-bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this shoot booking?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-subtle" style="width: 100%; justify-content: center; color: #ff8080; border-color: rgba(234, 67, 53, 0.4);">
          🗑️ Delete Booking
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

