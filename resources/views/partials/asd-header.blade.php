<header class="site-header" id="siteHeader">
      <nav class="nav" aria-label="Primary">
        <a
          class="nav__logo"
          href="{{ route('asd.home') }}"
          aria-label="ASDreamStudio — back to top"
        >
          <img
            src="{{ asset('images/asdreams/asd-logo.png') }}"
            alt="ASDreamStudio"
            width="666"
            height="470"
          />
        </a>

        <ul class="nav__links">
          <li>
            <a class="nav__link {{ request()->routeIs('asd.home') ? 'is-active' : '' }}" href="{{ route('asd.home') }}#home" data-nav="home"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-home" /></svg
              ><span>Home</span></a
            >
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('asd.about') ? 'is-active' : '' }}" href="{{ route('asd.about') }}" data-nav="about"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-diamond" /></svg
              ><span>About</span></a
            >
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('asd.gallery') ? 'is-active' : '' }}" href="{{ route('asd.gallery') }}" data-nav="gallery">
              <svg class="icon" aria-hidden="true">
                <use href="#i-image" /></svg
              ><span>Gallery</span></a
            >
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('asd.team') ? 'is-active' : '' }}" href="{{ route('asd.team') }}" data-nav="team"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-users" /></svg
              ><span>Team Members</span></a
            >
          </li>
        </ul>

        <div class="nav__actions">
          <button
            class="btn btn--gold btn--sm nav__register"
            type="button"
            data-shoot-book
            aria-haspopup="dialog"
            aria-controls="shootBookModal"
            aria-label="Book a Shoot"
          >
            <svg class="icon" aria-hidden="true">
              <use href="#i-calendar" /></svg
            ><span>Book a Shoot</span>
          </button>
          <button
            class="nav__toggle"
            type="button"
            aria-expanded="false"
            aria-controls="mobileMenu"
            aria-label="Open menu"
          >
            <span></span><span></span><span></span>
          </button>
        </div>
      </nav>
    </header>

    <!-- Mobile navigation -->
    <div
      class="mobile-menu"
      id="mobileMenu"
      role="dialog"
      aria-modal="true"
      aria-label="Site menu"
      hidden
    >
      <span class="mobile-menu__word" aria-hidden="true">IDFR</span>
      <ul class="mobile-menu__links">
        <li style="--i: 0">
          <a href="{{ route('asd.home') }}#home" data-nav="home"
            ><span class="mobile-menu__num">01</span>Home</a
          >
        </li>
        <li style="--i: 1">
          <a href="{{ route('idfr.about') }}" data-nav="about"
            ><span class="mobile-menu__num">02</span>About</a
          >
        </li>
        <li style="--i: 2">
          <a href="{{ route('idfr.models') }}" data-nav="models"
            ><span class="mobile-menu__num">03</span>Models</a
          >
        </li>
        <li style="--i: 3">
          <a href="{{ route('idfr.gallery') }}" data-nav="gallery"
            ><span class="mobile-menu__num">04</span>Gallery</a
          >
        </li>
        <li style="--i: 4">
          <a href="{{ route('asd.contact') }}" data-nav="contact"
            ><span class="mobile-menu__num">05</span>Contact</a
          >
        </li>
        <li style="--i: 5">
          <a href="{{ route('asd.team') }}" data-nav="team"
            ><span class="mobile-menu__num">06</span>Team Members</a
          >
        </li>
      </ul>
      <div class="mobile-menu__actions">
        <button class="btn btn--ghost" type="button" data-auth="login">
          <svg class="icon" aria-hidden="true">
            <use href="#i-user" /></svg
          ><span>Login</span>
        </button>
        <button class="btn btn--gold" type="button" data-auth="register">
          <svg class="icon" aria-hidden="true">
            <use href="#i-user-plus" /></svg
          ><span>Register</span>
        </button>
      </div>
      <p class="mobile-menu__foot">
        ASDreamStudio · Photography &amp; Videography Partner of IDFR
      </p>
    </div>