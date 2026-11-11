@extends('admin.layouts.app')

@section('title', 'Offer Enquiry #' . $enquiry->id)
@section('page_title', 'Offer Enquiry Details')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'offers']) }}" class="btn-back">
      <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      <span>Back to Enquiries</span>
    </a>
    <h2 class="module-title">Package Lead: <em>{{ $enquiry->name }}</em></h2>
    <p class="module-subtitle">Received on {{ $enquiry->created_at->format('d M Y, h:i A') }}</p>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
  <!-- Enquiry Details -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Client &amp; Package Requested</h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
        <div>
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Client Full Name</label>
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
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Preferred Shoot Date</label>
          <div style="color: #fff; font-size: 0.95rem; font-weight: 500; margin-top: 0.25rem;">
            {{ $enquiry->preferred_date ? $enquiry->preferred_date->format('l, d F Y') : 'Flexible / To Be Discussed' }}
          </div>
        </div>

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Selected Photography Offer</label>
          <div style="margin-top: 0.25rem; display: flex; align-items: center; gap: 0.75rem;">
            <span class="badge" style="background: rgba(245, 200, 66, 0.15); color: var(--gold); border: 1px solid var(--gold-a35); font-size: 0.9rem; padding: 4px 10px;">
              {{ $enquiry->offer->title ?? $enquiry->offer_title ?? 'Package Offer' }}
            </span>
            @if($enquiry->offer)
              <a href="{{ route('asd.offers.show', $enquiry->offer->slug) }}" target="_blank" style="color: var(--text-light); font-size: 0.8rem; text-decoration: underline;">
                View package offer page ↗
              </a>
            @endif
          </div>
        </div>

        @if($enquiry->banner)
          <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
            <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Originating Hero Banner</label>
            <div style="margin-top: 0.25rem; display: flex; align-items: center; gap: 0.75rem;">
              <span class="badge" style="background: rgba(255, 255, 255, 0.08); color: #fff; font-size: 0.9rem; padding: 4px 10px;">
                Banner #{{ $enquiry->banner->order }}: {{ $enquiry->banner->title }}
              </span>
              <a href="{{ route('admin.asdreams.banners.edit', $enquiry->banner) }}" style="color: var(--gold); font-size: 0.8rem; text-decoration: underline;">
                Edit banner ↗
              </a>
            </div>
          </div>
        @endif

        <div style="grid-column: span 2; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
          <label style="color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase;">Enquiry Message &amp; Requirements</label>
          <div style="margin-top: 0.5rem; background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 4px; padding: 1.25rem; color: #eee; font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;">{{ $enquiry->message }}</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Status & Notes Sidebar -->
  <div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div class="admin-card">
      <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Update Status &amp; Notes</h3>

      <form action="{{ route('admin.asdreams.enquiries.update-offer-status', $enquiry) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
          <label class="form-label" for="status">Follow-up Status</label>
          <select id="status" name="status" class="form-control">
            <option value="new" {{ old('status', $enquiry->status) == 'new' ? 'selected' : '' }}>New (Uncontacted)</option>
            <option value="read" {{ old('status', $enquiry->status) == 'read' ? 'selected' : '' }}>Read</option>
            <option value="contacted" {{ old('status', $enquiry->status) == 'contacted' ? 'selected' : '' }}>Contacted / In Discussion</option>
            <option value="completed" {{ old('status', $enquiry->status) == 'completed' ? 'selected' : '' }}>Completed / Booked</option>
            <option value="archived" {{ old('status', $enquiry->status) == 'archived' ? 'selected' : '' }}>Archived / Closed</option>
          </select>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="admin_notes">Internal Studio Notes</label>
          <textarea id="admin_notes" name="admin_notes" rows="4" class="form-control" placeholder="Log call notes, agreed budget, photographer assignment...">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
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
          <a href="mailto:{{ $enquiry->email }}?subject={{ urlencode('Regarding your ASDreamStudio Enquiry: ' . ($enquiry->offer->title ?? 'Package')) }}" class="btn-subtle" style="justify-content: center;">
            ✉️ Email Client
          </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
