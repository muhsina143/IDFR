@extends('admin.layouts.app')

@section('title', 'Contact Details & Studio Info')
@section('page_title', 'Contact Settings')

@section('content')
<div class="module-header">
  <div class="module-header__intro">
    <h2 class="module-title">Studio <em>Contact Settings</em></h2>
    <p class="module-subtitle">Manage studio address, Google Maps link, phone numbers, booking emails, and operating hours on the Contact page.</p>
  </div>
  <div class="module-header__actions">
    <a href="{{ route('asd.contact') }}" target="_blank" class="btn-subtle">
      <svg viewBox="0 0 24 24" width="14" height="14"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      <span>View Live Contact Page</span>
    </a>
  </div>
</div>

<form action="{{ route('admin.asdreams.contact.update') }}" method="POST">
  @csrf
  @method('PUT')

  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Form -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <!-- Header Copy -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Page Title &amp; Intro</h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="section_label">Section Label / Eyebrow</label>
            <input type="text" id="section_label" name="section_label" value="{{ old('section_label', $contact->section_label) }}" class="form-control" placeholder="GET IN TOUCH" />
            @error('section_label') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="title">Title Heading <span style="color: var(--gold);">*</span></label>
            <input type="text" id="title" name="title" value="{{ old('title', $contact->title) }}" required class="form-control" placeholder="Let’s create" />
            @error('title') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group">
            <label class="form-label" for="title_highlight">Highlight Italic Word</label>
            <input type="text" id="title_highlight" name="title_highlight" value="{{ old('title_highlight', $contact->title_highlight) }}" class="form-control" placeholder="together" />
            @error('title_highlight') <span class="form-error">{{ $message }}</span> @enderror
          </div>

          <div class="form-group" style="grid-column: span 2;">
            <label class="form-label" for="intro">Introductory Paragraph</label>
            <textarea id="intro" name="intro" rows="3" class="form-control">{{ old('intro', $contact->intro) }}</textarea>
            @error('intro') <span class="form-error">{{ $message }}</span> @enderror
          </div>
        </div>
      </div>

      <!-- Studio Location & Address -->
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Studio Physical Location</h3>

        <div class="form-group">
          <label class="form-label" for="studio_address">Studio Address <span style="color: var(--gold);">*</span></label>
          <textarea id="studio_address" name="studio_address" rows="3" required class="form-control">{{ old('studio_address', $contact->studio_address) }}</textarea>
          @error('studio_address') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="maps_url">Google Maps Directions URL</label>
          <input type="text" id="maps_url" name="maps_url" value="{{ old('maps_url', $contact->maps_url) }}" class="form-control" placeholder="https://maps.google.com/?q=..." />
          @error('maps_url') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <!-- Working Hours -->
      <div class="admin-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h3 style="font-size: 1.1rem; color: #fff; margin: 0;">Working &amp; Visiting Hours</h3>
          <button type="button" class="btn-subtle" id="btnAddHours" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
            + Add Schedule Line
          </button>
        </div>

        <div id="hoursList" style="display: flex; flex-direction: column; gap: 0.75rem;">
          @php
            $currentHours = old('hours_days')
              ? array_map(fn($d, $h) => ['days' => $d, 'hours' => $h], old('hours_days'), old('hours_time', []))
              : ($contact->working_hours ?: [
                  ['days' => 'Monday – Friday', 'hours' => '09:00 – 19:00'],
                  ['days' => 'Saturday', 'hours' => '10:00 – 18:00'],
                  ['days' => 'Sunday', 'hours' => 'By appointment only'],
                ]);
          @endphp
          @foreach($currentHours as $idx => $hr)
            <div class="hours-row" style="display: grid; grid-template-columns: 1fr 1fr 38px; gap: 0.75rem; align-items: center;">
              <input type="text" name="hours_days[]" value="{{ $hr['days'] ?? '' }}" class="form-control" placeholder="Days (e.g. Monday – Friday)" />
              <input type="text" name="hours_time[]" value="{{ $hr['hours'] ?? '' }}" class="form-control" placeholder="Hours (e.g. 09:00 – 19:00)" />
              <button type="button" class="btn-action btn-action--delete btnRemoveHours" title="Remove">
                <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
              </button>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Phone & Email Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Phone Numbers</h3>

        <div class="form-group">
          <label class="form-label" for="studio_phone">Studio Direct Line</label>
          <input type="text" id="studio_phone" name="studio_phone" value="{{ old('studio_phone', $contact->studio_phone) }}" class="form-control" placeholder="+91 8000 000 000" />
          @error('studio_phone') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="bookings_phone">Bookings &amp; Events Line</label>
          <input type="text" id="bookings_phone" name="bookings_phone" value="{{ old('bookings_phone', $contact->bookings_phone) }}" class="form-control" placeholder="+91 9999 999 999" />
          @error('bookings_phone') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="admin-card">
        <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 1.25rem;">Email Inboxes</h3>

        <div class="form-group">
          <label class="form-label" for="general_email">General Inquiries Email</label>
          <input type="email" id="general_email" name="general_email" value="{{ old('general_email', $contact->general_email) }}" class="form-control" placeholder="hello@asdreams.in" />
          @error('general_email') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="bookings_email">Bookings &amp; Shoots Email</label>
          <input type="email" id="bookings_email" name="bookings_email" value="{{ old('bookings_email', $contact->bookings_email) }}" class="form-control" placeholder="shoots@asdreams.in" />
          @error('bookings_email') <span class="form-error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-top: 1rem;">
          <label class="form-label" for="note">Footer Note / Guarantee</label>
          <textarea id="note" name="note" rows="2" class="form-control" placeholder="All bookings secured with confirmation contract.">{{ old('note', $contact->note) }}</textarea>
          @error('note') <span class="form-error">{{ $message }}</span> @enderror
        </div>
      </div>

      <div class="admin-card" style="background: rgba(245, 200, 66, 0.04); border-color: var(--gold-a35);">
        <button type="submit" class="btn-gold" style="width: 100%; justify-content: center; padding: 0.85rem;">
          Save Contact Settings
        </button>
      </div>
    </div>
  </div>
</form>

<script>
  document.getElementById('btnAddHours')?.addEventListener('click', function() {
    const container = document.getElementById('hoursList');
    const row = document.createElement('div');
    row.className = 'hours-row';
    row.style = 'display: grid; grid-template-columns: 1fr 1fr 38px; gap: 0.75rem; align-items: center;';
    row.innerHTML = `
      <input type="text" name="hours_days[]" class="form-control" placeholder="Days (e.g. Sunday)" />
      <input type="text" name="hours_time[]" class="form-control" placeholder="Hours (e.g. 10:00 – 16:00)" />
      <button type="button" class="btn-action btn-action--delete btnRemoveHours" title="Remove">
        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    `;
    container.appendChild(row);
  });

  document.getElementById('hoursList')?.addEventListener('click', function(e) {
    if (e.target.closest('.btnRemoveHours')) {
      const rows = document.querySelectorAll('.hours-row');
      if (rows.length > 1) {
        e.target.closest('.hours-row').remove();
      } else {
        alert('You must keep at least one hours row.');
      }
    }
  });
</script>
@endsection

