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

@if(!request()->routeIs('asd.offers.*') && !request()->filled('brand'))
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
        @forelse($asdBanners ?? [] as $bIndex => $banner)
          @php
            $isFirst = $loop->first;
            $highlight = $banner->highlight_title;
            $mainTitle = $banner->title;
            if ($highlight && str_contains($mainTitle, $highlight)) {
              $titleHtml = str_replace($highlight, '<em>' . e($highlight) . '</em>', e($mainTitle));
            } else {
              $titleHtml = e($mainTitle) . ($highlight ? ' <em>' . e($highlight) . '</em>' : '');
            }
            $offerUrl = $banner->offer_url;
          @endphp
          <figure
            class="brands-pop__slide {{ $isFirst ? 'is-active' : '' }}"
            role="group"
            aria-roledescription="slide"
            aria-label="{{ $loop->iteration }} of {{ count($asdBanners) }}"
            {!! $isFirst ? '' : 'aria-hidden="true" inert' !!}
          >
            <img
              class="brands-pop__img"
              src="{{ $banner->image_url }}"
              alt="{{ $banner->alt_text ?? $banner->title }}"
              width="1100"
              height="900"
            />
            <figcaption>
              @if($banner->label)
                <span class="brands-pop__meta">{{ $banner->label }}</span>
              @endif
              <span class="brands-pop__caption">{!! $titleHtml !!}</span>
              @if($banner->description)
                <span class="brands-pop__desc">{{ $banner->description }}</span>
              @endif
              <a class="brands-pop__cta" href="{{ $offerUrl }}">
                {{ $banner->button_text ?: 'Offer Details' }}
                <svg class="icon" aria-hidden="true">
                  <use href="#i-arrow-right" />
                </svg>
              </a>
            </figcaption>
          </figure>
        @empty
          <figure class="brands-pop__slide is-active" role="group" aria-roledescription="slide" aria-label="1 of 1">
            <img class="brands-pop__img" src="{{ asset('images/asdreams/hr2.png') }}" alt="Behind the Viewfinder" width="1100" height="900" />
            <figcaption>
              <span class="brands-pop__meta">Portrait Session · Kozhikode</span>
              <span class="brands-pop__caption">Behind the <em>Viewfinder</em></span>
              <span class="brands-pop__desc">Stories, emotions and timeless moments captured through our lens.</span>
              <a class="brands-pop__cta" href="{{ route('asd.offers.index') }}">
                Offer Details
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
              </a>
            </figcaption>
          </figure>
        @endforelse
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
          <span id="brandsPopTotal">{{ sprintf('%02d', max(1, count($asdBanners ?? []))) }}</span>
        </p>
        <div class="brands-pop__dots" id="brandsPopDots" style="display: none;" hidden></div>
      </div>
      <p
        class="visually-hidden"
        aria-live="polite"
        id="brandsPopStatus"
      ></p>
    </section>
    <section class="brands-pop__brands">
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
        @foreach($asdBrands ?? [] as $brand)
          @php
            $variantClass = match($brand->css_variant) {
              'wide' => 'brands-pop__logo--wide',
              'tall' => 'brands-pop__logo--tall',
              'lakme' => 'brands-pop__logo--lakme',
              default => '',
            };

            // Format brand name and subtitle consistently across all four cards
            $brandName = $brand->name;
            $brandTagline = $brand->tagline;

            if ($brand->name === 'Indian Designer Fashion Runway' || $brand->name === 'IDFR') {
              $brandName = 'IDFR';
              $brandTagline = ($brand->tagline && $brand->tagline !== 'IDFR') ? $brand->tagline : 'Indian Designer Fashion Runway';
            } elseif (str_contains($brand->name, 'Athul')) {
              $brandName = 'Athul’s Academy';
              $brandTagline = (!$brand->tagline || $brand->tagline === 'Modelling & Grooming') ? 'Fashion & Modelling' : $brand->tagline;
            } elseif ($brand->name === 'Beauty & Wellness' || $brand->name === 'Lakme' || $brand->name === 'Lakmé Academy') {
              $brandName = 'Lakmé Academy';
              $brandTagline = 'Beauty & Wellness';
            }

            $linkUrl = $brand->link_url;
            if ($brandName === 'IDFR' && ($linkUrl === '/' || empty($linkUrl) || $linkUrl === '#')) {
              $linkUrl = route('idfr.home');
            } elseif ($brandName === 'ASDreamStudio' && ($linkUrl === '/asdreams#home' || empty($linkUrl) || $linkUrl === '#')) {
              $linkUrl = route('asd.home');
            }
          @endphp
          <li class="brands-pop__item">
            <a class="brands-pop__link" href="{{ $linkUrl }}" aria-label="{{ $brandName }}">
              <span class="brands-pop__logo {{ $variantClass }}">
                <span class="brands-pop__mark">
                  <img
                    src="{{ $brand->logo_url }}"
                    alt="{{ $brandName }}"
                    loading="lazy"
                  />
                </span>
                <span class="asd-brand__name brands-pop__name">{{ $brandName }}</span>
                @if($brandTagline)
                  <span class="brands-pop__tag">{{ $brandTagline }}</span>
                @endif
                <span class="brands-pop__go" aria-hidden="true">
                  <svg class="icon"><use href="#i-arrow-right" /></svg>
                </span>
              </span>
            </a>
          </li>
        @endforeach
      </ul>
    </section>
  </div>
</div>
@endif

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

      <form class="profile-form" id="shootBookForm" method="POST" action="{{ route('asd.shoot-booking.submit') }}" data-validate data-ajax="true" novalidate>
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
            maxlength="60"
            data-rules="required|min:2|max:60|name"
            data-label="Name"
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
            maxlength="25"
            data-rules="required|phone"
            data-label="Phone Number"
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
            data-rules="required|email|max:120"
            data-label="Email Address"
            placeholder="you@example.com"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="shootDate"
            >Date of Shoot <span aria-hidden="true">*</span></label
          >
          <input type="date" id="shootDate" name="shoot_date" required data-rules="required|date|date_after_or_equal:today" data-label="Date of Shoot" />
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
            maxlength="150"
            data-rules="max:150"
            data-label="Subject"
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

      <form class="profile-form" id="studioBookForm" method="POST" action="{{ route('asd.studio-booking.submit') }}" data-validate data-ajax="true" novalidate>
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
            data-rules="required|min:2|max:100"
            data-label="Name / Company Name"
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
            maxlength="25"
            data-rules="required|phone"
            data-label="Phone Number"
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
            data-rules="required|email|max:120"
            data-label="Email Address"
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
              data-rules="required|numeric|min:1|max:60"
              data-label="Number of Days"
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
          <input
            type="text"
            id="studioPurpose"
            name="purpose"
            required
            minlength="2"
            maxlength="150"
            data-rules="required|min:2|max:150"
            data-label="Purpose of Rental"
            placeholder="e.g. Photography, Videography, Fashion shoot"
            autocomplete="off"
          />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioStart"
            >Booking Start Date <span aria-hidden="true">*</span></label
          >
          <input type="date" id="studioStart" name="start" required data-rules="required|date|date_after_or_equal:today" data-label="Booking Start Date" />
          <span class="field__error" aria-live="polite"></span>
        </div>

        <div class="field">
          <label for="studioEnd"
            >Booking End Date <span aria-hidden="true">*</span></label
          >
          <input type="date" id="studioEnd" name="end" required data-rules="required|date|date_after_or_equal:#studioStart" data-label="Booking End Date" />
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
