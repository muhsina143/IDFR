<!-- Universal Profile Booking Enquiry Template for Models & Creative Professionals -->
<template id="modelEnquiryTemplate">
  <button class="enquiry__back" type="button" data-enquiry-back>
    <svg class="icon" aria-hidden="true"><use href="#i-arrow-left" /></svg>
    Back to profile
  </button>

  <p class="mm__label" style="margin-top: 1.25rem">Booking Enquiry</p>
  <h2 class="mm__name" id="modelModalName">Book</h2>
  <p class="mm__types" id="modelModalCategory" style="margin-top: 0.25rem; color: var(--gold); font-size: 0.85rem; font-weight: 500;"></p>

  <form class="enquiry" id="enquiryForm" novalidate>
    <!-- Automatic Association Fields -->
    <input type="hidden" name="professional_type" id="enqProType" value="model">
    <input type="hidden" name="professional_id" id="enqProId" value="">
    <input type="hidden" name="professional_name" id="enqProName" value="">
    <input type="hidden" name="professional_category" id="enqProCategory" value="Model">

    <div class="field-row">
      <div class="field">
        <label for="enqName">Full Name *</label>
        <input
          id="enqName"
          name="name"
          type="text"
          autocomplete="name"
          required
          minlength="2"
          placeholder="Your full name"
        />
        <span class="field__error" aria-live="polite"></span>
      </div>

      <div class="field">
        <label for="enqPhone">Phone (with country code) *</label>
        <input
          id="enqPhone"
          name="phone"
          type="tel"
          autocomplete="tel"
          required
          placeholder="+91 9876543210"
        />
        <span class="field__error" aria-live="polite"></span>
      </div>
    </div>

    <!-- Email & OTP Verification -->
    <div class="field">
      <div style="display: flex; justify-content: space-between; align-items: baseline;">
        <label for="enqEmail">Email Address (Requires OTP) *</label>
        <span id="enqEmailVerifiedBadge" style="display: none; color: #4ade80; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
          &#10003; Email Verified
        </span>
      </div>
      <div style="display: flex; gap: 0.5rem;">
        <input
          id="enqEmail"
          name="email"
          type="email"
          autocomplete="email"
          required
          placeholder="you@company.com"
          style="flex: 1;"
        />
        <button type="button" class="btn btn--ghost" id="enqSendOtpBtn" style="white-space: nowrap; padding: 0 1.25rem; font-size: 0.75rem;">
          <span>Send OTP</span>
        </button>
      </div>
      <span class="field__error" id="enqEmailError" aria-live="polite"></span>
    </div>

    <!-- OTP Input Section (shown after sending OTP) -->
    <div class="field" id="enqOtpSection" style="display: none; background: rgba(229, 184, 61, 0.06); border: 1px dashed rgba(229, 184, 61, 0.35); padding: 0.85rem; border-radius: 4px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
        <label for="enqOtp" style="margin-bottom: 0; color: var(--gold);">Enter 6-Digit Code *</label>
        <span id="enqOtpTimer" style="font-size: 0.72rem; color: var(--white-70);">Expires in 05:00</span>
      </div>
      <div style="display: flex; gap: 0.5rem;">
        <input
          id="enqOtp"
          name="otp"
          type="text"
          maxlength="6"
          pattern="[0-9]{6}"
          inputmode="numeric"
          placeholder="6-digit OTP"
          style="letter-spacing: 4px; font-weight: 700; text-align: center; flex: 1;"
        />
        <button type="button" class="btn btn--gold" id="enqVerifyOtpBtn" style="white-space: nowrap; padding: 0 1.25rem; font-size: 0.75rem;">
          <span>Verify Code</span>
        </button>
      </div>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.35rem;">
        <span class="field__error" id="enqOtpStatus" aria-live="polite" style="margin: 0;"></span>
        <button type="button" id="enqResendOtpBtn" style="background: none; border: none; color: var(--gold); font-size: 0.72rem; cursor: pointer; text-decoration: underline; padding: 0;" disabled>
          Resend Code (60s)
        </button>
      </div>
    </div>

    <div class="field-row">
      <div class="field">
        <label for="enqType">Assignment Type *</label>
        <select id="enqType" name="assignment_type" required>
          <option value="Runway Show">Runway Show</option>
          <option value="Photoshoot">Photoshoot</option>
          <option value="Fashion Campaign">Fashion Campaign</option>
          <option value="Commercial Advertisement">Commercial Advertisement</option>
          <option value="Brand Promotion">Brand Promotion</option>
          <option value="Event">Event</option>
          <option value="Other">Other</option>
        </select>
        <span class="field__error" aria-live="polite"></span>
      </div>

      <div class="field">
        <label for="enqDate">Preferred Date</label>
        <input id="enqDate" name="preferred_date" type="date" />
        <span class="field__error" aria-live="polite"></span>
      </div>
    </div>

    <div class="field">
      <label for="enqLocation">Location / Venue *</label>
      <input
        id="enqLocation"
        name="location"
        type="text"
        required
        placeholder="City or Event Location"
      />
      <span class="field__error" aria-live="polite"></span>
    </div>

    <div class="field">
      <label for="enqBrief">Brief / Assignment Description *</label>
      <textarea
        id="enqBrief"
        name="brief"
        required
        minlength="10"
        placeholder="Please describe the project scope, theme, dates, and requirements..."
      ></textarea>
      <span class="field__error" aria-live="polite"></span>
    </div>

    <div id="enqFormStatus" style="display: none; padding: 0.75rem 1rem; border-radius: 4px; font-size: 0.85rem; margin-bottom: 0.5rem;"></div>

    <button class="btn btn--gold btn--block" type="submit" id="enqSubmitBtn">
      <span>Submit Booking Enquiry</span>
      <svg class="icon" aria-hidden="true"><use href="#i-arrow-right" /></svg>
    </button>
  </form>
</template>

