<div
      class="modal model-modal"
      id="modelModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="modelModalName"
      hidden
    >
      <div class="modal__backdrop" data-close></div>
      <div class="modal__panel model-modal__panel">
        <button
          class="modal__close"
          type="button"
          data-close
          aria-label="Close profile"
        >
          <svg class="icon" aria-hidden="true">
            <use href="#i-close" />
          </svg>
        </button>
        <div
          class="model-modal__media"
          id="modelModalMedia"
          aria-roledescription="carousel"
          aria-label="Model portfolio images"
        >
          <div class="model-modal__slides" id="modelModalSlides">
            <img
              class="photo model-modal__slide is-active"
              src="{{ asset('images/models/model-01.jpg') }}"
              alt=""
              width="700"
              height="933"
            />
          </div>
          <span class="model-modal__badge" id="modelModalBadge"
            >Premium Model</span
          >
          <button
            class="model-modal__nav model-modal__nav--prev"
            type="button"
            aria-label="Previous image"
          >
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-left" />
            </svg>
          </button>
          <button
            class="model-modal__nav model-modal__nav--next"
            type="button"
            aria-label="Next image"
          >
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
          <p class="model-modal__counter" aria-hidden="true">
            <span class="model-modal__current" id="modelModalCurrent">01</span>
            / <span id="modelModalTotal">01</span>
          </p>
          <p
            class="visually-hidden"
            id="modelModalStatus"
            aria-live="polite"
          ></p>
        </div>
        <div class="model-modal__body" id="modelModalBody"></div>
      </div>

    @include('partials.enquiry-modal-template')

    </div>

    <!-- ============ LIGHTBOX ============ -->
    <div
      class="modal lightbox"
      id="lightbox"
      role="dialog"
      aria-modal="true"
      aria-label="Gallery image viewer"
      hidden
    >
      <div class="lightbox__top">
        <p class="lightbox__season" id="lightboxSeason">Season 1</p>
        <p class="lightbox__counter" id="lightboxCounter">01 / 18</p>
        <button
          class="modal__close lightbox__close"
          type="button"
          data-close
          aria-label="Close gallery viewer"
        >
          <svg class="icon" aria-hidden="true">
            <use href="#i-close" />
          </svg>
        </button>
      </div>
      <button
        class="lightbox__nav lightbox__nav--prev"
        type="button"
        id="lightboxPrev"
        aria-label="Previous image"
      >
        <svg class="icon" aria-hidden="true">
          <use href="#i-arrow-left" />
        </svg>
      </button>
      <figure class="lightbox__figure">
        <img
          class="photo"
          id="lightboxImg"
          src="{{ asset('images/gallery/s1-01.jpg') }}"
          alt=""
          width="1000"
          height="1250"
        />
        <figcaption class="lightbox__caption" id="lightboxCaption"></figcaption>
      </figure>
      <button
        class="lightbox__nav lightbox__nav--next"
        type="button"
        id="lightboxNext"
        aria-label="Next image"
      >
        <svg class="icon" aria-hidden="true">
          <use href="#i-arrow-right" />
        </svg>
      </button>
    </div>

    <!-- ============ AUTH MODAL ============ -->
    <div
      class="modal auth-modal"
      id="authModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="authTitle"
      hidden
    >
      <div class="modal__backdrop" data-close></div>
      <div class="modal__panel auth-modal__panel">
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
        <img
          class="auth-modal__logo"
          src="{{ asset('images/logo/idfr-mark.png') }}"
          alt=""
          width="474"
          height="220"
        />
        <h2 class="auth-modal__title" id="authTitle">Welcome to IDFR</h2>
        <div class="auth-tabs" role="tablist" aria-label="Account options">
          <button
            class="auth-tab is-active"
            type="button"
            role="tab"
            id="tabLogin"
            aria-controls="panelLogin"
            aria-selected="true"
            data-tab="login"
          >
            Login
          </button>
          <button
            class="auth-tab"
            type="button"
            role="tab"
            id="tabRegister"
            aria-controls="panelRegister"
            aria-selected="false"
            data-tab="register"
            tabindex="-1"
          >
            Register
          </button>
        </div>

        <form
          class="auth-form"
          id="panelLogin"
          role="tabpanel"
          aria-labelledby="tabLogin"
          data-validate
          data-ajax="true"
          novalidate
        >
          @if(session('error'))
            <div class="auth-alert auth-alert--error" role="alert">
              {{ session('error') }}
            </div>
          @endif
          @if(session('status'))
            <div class="auth-alert auth-alert--success" role="status">
              {{ session('status') }}
            </div>
          @endif
          <div class="field">
            <label for="loginEmail">Email address</label>
            <input
              type="email"
              id="loginEmail"
              name="email"
              autocomplete="email"
              required
              data-rules="required|email"
              data-label="Email address"
              placeholder="you@example.com"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="loginPassword">Password</label>
            <input
              type="password"
              id="loginPassword"
              name="password"
              autocomplete="current-password"
              required
              data-rules="required"
              data-label="Password"
              placeholder="Enter your password"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="auth-form__row">
            <label class="check"
              ><input type="checkbox" name="remember" /><span
                >Remember me</span
              ></label
            >
            <button
              class="text-link text-link--small"
              type="button"
              id="forgotBtn"
            >
              Forgot password?
            </button>
          </div>
          <button class="btn btn--gold btn--block" type="submit">
            <span>Login</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>

          <div class="auth-divider">
            <span>OR</span>
          </div>

          <a href="{{ route('auth.google.redirect') }}" class="btn btn--google btn--block">
            <svg class="google-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3h3.86c2.26-2.09 3.68-5.17 3.68-9.09z"/>
              <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.86-3c-1.08.72-2.45 1.16-4.07 1.16-3.13 0-5.78-2.11-6.73-4.96H1.28v3.09C3.26 21.3 7.31 24 12 24z"/>
              <path fill="#FBBC05" d="M5.27 14.29c-.25-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29V6.62H1.28C.46 8.24 0 10.06 0 12s.46 3.76 1.28 5.38l3.99-3.09z"/>
              <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.28 6.62l3.99 3.09c.95-2.85 3.6-4.96 6.73-4.96z"/>
            </svg>
            <span>Continue with Google</span>
          </a>
        </form>

        <form
          class="auth-form"
          id="panelForgotPassword"
          role="tabpanel"
          aria-labelledby="forgotTitle"
          data-validate
          data-ajax="true"
          novalidate
          hidden
        >
          <div class="auth-modal__instructions">
            <p style="font-size: 0.85rem; color: #a1a1aa; line-height: 1.55; text-align: center; margin: -0.25rem 0 1rem 0;">
              Enter your registered email address. We will send you a password reset link.
            </p>
          </div>

          <div class="auth-alert auth-alert--error" id="forgotErrorAlert" role="alert" style="display: none;"></div>
          <div class="auth-alert auth-alert--success" id="forgotSuccessAlert" role="status" style="display: none;"></div>

          <div class="field">
            <label for="forgotEmail">Email address</label>
            <input
              type="email"
              id="forgotEmail"
              name="email"
              autocomplete="email"
              required
              data-rules="required|email"
              data-label="Email address"
              data-error-target="#forgotEmailError"
              placeholder="you@example.com"
            />
            <span class="field__error" id="forgotEmailError" aria-live="polite"></span>
          </div>

          <button class="btn btn--gold btn--block" type="submit" id="forgotSubmitBtn">
            <span>Send Reset Link</span>
            <svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>

          <div class="auth-form__row" style="justify-content: center; margin-top: 0.75rem;">
            <button class="text-link text-link--small" type="button" id="backToLoginFromForgotBtn">
              &larr; Back to Login
            </button>
          </div>
        </form>

        <form
          class="auth-form"
          id="panelRegister"
          role="tabpanel"
          aria-labelledby="tabRegister"
          data-validate
          data-ajax="true"
          novalidate
          hidden
        >
          <div class="field">
            <label for="regName">Full name</label>
            <input
              type="text"
              id="regName"
              name="name"
              autocomplete="name"
              required
              minlength="2"
              maxlength="60"
              data-rules="required|min:2|max:60|name"
              data-label="Full name"
              placeholder="Your full name"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="regEmail">Email address</label>
            <input
              type="email"
              id="regEmail"
              name="email"
              autocomplete="email"
              required
              data-rules="required|email"
              data-label="Email address"
              placeholder="you@example.com"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="regPhone">Phone number</label>
            <input
              type="tel"
              id="regPhone"
              name="phone"
              autocomplete="tel"
              required
              data-rules="required|phone"
              data-label="Phone number"
              placeholder="+91 98765 43210"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="regRole">I am joining as</label>
            <select id="regRole" name="role" required data-rules="select" data-label="Role">
              <option value="">Select your role</option>
              <option value="model">Model</option>
              @php
                $regCategories = \App\Models\ProfessionalCategory::where('is_active', true)->orderBy('display_order')->get();
              @endphp
              @foreach($regCategories as $rCat)
                <option value="{{ $rCat->slug }}">{{ $rCat->name }}</option>
              @endforeach
            </select>
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field" id="regModelTypeField" style="display: none;">
            <label for="regModelType">Model Type</label>
            <select id="regModelType" name="model_type">
              <option value="">Select your model type</option>
              <option value="Commercial Model">Commercial Model</option>
              <option value="Runway / Ramp Model">Runway / Ramp Model</option>
              <option value="Editorial Model">Editorial Model</option>
              <option value="High Fashion Model">High Fashion Model</option>
              <option value="Fitness Model">Fitness Model</option>
              <option value="Glamour Model">Glamour Model</option>
              <option value="Print Model">Print Model</option>
              <option value="Parts Model">Parts Model</option>
              <option value="Promotional Model">Promotional Model</option>
              <option value="Video Shoot Model">Video Shoot Model</option>
              <option value="Actor">Actor</option>
            </select>
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="regPassword">Create password</label>
            <input
              type="password"
              id="regPassword"
              name="password"
              autocomplete="new-password"
              required
              minlength="8"
              data-rules="required|password"
              data-label="Password"
              placeholder="Minimum 8 chars (letters & numbers)"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="regConfirmPassword">Confirm password</label>
            <input
              type="password"
              id="regConfirmPassword"
              name="password_confirmation"
              autocomplete="new-password"
              required
              minlength="8"
              data-match="#regPassword"
              data-rules="required|same:#regPassword"
              data-label="Confirm password"
              placeholder="Re-enter your password"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <label class="check"
            ><input type="checkbox" name="terms" id="regTerms" required data-rules="checked" data-label="Terms &amp; Conditions" data-error-target="#regTermsError" data-message="You must accept the terms and privacy policy." /><span
              >I agree to the
              <a href="#terms" data-program="terms"
                >Terms &amp; Conditions</a
              ></span
            ></label
          >
          <span
            class="field__error field__error--check"
            id="regTermsError"
            aria-live="polite"
          ></span>
          <button class="btn btn--gold btn--block" type="submit" id="regSubmitBtn">
            <span>Create Account</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
        </form>

        <form
          class="auth-form"
          id="panelVerifyOtp"
          role="tabpanel"
          aria-labelledby="tabRegister"
          data-validate
          data-ajax="true"
          novalidate
          hidden
        >
          <div
            class="auth-notice"
            id="otpNotice"
            style="background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.25); border-radius: 8px; padding: 14px 16px; margin-bottom: 20px; font-size: 13.5px; line-height: 1.5; color: #e4e4e7; text-align: center;"
          >
            A 6-digit verification code was sent to <strong id="otpTargetEmail" style="color: #facc15;">your email</strong>. It expires in 5 minutes.
          </div>
          <input type="hidden" id="verifyEmailInput" name="email" value="" />
          <div class="field">
            <label for="verifyOtpInput">6-Digit Verification Code</label>
            <input
              type="text"
              id="verifyOtpInput"
              name="otp"
              inputmode="numeric"
              autocomplete="one-time-code"
              maxlength="6"
              placeholder="••••••"
              required
              data-rules="required|digits:6"
              data-label="Verification code"
              data-error-target="#verifyOtpError"
              style="font-size: 22px; letter-spacing: 8px; text-align: center; font-family: monospace; font-weight: 600;"
            />
            <span class="field__error" id="verifyOtpError" aria-live="polite"></span>
          </div>
          <button class="btn btn--gold btn--block" type="submit" id="verifyOtpBtn" style="margin-top: 18px;">
            <span>Verify &amp; Activate Account</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
          <div class="auth-form__row" style="margin-top: 20px; justify-content: space-between; align-items: center;">
            <button class="text-link text-link--small" type="button" id="resendOtpBtn">
              Resend Code
            </button>
            <button class="text-link text-link--small" type="button" id="backToRegisterBtn">
              &larr; Back to Register
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ============ INFO / PROGRAM MODAL ============ -->
    <div
      class="modal info-modal"
      id="infoModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="infoModalTitle"
      hidden
    >
      <div class="modal__backdrop" data-close></div>
      <div class="modal__panel info-modal__panel">
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
        <div id="infoModalBody"></div>
      </div>
    </div>

    <!-- ============ SPONSORSHIP ENQUIRY MODAL ============ -->
    <span id="sponsor-enquiry" class="visually-hidden" style="position: absolute; top: 0; left: 0; width: 0; height: 0; pointer-events: none; opacity: 0;" aria-hidden="true"></span>
    <div
      class="modal shoot-book sponsor-enquiry"
      id="sponsorEnquiryModal"
      data-modal-name="sponsor-enquiry"
      role="dialog"
      aria-modal="true"
      aria-labelledby="sponsorEnquiryTitle"
      hidden
    >
      <div class="modal__backdrop" data-close></div>
      <div class="modal__panel auth-modal__panel shoot-book__panel">
        <button
          class="modal__close"
          type="button"
          data-close
          aria-label="Close sponsorship enquiry"
        >
          <svg class="icon" aria-hidden="true">
            <use href="#i-close" />
          </svg>
        </button>

                <div data-sponsor-form>
          <p class="info__label">IDFR · {{ $currentSeason ? $currentSeason->title : 'Season 8' }}</p>
          <h2
            class="auth-modal__title shoot-book__title"
            id="sponsorEnquiryTitle"
          >
            Sponsorship <em>Enquiry</em>
          </h2>
          <p class="shoot-book__lead">
            Tell us about your brand's interest in partnering with the upcoming IDFR runway season.
            Fields marked <span aria-hidden="true">*</span
            ><span class="visually-hidden">with an asterisk</span> are required.
            Our executive sponsorship desk will follow up promptly with presentation decks and package deliverables.
          </p>

          <form class="profile-form" id="sponsorEnquiryForm" method="POST" action="{{ route('sponsorship.enquiry') }}" data-validate data-ajax="true" novalidate>
            @csrf
            <input type="hidden" name="season_id" value="{{ $currentSeason?->id }}" />

            <div class="field">
              <label for="sponsorName"
                >Name / Company Name <span aria-hidden="true">*</span></label
              >
              <input
                type="text"
                id="sponsorName"
                name="name"
                autocomplete="organization"
                required
                minlength="2"
                maxlength="120"
                data-rules="required|min:2|max:120"
                data-label="Name / Company Name"
                placeholder="Your name or company"
                data-autofocus
              />
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="field">
              <label for="sponsorEmail"
                >Email Address <span aria-hidden="true">*</span></label
              >
              <input
                type="email"
                id="sponsorEmail"
                name="email"
                autocomplete="email"
                inputmode="email"
                required
                maxlength="120"
                data-rules="required|email|max:120"
                data-label="Email Address"
                placeholder="you@company.com"
              />
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="field">
              <label for="sponsorPhone"
                >Phone Number <span aria-hidden="true">*</span></label
              >
              <input
                type="tel"
                id="sponsorPhone"
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
              <label for="sponsorType"
                >Type of Sponsorship <span aria-hidden="true">*</span></label
              >
              <select id="sponsorType" name="sponsorship_type" required data-rules="select" data-label="Type of Sponsorship">
                <option value="" selected disabled>Select a sponsorship type</option>
                @php
                  $availableSponsorTypes = $sponsorshipTypes ?? \App\Models\SponsorshipEnquiry::availableTypes();
                @endphp
                @foreach($availableSponsorTypes as $val => $lbl)
                  <option value="{{ $val }}">{{ $lbl }}</option>
                @endforeach
              </select>
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="field profile-form__full">
              <label for="sponsorMessage"
                >Message <span class="field__note">Optional</span></label
              >
              <textarea
                id="sponsorMessage"
                name="message"
                rows="4"
                maxlength="1000"
                placeholder="Share any specific sponsorship goals, marketing initiatives or questions about partnering with IDFR"
              ></textarea>
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="profile-form__actions profile-form__full">
              <button class="btn btn--gold" type="submit" id="sponsorSubmitBtn">
                <span>Submit Sponsorship Enquiry</span
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-arrow-right" />
                </svg>
              </button>
            </div>
          </form>
        </div>

        <div class="shoot-book__success" data-sponsor-success hidden>
          <span class="shoot-book__mark" aria-hidden="true"
            ><svg class="icon">
              <use href="#i-check" /></svg
          ></span>
          <h2
            class="shoot-book__success-title"
            id="sponsorEnquirySuccessTitle"
            tabindex="-1"
          >
            Enquiry <em>Received</em>
          </h2>
          <p class="shoot-book__message" id="sponsorSuccessMsg" role="status">
            Thank you for your interest in sponsoring IDFR. Your enquiry has been securely received by our executive team. We will review your proposal and get in touch with you shortly.
          </p>
          <p class="shoot-book__note">
            Official IDFR Sponsorship Desk &bull; {{ $currentSeason ? $currentSeason->title : 'Season 8' }}
          </p>
          <button class="btn btn--gold" type="button" data-close>
            <span>Close</span>
          </button>
        </div>
      </div>
    </div>

    <script>
      (function () {
        function earlyOpenSponsorModal() {
          var modal = document.getElementById("sponsorEnquiryModal");
          if (!modal) return;
          if (window.Modal && typeof window.Modal.open === "function") {
            window.Modal.open(modal);
          } else {
            modal.hidden = false;
            void modal.offsetWidth;
            modal.classList.add("is-open");
          }
        }
        if (window.location.hash === "#sponsor-enquiry" || window.location.hash === "#sponsorEnquiryModal") {
          if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", earlyOpenSponsorModal);
          } else {
            earlyOpenSponsorModal();
          }
        }
      })();
    </script>

    <!-- ============ ACCOUNT MODAL ============ -->
    <div
      class="modal account-modal"
      id="accountModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="accountModalName"
      hidden
    >
      <div class="modal__backdrop" data-close></div>
      <div class="modal__panel account-modal__panel">
        <button
          class="modal__close"
          type="button"
          data-close
          aria-label="Close account modal"
        >
          <svg class="icon" aria-hidden="true">
            <use href="#i-close" />
          </svg>
        </button>

        <div class="account-modal__header">
          <div class="account-modal__avatar-wrap">
            <span class="account-modal__avatar" id="accountModalAvatar">U</span>
          </div>
          <div class="account-modal__profile">
            <h2 class="account-modal__name" id="accountModalName">My Account</h2>
            <span class="account-modal__role" id="accountModalRole">Model</span>
          </div>
        </div>

        <div class="account-modal__completion-card">
          <div class="account-modal__completion-head">
            <span class="account-modal__completion-title">Profile Completion</span>
            <span class="account-modal__completion-badge" id="accountModalCompletionBadge">Incomplete (85%)</span>
          </div>
          <div class="account-modal__progress-bar">
            <div class="account-modal__progress-fill" id="accountModalProgressFill" style="width: 85%;"></div>
          </div>
          <p class="account-modal__completion-hint" id="accountModalCompletionHint">Complete your profile details to maximize profile visibility.</p>
        </div>

        <div class="account-modal__details-grid">
          <div class="account-modal__field">
            <span class="account-modal__field-label">Email Address</span>
            <div class="account-modal__field-value-wrap">
              <span class="account-modal__field-value" id="accountModalEmail">user@example.com</span>
              <span class="account-modal__verified-pill">
                <svg class="icon" aria-hidden="true"><use href="#i-check" /></svg>
                <span>Verified</span>
              </span>
            </div>
          </div>

          <div class="account-modal__field">
            <span class="account-modal__field-label">Phone Number</span>
            <span class="account-modal__field-value" id="accountModalPhone">+91 98765 43210</span>
          </div>

          <div class="account-modal__field">
            <span class="account-modal__field-label">Talent Role</span>
            <span class="account-modal__field-value" id="accountModalRoleDetailed">Model</span>
          </div>

          <div class="account-modal__field">
            <span class="account-modal__field-label">Account Status</span>
            <span class="account-modal__field-value account-modal__field-value--active">Active Member</span>
          </div>
        </div>

        <div class="account-modal__actions">
          <a href="#" class="btn btn--gold" id="accountModalProfileBtn">
            <svg class="icon" aria-hidden="true"><use href="#i-diamond" /></svg>
            <span>Open My Profile</span>
          </a>
          <button type="button" class="btn btn--outline auth-logout-btn">
            <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
            <span>Logout</span>
          </button>
        </div>
      </div>
    </div>

    <div class="toast" id="toast" role="status" aria-live="polite"></div>