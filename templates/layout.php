<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Drive Master | Certified Professional Driving School') ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription ?? 'Drive Safe & Smart With Our School. Top certified driving school with 98.4% first-time pass rate, modern dual-control automatic and manual fleet.') ?>">

  <!-- Instant Clean URL Manager (Ensures clean address bar and zero .html) -->
  <script>
    (function () {
      try {
        var p = window.location.pathname;
        var h = window.location.hash;
        var s = window.location.search;
        if (p === '/' || p === '' || p.endsWith('/index.html') || p === '/index.html' || p === 'index.html' || p.endsWith('/index.php')) {
          var base = p.replace(/(^|\/)index\.(html|php)$/i, '');
          if (base === '/') base = '';
          var clean = (base || '') + '/home' + s + h;
          window.history.replaceState(null, '', clean);
        } else if (p.endsWith('.html')) {
          var clean = p.replace(/\.html$/i, '') + s + h;
          window.history.replaceState(null, '', clean);
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

  <!-- ==========================================================================
       GLOBAL SQUARE DUMMY PAYMENT GATEWAY & ADMIN APPROVAL MODAL
       ========================================================================== -->
  <div class="modal fade" id="squarePaymentModal" tabindex="-1" aria-labelledby="squarePaymentModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        
        <!-- Modal Top Bar with Square Branding -->
        <div class="modal-header bg-dark text-white px-4 py-3 border-0">
          <div class="d-flex align-items-center gap-2">
            <span class="square-brand-badge">
              <i class="bi bi-square-fill"></i> Square
            </span>
            <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.72rem;">
              <i class="bi bi-cpu-fill me-1"></i> TEST SANDBOX
            </span>
            <span class="small text-white-50 ms-1 d-none d-sm-inline">| SMTP Email Connected</span>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-4">
          <!-- STAGE 1: Student Information & Request -->
          <div id="sqStageRequest">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
              <div>
                <span class="text-muted small">Selected Driving Course</span>
                <h5 class="fw-bold mb-0 text-dark" id="sqCourseTitle">Standard Pass Course</h5>
              </div>
              <div class="text-end">
                <span class="badge bg-success-subtle text-success fs-6 fw-bold" id="sqCoursePrice">$780.00</span>
              </div>
            </div>

            <form id="sqEnrollmentForm">
              <input type="hidden" id="sqCourseId" value="1">
              <div class="mb-3">
                <label for="sqStudentName" class="form-label small fw-bold">Full Name *</label>
                <input type="text" class="form-control rounded-3" id="sqStudentName" placeholder="e.g. Alex Johnson" required>
              </div>

              <div class="mb-3">
                <label for="sqStudentEmail" class="form-label small fw-bold">Email Address (For SMTP Receipt) *</label>
                <input type="email" class="form-control rounded-3" id="sqStudentEmail" placeholder="student@example.com" required>
              </div>

              <div class="mb-3">
                <label for="sqStudentPhone" class="form-label small fw-bold">Phone Number *</label>
                <input type="tel" class="form-control rounded-3" id="sqStudentPhone" placeholder="+66 12 000 3456" required>
              </div>

              <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <div>
                  <strong>Next step:</strong> Submit details. Our administrator verifies your spot and unlocks your Square Test Sandbox payment.
                </div>
              </div>

              <button type="submit" id="btnSqSubmitRequest" class="btn btn-primary w-100 py-2 fw-bold rounded-pill">
                <i class="bi bi-send-fill me-1"></i> Send Request &amp; Wait For Approval
              </button>
            </form>
          </div>

          <!-- STAGE 2: Waiting for Admin Approval Screen -->
          <div id="sqStageWaiting" style="display: none;" class="text-center py-2">
            <div class="spinner-pulse-wrap mb-3">
              <div class="spinner-grow text-warning" style="width: 3.5rem; height: 3.5rem;" role="status"></div>
              <i class="bi bi-hourglass-split fs-2 text-warning"></i>
            </div>
            <h5 class="fw-bold mb-1 text-dark">Payment Request Sent to Admin!</h5>
            <p class="text-muted small mb-3">
              Your booking for <strong id="sqWaitCourse">Standard Pass Course</strong> has been emailed to the administrator via SMTP and is awaiting approval.
            </p>

            <div class="card bg-light border p-3 rounded-3 mb-3 text-start small">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Booking Reference:</span>
                <span class="badge bg-dark font-monospace" id="sqWaitRef">DM-SQ-XXXXX</span>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Payment Status:</span>
                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> Awaiting Admin Approval</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Total Payable:</span>
                <strong class="text-success" id="sqWaitAmount">$780.00</strong>
              </div>
            </div>

            <div class="p-2 mb-3 bg-warning-subtle text-dark rounded-3 small">
              <div class="spinner-grow spinner-grow-sm text-warning me-1" role="status"></div>
              <strong>Live Auto-Check Active:</strong> Checking every 3s. As soon as the admin approves, an email is dispatched to your inbox and this window will automatically unlock Square checkout!
            </div>

            <div class="d-flex gap-2 justify-content-center mb-3">
              <button type="button" id="btnCheckStatusManual" class="btn btn-outline-dark btn-sm fw-bold px-3">
                <i class="bi bi-arrow-clockwise me-1"></i> Check Status Now
              </button>
              <a href="/admin/enrollments.php" target="_blank" class="btn btn-outline-success btn-sm fw-bold px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> Open Admin Panel
              </a>
            </div>

            <div class="small text-muted" style="font-size: 0.78rem;">
              <i class="bi bi-lightbulb text-warning me-1"></i> <em>Demo Tip: Open Admin Panel in another tab, go to <strong>Course Bookings</strong>, and click <strong>"Approve Payment"</strong> to test live unlock!</em>
            </div>
          </div>

          <!-- STAGE 3: Approved! Enter Square Card Details -->
          <div id="sqStagePayment" style="display: none;">
            <div class="alert alert-success py-2 px-3 small d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-check-circle-fill text-success fs-5"></i>
              <div>
                <strong>Approved by Admin!</strong> You may now complete your test transaction below.
              </div>
            </div>

            <div class="square-credit-card-form p-3 border rounded-3 bg-light mb-3">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold small text-dark"><i class="bi bi-credit-card-2-front-fill me-1 text-primary"></i> Square Card Sandbox</span>
                <div class="d-flex gap-1">
                  <span class="badge bg-white text-dark border">VISA</span>
                  <span class="badge bg-white text-dark border">MC</span>
                  <span class="badge bg-white text-dark border">AMEX</span>
                </div>
              </div>

              <!-- Square Dummy Card Interface -->
              <form id="sqCardForm">
                <div class="mb-3">
                  <label class="form-label small text-muted mb-1">Card Number</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-credit-card"></i></span>
                    <input type="text" class="form-control" id="sqDummyCardNum" placeholder="4111 2222 3333 4444" maxlength="19" value="4111 2222 3333 4444" required>
                  </div>
                </div>

                <div class="row g-2 mb-3">
                  <div class="col-6">
                    <label class="form-label small text-muted mb-1">Expiry Date</label>
                    <input type="text" class="form-control" id="sqDummyExpiry" placeholder="MM/YY" maxlength="5" value="12/28" required>
                  </div>
                  <div class="col-6">
                    <label class="form-label small text-muted mb-1">CVV / CVC</label>
                    <input type="text" class="form-control" id="sqDummyCvv" placeholder="123" maxlength="4" value="789" required>
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label small text-muted mb-1">Cardholder Name</label>
                  <input type="text" class="form-control" id="sqDummyCardHolder" placeholder="Name on card" value="Alex Johnson" required>
                </div>

                <!-- Digital Wallets Dummy Options -->
                <div class="d-flex gap-2 mb-3">
                  <div class="p-2 border rounded-3 bg-light text-center flex-grow-1 small text-muted">
                    <i class="bi bi-apple me-1"></i> Apple Pay
                  </div>
                  <div class="p-2 border rounded-3 bg-light text-center flex-grow-1 small text-muted">
                    <i class="bi bi-google me-1"></i> Google Pay
                  </div>
                  <div class="p-2 border rounded-3 bg-light text-center flex-grow-1 small text-muted">
                    <i class="bi bi-cash me-1"></i> Cash App Pay
                  </div>
                </div>

                <button type="submit" id="btnSquarePaySubmit" class="square-pay-btn">
                  <i class="bi bi-lock-fill"></i>
                  <span>Pay <span id="sqPayBtnAmount">$780.00</span> via Square Sandbox</span>
                </button>
              </form>
            </div>

            <div class="text-center text-muted small" style="font-size: 0.75rem;">
              <i class="bi bi-shield-lock-fill text-dark me-1"></i> Square Test Sandbox mode &bull; Official receipt will be sent to your email via SMTP
            </div>
          </div>

          <!-- STAGE 4: Payment Confirmed Receipt -->
          <div id="sqStageSuccess" style="display: none;" class="text-center py-2">
            <div class="display-3 text-success mb-2">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <h4 class="fw-bold mb-1 text-dark">Square Payment Successful!</h4>
            <p class="text-muted small mb-3">
              Your driving course booking is confirmed and paid in full. An official receipt has been emailed to you via SMTP.
            </p>

            <div class="square-receipt-box mb-3 small">
              <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                <span class="text-muted">Square Sandbox Tx ID:</span>
                <span class="font-monospace fw-bold text-dark" id="sqReceiptTxId">sq_sandbox_tx_...</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Booking Reference:</span>
                <span class="font-monospace fw-bold" id="sqReceiptRef">DM-SQ-...</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Course:</span>
                <strong id="sqReceiptCourse">Standard Pass Course</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Student Name:</span>
                <span id="sqReceiptName">Alex Johnson</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Payment Method:</span>
                <span id="sqReceiptMethod">Square Sandbox Visa (•••• 4242)</span>
              </div>
              <div class="d-flex justify-content-between mb-2" id="sqReceiptUrlRow" style="display: none;">
                <span class="text-muted">Square Receipt:</span>
                <a href="#" target="_blank" id="sqReceiptUrlLink" class="text-success fw-bold text-decoration-none small">Open Official Receipt <i class="bi bi-box-arrow-up-right ms-1"></i></a>
              </div>
              <div class="d-flex justify-content-between pt-2 border-top">
                <span class="fw-bold">Total Paid:</span>
                <strong class="text-success fs-6" id="sqReceiptTotal">$780.00</strong>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="button" class="btn btn-dark w-100 py-2 rounded-pill fw-bold" data-bs-dismiss="modal">
                Close &amp; Finish
              </button>
              <a href="/admin/enrollments.php" target="_blank" class="btn btn-outline-success w-100 py-2 rounded-pill fw-bold">
                View in Admin
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- ==========================================================================
       GLOBAL SHOPPING CART OFFCANVAS DRAWER
       ========================================================================== -->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="width: 420px; max-width: 95vw;">
    <div class="offcanvas-header border-bottom py-3">
      <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="cartOffcanvasLabel">
        <i class="bi bi-cart3 text-primary"></i> Your Selected Packages
        <span class="badge bg-primary rounded-pill cart-total-count" style="font-size: 0.75rem;">0</span>
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-3">
      <!-- Items Container -->
      <div id="cartItemsContainer" class="flex-grow-1 overflow-auto pe-1">
        <!-- Rendered dynamically via main.js -->
      </div>

      <!-- Cart Footer / Summary -->
      <div id="cartFooterSection" class="border-top pt-3 mt-3">
        <div class="d-flex justify-content-between mb-2 text-muted small">
          <span>Subtotal:</span>
          <span class="fw-bold text-dark" id="cartSubtotalAmount">$0.00</span>
        </div>
        <div class="d-flex justify-content-between mb-3 fs-5">
          <span class="fw-bold">Total:</span>
          <span class="fw-bold text-primary" id="cartFinalTotalAmount">$0.00</span>
        </div>
        <div class="d-grid gap-2">
          <button type="button" class="btn btn-primary py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" id="btnCartProceedSquare">
            <i class="bi bi-square-fill text-warning"></i> Proceed to Square Checkout
          </button>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <button type="button" class="btn btn-link text-danger text-decoration-none p-0 small" id="btnClearCart">
              <i class="bi bi-trash3 me-1"></i> Clear Cart
            </button>
            <span class="text-muted small"><i class="bi bi-shield-check text-success"></i> 2-Step Protected</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Floating Sticky Cart Trigger -->
  <div class="floating-cart-btn" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas" title="Open Cart">
    <i class="bi bi-cart-fill fs-5"></i>
    <span>Cart (<span class="cart-total-count">0</span>) &bull; <span id="floatingCartTotal">$0.00</span></span>
  </div>

  <!-- Cart Notification Toast -->
  <div class="cart-toast-wrap">
    <div class="cart-toast" id="cartNotificationToast">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill text-success fs-5"></i>
        <div>
          <strong id="toastItemTitle">Package</strong> added to your cart!
        </div>
      </div>
    </div>
  </div>

  <!-- Local Vendor Scripts -->
  <script src="assets/vendor/jquery/jquery-3.7.1.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js"></script>

  <?= $extraJs ?? '' ?>
</body>
</html>
