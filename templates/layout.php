<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Drive Master | Certified Professional Driving School') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'Drive Safe & Smart With Our School. Top certified driving school with 98.4% first-time pass rate, modern dual-control automatic and manual fleet.') ?>">

  <base href="/">

  <!-- Instant Clean URL Manager (Ensures clean address bar, strips trailing slashes & invalid queries) -->
  <script>
    (function () {
      try {
        var p = window.location.pathname;
        var h = window.location.hash;
        var s = window.location.search;

        // Strip trailing slash if present (e.g. /home/ -> /home)
        if (p.length > 1 && p.endsWith('/')) {
          p = p.replace(/\/+$/, '');
        }

        // Sanitize queries on pages (strip invalid junk like ?aaa)
        var cleanSearch = '';
        if (s && s.length > 1) {
          var params = new URLSearchParams(s);
          var allowed = ['ref', 'filter', 'tab', 'page', 'course', 'id', 'price', 'source', 'action', 'booking_reference'];
          var validParams = new URLSearchParams();
          allowed.forEach(function (k) {
            if (params.has(k)) validParams.set(k, params.get(k));
          });
          cleanSearch = validParams.toString() ? ('?' + validParams.toString()) : '';
        }

        if (p === '/' || p === '' || p.endsWith('/index.html') || p === '/index.html' || p === 'index.html' || p.endsWith('/index.php')) {
          var base = p.replace(/(^|\/)index\.(html|php)$/i, '');
          if (base === '/') base = '';
          var clean = (base || '') + '/home' + cleanSearch + h;
          window.history.replaceState(null, '', clean);
        } else if (p.endsWith('.html')) {
          var clean = p.replace(/\.html$/i, '') + cleanSearch + h;
          window.history.replaceState(null, '', clean);
        } else if (window.location.pathname.endsWith('/') || (s && s !== cleanSearch)) {
          window.history.replaceState(null, '', p + cleanSearch + h);
        }

        if ('serviceWorker' in navigator) {
          navigator.serviceWorker.register('sw.js').catch(function () {});
        }
      } catch (e) {}
    })();
  </script>

  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22></text></svg>">

  <!-- Local Fonts: Plus Jakarta Sans & Space Grotesk -->
  <link rel="stylesheet" href="assets/vendor/fonts/fonts.css">

  <!-- Bootstrap 5.3.3 CSS -->
  <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="assets/vendor/bootstrap-icons/bootstrap-icons.min.css">

  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">

  <?= $extraCss ?? '' ?>
</head>
<body>

  <!-- Top Dark Green Header Strip -->
  <div class="topbar-strip">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="d-flex align-items-center">
        <span class="topbar-chevron-pattern">
          <i class="bi bi-chevron-right"></i>
          <i class="bi bi-chevron-right"></i>
          <i class="bi bi-chevron-right"></i>
        </span>
        <i class="bi bi-grid-fill me-2 text-success"></i>
        <span>Driving, Start Dialing <a href="tel:+66120003456" class="text-white fw-bold text-decoration-none ms-1">+66 12 000 3456</a></span>
      </div>

      <div class="d-flex align-items-center gap-3">
        <a href="about" class="topbar-link">
          <i class="bi bi-geo-alt-fill text-success me-1"></i> Starting Locations <i class="bi bi-chevron-down ms-1 small"></i>
        </a>
        <span class="topbar-divider"></span>
        <a href="courses" class="topbar-link">
          <i class="bi bi-clock-fill text-success me-1"></i> Mor Classes: 7.00 to 11.00 <i class="bi bi-chevron-down ms-1 small"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Main Navbar (Clean White Background) -->
  <header class="site-header">
    <div class="container">
      <nav class="navbar navbar-expand-lg py-3">
        <!-- Logo -->
        <a class="navbar-brand-custom" href="home">
          <div class="brand-icon-logo">
            <i class="bi bi-speedometer2"></i>
          </div>
          <div class="brand-title-wrap">
            <span class="brand-title-top">Drive</span>
            <span class="brand-title-bottom">
              <span class="chevrons">&gt;&gt;&gt;</span>Master
            </span>
          </div>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navContent" aria-controls="navContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list fs-2 text-dark"></i>
        </button>

        <!-- Nav Items -->
        <div class="collapse navbar-collapse" id="navContent">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link <?= ($activePage ?? 'home') === 'home' ? 'active' : '' ?>" href="home">
                Home
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($activePage ?? '') === 'about' ? 'active' : '' ?>" href="about">
                About
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($activePage ?? '') === 'courses' ? 'active' : '' ?>" href="courses">
                Our Package
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($activePage ?? '') === 'gallery' ? 'active' : '' ?>" href="gallery">
                Gallery
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($activePage ?? '') === 'contact' ? 'active' : '' ?>" href="contact">
                Contact
              </a>
            </li>
          </ul>

          <!-- Right Action Tools & Start Driving Button -->
          <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
            <a href="courses" class="nav-icon-action" title="Search Courses" aria-label="Search">
              <i class="bi bi-search"></i>
            </a>
            <a href="contact" class="nav-icon-action" title="Student Portal" aria-label="User Account">
              <i class="bi bi-person-circle"></i>
            </a>
            <a href="about" class="nav-icon-action" title="Instructors &amp; Services" aria-label="Grid Menu">
              <i class="bi bi-grid-3x3-gap-fill"></i>
            </a>

            <!-- Shopping Cart Action Link (Direct to Cart Page) -->
            <a href="cart" class="nav-icon-action position-relative <?= ($activePage ?? '') === 'cart' ? 'active' : '' ?>" title="View Shopping Cart" aria-label="Cart">
              <i class="bi bi-cart3"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-total-count cart-badge" style="font-size: 0.65rem; display: none;">0</span>
            </a>

            <!-- Reference START DRIVING Chevron Ribbon Button -->
            <a href="courses" class="btn-start-driving-wrap ms-2">
              <span class="btn-start-driving-label">START DRIVING</span>
              <span class="btn-start-driving-arrows">&gt;&gt;&gt;</span>
            </a>
          </div>
        </div>
      </nav>
    </div>
  </header>

  <!-- ==========================================================================
       PAGE UNIQUE CONTENT (Rendered dynamically)
       ========================================================================== -->
  <main id="main-content">
    <?= $content ?? '' ?>
  </main>

  <!-- ==========================================================================
       GLOBAL NEWSLETTER SECTION
       ========================================================================== -->
  <section class="newsletter-section" id="newsletter" aria-labelledby="newsletterTitle">
    <div class="container">
      <div class="newsletter-card">
        <span class="newsletter-deco-chevrons" aria-hidden="true">&gt;&gt;&gt;</span>
        <div class="row align-items-center g-4 position-relative">
          <div class="col-lg-6">
            <span class="newsletter-eyebrow">
              <i class="bi bi-envelope-paper-heart-fill"></i> Drive Master Newsletter
            </span>
            <h2 class="newsletter-title" id="newsletterTitle">
              Get Driving Tips &amp; <span>Exclusive Offers</span>
            </h2>
            <p class="newsletter-desc">
              Join our learner community for test-day checklists, road-safety tips and subscriber-only lesson discounts — delivered straight to your inbox.
            </p>
            <ul class="newsletter-perks">
              <li><i class="bi bi-check-circle-fill"></i> Monthly test prep tips</li>
              <li><i class="bi bi-check-circle-fill"></i> Early access to offers</li>
              <li><i class="bi bi-check-circle-fill"></i> No spam, ever</li>
            </ul>
          </div>

          <div class="col-lg-6">
            <form id="newsletterForm" class="newsletter-form" novalidate>
              <label for="newsletterEmail" class="visually-hidden">Email address</label>
              <div class="newsletter-input-group">
                <i class="bi bi-envelope newsletter-input-icon" aria-hidden="true"></i>
                <input type="email" id="newsletterEmail" name="email" class="newsletter-input" placeholder="Enter your email address" autocomplete="email" maxlength="254" required>
                <button type="submit" id="btnNewsletterSubscribe" class="newsletter-btn">
                  <span class="newsletter-btn-label">Subscribe</span>
                  <i class="bi bi-send-fill"></i>
                </button>
              </div>
              <!-- Honeypot (hidden from humans) -->
              <input type="text" name="website" id="newsletterWebsite" class="newsletter-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
              <div id="newsletterFeedback" class="newsletter-feedback" role="status" aria-live="polite"></div>
              <p class="newsletter-note">
                <i class="bi bi-shield-lock-fill"></i> We respect your privacy and never share your email.
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       GLOBAL FOOTER SECTION
       ========================================================================== -->
  <footer class="site-footer" id="contact">
    <div class="container">
      <!-- Pre-Footer Action Banner -->
      <div class="footer-cta-box">
        <div class="row align-items-center">
          <div class="col-lg-8 mb-3 mb-lg-0">
            <h3 class="footer-cta-title">Ready To Hit The Road &amp; Pass Your Test?</h3>
            <p class="footer-cta-desc">
              Book your introductory lesson today and get 15% off your first driving session. Friendly, patient instructors ready in your area.
            </p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <a href="tel:+66120003456" class="btn-footer-cta">
              <i class="bi bi-telephone-fill"></i> Call +66 12 000 3456
            </a>
          </div>
        </div>
      </div>

      <!-- Main Footer Columns -->
      <div class="row g-4 pb-4">
        <!-- Col 1: Brand & About -->
        <div class="col-lg-4 col-md-6">
          <a class="navbar-brand-custom mb-3 d-inline-flex" href="home">
            <div class="brand-icon-logo">
              <i class="bi bi-speedometer2"></i>
            </div>
            <div class="brand-title-wrap">
              <span class="brand-title-top text-white">Drive</span>
              <span class="brand-title-bottom text-white">
                <span class="chevrons">&gt;&gt;&gt;</span>Master
              </span>
            </div>
          </a>
          <p class="footer-about-text">
            Premier DVSA certified driving academy dedicated to transforming nervous beginners into confident, responsible drivers with dual-control vehicles and guaranteed pass support.
          </p>
          <div class="footer-social-wrap">
            <a href="#" class="footer-social-btn" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="footer-social-btn" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="footer-social-btn" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="#" class="footer-social-btn" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
            <a href="#" class="footer-social-btn" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <!-- Col 2: Navigation Links -->
        <div class="col-lg-2 col-md-6 col-6">
          <h4 class="footer-col-title">Quick Links</h4>
          <ul class="footer-nav-list">
            <li><a href="home"><i class="bi bi-chevron-right small"></i> Home</a></li>
            <li><a href="about"><i class="bi bi-chevron-right small"></i> About Us</a></li>
            <li><a href="home#features"><i class="bi bi-chevron-right small"></i> Features</a></li>
            <li><a href="courses"><i class="bi bi-chevron-right small"></i> Our Package</a></li>
            <li><a href="home#why-choose-us"><i class="bi bi-chevron-right small"></i> Why Choose Us</a></li>
            <li><a href="gallery"><i class="bi bi-chevron-right small"></i> Photo Gallery</a></li>
            <li><a href="contact"><i class="bi bi-chevron-right small"></i> Contact Us</a></li>
          </ul>
        </div>

        <!-- Col 3: Popular Courses -->
        <div class="col-lg-3 col-md-6 col-6">
          <h4 class="footer-col-title">Our Courses</h4>
          <ul class="footer-nav-list">
            <li><a href="courses"><i class="bi bi-chevron-right small"></i> Automatic Driving Lessons</a></li>
            <li><a href="courses"><i class="bi bi-chevron-right small"></i> Manual Transmission Course</a></li>
            <li><a href="courses"><i class="bi bi-chevron-right small"></i> Intensive Fast-Track Pass</a></li>
            <li><a href="courses"><i class="bi bi-chevron-right small"></i> Motorway &amp; Highway Training</a></li>
            <li><a href="courses"><i class="bi bi-chevron-right small"></i> Refresher &amp; Pass Plus</a></li>
          </ul>
        </div>

        <!-- Col 4: Contact & Working Hours -->
        <div class="col-lg-3 col-md-6">
          <h4 class="footer-col-title">Get In Touch</h4>
          <div class="footer-contact-item">
            <i class="bi bi-geo-alt-fill"></i>
            <div>
              124 Highway Central Blvd, Suite 400<br>
              City Driving Test Center District
            </div>
          </div>
          <div class="footer-contact-item">
            <i class="bi bi-telephone-fill"></i>
            <div>
              <a href="tel:+66120003456">+66 12 000 3456</a><br>
              <span class="small text-muted">24/7 Student Booking Line</span>
            </div>
          </div>
          <div class="footer-contact-item">
            <i class="bi bi-envelope-fill"></i>
            <div>
              <a href="mailto:info@drivemaster-school.com">info@drivemaster-school.com</a>
            </div>
          </div>
          <div class="footer-contact-item">
            <i class="bi bi-clock-fill"></i>
            <div>
              Mon - Sun: 7:00 AM - 9:00 PM<br>
              <span class="badge bg-success-subtle text-success small px-2 py-1 mt-1">Open All Week</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Copyright Bottom Bar -->
      <div class="footer-bottom">
        <div class="row align-items-center">
          <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
            &copy; 2026 DriveMaster Driving School. All rights reserved.
          </div>
          <div class="col-md-6 text-center text-md-end">
            <a href="backend/swagger/" target="_blank" class="me-3 text-decoration-none"><i class="bi bi-code-slash me-1"></i>Swagger API</a>
            <a href="admin/" target="_blank" class="me-3 text-decoration-none"><i class="bi bi-shield-lock me-1"></i>Admin Portal</a>
            <a href="#" class="me-3">Privacy Policy</a>
            <a href="#">DVSA Approved</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Square Test Sandbox is now hosted on its dedicated checkout page (/checkout) -->



  <!-- Floating Sticky Cart Trigger (Direct link to Cart Page) -->
  <a href="cart" class="floating-cart-btn text-decoration-none" title="View Full Shopping Cart">
    <i class="bi bi-cart-fill fs-5"></i>
    <span>Cart (<span class="cart-total-count">0</span>) &bull; <span id="floatingCartTotal">$0.00</span></span>
  </a>

  <!-- Cart Notification Toast with Direct View Cart Action -->
  <div class="cart-toast-wrap">
    <div class="cart-toast shadow-lg" id="cartNotificationToast">
      <div class="d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill text-success fs-5"></i>
          <div>
            <strong id="toastItemTitle">Package</strong> added to cart!
          </div>
        </div>
        <a href="cart" class="btn btn-sm btn-light py-1 px-3 fw-bold text-dark rounded-pill text-nowrap" style="font-size: 0.78rem;">
          View Cart &rarr;
        </a>
      </div>
    </div>
  </div>

  <!-- Local Vendor Scripts -->
  <script src="assets/vendor/jquery/jquery-3.7.1.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Square Web Payments SDK (Official Sandbox CDN) -->
  <script type="text/javascript" src="https://sandbox.web.squarecdn.com/v1/square.js"></script>
  <script src="assets/js/main.js?v=3.0"></script>

  <?= $extraJs ?? '' ?>
</body>
</html>
