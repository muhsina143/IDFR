@extends('layouts.asdreams')

@section('title', 'Contact ASDreamStudio | Photography &amp; Videography, Kozhikode')
@section('description', 'Contact ASDreamStudio, the photography and videography partner of IDFR. Studio address, phone, email, working hours and an enquiry form for weddings, fashion shoots, events and films.')
@section('canonical', route('asd.contact'))

@section('content')
<main id="main">
      <!-- ============ CONTACT ============ -->
      <section
        class="section asd-contact"
        id="asd-contact-page"
        aria-labelledby="asdContactTitle"
      >
        <div class="container">
          <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
              <li><a href="{{ route('asd.home') }}#home">Home</a></li>
              <li><a href="{{ route('asd.home') }}">ASDreamStudio</a></li>
              <li aria-current="page">Contact</li>
            </ol>
          </nav>

          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">Get in Touch</p>
              <h1 class="section-title" id="asdContactTitle">
                Let’s create <em>together</em>
              </h1>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Planning a wedding, a fashion shoot or a brand film? Share a few
              details and our team will get back to you within one working day.
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
                Visit the studio, call or write to us — we’d love to hear your
                story.
              </p>

              <ul class="asd-contact__list">
                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-pin" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Studio Address</h3>
                    <address class="asd-contact__value">
                      {!! nl2br(e($contact->studio_address)) !!}
                    </address>
                    @if($contact->maps_url)
                      <a
                        class="text-link asd-contact__link"
                        href="{{ $contact->maps_url }}"
                        target="_blank"
                        rel="noopener"
                        ><span>Get Directions</span
                        ><svg class="icon" aria-hidden="true">
                          <use href="#i-arrow-right" /></svg
                      ></a>
                    @endif
                  </div>
                </li>

                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-phone" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Phone</h3>
                    @if($contact->studio_phone)
                      <p class="asd-contact__value">
                        <span class="asd-contact__sub">Studio</span>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact->studio_phone) }}">{{ $contact->studio_phone }}</a>
                      </p>
                    @endif
                    @if($contact->bookings_phone)
                      <p class="asd-contact__value">
                        <span class="asd-contact__sub">Bookings &amp; WhatsApp</span>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact->bookings_phone) }}">{{ $contact->bookings_phone }}</a>
                      </p>
                    @endif
                  </div>
                </li>

                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-mail" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Email</h3>
                    @if($contact->general_email)
                      <p class="asd-contact__value">
                        <span class="asd-contact__sub">General</span>
                        <a href="mailto:{{ $contact->general_email }}">{{ $contact->general_email }}</a>
                      </p>
                    @endif
                    @if($contact->bookings_email)
                      <p class="asd-contact__value">
                        <span class="asd-contact__sub">Bookings</span>
                        <a href="mailto:{{ $contact->bookings_email }}">{{ $contact->bookings_email }}</a>
                      </p>
                    @endif
                  </div>
                </li>

                <li class="asd-contact__item">
                  <span class="asd-contact__icon" aria-hidden="true"
                    ><svg class="icon">
                      <use href="#i-clock" /></svg
                  ></span>
                  <div class="asd-contact__body">
                    <h3 class="asd-contact__label">Working Hours</h3>
                    <dl class="asd-contact__hours">
                      @foreach($contact->working_hours ?? [] as $wh)
                        <div>
                          <dt>{{ $wh['days'] ?? '' }}</dt>
                          <dd>{{ $wh['hours'] ?? '' }}</dd>
                        </div>
                      @endforeach
                    </dl>
                  </div>
                </li>
              </ul>

              @if($contact->note)
                <p class="asd-contact__note">
                  {{ $contact->note }}
                </p>
              @endif
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
                    value="{{ old('name') }}"
                  />
                  <span class="field__error" aria-live="polite">
                    @error('name') {{ $message }} @enderror
                  </span>
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
                    pattern="[0-9+\s\-]{8,25}"
                    placeholder="+91 98765 43210"
                    value="{{ old('phone') }}"
                  />
                  <span class="field__error" aria-live="polite">
                    @error('phone') {{ $message }} @enderror
                  </span>
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
                    value="{{ old('email') }}"
                  />
                  <span class="field__error" aria-live="polite">
                    @error('email') {{ $message }} @enderror
                  </span>
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
                    placeholder="e.g. Wedding film, December 2026"
                    value="{{ old('subject') }}"
                  />
                  <span class="field__error" aria-live="polite">
                    @error('subject') {{ $message }} @enderror
                  </span>
                </div>

                <div class="field profile-form__full">
                  <label for="enqMessage">Message <span aria-hidden="true">*</span></label>
                  <textarea
                    id="enqMessage"
                    name="message"
                    rows="5"
                    maxlength="1500"
                    required
                    placeholder="Tell us about your date, location and the story you want to tell"
                  >{{ old('message') }}</textarea>
                  <span class="field__error" aria-live="polite">
                    @error('message') {{ $message }} @enderror
                  </span>
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
      <h2 class="info__title" id="infoModalTitle">Season <em>4</em></h2>
      <p class="info__intro">
        Three runway nights celebrating emerging designers, established names
        and the next generation of Indian modelling talent. Season 4 brings
        couture, resort, menswear and handloom collections to one stage in
        Kozhikode.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Dates</dt>
          <dd>18 – 20 Dec 2026</dd>
        </div>
        <div>
          <dt>Venue</dt>
          <dd>IDFR Fashion House</dd>
        </div>
        <div>
          <dt>Designers</dt>
          <dd>24 Labels</dd>
        </div>
        <div>
          <dt>Models</dt>
          <dd>60+ Walking</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">18 December · 7:00 PM</p>
          <h3 class="info__item-name">Night One — New Faces</h3>
          <p class="info__item-desc">
            Debut collections walked by models from the Season 4 open auditions.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">19 December · 7:00 PM</p>
          <h3 class="info__item-name">Night Two — Heritage &amp; Handloom</h3>
          <p class="info__item-desc">
            Kerala handloom, Kanjeevaram and heritage textiles reimagined for
            today.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">20 December · 7:30 PM</p>
          <h3 class="info__item-name">Night Three — The Grand Finale</h3>
          <p class="info__item-desc">
            Couture showcases, awards and the closing walk of Season 4.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <a class="btn btn--gold" href="{{ route('idfr.tickets') }}"
          ><span>Reserve Your Seat</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" /></svg
        ></a>
        <button
          class="btn btn--ghost"
          type="button"
          data-auth="register"
          data-role="fresher"
        >
          <span>Register to Walk</span
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
    <template id="program-modelling-academy">
      <p class="info__label">IDFR Programs</p>
      <h2 class="info__title" id="infoModalTitle">
        Modelling <em>Academy</em>
      </h2>
      <p class="info__intro">
        Structured training led by working runway coaches, photographers and
        casting directors. Every graduate receives a professional portfolio
        shoot and an IDFR profile listing.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Batches</dt>
          <dd>Weekday & Weekend</dd>
        </div>
        <div>
          <dt>Location</dt>
          <dd>Kozhikode & Kochi</dd>
        </div>
        <div>
          <dt>Next Intake</dt>
          <dd>5 Jan 2027</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">8 Weeks · Beginners</p>
          <h3 class="info__item-name">Runway Foundation</h3>
          <p class="info__item-desc">
            Posture, walk technique, turns and runway etiquette.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">4 Weeks · All levels</p>
          <h3 class="info__item-name">Portfolio &amp; Posing Masterclass</h3>
          <p class="info__item-desc">
            Studio and outdoor posing with a professional portfolio shoot.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">6 Weeks · All levels</p>
          <h3 class="info__item-name">
            Grooming &amp; Personality Development
          </h3>
          <p class="info__item-desc">
            Skincare, styling, communication and audition confidence.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">12 Weeks · Advanced</p>
          <h3 class="info__item-name">Professional Model Track</h3>
          <p class="info__item-desc">
            Complete programme including a guaranteed IDFR Season 5 audition.
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