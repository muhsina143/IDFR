@extends('layouts.asdreams')

@section('title', 'About ASDreamStudio | Photography &amp; Videography, Kozhikode')
@section('description', 'About ASDreamStudio, the photography and videography partner of IDFR — 32+ years behind the lens, covering every IDFR season, weddings, destination shoots, brand films and live events from our Kozhikode studio.')
@section('canonical', route('asd.about'))

@section('content')
<main id="main">
      <!-- ============ ABOUT ============ -->
      <section
        class="asd-about asd-about--page"
        id="asd-about"
        data-spy="about"
        aria-labelledby="asdAboutTitle"
      >
        <div class="asd-banner">
          <img
            class="photo asd-banner__img"
            src="{{ asset('images/asdreams/ab2.png') }}"
            alt=""
            width="1800"
            height="1200"
            loading="lazy"
          />
          <!-- <img
            class="photo asd-banner__img"
            src="{{ asset('images/asdreams/about-banner.jpg') }}"
            alt=""
            width="1800"
            height="1200"
            loading="lazy"
          /> -->
          <div class="asd-banner__content" data-reveal="up">
            <h2 class="asd-banner__title" id="asdAboutTitle">About Us</h2>
            <nav class="breadcrumb asd-banner__crumbs" aria-label="Breadcrumb">
              <ol>
                <li><a href="{{ route('idfr.home') }}">Home</a></li>
                <li><a href="{{ route('idfr.home') }}#partners">Partners</a></li>
                <li aria-current="page">ASDreamStudio</li>
              </ol>
            </nav>
          </div>
        </div>

        <div class="container asd-about__grid">
          <div class="asd-about__content">
            <div class="asd-about__intro" data-reveal="up">
              <button
                class="asd-about__video"
                type="button"
                data-asd-reel
                aria-label="Watch how we work — play the ASDreamStudio showreel"
              >
                <img
                  class="photo"
                  src="{{ asset('images/asdreams/about-video.jpg') }}"
                  alt=""
                  width="800"
                  height="533"
                  loading="lazy"
                />
                <span class="asd-about__play" aria-hidden="true"
                  ><svg class="icon">
                    <use href="#i-play" /></svg
                ></span>
              </button>
              <p class="asd-about__years">
                <span class="asd-about__num"
                  ><span data-count="32">0</span>+</span
                >
                <span class="asd-about__years-label">Years of Experience</span>
              </p>
            </div>

            <div class="asd-about__how" data-reveal="up" data-delay="100">
              <h3 class="asd-about__how-title">
                How we work?
                <button class="asd-about__watch" type="button" data-asd-reel>
                  Watch The Video
                </button>
              </h3>
              <p>
                From the first mood board to the final colour grade, one
                dedicated team plans, shoots and edits every story — so nothing
                gets lost between the shutter and the screen.
              </p>
            </div>

            <span class="asd-about__rule" aria-hidden="true"></span>

            <div class="asd-about__story" data-reveal="up" data-delay="150">
              <h3 class="asd-about__heading">
                Behind the Lens, <em>Ahead of the Curve</em>
              </h3>
              <p>
                ASDreamStudio has photographed and filmed every IDFR season
                since the very first runway. We pair editorial instinct with
                cinema-grade gear to turn fleeting moments — a designer's final
                bow, a first look, a sunrise vow — into images that last a
                lifetime.
              </p>
              <ul class="asd-about__list">
                <li>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-check" /></svg
                  >Runway, backstage and campaign coverage for every IDFR season
                </li>
                <li>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-check" /></svg
                  >Cinema cameras, drones and a fully equipped lighting studio
                </li>
                <li>
                  <svg class="icon" aria-hidden="true">
                    <use href="#i-check" /></svg
                  >Same-week edits, colour grading and print-ready albums
                </li>
              </ul>
              <div class="asd-about__more" id="asdAboutMore" hidden>
                <p>
                  Our Kozhikode studio houses a 2,400 sq ft cyclorama, a
                  dedicated edit suite and a styling lounge. Beyond IDFR, we
                  travel across India and abroad for weddings, destination
                  shoots, brand films and live events — always with the same
                  small, senior crew.
                </p>
              </div>
              <button
                class="text-link asd-about__readmore"
                type="button"
                aria-expanded="false"
                aria-controls="asdAboutMore"
              >
                <span>Read More</span
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-arrow-right" />
                </svg>
              </button>
            </div>
          </div>

          <figure class="asd-about__media" data-reveal="clip">
            <img
              class="photo"
              src="{{ asset('images/asdreams/about-studio.jpg') }}"
              alt="Camera ready for a night shoot"
              width="900"
              height="1350"
              loading="lazy"
            />
            <figcaption class="asd-about__tag">
              <span>Studio 01</span>Kozhikode, Kerala
            </figcaption>
          </figure>
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