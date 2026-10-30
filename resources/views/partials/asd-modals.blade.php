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

<div
      class="modal brands-pop"
      id="brandsPopup"
      role="dialog"
      aria-modal="true"
      aria-labelledby="brandsPopupTitle"
      hidden
    >
      <div class="modal__panel brands-pop__panel">
        <!-- <button
          class="modal__close"
          type="button"
          data-close
          aria-label="Close brands window"
        >
          <svg class="icon" aria-hidden="true">
            <use href="#i-close" />
          </svg>
        </button> -->
        <section
          class="brands-pop__banner"
          aria-roledescription="carousel"
          aria-label="ASDreamStudio highlights"
        >
          <div class="brands-pop__slides" id="brandsPopSlides">
            <figure class="brands-pop__slide is-active" role="group" aria-roledescription="slide" aria-label="1 of 7">
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hr2.png') }}" alt="Photographer framing a shot through her camera" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Portrait Session · Kozhikode</span>
                <span class="brands-pop__caption">Behind the <em>Viewfinder</em></span>
                <span class="brands-pop__desc">Stories, emotions and timeless moments captured through our lens.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
              </figcaption>
            </figure>
            <figure class="brands-pop__slide" role="group" aria-roledescription="slide" aria-label="2 of 7" aria-hidden="true" inert>
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hr3.png') }}" alt="Vintage camera resting beside instant photo prints" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Studio Archive</span>
                <span class="brands-pop__caption">Prints &amp; <em>Polaroids</em></span>
                <span class="brands-pop__desc">Every frame printed with care: keepsakes to hold, share and treasure.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
              </figcaption>
            </figure>
            <figure class="brands-pop__slide" role="group" aria-roledescription="slide" aria-label="3 of 7" aria-hidden="true" inert>
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hr4.png') }}" alt="Bride and groom sharing a quiet moment" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Wedding Story · Kochi</span>
                <span class="brands-pop__caption">The First <em>Look</em></span>
                <span class="brands-pop__desc">Quiet glances and joyful tears, documented the way you felt them.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
              </figcaption>
            </figure>
            <figure class="brands-pop__slide" role="group" aria-roledescription="slide" aria-label="4 of 7" aria-hidden="true" inert>
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hero-08.jpg') }}" alt="Photography studio set with softboxes and a white backdrop" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Fashion Editorial · Studio 01</span>
                <span class="brands-pop__caption">Studio <em>Light</em></span>
                <span class="brands-pop__desc">Editorial portraits and fashion campaigns shaped by light in our studio.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
              </figcaption>
            </figure>
            <figure class="brands-pop__slide" role="group" aria-roledescription="slide" aria-label="5 of 7" aria-hidden="true" inert>
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hero-05.jpg') }}" alt="Photographer shooting on a busy city street" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Documentary · Bengaluru</span>
                <span class="brands-pop__caption">Street <em>Frames</em></span>
                <span class="brands-pop__desc">Honest, unposed stories found in the rhythm of the city.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
              </figcaption>
            </figure>
            <figure class="brands-pop__slide" role="group" aria-roledescription="slide" aria-label="6 of 7" aria-hidden="true" inert>
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hr5.png') }}" alt="Wedding couple embracing outdoors" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Wedding · Munnar</span>
                <span class="brands-pop__caption">Golden <em>Vows</em></span>
                <span class="brands-pop__desc">Weddings told with warmth, from the first ritual to the last dance.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
              </figcaption>
            </figure>
            <figure class="brands-pop__slide" role="group" aria-roledescription="slide" aria-label="7 of 7" aria-hidden="true" inert>
              <img class="brands-pop__img" src="{{ asset('images/asdreams/hero-07.jpg') }}" alt="Photographer on a mountain peak above a sea of clouds" width="1100" height="900" />
              <figcaption>
                <span class="brands-pop__meta">Destination · Wayanad</span>
                <span class="brands-pop__caption">Above the <em>Clouds</em></span>
                <span class="brands-pop__desc">Destination shoots that travel wherever your story takes us.</span>
                <!-- <a class="brands-pop__cta" href="#asd-gallery">Explore our work <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg></a> -->
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
              <button class="brands-pop__dot" type="button" data-banner-dot="0" aria-label="Show banner 1: Behind the Viewfinder" aria-current="true"></button>
              <button class="brands-pop__dot" type="button" data-banner-dot="1" aria-label="Show banner 2: Prints &amp; Polaroids"></button>
              <button class="brands-pop__dot" type="button" data-banner-dot="2" aria-label="Show banner 3: The First Look"></button>
              <button class="brands-pop__dot" type="button" data-banner-dot="3" aria-label="Show banner 4: Studio Light"></button>
              <button class="brands-pop__dot" type="button" data-banner-dot="4" aria-label="Show banner 5: Street Frames"></button>
              <button class="brands-pop__dot" type="button" data-banner-dot="5" aria-label="Show banner 6: Golden Vows"></button>
              <button class="brands-pop__dot" type="button" data-banner-dot="6" aria-label="Show banner 7: Above the Clouds"></button>
            </div>
          </div>

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
            required. This preview checks your details on this page only.
          </p>

          <form class="profile-form" id="shootBookForm" novalidate>
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
              <input type="date" id="shootDate" name="date" required />
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="field profile-form__full">
              <label for="shootSubject"
                >Subject <span class="field__note">Optional</span></label
              >
              <input
                type="text"
                id="shootSubject"
                name="subject"
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
                name="message"
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
          <p class="shoot-book__note">
            Preview only — these details stay in this window and have not been
            stored or sent to the studio.
          </p>
          <button class="btn btn--gold" type="button" data-close>
            <span>Close</span>
          </button>
        </div>
      </div>
    </div>

