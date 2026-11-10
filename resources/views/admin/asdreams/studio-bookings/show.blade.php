@extends('admin.layouts.app')

@section('title', 'Studio Booking Enquiry #' . $booking->id)
@section('page_title', 'Studio Booking Enquiry Details')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.studio-bookings.index') }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Studio Booking Enquiries</span>
    </a>
    <h2 class="module-title">Studio Hire: <em>{{ $booking->name }}</em></h2>
    <p class="module-subtitle">Received on {{ $booking->created_at->format('d M Y, h:i A') }}</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  <!-- Booking Info -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Client Details &amp; Studio Rental Schedule</h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Client / Company Name</label>
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
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Number of Days</label>
          @php
            $start = $booking->start_date ?: $booking->shoot_date;
            $end = $booking->end_date ?: $start;
            $days = $booking->days ?: ($start && $end ? $start->diffInDays($end) + 1 : 1);
          @endphp
          <div style="color: var(--gold); font-size: 1.05rem; font-weight: 600; margin-top: 0.25rem;">
            {{ $days }} {{ \Illuminate\Support\Str::plural('day', $days) }}
          </div>
        </div>

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Purpose of Rental</label>
          <div style="margin-top: 0.35rem;">
            <span class="badge" style="background: rgba(245, 200, 66, 0.15); color: var(--gold); border: 1px solid var(--gold-a35); font-size: 1rem; padding: 0.35rem 0.8rem; font-weight: 600;">
              {{ $booking->purpose ?: ($booking->subject ? str_replace('Studio Rental: ', '', $booking->subject) : 'Studio Rental') }}
            </span>
          </div>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Booking Start Date</label>
          <div style="color: #fff; font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">
            {{ $start ? $start->format('l, d F Y') : 'Not specified' }}
          </div>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Booking End Date</label>
          <div style="color: #fff; font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">
            {{ $end ? $end->format('l, d F Y') : 'Not specified' }}
          </div>
        </div>

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Additional Requirements / Notes</label>
          <div style="margin-top: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 4px; padding: 1.25rem; color: #eee; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;">{{ $booking->message ?: 'No additional notes provided by client.' }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Status & Notes Sidebar -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Booking Status &amp; Crew Notes</h3>

      <form action="{{ route('admin.asdreams.studio-bookings.update-status', $booking) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label class="form-label" for="status">Booking Status</label>
          <select id="status" name="status" class="form-control">
            <option value="new" {{ old('status', $booking->status) == 'new' ? 'selected' : '' }}>New (Uncontacted)</option>
            <option value="contacted" {{ old('status', $booking->status) == 'contacted' ? 'selected' : '' }}>Contacted / Quoting</option>
            <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed &amp; Scheduled</option>
            <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Rental Completed</option>
            <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="admin_notes">Internal Crew &amp; Studio Notes</label>
          <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control" placeholder="Key pickup time, advance deposit received, lighting kit requested...">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
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
          <a href="mailto:{{ $booking->email }}?subject={{ urlencode('Your ASDreamStudio Rental Enquiry for ' . ($start ? $start->format('d M Y') : 'Date')) }}" class="btn-subtle" style="justify-content: center;">
            ✉️ Email Client
          </a>
        @endif
      </div>
    </div>

    <!-- Delete Booking -->
    <div class="admin-card" style="border-color: rgba(234, 67, 53, 0.25);">
      <h3 style="font-size: 1.05rem; color: #ff8080; margin-bottom: 0.5rem;">Delete Enquiry</h3>
      <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1rem;">Permanently remove this studio booking enquiry from the database.</p>
      <form action="{{ route('admin.asdreams.studio-bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this studio booking enquiry?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-subtle" style="width: 100%; justify-content: center; color: #ff8080; border-color: rgba(234, 67, 53, 0.4);">
          🗑️ Delete Enquiry
        </button>
      </form>
    </div>
  </div>
</div>
@endsection

