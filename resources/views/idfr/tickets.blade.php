@extends('layouts.idfr')

@php
    $seasonTitle = $currentSeason ? $currentSeason->title : '';
    $seasonNum = $currentSeason ? $currentSeason->number : null;
    $seasonLocation = ($currentSeason && $currentSeason->location) ? $currentSeason->location : '';
    $seasonDates = ($currentSeason && $currentSeason->dates) ? $currentSeason->dates : '';
    $seasonYear = ($currentSeason && $currentSeason->year) ? $currentSeason->year : '';
    $seasonDesc = ($currentSeason && $currentSeason->description)
        ? $currentSeason->description
        : ($currentSeason ? 'Register as an official runway model for ' . $seasonTitle . ($seasonLocation ? ' in ' . $seasonLocation : '') . '. Showcase on premier fashion runways, connect with leading designers, and gain national modelling exposure.' : 'Model registration is currently unavailable.');
    $seasonHeroImage = ($currentSeason && $currentSeason->hero_image)
        ? $currentSeason->hero_image_url
        : asset('images/hero/hero-03.jpg');
    $feeFormatted = $formattedFee ?? ($currentSeason ? $currentSeason->formattedRegistrationFee() : '₹9,999');
@endphp

@section('title', $currentSeason ? 'Model Registration | IDFR Fashion Show — ' . $seasonTitle . ($seasonLocation ? ', ' . $seasonLocation : '') : 'Model Registration | IDFR Fashion Show')
@section('description', $currentSeason ? 'Official model registration for the IDFR Fashion Show ' . $seasonTitle . ($seasonDates ? ', ' . $seasonDates : '') . ($seasonLocation ? ' at ' . $seasonLocation : '') . '. Register now to participate.' : 'Official model registration for the IDFR Fashion Show.')
@section('canonical', route('idfr.tickets'))

@section('styles')
<style>
  .reg-price-card {
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.12) 0%, rgba(20, 20, 24, 0.85) 100%);
    border: 1px solid var(--gold, #d4af37);
    border-radius: 10px;
    padding: 1.5rem;
    margin-top: 1.5rem;
    position: relative;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
  }
  .reg-price-card__tag {
    font-size: 0.72rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--gold, #d4af37);
    font-weight: 700;
    margin-bottom: 0.35rem;
    display: block;
  }
  .reg-price-card__amount {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 2.2rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.1;
    margin-bottom: 0.5rem;
  }
  .reg-price-card__amount span {
    font-size: 0.9rem;
    font-family: var(--font-sans, system-ui, sans-serif);
    color: #a0a0a0;
    font-weight: 400;
    margin-left: 0.35rem;
  }
  .reg-price-card__note {
    font-size: 0.85rem;
    color: #c0c0c0;
    line-height: 1.45;
    margin-bottom: 1rem;
  }
  .reg-price-perks {
    list-style: none;
    padding: 0;
    margin: 0 0 1rem 0;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
  }
  .reg-price-perks li {
    font-size: 0.84rem;
    color: #e0e0e0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .reg-price-perks li svg {
    width: 14px;
    height: 14px;
    color: var(--gold, #d4af37);
    flex-shrink: 0;
  }
  .reg-price-card__badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.65rem;
    background: rgba(212, 175, 55, 0.15);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 4px;
    font-size: 0.78rem;
    color: var(--gold, #d4af37);
  }
  .guardian-box {
    background: rgba(255, 255, 255, 0.02);
    border: 1px dashed rgba(212, 175, 55, 0.4);
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
  }
  .guardian-box__title {
    font-size: 0.88rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--gold, #d4af37);
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
  }
  .guardian-box__desc {
    font-size: 0.8rem;
    color: #999;
    margin-bottom: 1rem;
  }
  .fee-inline-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(212, 175, 55, 0.12);
    border: 1px solid rgba(212, 175, 55, 0.35);
    padding: 0.4rem 0.85rem;
    border-radius: 6px;
    margin-bottom: 1.25rem;
  }
  .fee-inline-pill strong {
    color: var(--gold, #d4af37);
    font-size: 1.05rem;
  }
</style>
@endsection

@section('content')
<main id="main">
      <!-- ============ MODEL REGISTRATION ============ -->
      <section
        class="section booking"
        id="booking"
        aria-labelledby="bookingTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
              <li><a href="{{ route('idfr.home') }}">Home</a></li>
              <li aria-current="page">Model Registration</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              @if($currentSeason)
                <p class="section-label">IDFR Fashion Show · {{ $seasonTitle }}</p>
              @endif
              <h1 class="section-title" id="bookingTitle">
                Model <em>Registration</em>
              </h1>
            </div>
            @if($currentSeason && $seasonDesc)
              <p class="section-intro" data-reveal="up" data-delay="150">
                {{ $seasonDesc }}
              </p>
            @endif
          </div>

          @if(!$currentSeason)
            <div class="alert alert--info" style="padding: 3rem 1.5rem; text-align: center; background: rgba(20,20,24,0.85); border: 1px solid var(--border-gold, #d4af37); border-radius: 10px; margin-top: 2rem;">
              <svg viewBox="0 0 24 24" width="48" height="48" stroke="var(--gold, #d4af37)" stroke-width="1.8" fill="none" style="margin-bottom: 1rem; display:inline-block;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
              <h2 style="font-family: var(--font-serif); font-size: 1.65rem; color: #fff; margin-bottom: 0.5rem;">Model registration is currently unavailable.</h2>
              <p style="color: #a0a0a0; max-width: 520px; margin: 0 auto 1.5rem;">There are currently no active runway editions open for model registration. Please check back soon or follow our official announcements.</p>
              <a href="{{ route('idfr.home') }}" class="btn btn--gold"><span>Return to Home</span></a>
            </div>
          @else
          <div class="booking__layout">
            <section
              class="profile__details booking__card"
              aria-labelledby="bookingFormTitle"
            >
              <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom: 0.5rem;">
                <div>
                  <h2 class="profile__heading" id="bookingFormTitle" style="margin-bottom:0.25rem;">
                    Participant <em>Details</em>
                  </h2>
                  <p class="profile__lead" style="margin-bottom:0;">
                    Fields marked <span aria-hidden="true">*</span><span class="visually-hidden">with an asterisk</span> are required.
                  </p>
                </div>
                <div class="fee-inline-pill">
                  <span style="font-size:0.8rem; color:#b0b0b0;">Registration Fee:</span>
                  <strong>{{ $feeFormatted }}</strong>
                </div>
              </div>

              @if(session('success'))
                <div class="alert alert--success" style="padding:1rem 1.25rem; background:rgba(34,197,94,0.15); border:1px solid #22c55e; border-radius:6px; color:#22c55e; margin-bottom:1.5rem;">
                  {{ session('success') }}
                </div>
              @endif

              @if(session('info'))
                <div class="alert alert--info" style="padding:1rem 1.25rem; background:rgba(212,175,55,0.15); border:1px solid #d4af37; border-radius:6px; color:#d4af37; margin-bottom:1.5rem;">
                  {{ session('info') }}
                </div>
              @endif

              @if($errors->any())
                <div class="alert alert--danger" style="padding:1rem 1.25rem; background:rgba(239,68,68,0.15); border:1px solid #ef4444; border-radius:6px; color:#ef4444; margin-bottom:1.5rem;">
                  <ul style="margin:0; padding-left:1.25rem;">
                    @foreach($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <form
                class="profile-form booking__form"
                id="bookingForm"
                data-season-number="{{ $seasonNum }}"
                data-season-title="{{ $seasonTitle }}"
                action="{{ route('idfr.tickets.submit') }}"
                method="POST"
                novalidate
              >
                @csrf
                <input type="hidden" name="season_id" id="regSeasonId" value="{{ $currentSeason ? $currentSeason->id : '' }}">
                <input type="hidden" name="season_number" value="{{ $seasonNum }}">

                <!-- Full Name -->
                <div class="field profile-form__full">
                  <label for="regFullName"
                    >Full Name <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="text"
                    id="regFullName"
                    name="full_name"
                    autocomplete="name"
                    required
                    minlength="2"
                    maxlength="150"
                    placeholder="e.g. Ananya Menon"
                    value="{{ old('full_name') }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- WhatsApp Number -->
                <div class="field">
                  <label for="regWhatsapp"
                    >WhatsApp Number (with country code) <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="tel"
                    id="regWhatsapp"
                    name="whatsapp_number"
                    autocomplete="tel"
                    inputmode="tel"
                    required
                    pattern="\+?[0-9\s\-]{8,20}"
                    placeholder="e.g. +91 98765 43210"
                    value="{{ old('whatsapp_number') }}"
                  />
                  <span class="field__hint">Include country code (e.g. +91 for India). Official updates will be sent here.</span>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- Email Address (Optional) -->
                <div class="field">
                  <label for="regEmail">Email Address (Optional)</label>
                  <input
                    type="email"
                    id="regEmail"
                    name="email"
                    autocomplete="email"
                    maxlength="150"
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- Age -->
                <div class="field">
                  <label for="regAge"
                    >Age <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="number"
                    id="regAge"
                    name="age"
                    required
                    min="3"
                    max="99"
                    step="1"
                    placeholder="e.g. 21"
                    value="{{ old('age', 21) }}"
                  />
                  <span class="field__hint">Applicants under 18 require parent or legal guardian details below.</span>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- Gender -->
                <div class="field">
                  <label for="regGender"
                    >Gender <span aria-hidden="true">*</span></label
                  >
                  <select id="regGender" name="gender" required>
                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                  </select>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- Category -->
                <div class="field">
                  <label for="regCategory"
                    >Registration Category <span aria-hidden="true">*</span></label
                  >
                  <select id="regCategory" name="category" required>
                    <option value="adult" {{ old('category') === 'adult' ? 'selected' : 'selected' }}>Adult (18+ Years)</option>
                    <option value="kids" {{ old('category') === 'kids' ? 'selected' : '' }}>Kids / Minor (&lt; 18 Years)</option>
                  </select>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- Citizenship / Nationality -->
                <div class="field">
                  <label for="regCitizenship"
                    >Citizenship / Nationality <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="text"
                    id="regCitizenship"
                    name="citizenship"
                    required
                    maxlength="100"
                    placeholder="e.g. Indian"
                    value="{{ old('citizenship', 'Indian') }}"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <!-- Conditional Parent / Guardian Details for Kids / Minors (< 18) -->
                <div class="profile-form__full guardian-box" id="guardianDetails" style="display:none;">
                  <h3 class="guardian-box__title">
                    <svg class="icon" style="width:16px;height:16px;"><use href="#i-user"/></svg>
                    <span>Parent or Legal Guardian Information</span>
                  </h3>
                  <p class="guardian-box__desc">
                    Required for participants under 18 years of age. All casting coordination will be conducted with the designated guardian.
                  </p>

                  <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                    <div class="field">
                      <label for="regParentName">Parent / Guardian Name <span aria-hidden="true">*</span></label>
                      <input
                        type="text"
                        id="regParentName"
                        name="parent_name"
                        maxlength="150"
                        placeholder="e.g. Rajesh Menon"
                        value="{{ old('parent_name') }}"
                      />
                      <span class="field__error" aria-live="polite"></span>
                    </div>

                    <div class="field">
                      <label for="regParentContact">Guardian Contact / WhatsApp <span aria-hidden="true">*</span></label>
                      <input
                        type="tel"
                        id="regParentContact"
                        name="parent_contact"
                        maxlength="50"
                        placeholder="e.g. +91 98765 11223"
                        value="{{ old('parent_contact') }}"
                      />
                      <span class="field__error" aria-live="polite"></span>
                    </div>

                    <div class="field" style="grid-column: 1 / -1;">
                      <label for="regParentRelationship">Relationship to Participant</label>
                      <input
                        type="text"
                        id="regParentRelationship"
                        name="parent_relationship"
                        maxlength="50"
                        placeholder="e.g. Mother, Father, Legal Guardian"
                        value="{{ old('parent_relationship') }}"
                      />
                    </div>
                  </div>
                </div>

                <!-- Additional Information / Portfolio Link -->
                <div class="field profile-form__full">
                  <label for="regAdditionalInfo">Additional Information / Portfolio Link (Optional)</label>
                  <textarea
                    id="regAdditionalInfo"
                    name="additional_info"
                    rows="3"
                    maxlength="2000"
                    placeholder="Include height, previous runway or shoot experience, Instagram handle, or portfolio links."
                  >{{ old('additional_info') }}</textarea>
                  <span class="field__hint">Optional details to assist our casting panel review.</span>
                </div>

                <!-- Actions and Submit -->
                <div class="profile-form__actions profile-form__full booking__actions">
                  <p class="booking__secure">
                    <svg class="icon" aria-hidden="true"><use href="#i-check" /></svg>
                    <span>Zero payment required today. Registration fee ({{ $feeFormatted }}) will be verified and collected by the IDFR casting desk.</span>
                  </p>
                  <button class="btn btn--gold" type="submit" id="btnSubmitRegistration">
                    <span>Register for {{ $seasonTitle }} &mdash; {{ $feeFormatted }}</span>
                    <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
                  </button>
                </div>

                <div
                  class="booking__done profile-form__full"
                  id="bookingDone"
                  role="status"
                  tabindex="-1"
                  hidden
                >
                  <span class="booking__done-icon" aria-hidden="true">
                    <svg class="icon"><use href="#i-check" /></svg>
                  </span>
                  <div>
                    <p class="booking__done-title">Registration Successfully Submitted</p>
                    <p class="booking__done-text" id="bookingDoneText"></p>
                  </div>
                </div>
              </form>
            </section>

            <!-- Right Column: Season Showcase & Official Fee Card -->
            <aside
              class="booking__summary"
              aria-labelledby="bookingSummaryTitle"
            >
              <figure class="booking__poster">
                <img
                  class="photo"
                  src="{{ $seasonHeroImage }}"
                  alt="IDFR {{ $seasonTitle }} lookbook"
                  width="1100"
                  height="1650"
                  loading="lazy"
                />
                <figcaption>
                  <span class="booking__poster-label">{{ $seasonTitle }}</span>
                  <span class="booking__poster-title">IDFR Fashion <em>Show</em></span>
                </figcaption>
              </figure>

              <div class="booking__summary-body">
                <ul class="booking__event">
                  <li>
                    <svg class="icon" aria-hidden="true"><use href="#i-calendar" /></svg>
                    <span>{{ $seasonDates }} · 7:00 PM</span>
                  </li>
                  <li>
                    <svg class="icon" aria-hidden="true"><use href="#i-pin" /></svg>
                    <span>{{ $seasonLocation }}</span>
                  </li>
                </ul>

                <!-- Official Registration Fee Card -->
                <div class="reg-price-card">
                  <span class="reg-price-card__tag">Official Model Registration</span>
                  <div class="reg-price-card__amount">
                    {{ $feeFormatted }}
                    <span>/ participant</span>
                  </div>
                  <p class="reg-price-card__note">
                    Covers complete participation in the {{ $seasonTitle }} runway auditions, training sessions, and official showcases.
                  </p>

                  <ul class="reg-price-perks">
                    <li>
                      <svg class="icon reg-price-perks__icon" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" style="width:16px;height:16px;min-width:16px;color:var(--gold,#d4af37);flex-shrink:0;"><use href="#i-check"/></svg>
                      <span>Runway Audition &amp; Model Evaluation</span>
                    </li>
                    <li>
                      <svg class="icon reg-price-perks__icon" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" style="width:16px;height:16px;min-width:16px;color:var(--gold,#d4af37);flex-shrink:0;"><use href="#i-check"/></svg>
                      <span>Professional Grooming &amp; Ramp Walk Sessions</span>
                    </li>
                    <li>
                      <svg class="icon reg-price-perks__icon" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" style="width:16px;height:16px;min-width:16px;color:var(--gold,#d4af37);flex-shrink:0;"><use href="#i-check"/></svg>
                      <span>Official {{ $seasonTitle }} Lookbook &amp; Media Feature</span>
                    </li>
                    <li>
                      <svg class="icon reg-price-perks__icon" aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" style="width:16px;height:16px;min-width:16px;color:var(--gold,#d4af37);flex-shrink:0;"><use href="#i-check"/></svg>
                      <span>VIP Backstage Pass &amp; Designer Casting Access</span>
                    </li>
                  </ul>

                  <div class="reg-price-card__badge">
                    <svg class="icon" style="width:13px;height:13px;"><use href="#i-check"/></svg>
                    <span>Status: Unpaid upon initial registration</span>
                  </div>
                </div>
              </div>
            </aside>
          </div>
          @endif
        </div>
      </section>
    </main>
@endsection

@section('page_content_templates')
    @include('partials.season-info-template', ['season' => $season ?? ($currentSeason ?? null), 'templateId' => 'program-season-4'])
    @if(isset($season) && $season && $season->number != 4)
      @include('partials.season-info-template', ['season' => $season, 'templateId' => 'program-season-' . $season->number])
    @endif
@endsection
