<footer class="footer" id="contact">
      <div class="container footer__cta">
        <div class="footer__cta-copy" data-reveal="up">
          <p class="section-label">
            ASDreamStudio · Photography &amp; Videography
          </p>
          <h2 class="footer__cta-title">
            Your story, <em>beautifully framed.</em>
          </h2>
          <p class="footer__cta-text">
            Fashion, weddings, events and films — captured with an editorial eye
            and delivered with cinematic polish.
          </p>
        </div>
        <div class="footer__cta-actions" data-reveal="up" data-delay="150">
          <a class="btn btn--gold" href="{{ route('asd.gallery') }}"
            ><span>Go to Gallery</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" /></svg
          ></a>
          <a class="btn btn--ghost" href="{{ route('asd.contact') }}"
            ><span>Contact Us</span
            ><svg class="icon" aria-hidden="true">
              <use href="#i-arrow-right" /></svg
          ></a>
        </div>
      </div>

      <div class="container footer__main">
        <!-- <div class="footer__col footer__brand">
          <a href="{{ route('asd.home') }}#home" class="footer__logo" aria-label="IDFR — back to home" data-skip-brands-pop>
            <img
              src="{{ asset('images/asdreams/asd-logo.png') }}"
              alt="ASDreamStudio"
              width="666"
              height="470"
              loading="lazy"
            />
          </a>
          <p>
            ASDreamStudio captures life's meaningful moments through creative
            photography and cinematic videography — crafting timeless stories
            for fashion, weddings, brands and events.
          </p>
          <ul class="socials" aria-label="IDFR on social media">
            <li>
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noopener"
                aria-label="Instagram"
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-instagram" /></svg
              ></a>
            </li>
            <li>
              <a
                href="https://facebook.com"
                target="_blank"
                rel="noopener"
                aria-label="Facebook"
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-facebook" /></svg
              ></a>
            </li>
            <li>
              <a
                href="https://youtube.com"
                target="_blank"
                rel="noopener"
                aria-label="YouTube"
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-youtube" /></svg
              ></a>
            </li>
          </ul>
        </div> -->
        <div class="footer__col footer__brand">
          <a href="{{ route('asd.home') }}#home" class="footer__logo" aria-label="IDFR — back to home" data-skip-brands-pop>
            <img
              src="{{ asset('images/asdreams/asd-logo.png') }}"
              alt="ASDreamStudio"
              width="666"
              height="470"
              loading="lazy"
            />
          </a>
          <p>
            ASDreamStudio captures life's meaningful moments through creative
            photography and cinematic videography — crafting timeless stories
            for fashion, weddings, brands and events.
          </p>
          <ul class="socials" aria-label="IDFR on social media">
            <li>
              <a
                href="https://instagram.com"
                target="_blank"
                rel="noopener"
                aria-label="Instagram"
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-instagram" /></svg
              ></a>
            </li>
            <li>
              <a
                href="https://facebook.com"
                target="_blank"
                rel="noopener"
                aria-label="Facebook"
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-facebook" /></svg
              ></a>
            </li>
            <li>
              <a
                href="https://youtube.com"
                target="_blank"
                rel="noopener"
                aria-label="YouTube"
                ><svg class="icon" aria-hidden="true">
                  <use href="#i-youtube" /></svg
              ></a>
            </li>
          </ul>
        </div>

        <nav class="footer__col" aria-labelledby="footerQuick">
          <h3 class="footer__title" id="footerQuick">Quick Links</h3>
          <ul class="footer__links">
            <li><a href="{{ route('asd.home') }}">Home</a></li>
            <li><a href="{{ route('asd.about') }}">About</a></li>
            <li><a href="{{ route('asd.gallery') }}">Gallery</a></li>
            <li><a href="{{ route('asd.contact') }}">Contact</a></li>
          </ul>
        </nav>

        <nav class="footer__col" aria-labelledby="footerPrograms">
          <h3 class="footer__title" id="footerPrograms">Useful Links</h3>
          <ul class="footer__links">
            <li><a href="{{ route('idfr.home') }}">IDFR</a></li>
            <li>
              <a href="#season-4" data-program="season-4"
                >Fashion Show Season 4</a
              >
            </li>
            <li>
              <a href="#register" data-auth="register" data-role="fresher"
                >Model Registration</a
              >
            </li>
            <li>
              <a href="#modelling-academy" data-program="modelling-academy"
                >Modelling Academy</a
              >
            </li>
            <li>
              <a href="#lakme-academy" data-program="lakme-academy"
                >Lakme Academy</a
              >
            </li>
          </ul>
        </nav>

        <div class="footer__col">
          <h3 class="footer__title">Contact</h3>
          <address class="footer__contact">
            <p>
              <svg class="icon" aria-hidden="true">
                <use href="#i-pin" /></svg
              ><span>IDFR Fashion House,<br />Kozhikode, Kerala, India</span>
            </p>
            <p>
              <svg class="icon" aria-hidden="true">
                <use href="#i-phone" /></svg
              ><a href="tel:+919876543210">+91 98765 43210</a>
            </p>
            <p>
              <svg class="icon" aria-hidden="true">
                <use href="#i-mail" /></svg
              ><a href="mailto:info@idfr.example">info@idfr.example</a>
            </p>
          </address>
        </div>
      </div>

      <div class="container footer__bottom">
        <span class="footer__divider" aria-hidden="true"></span>
        <div class="footer__bottom-row">
          <p>
            &copy; <span id="year">2026</span> IDFR. All Rights Reserved|
            Designed by: Astraz SoftwareSolutions.
          </p>
          <ul class="footer__legal">
            <li>
              <a href="#privacy" data-program="privacy">Privacy Policy</a>
            </li>
            <li>
              <a href="#terms" data-program="terms">Terms &amp; Conditions</a>
            </li>
          </ul>
        </div>
      </div>
    </footer>

    <button class="to-top" type="button" id="toTop" aria-label="Back to top">
      <svg class="icon" aria-hidden="true">
        <use href="#i-arrow-up" />
      </svg>
    </button>