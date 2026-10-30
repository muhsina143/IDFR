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
          novalidate
        >
          <div class="field">
            <label for="loginEmail">Email address</label>
            <input
              type="email"
              id="loginEmail"
              name="email"
              autocomplete="email"
              required
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
              minlength="6"
              placeholder="Minimum 6 characters"
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
        </form>

        <form
          class="auth-form"
          id="panelRegister"
          role="tabpanel"
          aria-labelledby="tabRegister"
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
              pattern="[0-9+\s\-]{8,20}"
              placeholder="+91 98765 43210"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="regRole">I am joining as</label>
            <select id="regRole" name="role" required>
              <option value="">Select your role</option>
              <option value="model">Model</option>
              <option value="makeup-artist">Makeup Artist</option>
              <option value="hairstylist">Hairstylist</option>
              <option value="photographer">Photographer</option>
              <option value="videographer">Videographer</option>
              <option value="fashion-designer">Fashion Designer</option>
              <option value="drone-pilot">Drone Pilot</option>
              <option value="photo-video-editor">Photo &amp; Video Editor</option>
              <option value="jewellery-designer">Jewellery Designer</option>
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
              minlength="6"
              placeholder="Minimum 6 characters"
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
              minlength="6"
              data-match="#regPassword"
              placeholder="Re-enter your password"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <label class="check"
            ><input type="checkbox" name="terms" id="regTerms" required /><span
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

    <!-- ============ FORGOT PASSWORD MODAL ============ -->
    <div
      class="modal auth-modal forgot-modal"
      id="forgotModal"
      role="dialog"
      aria-modal="true"
      aria-labelledby="forgotTitle"
      aria-describedby="forgotLead"
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
        <p class="forgot__step" id="forgotStep">Step 1 of 3</p>
        <h2 class="auth-modal__title" id="forgotTitle">Forgot password</h2>
        <p class="forgot__lead" id="forgotLead"></p>

        <form class="auth-form" id="forgotEmailForm" novalidate>
          <div class="field">
            <label for="forgotEmail">Email address</label>
            <input
              type="email"
              id="forgotEmail"
              name="email"
              autocomplete="email"
              required
              placeholder="you@example.com"
              data-autofocus
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <button class="btn btn--gold btn--block" type="submit">
            <span>Submit</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
        </form>

        <form class="auth-form" id="forgotOtpForm" novalidate hidden>
          <div class="field">
            <label for="forgotOtp">Email OTP</label>
            <input
              type="text"
              id="forgotOtp"
              name="otp"
              inputmode="numeric"
              autocomplete="one-time-code"
              maxlength="6"
              required
              placeholder="Enter 6-digit code"
            />
            <span class="otp__hint" id="forgotOtpHint"></span>
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="auth-form__row">
            <button
              class="text-link text-link--small"
              type="button"
              id="forgotChangeEmail"
            >
              Change email
            </button>
            <button
              class="text-link text-link--small"
              type="button"
              id="forgotResend"
            >
              Resend code
            </button>
          </div>
          <button class="btn btn--gold btn--block" type="submit">
            <span>Submit</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" />
            </svg>
          </button>
        </form>

        <form class="auth-form" id="forgotResetForm" novalidate hidden>
          <div class="field">
            <label for="forgotNewPassword">New password</label>
            <input
              type="password"
              id="forgotNewPassword"
              name="newPassword"
              autocomplete="new-password"
              required
              minlength="6"
              placeholder="Minimum 6 characters"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <div class="field">
            <label for="forgotConfirmPassword">Confirm new password</label>
            <input
              type="password"
              id="forgotConfirmPassword"
              name="confirmNewPassword"
              autocomplete="new-password"
              required
              minlength="6"
              data-match="#forgotNewPassword"
              placeholder="Re-enter your new password"
            />
            <span class="field__error" aria-live="polite"></span>
          </div>
          <button class="btn btn--gold btn--block" type="submit">
            <span>Save New Password</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-check" />
            </svg>
          </button>
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
    <div
      class="modal shoot-book sponsor-enquiry"
      id="sponsorEnquiryModal"
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
          <p class="info__label">IDFR · Season 5</p>
          <h2
            class="auth-modal__title shoot-book__title"
            id="sponsorEnquiryTitle"
          >
            Sponsorship <em>Enquiry</em>
          </h2>
          <p class="shoot-book__lead">
            Tell us about your interest in sponsoring an IDFR fashion show.
            Fields marked <span aria-hidden="true">*</span
            ><span class="visually-hidden">with an asterisk</span> are required.
            We’ll follow up at
            <a href="mailto:info@idfr.example">info@idfr.example</a>
            with the relevant sponsorship details.
          </p>

          <form class="profile-form" id="sponsorEnquiryForm" novalidate>
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
                maxlength="20"
                placeholder="+91 98765 43210"
              />
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="field">
              <label for="sponsorType"
                >Type of Sponsorship <span aria-hidden="true">*</span></label
              >
              <select id="sponsorType" name="type" required>
                <option value="" selected disabled>Select a sponsorship type</option>
                <option value="Title Sponsor">Title Sponsor</option>
                <option value="Associate Sponsors">Associate Sponsors</option>
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
                placeholder="Share any additional details, goals or questions about sponsoring IDFR"
              ></textarea>
              <span class="field__error" aria-live="polite"></span>
            </div>

            <div class="profile-form__actions profile-form__full">
              <button class="btn btn--gold" type="submit">
                <span>Send Me Details</span
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
            Enquiry <em>sent</em>
          </h2>
          <p class="shoot-book__message" role="status">
            Thank you for your interest in sponsoring IDFR. Your enquiry is ready
            to send to
            <strong>info@idfr.example</strong>. Our team will follow up with the
            relevant sponsorship details.
          </p>
          <p class="shoot-book__note">
            If your email app did not open, please write to
            <a href="mailto:info@idfr.example?subject=Sponsorship%20Enquiry"
              >info@idfr.example</a
            >
            with your sponsorship enquiry.
          </p>
          <button class="btn btn--gold" type="button" data-close>
            <span>Close</span>
          </button>
        </div>
      </div>
    </div>

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