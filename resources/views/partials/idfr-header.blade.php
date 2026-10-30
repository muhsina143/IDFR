<header class="site-header" id="siteHeader">
      <nav class="nav" aria-label="Primary">
        <a class="nav__logo" href="{{ route('asd.home') }}" aria-label="IDFR — back to home">
          <img
            src="{{ asset('images/logo/idfr-logo.png') }}"
            alt="IDFR"
            width="474"
            height="220"
          />
        </a>

        <ul class="nav__links">
          <li>
            <a class="nav__link {{ request()->routeIs('idfr.home') ? 'is-active' : '' }}" href="{{ route('idfr.home') }}#home" data-nav="home"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-home" /></svg
              ><span>Home</span></a
            >
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('idfr.about') ? 'is-active' : '' }}" href="{{ route('idfr.about') }}" data-nav="about"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-diamond" /></svg
              ><span>About</span></a
            >
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('idfr.models') ? 'is-active' : '' }}" href="{{ route('idfr.models') }}" data-nav="models"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-users" /></svg
              ><span>Models</span></a
            >
          </li>
          <li class="nav__item nav__item--drop">
            <a
              class="nav__link {{ (request()->routeIs('idfr.professionals') || request()->routeIs('profile.*')) ? 'is-active' : '' }}" href="{{ route('idfr.professionals') }}"
              data-nav="creative"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-star" /></svg
              ><span>Creative Professionals</span></a
            >
            <button
              class="nav__drop-toggle"
              type="button"
              aria-expanded="false"
              aria-controls="navCreativeMenu"
              aria-label="Creative Professionals categories"
            ></button>
            <ul class="nav__dropdown" id="navCreativeMenu">
              <li><a href="{{ route('idfr.professionals') }}#creative-makeup-artist">Makeup Artist</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-hairstylist">Hairstylist</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-photographer">Photographer</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-videographer">Videography</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-fashion-designers">Fashion Designers</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-drone-pilot">Drone Pilot</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-photo-video-editor">Photo &amp; Video Editor</a></li>
              <li><a href="{{ route('idfr.professionals') }}#creative-jewellery-designer">Jewellery Designer</a></li>
            </ul>
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('idfr.gallery') ? 'is-active' : '' }}" href="{{ route('idfr.gallery') }}" data-nav="gallery"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-image" /></svg
              ><span>Gallery</span></a
            >
          </li>
          <li>
            <a class="nav__link {{ request()->routeIs('idfr.contact') ? 'is-active' : '' }}" href="{{ route('idfr.contact') }}" data-nav="contact"
              ><svg class="icon" aria-hidden="true">
                <use href="#i-mail" /></svg
              ><span>Contact</span></a
            >
          </li>
        </ul>

        <div class="nav__actions">
          @if(Auth::guard('web')->check())
            @php
              $webUser = Auth::guard('web')->user();
              $userInitial = strtoupper(substr($webUser->name, 0, 1));
              $profileCompletion = $webUser->profileCompletion();
            @endphp
            <div class="nav__account" id="navAccount">
              <button
                type="button"
                class="nav__avatar-btn"
                id="navAvatarBtn"
                aria-expanded="false"
                aria-haspopup="true"
                aria-controls="navAccountDropdown"
                title="Account menu for {{ $webUser->name }}"
              >
                <span class="nav__avatar-circle">{{ $userInitial }}</span>
              </button>

              <div class="nav__account-dropdown" id="navAccountDropdown" role="menu" aria-labelledby="navAvatarBtn" hidden>
                <div class="nav__account-header">
                  <div class="nav__account-user-info">
                    <span class="nav__account-name">{{ $webUser->name }}</span>
                    <span class="nav__account-email">{{ $webUser->email }}</span>
                  </div>
                  <div class="nav__account-meta">
                    <span class="nav__account-role-badge">{{ $webUser->roleLabel() }}</span>
                  </div>
                  <div class="nav__account-completion">
                    <div class="nav__account-completion-header">
                      <span class="nav__completion-title">Profile Status</span>
                      <span class="nav__completion-badge {{ $profileCompletion['is_complete'] ? 'is-complete' : 'is-incomplete' }}">
                        {{ $profileCompletion['label'] }}
                      </span>
                    </div>
                    <div class="nav__completion-bar" role="progressbar" aria-valuenow="{{ $profileCompletion['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                      <div class="nav__completion-fill" style="width: {{ $profileCompletion['percentage'] }}%;"></div>
                    </div>
                  </div>
                </div>

                <div class="nav__account-menu-divider"></div>

                <ul class="nav__account-links">
                  <li>
                    <button type="button" class="nav__account-link-btn" id="openAccountModalBtn" role="menuitem">
                      <svg class="icon" aria-hidden="true"><use href="#i-user" /></svg>
                      <span>My Account</span>
                    </button>
                  </li>
                  <li>
                    <a href="{{ $webUser->profileUrl() }}" class="nav__account-link" role="menuitem">
                      <svg class="icon" aria-hidden="true"><use href="#i-diamond" /></svg>
                      <span>My Profile</span>
                    </a>
                  </li>
                  <li>
                    <button type="button" class="nav__account-link-btn nav__account-link-btn--logout auth-logout-btn" role="menuitem">
                      <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
                      <span>Logout</span>
                    </button>
                  </li>
                </ul>
              </div>
            </div>

            <button class="nav__login" type="button" data-auth="login" hidden>
              <svg class="icon" aria-hidden="true">
                <use href="#i-user" /></svg
              ><span>Login</span>
            </button>
            <button
              class="btn btn--gold btn--sm nav__register"
              type="button"
              data-auth="register"
              hidden
            >
              <svg class="icon" aria-hidden="true">
                <use href="#i-user-plus" /></svg
              ><span>Register</span>
            </button>
          @else
            <button class="nav__login" type="button" data-auth="login">
              <svg class="icon" aria-hidden="true">
                <use href="#i-user" /></svg
              ><span>Login</span>
            </button>
            <button
              class="btn btn--gold btn--sm nav__register"
              type="button"
              data-auth="register"
            >
              <svg class="icon" aria-hidden="true">
                <use href="#i-user-plus" /></svg
              ><span>Register</span>
            </button>
          @endif
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
          <a href="{{ route('idfr.home') }}#home" data-nav="home"
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
          <a href="{{ route('idfr.professionals') }}" data-nav="creative"
            ><span class="mobile-menu__num">04</span>Creative Professionals</a
          >
          <ul
            class="mobile-menu__sub"
            aria-label="Creative Professionals categories"
          >
            <li><a href="{{ route('idfr.professionals') }}#creative-makeup-artist">Makeup Artist</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-hairstylist">Hairstylist</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-photographer">Photographer</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-videographer">Videography</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-fashion-designers">Fashion Designers</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-drone-pilot">Drone Pilot</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-photo-video-editor">Photo &amp; Video Editor</a></li>
            <li><a href="{{ route('idfr.professionals') }}#creative-jewellery-designer">Jewellery Designer</a></li>
          </ul>
        </li>
        <li style="--i: 4">
          <a href="{{ route('idfr.gallery') }}" data-nav="gallery"
            ><span class="mobile-menu__num">05</span>Gallery</a
          >
        </li>
        <li style="--i: 5">
          <a href="{{ route('idfr.contact') }}" data-nav="contact"
            ><span class="mobile-menu__num">06</span>Contact</a
          >
        </li>
      </ul>
      <div class="mobile-menu__actions">
        @if(Auth::guard('web')->check())
          @php
            $webUserMobile = Auth::guard('web')->user();
            $userInitialMobile = strtoupper(substr($webUserMobile->name, 0, 1));
            $mobileCompletion = $webUserMobile->profileCompletion();
          @endphp
          <div class="mobile-menu__account-card">
            <div class="mobile-menu__user-header">
              <span class="nav__avatar-circle">{{ $userInitialMobile }}</span>
              <div class="mobile-menu__user-details">
                <span class="mobile-menu__user-name">{{ $webUserMobile->name }}</span>
                <span class="mobile-menu__user-email">{{ $webUserMobile->email }}</span>
                <span class="nav__account-role-badge">{{ $webUserMobile->roleLabel() }}</span>
              </div>
            </div>
            <div class="mobile-menu__completion">
              <div class="nav__account-completion-header">
                <span class="nav__completion-title">Profile Status</span>
                <span class="nav__completion-badge {{ $mobileCompletion['is_complete'] ? 'is-complete' : 'is-incomplete' }}">
                  {{ $mobileCompletion['label'] }}
                </span>
              </div>
              <div class="nav__completion-bar" role="progressbar" aria-valuenow="{{ $mobileCompletion['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                <div class="nav__completion-fill" style="width: {{ $mobileCompletion['percentage'] }}%;"></div>
              </div>
            </div>
            <div class="mobile-menu__account-links">
              <button type="button" class="btn btn--outline btn--sm mobile-account-modal-btn" style="width:100%; margin-bottom:8px;">
                <svg class="icon" aria-hidden="true"><use href="#i-user" /></svg>
                <span>My Account</span>
              </button>
              <a href="{{ $webUserMobile->profileUrl() }}" class="btn btn--gold btn--sm" style="display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:8px;">
                <svg class="icon" aria-hidden="true"><use href="#i-diamond" /></svg>
                <span>My Profile</span>
              </a>
              <button class="btn btn--ghost btn--sm auth-logout-btn" type="button" style="width:100%;">
                <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
                <span>Logout</span>
              </button>
            </div>
          </div>
        @else
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
        @endif
      </div>
      <p class="mobile-menu__foot">Fashion Show — Season 4 · Kozhikode</p>
    </div>