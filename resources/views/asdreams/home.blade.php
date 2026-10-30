@extends('layouts.asdreams')

@section('title', 'ASDreamStudio | Photography &amp; Videography Partner of IDFR')
@section('description', 'ASDreamStudio is the official photography and videography partner of IDFR — weddings, fashion shows, destination shoots, portraits, events and films.')
@section('canonical', route('asd.home'))
@section('preload')
<link rel="preload" as="image" href="{{ asset('images/asdreams/hero-01.jpg') }}" />
@endsection

@section('content')
<main id="main">
      <!-- ============ HERO ============ -->
      <section
        class="asd-hero"
        id="asd-home"
        aria-roledescription="carousel"
        aria-label="ASDreamStudio featured work"
      >
        <div class="asd-hero__inner">
          <h1 class="visually-hidden">
            ASDreamStudio — Photography &amp; Videography Partner of IDFR
          </h1>
          <p class="asd-hero__badge">
            Official Photography &amp; Videography Partner of IDFR
          </p>

          <div class="asd-hero__stage">
            <p class="asd-hero__type asd-hero__type--solid">
              <span class="asd-hero__word asd-hero__word--top"
                >Photography</span
              >
              <span class="asd-hero__word asd-hero__word--bottom"
                >Capturing <em>Moments</em></span
              >
            </p>

            <div class="asd-hero__slides" id="asdSlides"></div>

            <div class="asd-hero__frame" id="asdFrame" aria-hidden="true">
              <span class="asd-hero__corner asd-hero__corner--tl"></span>
              <span class="asd-hero__corner asd-hero__corner--tr"></span>
              <span class="asd-hero__corner asd-hero__corner--bl"></span>
              <span class="asd-hero__corner asd-hero__corner--br"></span>
              <span class="asd-hero__focus"></span>
              <span class="asd-hero__readout asd-hero__readout--top"
                ><i></i>REC</span
              >
              <span class="asd-hero__readout asd-hero__readout--bottom"
                >1/250 &nbsp; f2.8 &nbsp; ISO 100</span
              >
              <span class="asd-hero__flash"></span>
            </div>

            <p
              class="asd-hero__type asd-hero__type--outline"
              aria-hidden="true"
            >
              <span class="asd-hero__word asd-hero__word--top"
                >Photography</span
              >
              <span class="asd-hero__word asd-hero__word--bottom"
                >Capturing <em>Moments</em></span
              >
            </p>

            <button
              class="asd-hero__arrow asd-hero__arrow--prev"
              type="button"
              id="asdPrev"
              aria-label="Previous slide"
            >
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-left" />
              </svg>
            </button>
            <button
              class="asd-hero__arrow asd-hero__arrow--next"
              type="button"
              id="asdNext"
              aria-label="Next slide"
            >
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" />
              </svg>
            </button>
          </div>

          <div class="asd-hero__bar">
            <div class="asd-hero__caption" id="asdCaption" aria-live="off">
              <p class="asd-hero__meta" id="asdCaptionMeta"></p>
              <p class="asd-hero__title" id="asdCaptionTitle"></p>
            </div>
            <div class="asd-hero__controls">
              <p class="asd-hero__count">
                <span class="asd-hero__current" id="asdCurrent">01</span> /
                <span id="asdTotal">07</span>
              </p>
              <span class="asd-hero__progress" aria-hidden="true"
                ><span id="asdProgress"></span
              ></span>
              <button
                class="asd-hero__pause"
                type="button"
                id="asdPause"
                aria-label="Pause slideshow"
              >
                <svg class="icon" aria-hidden="true">
                  <use href="#i-pause" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- ============ BRANDS ============ -->
      <section
        class="section asd-brands"
        id="asd-brands"
        aria-labelledby="asdBrandsTitle"
      >
        <div class="container">
          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">One Creative Family</p>
              <h2 class="section-title" id="asdBrandsTitle">
                Our <em>Brands</em>
              </h2>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Photography, runway, model training and beauty education — four
              houses working together to launch careers in fashion.
            </p>
          </div>

          <ul class="asd-brands__grid">
            <li class="asd-brand" data-reveal="up">
              <img
                class="photo asd-brand__img"
                src="{{ asset('images/asdreams/brands/asdreamstudio.jpg') }}"
                alt="Photographer focusing a camera through flowers"
                width="900"
                height="1350"
                loading="lazy"
              />
              <div class="asd-brand__body">
                <div class="asd-brand__logo">
                  <img
                    src="{{ asset('images/asdreams/asd-logo.png') }}"
                    alt=""
                    width="666"
                    height="470"
                    loading="lazy"
                  />
                </div>
                <h3 class="asd-brand__name"><span>AS</span>DreamStudio</h3>
                <p class="asd-brand__tagline">Photography &amp; Videography</p>
                <span class="asd-brand__rule" aria-hidden="true"></span>
                <ul class="asd-brand__tags">
                  <li>Fashion</li>
                  <li>Wedding</li>
                  <li>Events</li>
                  <li>Commercial</li>
                  <li>Products</li>
                  <li>Studio Rental</li>
                </ul>
                <a
                  class="btn btn--ghost btn--sm asd-brand__btn"
                  href="{{ route('asd.home') }}"
                  aria-label="Explore ASDreamStudio portfolio"
                  ><span>Explore</span
                  ><svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" /></svg
                ></a>
              </div>
            </li>

            <li class="asd-brand" data-reveal="up" data-delay="100">
              <img
                class="photo asd-brand__img"
                src="{{ asset('images/hero/hero-04.jpg') }}"
                alt="Model in a flowing gown on the runway"
                width="1100"
                height="1321"
                loading="lazy"
              />
              <div class="asd-brand__body">
                <div class="asd-brand__logo asd-brand__logo--wide">
                  <img
                    src="{{ asset('images/logo/idfr-mark.png') }}"
                    alt="IDFR"
                    width="474"
                    height="220"
                    loading="lazy"
                  />
                </div>
                <h3 class="asd-brand__name asd-brand__name--caps">
                  Indian Designer Fashion Runway
                </h3>
                <span class="asd-brand__rule" aria-hidden="true"></span>
                <ul class="asd-brand__tags">
                  <li>Designers</li>
                  <li>Models</li>
                  <li>Fashion Shows</li>
                  <li>Brands</li>
                  <li>Events</li>
                  <li>Networking</li>
                </ul>
                <a
                  class="btn btn--ghost btn--sm asd-brand__btn"
                  href="{{ route('idfr.home') }}"
                  aria-label="Explore IDFR"
                  ><span>Explore</span
                  ><svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" /></svg
                ></a>
              </div>
            </li>

            <li class="asd-brand" data-reveal="up" data-delay="200">
              <img
                class="photo asd-brand__img"
                src="{{ asset('images/models/model-01.jpg') }}"
                alt="Model posing in a black outfit"
                width="700"
                height="1050"
                loading="lazy"
              />
              <div class="asd-brand__body">
                <div class="asd-brand__logo">
                  <img
                    src="{{ asset('images/asdreams/brands/athuls-academy-logo.png') }}"
                    alt=""
                    width="110"
                    height="177"
                    loading="lazy"
                  />
                </div>
                <h3 class="asd-brand__name">Athul’s Academy</h3>
                <p class="asd-brand__tagline">Modelling &amp; Grooming</p>
                <span class="asd-brand__rule" aria-hidden="true"></span>
                <ul class="asd-brand__tags">
                  <li>Model Training</li>
                  <li>Grooming</li>
                  <li>Portfolio</li>
                  <li>Casting</li>
                  <li>Industry Opportunities</li>
                </ul>
                <a
                  class="btn btn--ghost btn--sm asd-brand__btn"
                  href="https://athulsacademy.com/"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Explore Athul’s Academy"
                  ><span>Explore</span
                  ><svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" /></svg
                ></a>
              </div>
            </li>

            <li class="asd-brand" data-reveal="up" data-delay="300">
              <img
                class="photo asd-brand__img"
                src="{{ asset('images/asdreams/brands/lakme-academy.jpg') }}"
                alt="Makeup artist applying eyeshadow"
                width="900"
                height="1300"
                loading="lazy"
              />
              <div class="asd-brand__body">
                <div class="asd-brand__logo">
                  <span
                    class="asd-lakme"
                    role="img"
                    aria-label="Lakmé Academy, Vidyaranyapura"
                  >
                    <span class="asd-lakme__word" aria-hidden="true"
                      >Lakmé</span
                    >
                    <span class="asd-lakme__sub" aria-hidden="true"
                      >Academy</span
                    >
                    <span class="asd-lakme__place" aria-hidden="true"
                      >Vidyaranyapura</span
                    >
                  </span>
                </div>
                <h3 class="visually-hidden">Lakmé Academy</h3>
                <p class="asd-brand__tagline">
                  <span>Makeup &bull; Hair</span> <span>Nails &bull; Skin</span>
                </p>
                <span class="asd-brand__rule" aria-hidden="true"></span>
                <ul class="asd-brand__tags">
                  <li>Professional Courses</li>
                  <li>Workshops</li>
                  <li>Practical Training</li>
                  <li>Career Development</li>
                </ul>
                <a
                  class="btn btn--ghost btn--sm asd-brand__btn"
                  href="https://www.lakme-academy.com/"
                  target="_blank"
                  rel="noopener noreferrer"
                  aria-label="Explore Lakmé Academy"
                  ><span>Explore</span
                  ><svg class="icon" aria-hidden="true">
                    <use href="#i-arrow-right" /></svg
                ></a>
              </div>
            </li>
          </ul>
        </div>
      </section>

      <!-- ============ ABOUT ============ -->
      <section class="asd-about" id="asd-about" aria-labelledby="asdAboutTitle">
        <div class="asd-banner">
          <img
            class="photo asd-banner__img"
            src="{{ asset('images/asdreams/hero-08.jpg') }}"
            alt=""
            width="1800"
            height="1200"
            loading="lazy"
          />
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

      <!-- ============ GALLERY ============ -->
      <section
        class="section asd-gallery"
        id="asd-gallery"
        aria-labelledby="asdGalleryTitle"
      >
        <div class="container">
          <div class="section-head">
            <div data-reveal="up">
              <p class="section-label">Our Portfolio</p>
              <h2 class="section-title" id="asdGalleryTitle">
                Every story, <em>framed</em>
              </h2>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="150">
              Seven ways we tell stories. Open a category to explore more of our
              work.
            </p>
          </div>
        </div>

        <div
          class="asd-carousel"
          aria-roledescription="carousel"
          aria-label="Photography categories"
          data-reveal="up"
        >
          <button
            class="asd-carousel__nav asd-carousel__nav--prev"
            type="button"
            aria-label="Previous categories"
            aria-controls="asdTrack"
          >
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-left" />
            </svg>
          </button>
          <ul class="asd-carousel__track" id="asdTrack"></ul>
          <button
            class="asd-carousel__nav asd-carousel__nav--next"
            type="button"
            aria-label="Next categories"
            aria-controls="asdTrack"
          >
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
        </div>

        <div class="container asd-carousel__foot" aria-hidden="true">
          <span class="asd-carousel__progress"
            ><span id="asdTrackProgress"></span
          ></span>
        </div>
      </section>

      <!-- ============ CLIENTS ============ -->
      <section
        class="section section--light clients"
        id="clients"
        aria-labelledby="clientsTitle"
      >
        <div class="container">
          <div class="section-head">
            <div>
              <p class="section-label" data-reveal="up">07 — Trusted By</p>
              <h2
                class="section-title"
                id="clientsTitle"
                data-reveal="up"
                data-delay="100"
              >
                Our
                <em>Clients</em>
              </h2>
            </div>
            <p class="section-intro" data-reveal="up" data-delay="200">
              Fashion houses, publications and agencies who cast their campaigns
              with IDFR talent. Select a client to read their story.
            </p>
          </div>

          <div class="clients__layout">
            <article
              class="client-feature"
              id="clientFeature"
              data-reveal="clip"
              aria-live="polite"
            >
              <div class="client-feature__media">
                <img
                  class="photo"
                  id="clientFeatureImg"
                  src="{{ asset('images/clients/client-featured.jpg') }}"
                  alt=""
                  loading="lazy"
                  width="1000"
                  height="1250"
                />
              </div>
              <div class="client-feature__body">
                <div class="client-feature__logo" id="clientFeatureLogo"></div>
                <blockquote
                  class="client-feature__quote"
                  id="clientFeatureQuote"
                ></blockquote>
                <p class="client-feature__person" id="clientFeaturePerson"></p>
              </div>
            </article>
            <div
              class="clients__grid"
              id="clientsGrid"
              role="group"
              aria-label="Select a client"
            ></div>
          </div>
        </div>
        <div class="logo-strip" aria-hidden="true">
          <div class="logo-strip__track" id="clientStrip"></div>
        </div>
      </section>
    </main>
@endsection
@section('page_content_templates')
<!-- ============ PAGE CONTENT ============
         Content for the sections built by js/script.js. <template> content is not displayed;
         the script reads it and renders each section. Edit text, images and options here. -->

    <!-- Hero capture slider (#asdSlides): one figure per slide. figcaption = caption title, data-meta = caption line. -->
    <template id="asdHeroData">
      <figure data-meta="Portrait Session · Kozhikode">
        <img
          src="{{ asset('images/asdreams/hero-01.jpg') }}"
          alt="Photographer framing a shot through her camera"
        />
        <figcaption>Behind the Viewfinder</figcaption>
      </figure>
      <figure data-meta="Studio Archive">
        <img
          src="{{ asset('images/asdreams/hero-02.jpg') }}"
          alt="Vintage camera resting beside instant photo prints"
        />
        <figcaption>Prints &amp; Polaroids</figcaption>
      </figure>
      <figure data-meta="Wedding Story · Kochi">
        <img
          src="{{ asset('images/asdreams/hero-03.jpg') }}"
          alt="Bride and groom sharing a quiet moment"
        />
        <figcaption>The First Look</figcaption>
      </figure>
      <figure data-meta="Fashion Editorial · Studio 01">
        <img
          src="{{ asset('images/asdreams/hero-08.jpg') }}"
          alt="Photography studio set with softboxes and a white backdrop"
        />
        <figcaption>Studio Light</figcaption>
      </figure>
      <figure data-meta="Documentary · Bengaluru">
        <img
          src="{{ asset('images/asdreams/hero-05.jpg') }}"
          alt="Photographer shooting on a busy city street"
        />
        <figcaption>Street Frames</figcaption>
      </figure>
      <figure data-meta="Wedding · Munnar">
        <img
          src="{{ asset('images/asdreams/hero-06.jpg') }}"
          alt="Wedding couple embracing outdoors"
        />
        <figcaption>Golden Vows</figcaption>
      </figure>
      <figure data-meta="Destination · Wayanad">
        <img
          src="{{ asset('images/asdreams/hero-07.jpg') }}"
          alt="Photographer on a mountain peak above a sea of clouds"
        />
        <figcaption>Above the Clouds</figcaption>
      </figure>
    </template>

    <!-- Portfolio categories: the carousel on asdreams.html and the gallery tabs on asd_gallery.html. A card's cover is
         its first photo; data-tab overrides the gallery tab label. width/height must be each file's real pixel size. -->
    <template id="asdCategoriesData">
      <section data-id="weddings" data-tab="Wedding">
        <h3>Weddings</h3>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/wedding-01.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>Vows at Golden Hour</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/wedding-02.jpg') }}"
            alt=""
            width="1000"
            height="668"
          />
          <figcaption>The Bridal Walk</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/wedding-03.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>Under the Palms</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/wedding-04.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>Family Blessings</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/wedding-05.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Bouquet in Candlelight</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/wedding-06.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>Together, Finally</figcaption>
        </figure>
      </section>
      <section data-id="engagements">
        <h3>Engagements</h3>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/engagement-01.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Autumn Promise</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/engagement-02.jpg') }}"
            alt=""
            width="1000"
            height="668"
          />
          <figcaption>The Ring</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/engagement-03.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Hand in Hand</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/engagement-04.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Roses &amp; Rings</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/engagement-05.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Garden Ceremony</figcaption>
        </figure>
      </section>
      <section data-id="destinations">
        <h3>Destinations</h3>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/destination-01.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Black Sand Elopement</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/destination-02.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Desert Light</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/destination-03.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Lakeside Morning</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/destination-04.jpg') }}"
            alt=""
            width="1000"
            height="707"
          />
          <figcaption>Paris After Dark</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/destination-05.jpg') }}"
            alt=""
            width="1000"
            height="665"
          />
          <figcaption>Shoreline Sunset</figcaption>
        </figure>
      </section>
      <section data-id="fashion-shows">
        <h3>Fashion Shows</h3>
        <figure>
          <img
            src="{{ asset('images/gallery/s3-01.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>IDFR Season 3 — Finale Walk</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/gallery/s2-02.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>IDFR Season 2 — Couture Line</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/gallery/s1-03.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>IDFR Season 1 — Runway Debut</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/gallery/s3-04.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>IDFR Season 3 — Backstage</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/gallery/s2-05.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>IDFR Season 2 — Designer Bow</figcaption>
        </figure>
      </section>
      <section data-id="portraits">
        <h3>Portraits</h3>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/portrait-01.jpg') }}"
            alt=""
            width="1000"
            height="1250"
          />
          <figcaption>Quiet Strength</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/portrait-02.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Natural Light</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/portrait-03.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>The Smile</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/portrait-04.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Studio Glow</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/portrait-05.jpg') }}"
            alt=""
            width="1000"
            height="1500"
          />
          <figcaption>Blue Hour</figcaption>
        </figure>
      </section>
      <section data-id="events">
        <h3>Events</h3>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/event-01.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Concert Night</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/event-02.jpg') }}"
            alt=""
            width="1000"
            height="750"
          />
          <figcaption>Front Row</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/event-03.jpg') }}"
            alt=""
            width="1000"
            height="541"
          />
          <figcaption>Grand Hall</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/event-04.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Festival Lights</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/event-05.jpg') }}"
            alt=""
            width="1000"
            height="714"
          />
          <figcaption>Keynote Moments</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/event-06.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Conference Day</figcaption>
        </figure>
      </section>
      <section data-id="films">
        <h3>Films</h3>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/film-01.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Edit Suite</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/film-02.jpg') }}"
            alt=""
            width="1000"
            height="667"
          />
          <figcaption>Projection Room</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/film-03.jpg') }}"
            alt=""
            width="1000"
            height="563"
          />
          <figcaption>Rolling Camera</figcaption>
        </figure>
        <figure>
          <img
            src="{{ asset('images/asdreams/gallery/film-04.jpg') }}"
            alt=""
            width="1000"
            height="1250"
          />
          <figcaption>Cinema Glass</figcaption>
        </figure>
      </section>
    </template>

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
    <template id="program-asd-services">
      <p class="info__label">ASDreamStudio</p>
      <h2 class="info__title" id="infoModalTitle">Our <em>Services</em></h2>
      <p class="info__intro">
        One senior crew for stills and motion — planned, shot, graded and
        delivered in-house, from intimate portraits to full runway productions.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Coverage</dt>
          <dd>India &amp; Abroad</dd>
        </div>
        <div>
          <dt>Previews</dt>
          <dd>Within 7 Days</dd>
        </div>
        <div>
          <dt>Crew</dt>
          <dd>Photo + Film</dd>
        </div>
      </dl>
      <div class="info__list">
        <div class="info__item">
          <span class="info__item-num">01</span>
          <p class="info__item-meta">Photography</p>
          <h3 class="info__item-name">Fashion &amp; Editorial</h3>
          <p class="info__item-desc">
            Lookbooks, campaigns, model portfolios and runway coverage.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">02</span>
          <p class="info__item-meta">Photo + Film</p>
          <h3 class="info__item-name">Weddings &amp; Engagements</h3>
          <p class="info__item-desc">
            Candid storytelling, cinematic films and handcrafted albums.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">03</span>
          <p class="info__item-meta">Photo + Film</p>
          <h3 class="info__item-name">Events &amp; Fashion Shows</h3>
          <p class="info__item-desc">
            Multi-camera coverage, backstage moments and same-day highlights.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">04</span>
          <p class="info__item-meta">Photography</p>
          <h3 class="info__item-name">Commercial &amp; Product</h3>
          <p class="info__item-desc">
            E-commerce, catalogue and brand imagery with studio lighting.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">05</span>
          <p class="info__item-meta">Videography</p>
          <h3 class="info__item-name">Films &amp; Reels</h3>
          <p class="info__item-desc">
            Brand films, music videos and social-first vertical reels.
          </p>
        </div>
        <div class="info__item">
          <span class="info__item-num">06</span>
          <p class="info__item-meta">Kozhikode</p>
          <h3 class="info__item-name">Studio Rental</h3>
          <p class="info__item-desc">
            Cyclorama, lighting kit and styling lounge by the hour or day.
          </p>
        </div>
      </div>
      <div class="info__actions">
        <button class="btn btn--gold" type="button" data-program="asd-contact">
          <span>Contact Us</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" />
          </svg>
        </button>
        <button class="btn btn--ghost" type="button" data-close>
          <span>Close</span>
        </button>
      </div>
    </template>
    <template id="program-asd-contact">
      <p class="info__label">Get in Touch</p>
      <h2 class="info__title" id="infoModalTitle">
        Contact <em>ASDreamStudio</em>
      </h2>
      <p class="info__intro">
        Tell us your date, location and the story you want to tell — we reply
        within one working day.
      </p>
      <dl class="info__facts">
        <div>
          <dt>Studio</dt>
          <dd>Kozhikode, Kerala</dd>
        </div>
        <div>
          <dt>Phone</dt>
          <dd><a href="tel:+919876500123">+91 98765 00123</a></dd>
        </div>
        <div>
          <dt>Email</dt>
          <dd>
            <a href="mailto:hello@asdreamstudio.example"
              >hello@asdreamstudio.example</a
            >
          </dd>
        </div>
        <div>
          <dt>Hours</dt>
          <dd>Mon – Sat · 10 AM – 7 PM</dd>
        </div>
      </dl>
      <div class="info__actions">
        <a
          class="btn btn--gold"
          href="mailto:hello@asdreamstudio.example?subject=Shoot%20enquiry"
          ><span>Email the Studio</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" /></svg
        ></a>
        <a class="btn btn--ghost" href="tel:+919876500123"
          ><span>Call Now</span
          ><svg class="icon" aria-hidden="true">
            <use href="#i-arrow-right" /></svg
        ></a>
      </div>
    </template>


    <!-- Clients (#clientsGrid + featured testimonial). The logo strip shows every client, then every sponsor except the title sponsor. -->
    <template id="clientsData">
      <article>
        <h3>Maison Kerala</h3>
        <p data-industry>Couture House</p>
        <img src="{{ asset('images/clients/client-featured.jpg') }}" alt="" />
        <span
          data-logo="arch"
          data-mark="M"
          data-word="MAISON"
          data-sub="KERALA"
        ></span>
        <blockquote>
          IDFR helped us cast our entire festive campaign in a single week. The
          talent was professional, prepared and camera-ready.
        </blockquote>
        <p data-person>Neha Varghese</p>
        <p data-role>Creative Director</p>
      </article>
      <article>
        <h3>Vasa Bridal</h3>
        <p data-industry>Bridal Wear</p>
        <img src="{{ asset('images/clients/client-01.jpg') }}" alt="" />
        <span
          data-logo="diamond"
          data-mark="V"
          data-word="VASA"
          data-sub="BRIDAL"
        ></span>
        <blockquote>
          Every bride in our lookbook was an IDFR model. Their portfolios made
          shortlisting effortless.
        </blockquote>
        <p data-person>Lakshmi Prasad</p>
        <p data-role>Founder</p>
      </article>
      <article>
        <h3>Urban Thread</h3>
        <p data-industry>Streetwear</p>
        <img src="{{ asset('images/clients/client-02.jpg') }}" alt="" />
        <span
          data-logo="bars"
          data-mark="U"
          data-word="URBAN"
          data-sub="THREAD"
        ></span>
        <blockquote>
          We found fresh, authentic faces for our youth line — exactly the
          energy our brand needed.
        </blockquote>
        <p data-person>Karan Shah</p>
        <p data-role>Brand Manager</p>
      </article>
      <article>
        <h3>Lumen Magazine</h3>
        <p data-industry>Fashion Publication</p>
        <img src="{{ asset('images/gallery/s2-06.jpg') }}" alt="" />
        <span
          data-logo="circle"
          data-mark="L"
          data-word="LUMEN"
          data-sub="MAGAZINE"
        ></span>
        <blockquote>
          Our cover stars from IDFR consistently deliver striking editorial
          work. It is our first stop for new faces.
        </blockquote>
        <p data-person>Rhea Kapoor</p>
        <p data-role>Fashion Editor</p>
      </article>
      <article>
        <h3>Prism Advertising</h3>
        <p data-industry>Advertising Agency</p>
        <img src="{{ asset('images/gallery/s3-04.jpg') }}" alt="" />
        <span
          data-logo="hex"
          data-mark="P"
          data-word="PRISM"
          data-sub="ADVERTISING"
        ></span>
        <blockquote>
          From TV commercials to digital films, IDFR talent has elevated
          campaigns for our biggest accounts.
        </blockquote>
        <p data-person>Siddharth Rao</p>
        <p data-role>Casting Head</p>
      </article>
      <article>
        <h3>Kanji Silks</h3>
        <p data-industry>Heritage Textiles</p>
        <img src="{{ asset('images/partners/partner-03.jpg') }}" alt="" />
        <span
          data-logo="weave"
          data-mark="K"
          data-word="KANJI"
          data-sub="SILKS"
        ></span>
        <blockquote>
          IDFR models carry our heritage silks with grace. Their runway training
          shows in every frame.
        </blockquote>
        <p data-person>Meenakshi Sundaram</p>
        <p data-role>Managing Director</p>
      </article>
      <article>
        <h3>Northstar Footwear</h3>
        <p data-industry>Footwear</p>
        <img src="{{ asset('images/gallery/s2-01.jpg') }}" alt="" />
        <span
          data-logo="star"
          data-mark="N"
          data-word="NORTHSTAR"
          data-sub="FOOTWEAR"
        ></span>
        <blockquote>
          Booking models through IDFR is fast and transparent. We now cast every
          season with them.
        </blockquote>
        <p data-person>Imran Qureshi</p>
        <p data-role>Marketing Lead</p>
      </article>
      <article>
        <h3>Aura Cosmetics</h3>
        <p data-industry>Beauty</p>
        <img src="{{ asset('images/gallery/s2-03.jpg') }}" alt="" />
        <span
          data-logo="drop"
          data-mark="A"
          data-word="AURA"
          data-sub="COSMETICS"
        ></span>
        <blockquote>
          The beauty talent on IDFR is outstanding — flawless skin, great
          expressions and total professionalism.
        </blockquote>
        <p data-person>Fatima Sheikh</p>
        <p data-role>Head of Brand</p>
      </article>
    </template>
@endsection