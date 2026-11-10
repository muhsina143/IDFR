@extends('admin.layouts.app')

@section('title', 'Contact Message #' . $enquiry->id)
@section('page_title', 'Contact Message Details')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'contact']) }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Contact Messages</span>
    </a>
    <h2 class="module-title">Message from: <em>{{ $enquiry->name }}</em></h2>
    <p class="module-subtitle">Received on {{ $enquiry->created_at->format('d M Y, h:i A') }}</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  <!-- Message Content -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Sender Information &amp; Message</h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Full Name</label>
          <div style="color: #fff; font-size: 1.05rem; font-weight: 600; margin-top: 0.25rem;">{{ $enquiry->name }}</div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Phone Number</label>
          <div style="margin-top: 0.25rem;">
            <a href="tel:{{ $enquiry->phone }}" style="color: var(--gold); font-size: 1.05rem; font-weight: 600;">{{ $enquiry->phone }}</a>
          </div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Email Address</label>
          <div style="margin-top: 0.25rem;">
            @if($enquiry->email)
              <a href="mailto:{{ $enquiry->email }}" style="color: #fff; font-size: 0.95rem;">{{ $enquiry->email }}</a>
            @else
              <span style="color: var(--text-muted);">Not provided</span>
            @endif
          </div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Brand / Academy</label>
          <div style="color: var(--gold); font-size: 1.05rem; font-weight: 600; margin-top: 0.25rem;">
            @php
              $brandLabel = match($enquiry->brand) {
                'athuls', 'athuls-academy' => "Athul's Academy",
                'lakme', 'lakme-academy' => 'Lakmé Academy',
                default => 'ASDreamStudio',
              };
            @endphp
            {{ $brandLabel }}
          </div>
        </div>

        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Subject</label>
          <div style="color: #fff; font-size: 0.95rem; font-weight: 500; margin-top: 0.25rem;">
            {{ $enquiry->subject ?: 'General Enquiry' }}
          </div>
        </div>

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Message Content</label>
          <div style="margin-top: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 4px; padding: 1.25rem; color: #eee; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;">{{ $enquiry->message }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Status & Notes Sidebar -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Update Status &amp; Notes</h3>

      <form action="{{ route('admin.asdreams.enquiries.update-contact-status', $enquiry) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="form-group">
          <label class="form-label" for="status">Follow-up Status</label>
          <select id="status" name="status" class="form-control">
            <option value="new" {{ old('status', $enquiry->status) == 'new' ? 'selected' : '' }}>New (Unread)</option>
            <option value="contacted" {{ old('status', $enquiry->status) == 'contacted' ? 'selected' : '' }}>Contacted</option>
            <option value="completed" {{ old('status', $enquiry->status) == 'completed' ? 'selected' : '' }}>Resolved</option>
            <option value="archived" {{ old('status', $enquiry->status) == 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="admin_notes">Internal Studio Notes</label>
          <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control" placeholder="Internal notes...">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
        </div>

        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center; margin-top: 1.25rem;">
          Save Follow-up Notes
        </button>
      </form>
    </div>

    <!-- Quick Actions Card -->
    <div class="admin-card">
      <h3 style="font-size: 1.05rem; color: #fff; margin-bottom: 1rem;">Quick Contact</h3>
      <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <a href="tel:{{ $enquiry->phone }}" class="btn-subtle" style="justify-content: center;">
          📞 Call {{ $enquiry->phone }}
        </a>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $enquiry->phone) }}" target="_blank" class="btn-subtle" style="justify-content: center; color: #25D366; border-color: rgba(37,211,102,0.3);">
          💬 WhatsApp Client
        </a>
        @if($enquiry->email)
          <a href="mailto:{{ $enquiry->email }}?subject={{ urlencode('Regarding your ASDreamStudio Contact Message') }}" class="btn-subtle" style="justify-content: center;">
            ✉️ Email Client
          </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
