@extends('layouts.asdreams')

@section('no_header', true)

@section('title', ($activeOffer ? $activeOffer->title : 'Offer Details') . ' | ASDreamStudio Photography & Videography')
@section('description', 'Offer details from ASDreamStudio, the photography and videography partner of IDFR. See what is included and send an enquiry about a portrait, wedding, studio or destination offer.')
@section('canonical', $activeOffer ? route('asd.offers.show', $activeOffer->slug) : route('asd.offers.index'))

@push('styles')
<style>
  /* ASDreamStudio Offer Details Layout & Styles */
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
  .offer-page__details {
    min-width: 0;
  }
  .offer-page__details[hidden] {
    display: none !important;
  }
  .offer-page__details.is-visible {
    opacity: 1 !important;
    transform: none !important;
    visibility: visible !important;
  }
  .offer-page .asd-contact__form {
    opacity: 1 !important;
    transform: none !important;
    visibility: visible !important;
  }
  .offer-page__list {
    display: grid;
    gap: 0.85rem;
    margin: 1.5rem 0 1.75rem;
    padding: 0;
    list-style: none;
  }
  .offer-page__list li {
    position: relative;
    padding-left: 1.35rem;
    font-size: 0.95rem;
    color: var(--white-90);
    line-height: 1.6;
  }
  .offer-page__list li::before {
    content: "—";
    position: absolute;
    left: 0;
    color: var(--gold);
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
  <section class="section offer-page" id="offer-details">
    <div class="container">
      <a
        class="btn btn--ghost offer-page__back"
        href="{{ route('landing') }}"
        data-nav="home"
      >
        <svg class="icon" aria-hidden="true">
          <use href="#i-arrow-left" />
        </svg><span>Back to Home</span>
      </a>

      <div class="offer-page__layout">
        {{-- Dynamic Offer Details Panels --}}
        @forelse($offers as $offer)
          @php
            $isActive = ($offer->slug === ($activeOffer?->slug ?? 'viewfinder') || $offer->id === ($activeOffer?->id ?? 0));
          @endphp
          <section
            class="offer-page__details {{ $isActive ? 'is-visible' : '' }}"
            data-offer="{{ $offer->slug }}"
            data-id="{{ $offer->id }}"
            data-banner-id="{{ $offer->banner_id ?? ($offer->banner?->id ?? '') }}"
            data-title="{{ $offer->title }}"
            data-message="{{ $offer->enquiry_placeholder }}"
            aria-labelledby="offerTitle-{{ $offer->slug }}"
            data-reveal="up"
            {!! $isActive ? '' : 'hidden' !!}
          >
            @if($offer->category_label)
              <p class="section-label">{{ $offer->category_label }}</p>
            @endif
            <h1 class="profile__heading" id="offerTitle-{{ $offer->slug }}">
              {{ $offer->heading_main }} @if($offer->heading_highlight)<em>{{ $offer->heading_highlight }}</em>@endif
            </h1>
            @if($offer->lead_text)
              <p class="profile__lead">{{ $offer->lead_text }}</p>
            @endif

            @if($offer->price || $offer->validity)
              <div class="offer-page__meta-tags" style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin: 1rem 0;">
                @if($offer->price)
                  <span class="badge" style="background: rgba(212, 175, 55, 0.15); color: var(--gold); border: 1px solid var(--gold-a35); font-size: 0.85rem; padding: 4px 12px; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg class="icon" style="width: 13px; height: 13px;"><use href="#i-diamond" /></svg>
                    <strong>Price:</strong> {{ $offer->price }}
                  </span>
                @endif
                @if($offer->validity)
                  <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--text-light); border: 1px solid var(--border-subtle); font-size: 0.85rem; padding: 4px 12px; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <svg class="icon" style="width: 13px; height: 13px;"><use href="#i-calendar" /></svg>
                    <strong>Validity:</strong> {{ $offer->validity }}
                  </span>
                @endif
              </div>
            @endif

            <span class="gold-line" aria-hidden="true"></span>

            @if($offer->features && $offer->features->isNotEmpty())
              <ul class="offer-page__list">
                @foreach($offer->features as $feature)
                  <li>{{ $feature->feature_text }}</li>
                @endforeach
              </ul>
            @endif

            @if($offer->full_description)
              <div class="offer-page__full-desc" style="color: var(--white-90); font-size: 0.95rem; line-height: 1.6; margin: 1.5rem 0;">
                {!! nl2br(e($offer->full_description)) !!}
              </div>
            @endif

            @if($offer->notes)
              <p class="asd-contact__note">{{ $offer->notes }}</p>
            @endif

            @if($offer->terms)
              <div class="asd-contact__note" style="margin-top: 1rem;">
                <strong style="color: var(--gold); display: block; margin-bottom: 0.35rem; font-size: 0.85rem; letter-spacing: 0.1em; text-transform: uppercase;">Terms &amp; Conditions</strong>
                <p style="margin: 0; font-size: 0.9rem; line-height: 1.5;">{!! nl2br(e($offer->terms)) !!}</p>
              </div>
            @endif

            @if($offer->important_notes)
              <div class="asd-contact__note" style="margin-top: 1rem;">
                <strong style="color: var(--gold); display: block; margin-bottom: 0.35rem; font-size: 0.85rem; letter-spacing: 0.1em; text-transform: uppercase;">Important Notes</strong>
                <p style="margin: 0; font-size: 0.9rem; line-height: 1.5;">{!! nl2br(e($offer->important_notes)) !!}</p>
              </div>
            @endif
          </section>
        @empty
          <section
            class="offer-page__details is-visible"
            data-offer="viewfinder"
            data-title="Behind the Viewfinder"
            data-message="Tell us who the portraits are for, and whether you prefer the studio or an outdoor location"
            aria-labelledby="offerTitle-viewfinder"
            data-reveal="up"
          >
            <p class="section-label">Portrait Session · Kozhikode</p>
            <h1 class="profile__heading" id="offerTitle-viewfinder">
              Behind the <em>Viewfinder</em>
            </h1>
            <p class="profile__lead">
              A directed portrait session for people who want photographs that feel quiet, close and true.
            </p>
            <span class="gold-line" aria-hidden="true"></span>
            <ul class="offer-page__list">
              <li>90-minute session in the Kozhikode studio or at a nearby outdoor location</li>
              <li>Lighting and posing directed for individuals, couples or small families</li>
              <li>Wardrobe guidance before the shoot, plus on-set styling support</li>
              <li>25 retouched photographs delivered in a private online gallery</li>
              <li>One 12×18 fine-art print, ready to frame</li>
            </ul>
            <p class="asd-contact__note">
              Sessions are scheduled Tuesday to Sunday. A printed proof set can be added once the gallery is delivered.
            </p>
          </section>
        @endforelse

        {{-- Enquiry Form --}}
        <section
          class="profile__details asd-contact__form is-visible"
          aria-labelledby="offerEnquiryTitle"
        >
          <h2 class="profile__heading" id="offerEnquiryTitle">
            Enquire about <em>this offer</em>
          </h2>
          <p class="profile__lead">
            Fields marked <span aria-hidden="true">*</span><span class="visually-hidden">with an asterisk</span> are
            required. Tell us how you would like to use this offer and we
            will reply within one working day.
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

          <form
            class="profile-form"
            id="asdEnquiryForm"
            method="POST"
            action="{{ route('asd.offers.enquire') }}"
            data-validate
            data-ajax="true"
            data-server-errors='@json($errors->getMessages())'
            novalidate
          >
            @csrf
            <input type="hidden" name="banner_id" id="enqBannerId" value="{{ old('banner_id', $activeOffer?->banner_id ?? ($activeOffer?->banner?->id ?? '')) }}" />
            <input type="hidden" name="offer_id" id="enqOfferId" value="{{ old('offer_id', $activeOffer?->id ?? '') }}" />
            <input type="hidden" name="offer" id="enqOffer" value="{{ old('offer', $activeOffer?->slug ?? 'viewfinder') }}" />

            <div class="field">
              <label for="enqName">Name <span aria-hidden="true">*</span></label>
              <input
                type="text"
                id="enqName"
                name="name"
                autocomplete="name"
                required
                minlength="2"
                maxlength="60"
                data-rules="required|min:2|max:60|name"
                data-label="Name"
                placeholder="Your full name"
                value="{{ old('name') }}"
              />
              <span class="field__error" aria-live="polite">
                @error('name') {{ $message }} @enderror
              </span>
            </div>

            <div class="field">
              <label for="enqPhone">Phone Number <span aria-hidden="true">*</span></label>
              <input
                type="tel"
                id="enqPhone"
                name="phone"
                autocomplete="tel"
                required
                data-rules="required|phone"
                data-label="Phone Number"
                placeholder="+91 98765 43210"
                value="{{ old('phone') }}"
              />
              <span class="field__error" aria-live="polite">
                @error('phone') {{ $message }} @enderror
              </span>
            </div>

            <div class="field">
              <label for="enqEmail">Email Address <span class="field__note">Optional</span></label>
              <input
                type="email"
                id="enqEmail"
                name="email"
                autocomplete="email"
                maxlength="120"
                data-rules="email|max:120"
                data-label="Email Address"
                placeholder="you@example.com"
                value="{{ old('email') }}"
              />
              <span class="field__error" aria-live="polite">
                @error('email') {{ $message }} @enderror
              </span>
            </div>

            <div class="field">
              <label for="enqDate">Preferred Date <span class="field__note">Optional</span></label>
              <input
                type="date"
                id="enqDate"
                name="date"
                data-rules="date|date_after_or_equal:today"
                data-label="Preferred Date"
                value="{{ old('date') }}"
              />
              <span class="field__error" aria-live="polite">
                @error('date') {{ $message }} @enderror
              </span>
            </div>

            <div class="field profile-form__full">
              <label for="enqMessage">Your Enquiry <span class="field__note">Optional</span></label>
              <textarea
                id="enqMessage"
                name="message"
                rows="5"
                maxlength="1000"
                data-rules="max:1000"
                data-label="Your Enquiry"
                placeholder="{{ $activeOffer?->enquiry_placeholder ?? 'Tell us who the portraits are for, and whether you prefer the studio or an outdoor location' }}"
              >{{ old('message') }}</textarea>
              <span class="field__error" aria-live="polite">
                @error('message') {{ $message }} @enderror
              </span>
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
    var offerInput = document.getElementById("enqOffer");
    var offerIdInput = document.getElementById("enqOfferId");
    var bannerIdInput = document.getElementById("enqBannerId");
    var message = document.getElementById("enqMessage");
    var date = document.getElementById("enqDate");
    var panels = document.querySelectorAll(".offer-page__details");
    var allowed = {};

    Array.prototype.forEach.call(panels, function (panel) {
      var slug = panel.getAttribute("data-offer");
      if (slug) {
        allowed[slug] = panel;
      }
    });

    if (date) {
      var now = new Date();
      date.min = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .slice(0, 10);
    }

    function getOfferFromUrl() {
      // 1. Check query parameter ?offer=slug
      try {
        var queryOffer = new URLSearchParams(window.location.search).get("offer");
        if (queryOffer && allowed[queryOffer]) {
          return queryOffer;
        }
      } catch (e) {}

      // 2. Check path slug /asdreams/offers/{slug}
      try {
        var path = window.location.pathname.replace(/\/+$/, "");
        var match = path.match(/\/asdreams\/offers\/([^\/]+)$/i);
        if (match && match[1] && allowed[match[1]]) {
          return match[1];
        }
      } catch (e) {}

      // 3. Fallback to active server offer or 'viewfinder'
      var defaultSlug = "{{ $activeOffer?->slug ?? 'viewfinder' }}";
      return allowed[defaultSlug] ? defaultSlug : (Object.keys(allowed)[0] || "viewfinder");
    }

    function render(id) {
      if (!allowed[id]) {
        var fallbackSlug = "{{ $activeOffer?->slug ?? 'viewfinder' }}";
        id = allowed[fallbackSlug] ? fallbackSlug : (Object.keys(allowed)[0] || "viewfinder");
      }

      Array.prototype.forEach.call(panels, function (panel) {
        var on = panel.getAttribute("data-offer") === id;
        panel.hidden = !on;
        if (on) {
          panel.classList.add("is-visible");
        } else {
          panel.classList.remove("is-visible");
        }
      });

      var details = allowed[id] || document.querySelector('.offer-page__details[data-offer="' + id + '"]');
      if (details) {
        if (message) {
          message.placeholder = details.getAttribute("data-message") || "";
        }
        var newTitle = details.getAttribute("data-title");
        if (newTitle) {
          document.title = newTitle + " | ASDreamStudio Photography & Videography";
        }
        if (offerInput) {
          offerInput.value = id;
          offerInput.setAttribute("value", id);
        }
        if (offerIdInput) {
          var offerIdVal = details.getAttribute("data-id") || "";
          offerIdInput.value = offerIdVal;
          offerIdInput.setAttribute("value", offerIdVal);
        }
        if (bannerIdInput) {
          var bannerIdVal = details.getAttribute("data-banner-id") || "";
          bannerIdInput.value = bannerIdVal;
          bannerIdInput.setAttribute("value", bannerIdVal);
        }
      }
    }

    // Initial render on load
    render(getOfferFromUrl());

    // Browser back / forward navigation support
    window.addEventListener("popstate", function (e) {
      if (e.state && e.state.offer) {
        render(e.state.offer);
      } else {
        render(getOfferFromUrl());
      }
    });
  })();
</script>
@endpush
