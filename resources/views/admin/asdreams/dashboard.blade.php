@extends('admin.layouts.app')

@section('title', 'ASDreamStudio Executive Dashboard')
@section('page_title', 'ASDreamStudio Dashboard')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">ASDreamStudio <em>Management</em></h2>
    <p class="module-subtitle">Manage photography &amp; videography hero banners, client brands, offers, enquiries, shoot bookings, team members, and the portfolio gallery.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('asd.home') }}" target="_blank" class="btn-subtle">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
      <span>Open Live Studio</span>
    </a>
    <a href="{{ route('admin.asdreams.banners.create') }}" class="btn-gold">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
      <span>Add New Banner</span>
    </a>
  </div>
</div>

<!-- Stats Overview Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Hero Banners</span>
      <span style="background: rgba(229, 184, 61, 0.1); color: var(--gold); padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $stats['banners_active'] }} Active</span>
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['banners_count'] }}</div>
    <a href="{{ route('admin.asdreams.banners.index') }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">Manage Banners &rarr;</a>
  </div>

  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Packages &amp; Offers</span>
      <span style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $stats['offers_published'] }} Published</span>
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['offers_count'] }}</div>
    <a href="{{ route('admin.asdreams.offers.index') }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">Manage Offers &rarr;</a>
  </div>

  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Offer Enquiries</span>
      @if($stats['offer_enquiries_new'] > 0)
        <span style="background: rgba(229, 184, 61, 0.2); color: var(--gold); padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $stats['offer_enquiries_new'] }} New</span>
      @endif
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['offer_enquiries_total'] }}</div>
    <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'offers']) }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">View Enquiries &rarr;</a>
  </div>

  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Shoot Bookings</span>
      @if($stats['shoot_bookings_new'] > 0)
        <span style="background: rgba(229, 184, 61, 0.2); color: var(--gold); padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $stats['shoot_bookings_new'] }} New</span>
      @endif
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['shoot_bookings_total'] }}</div>
    <a href="{{ route('admin.asdreams.shoot-bookings.index') }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">View Bookings &rarr;</a>
  </div>

  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Gallery Photos</span>
      <span style="color: var(--text-muted); font-size: 0.75rem;">{{ $stats['gallery_categories_count'] }} Categories</span>
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['gallery_images_count'] }}</div>
    <a href="{{ route('admin.asdreams.gallery.index') }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">Manage Gallery &rarr;</a>
  </div>

  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Our Clients</span>
      <span style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $stats['clients_active'] }} Active</span>
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['clients_count'] }}</div>
    <a href="{{ route('admin.asdreams.clients.index') }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">Manage Clients &rarr;</a>
  </div>

  <div class="admin-card" style="padding: 1.25rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
      <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Moving Client Logos</span>
      <span style="background: rgba(229, 184, 61, 0.1); color: var(--gold); padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 700;">{{ $stats['moving_logos_active'] }} Active</span>
    </div>
    <div style="font-size: 2rem; font-family: var(--font-serif); font-weight: 700; color: #fff;">{{ $stats['moving_logos_count'] }}</div>
    <a href="{{ route('admin.asdreams.moving-logos.index') }}" style="font-size: 0.8rem; color: var(--gold); text-decoration: none; margin-top: 0.5rem; display: inline-block;">Manage Moving Logos &rarr;</a>
  </div>
</div>

<!-- Recent Leads & Enquiries Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 1.5rem;">
  <!-- Recent Offer Enquiries -->
  <div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #fff; margin: 0;">Recent Offer Enquiries</h3>
      <a href="{{ route('admin.asdreams.enquiries.index', ['type' => 'offers']) }}" class="btn-subtle" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">View All</a>
    </div>

    @if($recentOfferEnquiries->isEmpty())
      <p style="color: var(--text-muted); font-size: 0.88rem; text-align: center; padding: 2rem 0;">No offer enquiries received yet.</p>
    @else
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Client</th>
              <th>Offer</th>
              <th>Status</th>
              <th style="text-align: right;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentOfferEnquiries as $enq)
              <tr>
                <td>
                  <strong style="color: #fff; display: block;">{{ $enq->name }}</strong>
                  <a href="tel:{{ $enq->phone }}" style="color: var(--gold); font-size: 0.78rem; text-decoration: none;">{{ $enq->phone }}</a>
                </td>
                <td>
                  <span style="font-size: 0.82rem; color: var(--text-secondary);">{{ $enq->offer_title ?? 'Offer' }}</span>
                </td>
                <td>
                  <span class="badge badge--{{ $enq->status === 'new' ? 'gold' : ($enq->status === 'completed' ? 'success' : 'subtle') }}">
                    {{ ucfirst($enq->status) }}
                  </span>
                </td>
                <td style="text-align: right;">
                  <a href="{{ route('admin.asdreams.enquiries.show-offer', $enq) }}" class="btn-table-action" title="View Details">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <!-- Recent Shoot Bookings -->
  <div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
      <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: #fff; margin: 0;">Recent Shoot Bookings</h3>
      <a href="{{ route('admin.asdreams.shoot-bookings.index') }}" class="btn-subtle" style="font-size: 0.75rem; padding: 0.35rem 0.65rem;">View All</a>
    </div>

    @if($recentShootBookings->isEmpty())
      <p style="color: var(--text-muted); font-size: 0.88rem; text-align: center; padding: 2rem 0;">No shoot bookings received yet.</p>
    @else
      <div class="table-responsive">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Client</th>
              <th>Date &amp; Type</th>
              <th>Status</th>
              <th style="text-align: right;">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentShootBookings as $booking)
              <tr>
                <td>
                  <strong style="color: #fff; display: block;">{{ $booking->name }}</strong>
                  <a href="tel:{{ $booking->phone }}" style="color: var(--gold); font-size: 0.78rem; text-decoration: none;">{{ $booking->phone }}</a>
                </td>
                <td>
                  <span style="font-size: 0.82rem; color: #fff; display: block;">{{ $booking->shoot_date ? $booking->shoot_date->format('M d, Y') : 'TBD' }}</span>
                  <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->shoot_type ?? 'Shoot' }}</span>
                </td>
                <td>
                  <span class="badge badge--{{ $booking->status === 'new' ? 'gold' : ($booking->status === 'confirmed' ? 'success' : 'subtle') }}">
                    {{ ucfirst($booking->status) }}
                  </span>
                </td>
                <td style="text-align: right;">
                  <a href="{{ route('admin.asdreams.shoot-bookings.show', $booking) }}" class="btn-table-action" title="View Details">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection

