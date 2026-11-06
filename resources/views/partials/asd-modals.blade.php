<!-- ============ SHOWREEL MODAL ============ -->
<div
  class="modal asd-reel"
  id="asdReel"
  role="dialog"
  aria-modal="true"
  aria-labelledby="asdReelTitle"
  hidden
>
  <div class="modal__backdrop" data-close></div>
  <div class="modal__panel asd-reel__panel">
    <button
      class="modal__close"
      type="button"
      data-close
      aria-label="Close showreel"
    >
      <svg class="icon" aria-hidden="true">
        <use href="#i-close" />
      </svg>
    </button>
    <div class="asd-reel__screen" id="asdReelScreen">
      <!-- Showreel scenes: one per step in #asdReelSteps, shown in this order -->
      <img
        class="photo asd-reel__scene"
        src="{{ asset('images/asdreams/hero-02.jpg') }}"
        alt=""
        loading="lazy"
      />
      <img
        class="photo asd-reel__scene"
        src="{{ asset('images/asdreams/hero-04.jpg') }}"
        alt=""
        loading="lazy"
      />
      <img
        class="photo asd-reel__scene"
        src="{{ asset('images/asdreams/hero-05.jpg') }}"
        alt=""
        loading="lazy"
      />
      <img
        class="photo asd-reel__scene"
        src="{{ asset('images/asdreams/gallery/film-01.jpg') }}"
        alt=""
        loading="lazy"
      />
      <span class="asd-reel__rec" aria-hidden="true"
        ><i></i>Showreel preview</span
      >
      <span class="asd-reel__bar" aria-hidden="true"
        ><span id="asdReelBar"></span
      ></span>
    </div>
    <div class="asd-reel__info">
      <p class="asd-reel__label">How we work</p>
      <h2 class="asd-reel__title" id="asdReelTitle">
        From first frame to <em>final film</em>
      </h2>
      <ol class="asd-reel__steps" id="asdReelSteps">
        <li>
          <span>01</span><strong>Discover</strong>We learn your story, venue
          and mood.
        </li>
        <li>
          <span>02</span><strong>Plan</strong>Shot lists, lighting and crew
          are locked in.
        </li>
        <li>
          <span>03</span><strong>Shoot</strong>Photo and film teams capture
          every moment.
        </li>
        <li>
          <span>04</span><strong>Deliver</strong>Graded films and albums,
          ready to share.
        </li>
      </ol>
    </div>
  </div>
</div>

<!-- ============ OUR BRANDS WINDOW (full screen, opens when the page loads) ============ -->
<!-- Top: banner slider (one figure and one dot per banner; keep #brandsPopTotal in step). Bottom: brand links that mirror the Explore buttons in the Brands section above -->
<div
  class="modal brands-pop"
  id="brandsPopup"
  role="dialog"
  aria-modal="true"
  aria-labelledby="brandsPopupTitle"
  hidden
>
  <div class="modal__panel brands-pop__panel">
    <section
      class="brands-pop__banner"
      aria-roledescription="carousel"
      aria-label="ASDreamStudio highlights"
    >
      <div class="brands-pop__slides" id="brandsPopSlides">
        <figure
          class="brands-pop__slide is-active"
          role="group"
          aria-roledescription="slide"
          aria-label="1 of 7"
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hr2.png') }}"
            alt="Photographer framing a shot through her camera"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta"
              >Portrait Session · Kozhikode</span
            >
            <span class="brands-pop__caption"
              >Behind the <em>Viewfinder</em></span
            >
            <span class="brands-pop__desc"
              >Stories, emotions and timeless moments captured through our
              lens.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=viewfinder"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
        <figure
          class="brands-pop__slide"
          role="group"
          aria-roledescription="slide"
          aria-label="2 of 7"
          aria-hidden="true"
          inert
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hr3.png') }}"
            alt="Vintage camera resting beside instant photo prints"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta">Studio Archive</span>
            <span class="brands-pop__caption"
              >Prints &amp; <em>Polaroids</em></span
            >
            <span class="brands-pop__desc"
              >Every frame printed with care: keepsakes to hold, share and
              treasure.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=polaroids"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
        <figure
          class="brands-pop__slide"
          role="group"
          aria-roledescription="slide"
          aria-label="3 of 7"
          aria-hidden="true"
          inert
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hr4.png') }}"
            alt="Bride and groom sharing a quiet moment"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta">Wedding Story · Kochi</span>
            <span class="brands-pop__caption">The First <em>Look</em></span>
            <span class="brands-pop__desc"
              >Quiet glances and joyful tears, documented the way you felt
              them.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=first-look"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
        <figure
          class="brands-pop__slide"
          role="group"
          aria-roledescription="slide"
          aria-label="4 of 7"
          aria-hidden="true"
          inert
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hero-08.jpg') }}"
            alt="Photography studio set with softboxes and a white backdrop"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta">Fashion Editorial · Studio 01</span>
            <span class="brands-pop__caption">Studio <em>Light</em></span>
            <span class="brands-pop__desc"
              >Editorial portraits and fashion campaigns shaped by light in
              our studio.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=studio-light"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
        <figure
          class="brands-pop__slide"
          role="group"
          aria-roledescription="slide"
          aria-label="5 of 7"
          aria-hidden="true"
          inert
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hero-05.jpg') }}"
            alt="Photographer shooting on a busy city street"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta">Documentary · Bengaluru</span>
            <span class="brands-pop__caption">Street <em>Frames</em></span>
            <span class="brands-pop__desc"
              >Honest, unposed stories found in the rhythm of the city.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=street-frames"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
        <figure
          class="brands-pop__slide"
          role="group"
          aria-roledescription="slide"
          aria-label="6 of 7"
          aria-hidden="true"
          inert
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hr5.png') }}"
            alt="Wedding couple embracing outdoors"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta">Wedding · Munnar</span>
            <span class="brands-pop__caption">Golden <em>Vows</em></span>
            <span class="brands-pop__desc"
              >Weddings told with warmth, from the first ritual to the last
              dance.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=golden-vows"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
        <figure
          class="brands-pop__slide"
          role="group"
          aria-roledescription="slide"
          aria-label="7 of 7"
          aria-hidden="true"
          inert
        >
          <img
            class="brands-pop__img"
            src="{{ asset('images/asdreams/hero-07.jpg') }}"
            alt="Photographer on a mountain peak above a sea of clouds"
            width="1100"
            height="900"
          />
          <figcaption>
            <span class="brands-pop__meta">Destination · Wayanad</span>
            <span class="brands-pop__caption">Above the <em>Clouds</em></span>
            <span class="brands-pop__desc"
              >Destination shoots that travel wherever your story takes
              us.</span
            >
            <a class="brands-pop__cta" href="{{ route('asd.contact') }}?offer=above-clouds"
              >Offer Details
              <svg class="icon" aria-hidden="true">
                <use href="#i-arrow-right" /></svg
            ></a>
          </figcaption>
        </figure>
      </div>
      <button
        class="asd-hero__arrow brands-pop__arrow brands-pop__arrow--prev"
        type="button"
        data-banner-step="-1"
        aria-label="Previous banner"
      >
        <svg class="icon" aria-hidden="true">
          <use href="#i-arrow-left" />
        </svg>
      </button>
      <button
        class="asd-hero__arrow brands-pop__arrow brands-pop__arrow--next"
        type="button"
        data-banner-step="1"
        aria-label="Next banner"
      >
        <svg class="icon" aria-hidden="true">
          <use href="#i-arrow-right" />
        </svg>
      </button>
      <div class="brands-pop__pager">
        <p class="brands-pop__count" aria-hidden="true">
          <span id="brandsPopCurrent">01</span> /
          <span id="brandsPopTotal">07</span>
        </p>
        <div class="brands-pop__dots" id="brandsPopDots">
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="0"
            aria-label="Show banner 1: Behind the Viewfinder"
            aria-current="true"
          ></button>
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="1"
            aria-label="Show banner 2: Prints &amp; Polaroids"
          ></button>
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="2"
            aria-label="Show banner 3: The First Look"
          ></button>
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="3"
            aria-label="Show banner 4: Studio Light"
          ></button>
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="4"
            aria-label="Show banner 5: Street Frames"
          ></button>
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="5"
            aria-label="Show banner 6: Golden Vows"
          ></button>
          <button
            class="brands-pop__dot"
            type="button"
            data-banner-dot="6"
            aria-label="Show banner 7: Above the Clouds"
          ></button>
        </div>
      </div>
      <p
        class="visually-hidden"
        aria-live="polite"
        id="brandsPopStatus"
      ></p>
    </section>
    <section class="brands-pop__brands">
      <!-- Golden smoke & silks -->
      <div class="brands-pop__smoke" aria-hidden="true">
        <svg
          class="brands-pop__smoke-silk brands-pop__smoke-silk--left"
          viewBox="0 -70 1440 400"
          preserveAspectRatio="none"
          focusable="false"
        >
          <use
            href="#brandsSilkL"
            stroke="url(#brandsSmokeGoldL)"
            stroke-width="3"
            filter="url(#brandsSilkBlur)"
            opacity="0.5"
          />
          <use
            href="#brandsSilkL"
            stroke="url(#brandsSmokeGoldL)"
            stroke-width="0.8"
          />
        </svg>
        <svg
          class="brands-pop__smoke-silk brands-pop__smoke-silk--right"
          viewBox="0 -70 1440 400"
          preserveAspectRatio="none"
          focusable="false"
        >
          <use
            href="#brandsSilkR"
            stroke="url(#brandsSmokeGoldR)"
            stroke-width="3"
            filter="url(#brandsSilkBlur)"
            opacity="0.5"
          />
          <use
            href="#brandsSilkR"
            stroke="url(#brandsSmokeGoldR)"
            stroke-width="0.8"
          />
        </svg>
        <svg
          class="brands-pop__smoke-shine"
          viewBox="0 -70 1440 400"
          preserveAspectRatio="none"
          focusable="false"
        >
          <path
            class="brands-pop__shine brands-pop__shine--glow"
            d="M0 66 C 260 40 520 14 760 14 C 1000 14 1220 36 1440 54"
            pathLength="1000"
          />
          <path
            class="brands-pop__shine"
            d="M0 66 C 260 40 520 14 760 14 C 1000 14 1220 36 1440 54"
            pathLength="1000"
          />
          <path
            class="brands-pop__shine brands-pop__shine--back"
            d="M1440 54 C 1220 36 1000 14 760 14 C 520 14 260 40 0 66"
            pathLength="1000"
          />
        </svg>
      </div>
      <div class="brands-pop__head">
        <h2
          class="section-title brands-pop__title"
          id="brandsPopupTitle"
          tabindex="-1"
          data-autofocus
        >
          Our <em>Brands</em>
        </h2>
      </div>
      <ul class="brands-pop__grid">
        <li class="brands-pop__item">
          <a class="brands-pop__link" href="{{ route('asd.home') }}#home">
            <span class="brands-pop__logo">
              <span class="brands-pop__mark">
                <img
                  src="{{ asset('images/asdreams/asd-logo.png') }}"
                  alt=""
                  width="666"
                  height="470"
                />
              </span>
              <span class="asd-brand__name brands-pop__name"
                ><span>AS</span>DreamStudio</span
              >
              <span class="brands-pop__tag"
                >Photography &amp; Videography</span
              >
              <span class="brands-pop__go" aria-hidden="true">
                <svg class="icon"><use href="#i-arrow-right" /></svg>
              </span>
            </span>
          </a>
        </li>
        <li class="brands-pop__item">
          <a
            class="brands-pop__link"
            href="{{ route('idfr.home') }}"
            aria-label="Indian Designer Fashion Runway"
          >
            <span class="brands-pop__logo brands-pop__logo--wide">
              <span class="brands-pop__mark">
                <img
                  src="{{ asset('images/logo/idfr-logo.png') }}"
                  alt="IDFR — Indian Designer Fashion Runway"
                  width="487"
                  height="413"
                />
              </span>
              <span class="brands-pop__go" aria-hidden="true">
                <svg class="icon"><use href="#i-arrow-right" /></svg>
              </span>
            </span>
          </a>
        </li>
        <li class="brands-pop__item">
          <a
            class="brands-pop__link"
            href="{{ route('asd.contact') }}?brand=athuls"
          >
            <span class="brands-pop__logo brands-pop__logo--tall">
              <span class="brands-pop__mark">
                <img
                  src="{{ asset('images/asdreams/brands/athuls-academy-logo.png') }}"
                  alt=""
                  width="901"
                  height="906"
                />
              </span>
              <span class="asd-brand__name brands-pop__name"
                >Athul’s Academy</span
              >
              <span class="brands-pop__tag">Fashion &amp; Modelling</span>
              <span class="brands-pop__go" aria-hidden="true">
                <svg class="icon"><use href="#i-arrow-right" /></svg>
              </span>
            </span>
          </a>
        </li>
        <li class="brands-pop__item">
          <a
            class="brands-pop__link"
            href="{{ route('asd.contact') }}?brand=lakme"
            aria-label="Lakmé Academy"
          >
            <span class="brands-pop__logo brands-pop__logo--lakme">
              <span class="brands-pop__mark">
                <img
                  src="{{ asset('images/asdreams/brands/lakme-academy.jpg') }}"
                  alt="Lakmé Academy, Vidyaranyapura"
                  width="864"
                  height="525"
                />
              </span>
              <span class="brands-pop__tag">Beauty &amp; Wellness</span>
              <span class="brands-pop__go" aria-hidden="true">
                <svg class="icon"><use href="#i-arrow-right" /></svg>
              </span>
            </span>
          </a>
        </li>
      </ul>
    </section>
  </div>
</div>

<!-- ============ BOOK A SHOOT MODAL ============ -->
<div
  class="modal shoot-book"
  id="shootBookModal"
  role="dialog"
  aria-modal="true"
  aria-labelledby="shootBookTitle"
  hidden
>
  <div class="modal__backdrop" data-close></div>
  <div class="modal__panel auth-modal__panel shoot-book__panel">
    <button
      class="modal__close"
      type="button"
      data-close
      aria-label="Close"
    >
      <svg class="icon" aria-hidden="true">
        <use href="#i-close" />
      </svg>
    </button>

    <div data-shoot-form>
      <p class="info__label">ASDreamStudio</p>
      <h2 class="auth-modal__title shoot-book__title" id="shootBookTitle">
        Book a <em>Shoot</em>
      </h2>
      <p class="shoot-book__lead">
        Fields marked <span aria-hidden="true">*</span
        ><span class="visually-hidden">with an asterisk</span> are
        required. Our studio team will contact you to confirm details.
      </p>

      <form class="profile-form" id="shootBookForm" method="POST" action="{{ route('asd.shoot-booking.submit') }}" novalidate>
        @csrf
        <div class="field">
          <label for="shootName"
            >Name <span aria-hidden="true">*</span></label
          >
          <input
            type="text"
            id="shootName"
            name="name"
            autocomplete="name"
            required
            minlength="2"
            maxlength="80"
            placeholder="Your full name"
            data-autofocus
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="shootPhone"
            >Phone Number <span aria-hidden="true">*</span></label
          >
          <input
            type="tel"
            id="shootPhone"
            name="phone"
            autocomplete="tel"
            inputmode="tel"
            required
            maxlength="20"
            placeholder="+91 98765 43210"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="shootEmail"
            >Email Address <span aria-hidden="true">*</span></label
          >
          <input
            type="email"
            id="shootEmail"
            name="email"
            autocomplete="email"
            inputmode="email"
            required
            maxlength="120"
            placeholder="you@example.com"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="shootDate"
            >Date of Shoot <span aria-hidden="true">*</span></label
          >
          <input type="date" id="shootDate" name="shoot_date" required />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field profile-form__full">
          <label for="shootSubject"
            >Subject <span class="field__note">Optional</span></label
          >
          <input
            type="text"
            id="shootSubject"
            name="shoot_type"
            maxlength="120"
            placeholder="e.g. Wedding film, December 2026"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field profile-form__full">
          <label for="shootMessage"
            >Message <span class="field__note">Optional</span></label
          >
          <textarea
            id="shootMessage"
            name="notes"
            rows="4"
            maxlength="1000"
            placeholder="Tell us about the location and the story you want to tell"
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
    </div>

    <div class="shoot-book__success" data-shoot-success hidden>
      <span class="shoot-book__mark" aria-hidden="true"
        ><svg class="icon">
          <use href="#i-check" /></svg
      ></span>
      <h2
        class="shoot-book__success-title"
        id="shootBookSuccessTitle"
        tabindex="-1"
      >
        Thank <em>you</em>
      </h2>
      <p class="shoot-book__message" role="status">
        Thank you for choosing ASDreams Studio! Your shoot booking request
        has been received. Our team will contact you within 24 hours to
        discuss the details and make your vision a reality.
      </p>
      <button class="btn btn--gold" type="button" data-close>
        <span>Close</span>
      </button>
    </div>
  </div>
</div>

<!-- ============ BOOK STUDIO FOR RENT MODAL ============ -->
<div
  class="modal shoot-book studio-book"
  id="studioBookModal"
  role="dialog"
  aria-modal="true"
  aria-labelledby="studioBookTitle"
  hidden
>
  <div class="modal__backdrop" data-close></div>
  <div class="modal__panel auth-modal__panel shoot-book__panel studio-book__panel">
    <button
      class="modal__close"
      type="button"
      data-close
      aria-label="Close"
    >
      <svg class="icon" aria-hidden="true">
        <use href="#i-close" />
      </svg>
    </button>

    <div data-studio-form>
      <p class="info__label">ASDreamStudio</p>
      <h2 class="auth-modal__title shoot-book__title" id="studioBookTitle">
        Book the <em>Studio</em>
      </h2>
      <p class="shoot-book__lead">
        Fields marked <span aria-hidden="true">*</span
        ><span class="visually-hidden">with an asterisk</span> are required.
        Our team will contact you to confirm studio availability and equipment setup.
      </p>

      <form class="profile-form" id="studioBookForm" method="POST" action="{{ route('asd.studio-booking.submit') }}" novalidate>
        @csrf
        <div class="field profile-form__full">
          <label for="studioName"
            >Name / Company Name <span aria-hidden="true">*</span></label
          >
          <input
            type="text"
            id="studioName"
            name="name"
            autocomplete="organization"
            required
            minlength="2"
            maxlength="100"
            placeholder="Your name or company"
            data-autofocus
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioPhone"
            >Phone Number <span aria-hidden="true">*</span></label
          >
          <input
            type="tel"
            id="studioPhone"
            name="phone"
            autocomplete="tel"
            inputmode="tel"
            required
            maxlength="20"
            placeholder="+91 98765 43210"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioEmail"
            >Email Address <span aria-hidden="true">*</span></label
          >
          <input
            type="email"
            id="studioEmail"
            name="email"
            autocomplete="email"
            inputmode="email"
            required
            maxlength="120"
            placeholder="you@example.com"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioDays"
            >Number of Days <span aria-hidden="true">*</span></label
          >
          <div class="unit">
            <input
              type="number"
              id="studioDays"
              name="days"
              inputmode="numeric"
              required
              min="1"
              max="60"
              step="1"
              value="1"
            />
            <span class="unit__label" aria-hidden="true">days</span>
          </div>
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioPurpose"
            >Purpose of Rental <span aria-hidden="true">*</span></label
          >
          <select id="studioPurpose" name="purpose" required>
            <option value="" disabled selected>Select a purpose</option>
            <option value="Photography">Photography</option>
            <option value="Videography">Videography</option>
            <option value="Fashion shoot">Fashion shoot</option>
            <option value="Commercial production">
              Commercial production
            </option>
            <option value="Other">Other</option>
          </select>
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioStart"
            >Booking Start Date <span aria-hidden="true">*</span></label
          >
          <input type="date" id="studioStart" name="start" required />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioEnd"
            >Booking End Date <span aria-hidden="true">*</span></label
          >
          <input type="date" id="studioEnd" name="end" required />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field profile-form__full">
          <label for="studioNotes"
            >Additional Requirements
            <span class="field__note">Optional</span></label
          >
          <textarea
            id="studioNotes"
            name="notes"
            rows="3"
            maxlength="1000"
            placeholder="Special requests, crew size, extra gear needs…"
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
    </div>

    <div class="shoot-book__success" data-studio-success hidden>
      <span class="shoot-book__mark" aria-hidden="true"
        ><svg class="icon">
          <use href="#i-check" /></svg
      ></span>
      <h2
        class="shoot-book__success-title"
        id="studioBookSuccessTitle"
        tabindex="-1"
      >
        Request <em>received</em>
      </h2>
      <p class="shoot-book__message" role="status">
        Your studio booking request has been submitted successfully! Our
        representative will contact you within 5 hours to discuss your
        requirements and confirm availability.
      </p>
      <button class="btn btn--gold" type="button" data-close>
        <span>Close</span>
      </button>
    </div>
  </div>
</div>
