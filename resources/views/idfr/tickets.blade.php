@extends('layouts.idfr')

@section('title', 'Book Tickets | IDFR Fashion Show — Season 4, Kozhikode')
@section('description', 'Book tickets for the IDFR Fashion Show Season 4, 18 – 20 December 2026 at IDFR Fashion House, Kozhikode. Choose Standard, Premium or VIP tickets and seating.')
@section('canonical', route('idfr.tickets'))

@section('content')
<main id="main">
      <!-- ============ TICKET BOOKING ============ -->
      <section
        class="section booking"
        id="booking"
        aria-labelledby="bookingTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
              <li><a href="{{ route('idfr.home') }}">Home</a></li>
              <li aria-current="page">Book Tickets</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">IDFR Fashion Show · Season 4</p>
              <h1 class="section-title" id="bookingTitle">
                Book your <em>seat</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Three runway nights of couture, handloom and new faces in
              Kozhikode. Choose your ticket and seating, tell us who is coming
              and we will hold your seats.
            </p>
          </div>

          <div class="booking__layout">
            <section
              class="profile__details booking__card"
              aria-labelledby="bookingFormTitle"
              data-reveal="up"
            >
              <h2 class="profile__heading" id="bookingFormTitle">
                Ticket <em>Details</em>
              </h2>
              <p class="profile__lead">
                Fields marked <span aria-hidden="true">*</span
                ><span class="visually-hidden">with an asterisk</span> are
                required.
              </p>

              <form
                class="profile-form booking__form"
                id="bookingForm"
                novalidate
              >
                <div class="field profile-form__full">
                  <label for="bkName"
                    >Full Name <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="text"
                    id="bkName"
                    name="name"
                    autocomplete="name"
                    required
                    minlength="2"
                    maxlength="60"
                    placeholder="e.g. Ananya Menon"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="bkEmail"
                    >Email Address <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="email"
                    id="bkEmail"
                    name="email"
                    autocomplete="email"
                    required
                    maxlength="80"
                    placeholder="you@example.com"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="bkPhone"
                    >Phone Number <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="tel"
                    id="bkPhone"
                    name="phone"
                    autocomplete="tel"
                    inputmode="tel"
                    required
                    pattern="\+?[0-9][0-9\s\-]{7,15}"
                    placeholder="+91 98765 43210"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="bkType"
                    >Ticket Type <span aria-hidden="true">*</span></label
                  >
                  <select
                    id="bkType"
                    name="ticketType"
                    required
                    aria-describedby="bkTypeHint"
                  >
                    <option value="" disabled selected>
                      Select a ticket type
                    </option>
                    <option
                      value="standard"
                      data-perks="General entry to all three show nights."
                    >
                      Standard
                    </option>
                    <option
                      value="premium"
                      data-perks="Priority entry, welcome drink and a Season 4 lookbook."
                    >
                      Premium
                    </option>
                    <option
                      value="vip"
                      data-perks="Backstage tour, lounge access and the closing-night after-party."
                    >
                      VIP
                    </option>
                  </select>
                  <span class="field__hint" id="bkTypeHint"
                    >Every ticket type includes all three show nights.</span
                  >
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="bkQty"
                    >Number of Attendees
                    <span aria-hidden="true">*</span></label
                  >
                  <div class="qty">
                    <button
                      class="qty__btn"
                      type="button"
                      data-qty="-1"
                      aria-controls="bkQty"
                      aria-label="Remove one attendee"
                    >
                      <span aria-hidden="true">&minus;</span>
                    </button>
                    <input
                      type="number"
                      id="bkQty"
                      name="attendees"
                      required
                      min="1"
                      max="10"
                      step="1"
                      value="1"
                      inputmode="numeric"
                      aria-describedby="bkQtyHint"
                    />
                    <button
                      class="qty__btn"
                      type="button"
                      data-qty="1"
                      aria-controls="bkQty"
                      aria-label="Add one attendee"
                    >
                      <span aria-hidden="true">+</span>
                    </button>
                  </div>
                  <span class="field__hint" id="bkQtyHint"
                    >Up to 10 tickets per booking.</span
                  >
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field profile-form__full">
                  <label for="bkSeat"
                    >Preferred Seating Type
                    <span aria-hidden="true">*</span></label
                  >
                  <select
                    id="bkSeat"
                    name="seating"
                    required
                    aria-describedby="bkSeatHint"
                  >
                    <option value="" disabled selected>
                      Select a seating category
                    </option>
                    <option
                      value="standard"
                      data-name="Standard Seating"
                      data-price="500"
                      data-note="Tiered rows"
                    >
                      Standard Seating – ₹500
                    </option>
                    <option
                      value="premium"
                      data-name="Premium Seating"
                      data-price="1000"
                      data-note="Second &amp; third row"
                    >
                      Premium Seating – ₹1,000
                    </option>
                    <option
                      value="vip"
                      data-name="VIP Seating"
                      data-price="2000"
                      data-note="Front row, runway side"
                    >
                      VIP Seating – ₹2,000
                    </option>
                  </select>
                  <span class="field__hint" id="bkSeatHint"
                    >Prices are per person, inclusive of taxes.</span
                  >
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div
                  class="profile-form__actions profile-form__full booking__actions"
                >
                  <p class="booking__secure">
                    <svg class="icon" aria-hidden="true">
                      <use href="#i-check" /></svg
                    ><span>No payment now — seats are held for 48 hours.</span>
                  </p>
                  <button class="btn btn--gold" type="submit">
                    <span>Submit</span
                    ><svg class="icon" aria-hidden="true">
                      <use href="#i-arrow-right" />
                    </svg>
                  </button>
                </div>

                <div
                  class="booking__done profile-form__full"
                  id="bookingDone"
                  role="status"
                  tabindex="-1"
                  hidden
                >
                  <span class="booking__done-icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-check" /></svg
                  ></span>
                  <div>
                    <p class="booking__done-title">Booking request received</p>
                    <p class="booking__done-text" id="bookingDoneText"></p>
                  </div>
                </div>
              </form>
            </section>

            <aside
              class="booking__summary"
              aria-labelledby="bookingSummaryTitle"
              data-reveal="up"
              data-delay="100"
            >
              <figure class="booking__poster">
                <img
                  class="photo"
                  src="{{ asset('images/hero/hero-03.jpg') }}"
                  alt="Model on the IDFR runway"
                  width="1100"
                  height="1650"
                  loading="lazy"
                />
                <figcaption>
                  <span class="booking__poster-label">Season 4</span>
                  <span class="booking__poster-title"
                    >IDFR Fashion <em>Show</em></span
                  >
                </figcaption>
              </figure>

              <div class="booking__summary-body">
                <ul class="booking__event">
                  <li>
                    <svg class="icon" aria-hidden="true">
                      <use href="#i-calendar" /></svg
                    ><span>18 – 20 Dec 2026 · 7:00 PM</span>
                  </li>
                  <li>
                    <svg class="icon" aria-hidden="true">
                      <use href="#i-pin" /></svg
                    ><span>IDFR Fashion House, Kozhikode</span>
                  </li>
                </ul>

                <h2 class="booking__summary-title" id="bookingSummaryTitle">
                  Order Summary
                </h2>
                <dl class="booking__lines">
                  <div>
                    <dt>Ticket Type</dt>
                    <dd id="sumType">—</dd>
                  </div>
                  <div>
                    <dt>Seating</dt>
                    <dd id="sumSeat">—</dd>
                  </div>
                  <div>
                    <dt>Price per Seat</dt>
                    <dd id="sumPrice">—</dd>
                  </div>
                  <div>
                    <dt>Attendees</dt>
                    <dd id="sumQty">1</dd>
                  </div>
                </dl>
                <p class="booking__total">
                  <span>Total</span><strong id="sumTotal">₹0</strong>
                </p>

                <h3 class="booking__prices-title">Seating Prices</h3>
                <ul class="booking__prices" id="bookingPrices"></ul>
              </div>
            </aside>
          </div>
        </div>
      </section>
    </main>
@endsection
@section('page_content_templates')
<!-- ============ PAGE CONTENT ============
         Content for the sections built by js/script.js. <template> content is not displayed;
         the script reads it and renders each section. Edit text, images and options here. -->

    <!-- Info modal content: one template per program, opened by links with a matching data-program. -->
    <template id="program-season-4">
      <p class="info__label">IDFR Fashion Show</p>
      <h2 class="info__title" id="infoModalTitle">{{ $currentSeason ? $currentSeason->title : 'Season 4' }}</h2>
      <p class="info__intro">
        {{ $currentSeason && $currentSeason->description ? $currentSeason->description : 'Three runway nights celebrating emerging designers, established names and the next generation of Indian modelling talent. Season 4 brings couture, resort, menswear and handloom collections to one stage in Kozhikode.' }}
      </p>
      <dl class="info__facts">
        <div>
          <dt>Dates</dt>
          <dd>{{ $currentSeason ? $currentSeason->dates : '18 – 20 Dec 2026' }}</dd>
        </div>
        <div>
          <dt>Venue</dt>
          <dd>{{ $currentSeason ? $currentSeason->location : 'IDFR Fashion House' }}</dd>
        </div>
        <div>
          <dt>Designers</dt>
          <dd>24 Labels</dd>
        </div>
        <div>
          <dt>Models</dt>
          <dd>60+ Talent</dd>
        </div>
      </dl>
      <div class="info__schedule">
        <article>
          <h3>Day 1 · Opening Night</h3>
          <p>Debut collections walked by models from the Season 4 open auditions.</p>
        </article>
        <article>
          <h3>Day 2 · Designer Showcase</h3>
          <p>Evening gowns, bridal couture and prêt lines from across South India.</p>
        </article>
        <article>
          <h3>Day 3 · Grand Finale</h3>
          <p>Couture showcases, awards and the closing walk of {{ $currentSeason ? $currentSeason->title : 'Season 4' }}.</p>
        </article>
      </div>
    </template>
    <template id="program-athuls-academy">
      <p class="info__label">Modelling & Grooming</p>
      <h2 class="info__title" id="infoModalTitle">Athul’s <em>Academy</em></h2>
      <p class="info__intro">
        A finishing school for models — from first walk to first casting. Small
        batches, working industry mentors and a direct pathway to IDFR runways
        and ASDreamStudio portfolio shoots.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Batches</dt>
          <dd>Weekend & Evening</dd>
        </div>
        <div>
          <dt>Mentors</dt>
          <dd>Runway & Casting</dd>
        </div>
        <div>
          <dt>Next Intake</dt>
          <dd>19 Jan 2027</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">10 Weeks · Beginners</p>
          <h3 class="info__item-name">Model Training</h3>
          <p class="info__item-desc">
            Runway walk, posture, turns, posing and camera confidence.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">6 Weeks · All levels</p>
          <h3 class="info__item-name">Grooming &amp; Etiquette</h3>
          <p class="info__item-desc">
            Skincare, fitness, wardrobe, communication and interview skills.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">1 Day · With ASDreamStudio</p>
          <h3 class="info__item-name">Portfolio Shoot</h3>
          <p class="info__item-desc">
            Studio and outdoor looks, retouched and ready for agencies.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">Ongoing</p>
          <h3 class="info__item-name">Casting &amp; Industry Opportunities</h3>
          <p class="info__item-desc">
            Audition preparation, casting calls and placement with IDFR
            partners.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <button
          class="btn btn--gold"
          type="button"
          data-auth="register"
          data-role="fresher"
        >
          <span>Apply Now</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" />
          </svg>
        </button>
      </div>
    </template>
    <template id="program-lakme-academy">
      <p class="info__label">Beauty & Styling</p>
      <h2 class="info__title" id="infoModalTitle">Lakme <em>Academy</em></h2>
      <p class="info__intro">
        Industry-led beauty and styling courses for aspiring makeup artists,
        hair stylists and fashion stylists — with live backstage experience at
        IDFR fashion shows.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Format</dt>
          <dd>Classroom + Backstage</dd>
        </div>
        <div>
          <dt>Certification</dt>
          <dd>Professional</dd>
        </div>
        <div>
          <dt>Next Intake</dt>
          <dd>12 Jan 2027</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">16 Weeks</p>
          <h3 class="info__item-name">Professional Makeup Artistry</h3>
          <p class="info__item-desc">
            Bridal, editorial, runway and camera makeup techniques.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">12 Weeks</p>
          <h3 class="info__item-name">Hair Styling &amp; Design</h3>
          <p class="info__item-desc">
            Cutting, colour theory, runway hair and bridal styling.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">8 Weeks</p>
          <h3 class="info__item-name">Fashion Styling</h3>
          <p class="info__item-desc">
            Wardrobe styling for shoots, campaigns and runway shows.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">6 Weeks</p>
          <h3 class="info__item-name">Skin &amp; Beauty Essentials</h3>
          <p class="info__item-desc">
            Skincare science, consultation and beauty treatments.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <button
          class="btn btn--gold"
          type="button"
          data-auth="register"
          data-role="fresher"
        >
          <span>Enquire Now</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" />
          </svg>
        </button>
      </div>
    </template>
    <template id="program-privacy">
      <p class="info__label">Legal</p>
      <h2 class="info__title" id="infoModalTitle">Privacy <em>Policy</em></h2>
      <div class="info__text">
        <h3>Information we collect</h3>
        <p>
          When you create an IDFR profile we collect the details you provide —
          such as your name, contact information, measurements and portfolio
          images — to present your profile to verified industry partners and clients.
        </p>
        <h3>How we use it</h3>
        <p>
          Your information is used to match you with relevant assignments,
          communicate about fashion shows and improve our platform. We never
          sell personal data to third parties.
        </p>
        <h3>Your choices</h3>
        <p>
          You can edit, hide or delete your profile at any time from your
          account settings, or by writing to info@idfr.example.
        </p>
      </div>
      <div class="info__actions">
        <button class="btn btn--gold" type="button" data-close>
          <span>Close</span>
        </button>
      </div>
    </template>
    <template id="program-terms">
      <p class="info__label">Legal</p>
      <h2 class="info__title" id="infoModalTitle">
        Terms &amp; <em>Conditions</em>
      </h2>
      <div class="info__text">
        <h3>Using IDFR</h3>
        <p>
          IDFR is a marketplace that connects models with
          brands and fashion shows. Members agree to provide accurate
          information and to conduct themselves professionally.
        </p>
        <h3>Profiles & content</h3>
        <p>
          Models retain ownership of their portfolio images and grant IDFR
          permission to display them on the platform for the purpose of
          discovery and casting.
        </p>
        <h3>Assignments</h3>
        <p>
          Assignments are agreed directly between models and clients. IDFR is not party to individual contracts.
        </p>
      </div>
      <div class="info__actions">
        <button class="btn btn--gold" type="button" data-close>
          <span>Close</span>
        </button>
      </div>
    </template>
@endsection