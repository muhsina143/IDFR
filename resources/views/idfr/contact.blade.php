@extends('layouts.idfr')

@section('title', 'Contact IDFR | Indian Designer Fashion Runway, Kozhikode')
@section('description', 'Contact IDFR — Indian Designer Fashion Runway. Address, phone and email for model registrations, castings, designer showcases, sponsorships and Fashion Show Season 4, plus an enquiry form.')
@section('canonical', route('idfr.contact'))

@section('content')
<main id="main">
      <!-- ============ CONTACT ============ -->
      <section
        class="section asd-contact"
        id="idfr-contact-page"
        data-spy="contact"
        aria-labelledby="asdContactTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
              <li><a href="{{ route('idfr.home') }}">Home</a></li>
              <li aria-current="page">Contact</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">Get in Touch</p>
              <h1 class="section-title" id="asdContactTitle">
                Let’s talk <em>fashion</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Model, designer, brand or agency? Tell us what you are
              working on and the IDFR team will get back to you within one
              working day.
            </p>
          </div>

          <div class="asd-contact__layout">
            <section
              class="asd-contact__info"
              aria-labelledby="asdContactInfoTitle"
              data-reveal="up"
            >
              <h2 class="profile__heading" id="asdContactInfoTitle">
                Contact <em>Information</em>
              </h2>
              <p class="profile__lead">
                Visit IDFR Fashion House, call or write to us — we are here to
                help you take the runway.
              </p>

              <ul class="asd-contact__list">
                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-pin" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Fashion House Address</h3>
                    <address class="asd-contact__value">
                      IDFR Fashion House, 3rd Floor, Kannur Road,<br />
                      Nadakkavu, Kozhikode, Kerala 673011, India
                    </address>
                    <a
                      class="text-link asd-contact__link"
                      href="https://www.google.com/maps/search/?api=1&amp;query=Kannur+Road+Nadakkavu+Kozhikode+Kerala"
                      target="_blank"
                      rel="noopener"
                      ><span>Get Directions</span
                      ><svg class="icon" aria-hidden="true">
                        <use href="#i-arrow-right" /></svg
                    ></a>
                  </div>
                </li>

                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-phone" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Phone</h3>
                    <p class="asd-contact__value">
                      <span class="asd-contact__sub">General Enquiries</span>
                      <a href="tel:+919876543210">+91 98765 43210</a>
                    </p>
                    <p class="asd-contact__value">
                      <span class="asd-contact__sub"
                        >Castings &amp; Model Registrations</span
                      >
                      <a href="tel:+919847011220">+91 98470 11220</a>
                    </p>
                  </div>
                </li>

                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-mail" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Email</h3>
                    <p class="asd-contact__value">
                      <span class="asd-contact__sub">General</span>
                      <a href="mailto:info@idfr.example">info@idfr.example</a>
                    </p>
                    <p class="asd-contact__value">
                      <span class="asd-contact__sub"
                        >Designers, Brands &amp; Sponsorships</span
                      >
                      <a href="mailto:partners@idfr.example"
                        >partners@idfr.example</a
                      >
                    </p>
                  </div>
                </li>
              </ul>

              <p class="asd-contact__note">
                Fashion Show Season 4 runway auditions are open in Kozhikode —
                bring a recent photo and register online to book your slot.
              </p>
            </section>

            <section
              class="profile__details asd-contact__form"
              aria-labelledby="asdEnquiryTitle"
              data-reveal="up"
              data-delay="100"
            >
              <h2 class="profile__heading" id="asdEnquiryTitle">
                Send an <em>Enquiry</em>
              </h2>
              <p class="profile__lead">
                Fields marked <span aria-hidden="true">*</span
                ><span class="visually-hidden">with an asterisk</span> are
                required.
              </p>

              <form class="profile-form" id="asdEnquiryForm" action="{{ route('idfr.contact.submit') }}" method="POST" novalidate>
                @csrf
                <div class="field">
                  <label for="enqName"
                    >Name <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="text"
                    id="enqName"
                    name="name"
                    autocomplete="name"
                    required
                    minlength="2"
                    placeholder="Your full name"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="enqPhone"
                    >Phone Number <span aria-hidden="true">*</span></label
                  >
                  <input
                    type="tel"
                    id="enqPhone"
                    name="phone"
                    autocomplete="tel"
                    required
                    pattern="[0-9+\s\-]{8,16}"
                    placeholder="+91 98765 43210"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="enqEmail"
                    >Email Address
                    <span class="field__note">Optional</span></label
                  >
                  <input
                    type="email"
                    id="enqEmail"
                    name="email"
                    autocomplete="email"
                    placeholder="you@example.com"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field">
                  <label for="enqSubject"
                    >Subject <span class="field__note">Optional</span></label
                  >
                  <input
                    type="text"
                    id="enqSubject"
                    name="subject"
                    maxlength="120"
                    placeholder="e.g. Season 4 runway auditions"
                  />
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="field profile-form__full">
                  <label for="enqMessage">Message</label>
                  <textarea
                    id="enqMessage"
                    name="message"
                    rows="5"
                    maxlength="1000"
                    placeholder="Tell us about your enquiry — model registration, casting, designer showcase or sponsorship"
                  ></textarea>
                  <span class="field__error" aria-live="polite"></span>
                </div>

                <div class="profile-form__actions profile-form__full">
                  <button class="btn btn--gold" type="submit">
                    <span>Submit</span
                    ><svg class="icon" aria-hidden="true">
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