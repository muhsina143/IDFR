@extends('layouts.asdreams')

@section('no_header', true)
@section('no_footer', true)

@php
  $pageTitle = ($activeBrand === 'lakme') ? 'Lakmé Academy | Contact' : "Athul's Academy | Contact";
  $pageDesc = ($activeBrand === 'lakme')
    ? 'Contact Lakmé Academy. Find email and phone details, then send an enquiry about beauty, makeup, hair and styling courses.'
    : "Contact Athul's Academy. Find email and phone details, then send an enquiry about modelling and grooming courses.";
@endphp

@section('title', $pageTitle)
@section('description', $pageDesc)
@section('canonical', url('/asdreams/contact?brand=' . $activeBrand))

@push('styles')
<style>
  /* ASDreamStudio Brand Contact Layout & Styles (matches contact_brand.html reference) */
  .offer-page__back {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 2rem;
  }
  .offer-page__layout {
    display: grid;
    grid-template-columns: minmax(0, 0.95fr) minmax(0, 1.05fr);
    gap: clamp(2.5rem, 5vw, 5.5rem);
    align-items: start;
    opacity: 1 !important;
    visibility: visible !important;
  }
  .brand-contact__info {
    min-width: 0;
  }
  .brand-contact__info[hidden] {
    display: none !important;
  }
  .brand-contact__info.is-visible {
    opacity: 1 !important;
    transform: none !important;
    visibility: visible !important;
  }
  .brand-contact .asd-contact__form {
    opacity: 1 !important;
    transform: none !important;
    visibility: visible !important;
  }
  .brand-contact .asd-contact__list {
    display: grid;
    margin-top: 2rem;
    border-top: 1px solid var(--white-10);
  }
  .brand-contact .gold-line {
    display: block;
    width: 3.5rem;
    height: 1px;
    background: var(--gold);
    margin: 1.5rem 0;
  }
  @media (max-width: 900px) {
    .offer-page__layout {
      grid-template-columns: 1fr;
      gap: 2.75rem;
    }
  }
</style>
@endpush

@section('content')
<main id="main">
  <section class="section offer-page brand-contact" id="brand-contact">
    <div class="container">
      <a class="btn btn--ghost offer-page__back" href="{{ route('landing') }}">
        <svg class="icon" aria-hidden="true">
          <use href="#i-arrow-left" />
        </svg>
        <span>Back to Home</span>
      </a>

      <div class="offer-page__layout">
        <!-- ============ ATHUL'S ACADEMY DETAILS ============ -->
        <section
          class="offer-page__details brand-contact__info {{ $activeBrand === 'athuls' ? 'is-visible' : '' }}"
          data-brand="athuls"
          data-title="Athul's Academy"
          aria-labelledby="brandTitle-athuls"
          data-reveal="up"
          {{ $activeBrand === 'athuls' ? '' : 'hidden' }}
        >
          <p class="section-label">Modelling &amp; Grooming</p>
          <h1 class="profile__heading" id="brandTitle-athuls">
            Athul’s <em>Academy</em>
          </h1>
          <p class="profile__lead">
            A finishing school for models — from first walk to first casting.
            Small batches, working industry mentors and a direct pathway to
            IDFR runways and ASDreamStudio portfolio shoots.
          </p>
          <span class="gold-line" aria-hidden="true"></span>
          <ul class="asd-contact__list">
            <li class="asd-contact__item">
              <span class="asd-contact__icon" aria-hidden="true">
                <svg class="icon"><use href="#i-mail" /></svg>
              </span>
              <div class="asd-contact__body">
                <h2 class="asd-contact__label">Email</h2>
                <p class="asd-contact__value">
                  <a href="mailto:hello@athulsacademy.example">hello@athulsacademy.example</a>
                </p>
              </div>
            </li>
            <li class="asd-contact__item">
              <span class="asd-contact__icon" aria-hidden="true">
                <svg class="icon"><use href="#i-phone" /></svg>
              </span>
              <div class="asd-contact__body">
                <h2 class="asd-contact__label">Phone</h2>
                <p class="asd-contact__value">
                  <a href="tel:+919847012340">+91 98470 12340</a>
                </p>
              </div>
            </li>
          </ul>
          <p class="asd-contact__note">
            Weekend and evening batches are open in Kozhikode. Share your
            level and preferred intake when you enquire.
          </p>
        </section>

        <!-- ============ LAKMÉ ACADEMY DETAILS ============ -->
        <section
          class="offer-page__details brand-contact__info {{ $activeBrand === 'lakme' ? 'is-visible' : '' }}"
          data-brand="lakme"
          data-title="Lakmé Academy"
          aria-labelledby="brandTitle-lakme"
          data-reveal="up"
          {{ $activeBrand === 'lakme' ? '' : 'hidden' }}
        >
          <p class="section-label">Beauty &amp; Wellness</p>
          <h1 class="profile__heading" id="brandTitle-lakme">
            Lakmé <em>Academy</em>
          </h1>
          <p class="profile__lead">
            Industry-led beauty and styling courses for aspiring makeup
            artists, hair stylists and fashion stylists — with live
            backstage experience at IDFR fashion shows.
          </p>
          <span class="gold-line" aria-hidden="true"></span>
          <ul class="asd-contact__list">
            <li class="asd-contact__item">
              <span class="asd-contact__icon" aria-hidden="true">
                <svg class="icon"><use href="#i-mail" /></svg>
              </span>
              <div class="asd-contact__body">
                <h2 class="asd-contact__label">Email</h2>
                <p class="asd-contact__value">
                  <a href="mailto:hello@lakmeacademy.example">hello@lakmeacademy.example</a>
                </p>
              </div>
            </li>
            <li class="asd-contact__item">
              <span class="asd-contact__icon" aria-hidden="true">
                <svg class="icon"><use href="#i-phone" /></svg>
              </span>
              <div class="asd-contact__body">
                <h2 class="asd-contact__label">Phone</h2>
                <p class="asd-contact__value">
                  <a href="tel:+919847056780">+91 98470 56780</a>
                </p>
              </div>
            </li>
          </ul>
          <p class="asd-contact__note">
            Professional makeup, hair, nails and skin courses run year-round.
            Tell us which programme you are interested in.
          </p>
        </section>

        <!-- ============ BRAND ENQUIRY FORM ============ -->
        <section
          class="profile__details asd-contact__form"
          aria-labelledby="brandEnquiryTitle"
          data-reveal="up"
          data-delay="100"
        >
          <h2 class="profile__heading" id="brandEnquiryTitle">
            Send an <em>Enquiry</em>
          </h2>
          <p class="profile__lead">
            Fields marked <span aria-hidden="true">*</span><span class="visually-hidden">with an asterisk</span> are required.
          </p>

          @if(session('success'))
            <div class="alert alert--success" style="background: rgba(52, 168, 83, 0.15); border: 1px solid rgba(52, 168, 83, 0.4); color: #70e090; padding: 1rem 1.25rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.9rem;">
              {{ session('success') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert--error" style="background: rgba(234, 67, 53, 0.15); border: 1px solid rgba(234, 67, 53, 0.4); color: #ff8080; padding: 1rem 1.25rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.9rem;">
              <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form class="profile-form" id="asdEnquiryForm" method="POST" action="{{ route('asd.contact.enquire') }}" novalidate>
            @csrf
            <div class="field">
              <label for="enqName">Name <span aria-hidden="true">*</span></label>
              <input
                type="text"
                id="enqName"
                name="name"
                autocomplete="name"
                required
                minlength="2"
                placeholder="Your full name"
                value="{{ old('name') }}"
              />
              <span class="field__error" aria-live="polite">@error('name') {{ $message }} @enderror</span>
            </div>

            <div class="field">
              <label for="enqPhone">Phone Number <span aria-hidden="true">*</span></label>
              <input
                type="tel"
                id="enqPhone"
                name="phone"
                autocomplete="tel"
                required
                pattern="[0-9+\s\-]{8,25}"
                placeholder="+91 98765 43210"
                value="{{ old('phone') }}"
              />
              <span class="field__error" aria-live="polite">@error('phone') {{ $message }} @enderror</span>
            </div>

            <div class="field">
              <label for="enqEmail">Email Address <span class="field__note">Optional</span></label>
              <input
                type="email"
                id="enqEmail"
                name="email"
                autocomplete="email"
                placeholder="you@example.com"
                value="{{ old('email') }}"
              />
              <span class="field__error" aria-live="polite">@error('email') {{ $message }} @enderror</span>
            </div>

            <div class="field">
              <label for="enqSubject">Subject <span class="field__note">Optional</span></label>
              <input
                type="text"
                id="enqSubject"
                name="subject"
                maxlength="120"
                placeholder="e.g. Weekend batch, January intake"
                value="{{ old('subject') }}"
              />
              <span class="field__error" aria-live="polite">@error('subject') {{ $message }} @enderror</span>
            </div>

            <input type="hidden" id="enqBrand" name="brand" value="{{ old('brand', $activeBrand) }}" />

            <div class="field profile-form__full">
              <label for="enqMessage">Message</label>
              <textarea
                id="enqMessage"
                name="message"
                rows="5"
                maxlength="1000"
                placeholder="{{ $activeBrand === 'lakme' ? 'Tell us about the beauty or styling course you are interested in' : 'Tell us about the modelling or grooming course you are interested in' }}"
              >{{ old('message') }}</textarea>
              <span class="field__error" aria-live="polite">@error('message') {{ $message }} @enderror</span>
            </div>

            <div class="profile-form__actions profile-form__full">
              <button class="btn btn--gold" type="submit">
                <span>Submit</span>
                <svg class="icon" aria-hidden="true">
                  <use href="#i-arrow-right" />
                </svg>
              </button>
            </div>
          </form>
        </section>
      </div>
    </div>
  </section>
</main>
@endsection

@push('scripts')
<script>
  (function () {
    var panels = document.querySelectorAll(".brand-contact__info");
    var brandInput = document.getElementById("enqBrand");
    var message = document.getElementById("enqMessage");
    var aliases = {
      athuls: "athuls",
      "athuls-academy": "athuls",
      "modelling-academy": "athuls",
      modeling: "athuls",
      lakme: "lakme",
      "lakme-academy": "lakme"
    };
    var placeholders = {
      athuls: "Tell us about the modelling or grooming course you are interested in",
      lakme: "Tell us about the beauty or styling course you are interested in"
    };

    function render(id) {
      id = aliases[id] || "{{ $activeBrand }}";
      Array.prototype.forEach.call(panels, function (panel) {
        var on = panel.getAttribute("data-brand") === id;
        panel.hidden = !on;
        if (on) panel.classList.add("is-visible");
        else panel.classList.remove("is-visible");
      });
      var details = document.querySelector('.brand-contact__info[data-brand="' + id + '"]');
      if (brandInput) brandInput.value = id;
      if (message) message.placeholder = placeholders[id] || placeholders.athuls;
      if (details) document.title = details.getAttribute("data-title") + " | Contact";
    }

    var requested = "";
    try {
      requested = new URLSearchParams(window.location.search).get("brand") || "";
    } catch (err) {
      requested = "";
    }
    if (requested) {
      render(requested);
    }
  })();
</script>
@endpush

