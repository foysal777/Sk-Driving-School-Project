<?php
declare(strict_types=1);

/**
 * DriveMaster Driving Academy - Dedicated Square Test Sandbox Checkout Page
 * Comprehensive 4-Stage Protected Gateway with Real-Time Admin Approval & Official Square Web Payments SDK
 */
?>

<!-- Checkout Hero & Breadcrumbs Section -->
<section class="checkout-hero-section py-4 bg-light border-bottom">
  <div class="container">
    <div class="row align-items-center gy-3">
      <div class="col-lg-7">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2 small">
            <li class="breadcrumb-item"><a href="home" class="text-decoration-none text-muted"><i class="bi bi-house-door-fill me-1"></i>Home</a></li>
            <li class="breadcrumb-item"><a href="courses" class="text-decoration-none text-muted">Courses</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Square Sandbox Checkout</li>
          </ol>
        </nav>
        <h1 class="h2 fw-bold mb-1 text-dark d-flex flex-wrap align-items-center gap-2">
          Square Sandbox Checkout
          <span class="badge bg-dark text-white rounded-pill fs-6 px-3 py-1">
            <i class="bi bi-square-fill text-warning me-1"></i> TEST SANDBOX
          </span>
        </h1>
        <p class="text-muted mb-0 small">
          Official 2-step protected gateway &bull; DVSA Grade-A Certified Instructors &bull; Live administrator verification &amp; instant SMTP receipt
        </p>
      </div>
      <div class="col-lg-5 text-lg-end">
        <div class="d-inline-flex flex-wrap gap-2 justify-content-lg-end">
          <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill small">
            <i class="bi bi-shield-check me-1"></i> 256-Bit SSL Secured
          </span>
          <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill small">
            <i class="bi bi-envelope-check me-1"></i> SMTP Connected
          </span>
          <span class="badge bg-warning-subtle text-dark px-3 py-2 rounded-pill small">
            <i class="bi bi-cpu me-1"></i> Sandbox Mode
          </span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 4-Step Checkout Stepper Indicator -->
<section class="checkout-stepper-wrap py-3 bg-white border-bottom shadow-sm">
  <div class="container">
    <div class="checkout-steps-nav d-flex justify-content-between align-items-center">
      
      <div class="step-item active" id="stepIndicator1">
        <div class="step-icon">
          <i class="bi bi-person-badge-fill"></i>
        </div>
        <div class="step-text d-none d-sm-block">
          <span class="step-num">Step 1</span>
          <strong class="step-title">Student Details</strong>
        </div>
      </div>
      <div class="step-line" id="stepLine1"></div>

      <div class="step-item" id="stepIndicator2">
        <div class="step-icon">
          <i class="bi bi-hourglass-split"></i>
        </div>
        <div class="step-text d-none d-sm-block">
          <span class="step-num">Step 2</span>
          <strong class="step-title">Admin Approval</strong>
        </div>
      </div>
      <div class="step-line" id="stepLine2"></div>

      <div class="step-item" id="stepIndicator3">
        <div class="step-icon">
          <i class="bi bi-credit-card-2-front-fill"></i>
        </div>
        <div class="step-text d-none d-sm-block">
          <span class="step-num">Step 3</span>
          <strong class="step-title">Square Pay</strong>
        </div>
      </div>
      <div class="step-line" id="stepLine3"></div>

      <div class="step-item" id="stepIndicator4">
        <div class="step-icon">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="step-text d-none d-sm-block">
          <span class="step-num">Step 4</span>
          <strong class="step-title">Confirmed</strong>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Main Checkout Interactive Body -->
<section class="checkout-body-section py-5 bg-light">
  <div class="container">
    <div class="row g-4">

      <!-- LEFT 8 COLUMNS: Interactive Stages -->
      <div class="col-lg-8">

        <!-- ====================================================================
             STAGE 1: STUDENT DETAILS & BOOKING REQUEST
             ==================================================================== -->
        <div id="sqStageRequest" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="badge bg-warning text-dark fw-bold mb-2">
                  <i class="bi bi-1-circle-fill me-1"></i> STEP 1 OF 3
                </span>
                <h4 class="fw-bold mb-0 text-white">Student Information &amp; Lesson Preferences</h4>
              </div>
              <span class="square-brand-badge fs-6">
                <i class="bi bi-square-fill"></i> Square
              </span>
            </div>
          </div>

          <div class="card-body p-4 p-md-5">
            <!-- Selected Package Highlight Banner -->
            <div class="p-3 rounded-3 bg-light border mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                  <i class="bi bi-car-front-fill fs-4"></i>
                </div>
                <div>
                  <small class="text-muted d-block">Enrolling in Package:</small>
                  <strong class="fs-5 text-dark" id="sqCourseTitle">Standard Pass Course</strong>
                  <span class="badge bg-success-subtle text-success ms-2" id="sqPackageBadge">DVSA Approved</span>
                </div>
              </div>
              <div class="text-md-end">
                <small class="text-muted d-block">Total Authorized Amount:</small>
                <span class="fs-3 fw-bold text-success font-monospace" id="sqCoursePrice">$780.00</span>
              </div>
            </div>

            <!-- Booking Request Form -->
            <form id="sqEnrollmentForm" class="needs-validation" novalidate>
              <input type="hidden" id="sqCourseId" value="1">
              <input type="hidden" id="sqHiddenCourseName" value="Standard Pass Course">
              <input type="hidden" id="sqHiddenCoursePrice" value="780">

              <div class="row g-3">
                <div class="col-md-6">
                  <label for="sqStudentName" class="form-label fw-bold small text-dark">
                    Student Full Name <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="sqStudentName" placeholder="e.g. Alex Johnson" value="Alex Johnson" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="sqStudentEmail" class="form-label fw-bold small text-dark">
                    Email Address (For SMTP Receipt) <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" id="sqStudentEmail" placeholder="student@example.com" value="student@example.com" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="sqStudentPhone" class="form-label fw-bold small text-dark">
                    Mobile Phone Number <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                    <input type="tel" class="form-control border-start-0 ps-0" id="sqStudentPhone" placeholder="+66 12 000 3456" value="+66 12 000 3456" required>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="sqTransmission" class="form-label fw-bold small text-dark">
                    Preferred Transmission <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-gear-wide-connected"></i></span>
                    <select class="form-select border-start-0 ps-0" id="sqTransmission">
                      <option value="automatic" selected>Automatic (Dual-Control Hybrid)</option>
                      <option value="manual">Manual (6-Speed Dual-Control)</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="sqScheduleTime" class="form-label fw-bold small text-dark">
                    Lesson Scheduling Preference
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                    <select class="form-select border-start-0 ps-0" id="sqScheduleTime">
                      <option value="flexible" selected>Flexible / Any Available Slot</option>
                      <option value="weekdays">Weekdays Morning (07:00 - 12:00)</option>
                      <option value="afternoon">Weekdays Afternoon (13:00 - 18:00)</option>
                      <option value="weekends">Weekends Only (Sat &amp; Sun)</option>
                      <option value="intensive">Daily Intensive Accelerated</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <label for="sqPickupArea" class="form-label fw-bold small text-dark">
                    Home Pickup Suburb / Postcode
                  </label>
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="sqPickupArea" placeholder="e.g. Central District, 90210" value="Downtown Central District">
                  </div>
                </div>

                <div class="col-12">
                  <label for="sqNotes" class="form-label fw-bold small text-dark">
                    Instructor Notes / Specific Learning Goals (Optional)
                  </label>
                  <textarea class="form-control" id="sqNotes" rows="2" placeholder="Tell us if you have prior driving experience, mock test needs, or preferred instructors..."></textarea>
                </div>
              </div>

              <!-- Protected 2-Step Explanation Banner -->
              <div class="alert alert-info border-0 bg-info-subtle text-dark p-3 rounded-3 mt-4 small d-flex gap-3 align-items-center">
                <i class="bi bi-shield-lock-fill fs-3 text-primary flex-shrink-0"></i>
                <div>
                  <strong>How the Square Protected Sandbox Works:</strong>
                  Submitting this request dispatches an instant automated notification email to the <strong>Administrator</strong> via SMTP. Once the admin confirms instructor availability in the admin panel, this page will <strong>automatically unlock the Square Sandbox card checkout</strong>!
                </div>
              </div>

              <div class="mt-4 pt-2">
                <button type="submit" id="btnSubmitSquareRequest" class="btn btn-primary btn-lg w-100 py-3 fw-bold rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                  <i class="bi bi-send-fill"></i>
                  <span>Submit Booking Request &amp; Await Admin Approval &rarr;</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- ====================================================================
             STAGE 2: LIVE WAITING FOR ADMIN APPROVAL SCREEN
             ==================================================================== -->
        <div id="sqStageWaiting" style="display: none;" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-dark text-white p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="badge bg-warning text-dark fw-bold mb-2">
                  <i class="bi bi-2-circle-fill me-1"></i> STEP 2 OF 3
                </span>
                <h4 class="fw-bold mb-0 text-white">Awaiting Administrator Approval</h4>
              </div>
              <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                <span class="spinner-grow spinner-grow-sm me-1" role="status"></span> Live Polling
              </span>
            </div>
          </div>

          <div class="card-body p-4 p-md-5 text-center">
            
            <!-- Pulsing Status Radar Icon -->
            <div class="square-status-pulse mb-3">
              <i class="bi bi-hourglass-split fs-2 text-warning"></i>
            </div>

            <h3 class="fw-bold text-dark mb-2">Booking Request Sent to Administrator!</h3>
            <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
              Your enrollment for <strong id="sqWaitCourse" class="text-dark">Standard Pass Course</strong> has been registered and emailed to the admin team via SMTP. We are verifying vehicle and instructor availability.
            </p>

            <!-- Booking Tracker Card -->
            <div class="card bg-light border p-4 rounded-4 mx-auto mb-4 text-start" style="max-width: 580px;">
              <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="text-muted small">Booking Reference Number:</span>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-dark font-monospace fs-6 px-3 py-2" id="sqWaitRef">DM-SQ-XXXXX</span>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2" id="btnCopyWaitRef" title="Copy Reference">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="text-muted small">Current Approval Status:</span>
                <span class="badge bg-warning text-dark px-3 py-2 fw-bold" id="sqWaitStatusBadge">
                  <i class="bi bi-clock-history me-1"></i> Awaiting Admin Approval
                </span>
              </div>
              <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="text-muted small">Authorized Payable Amount:</span>
                <strong class="text-success fs-5 font-monospace" id="sqWaitAmount">$780.00</strong>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">Payment Gateway:</span>
                <span class="fw-bold text-dark"><i class="bi bi-square-fill text-warning me-1"></i> Square Test Sandbox</span>
              </div>
            </div>

            <!-- Auto-Polling Pulsing Banner -->
            <div class="p-3 mb-4 bg-warning-subtle text-dark rounded-4 text-start small border border-warning-subtle mx-auto" style="max-width: 580px;">
              <div class="d-flex align-items-center gap-2">
                <div class="spinner-grow spinner-grow-sm text-warning flex-shrink-0" role="status"></div>
                <div>
                  <strong>Live Real-Time Auto-Check Active:</strong> Checking server status every <strong>3 seconds</strong>. As soon as the administrator clicks <strong>Approve</strong>, an approval email is dispatched to your inbox and this page will automatically unlock the Square payment card terminal!
                </div>
              </div>
            </div>

            <!-- Manual Check & Demo Shortcut Buttons -->
            <div class="d-flex flex-wrap gap-3 justify-content-center mb-4">
              <button type="button" id="btnCheckStatusManual" class="btn btn-dark px-4 py-2 fw-bold rounded-pill shadow-sm">
                <i class="bi bi-arrow-clockwise me-1"></i> Check Approval Status Now
              </button>
              <a href="/admin/enrollments.php" target="_blank" class="btn btn-outline-success px-4 py-2 fw-bold rounded-pill shadow-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i> Open Admin Panel to Approve
              </a>
            </div>

            <!-- Sandbox Testing Tip Box -->
            <div class="alert alert-light border rounded-3 p-3 mx-auto text-start small" style="max-width: 580px; font-size: 0.82rem;">
              <div class="d-flex gap-2">
                <i class="bi bi-lightbulb-fill text-warning fs-5 flex-shrink-0"></i>
                <div>
                  <strong>Testing the Live Approval Flow:</strong>
                  Open the <strong>Admin Panel</strong> in another tab (via button above), navigate to <strong>Course Bookings</strong>, and click <strong>"Approve"</strong> on your pending booking. You will see this page transition to Stage 3 in real time without refreshing!
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- ====================================================================
             STAGE 3: APPROVED! SQUARE OFFICIAL SDK & TEST CARD TERMINAL
             ==================================================================== -->
        <div id="sqStageCheckout" style="display: none;" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-success text-white p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="badge bg-white text-success fw-bold mb-2">
                  <i class="bi bi-check-circle-fill me-1"></i> APPROVED BY ADMIN
                </span>
                <h4 class="fw-bold mb-0 text-white">Complete Square Sandbox Payment</h4>
              </div>
              <span class="square-brand-badge bg-white text-dark fs-6">
                <i class="bi bi-square-fill text-dark"></i> Square SDK
              </span>
            </div>
          </div>

          <div class="card-body p-4 p-md-5">

            <div class="alert alert-success border-0 bg-success-subtle text-dark p-3 rounded-3 mb-4 d-flex gap-3 align-items-center">
              <i class="bi bi-check-circle-fill fs-3 text-success flex-shrink-0"></i>
              <div>
                <strong>Administrator Authorized!</strong> Your driving lesson spot is confirmed. Complete your payment below with the Square Test Sandbox card details.
              </div>
            </div>

            <!-- Interactive Virtual Credit Card Graphic Preview -->
            <div class="credit-card-preview-container mb-4">
              <div class="credit-card-visual" id="cardVisual">
                <div class="card-visual-top d-flex justify-content-between align-items-center">
                  <div class="card-chip"></div>
                  <div class="card-contactless"><i class="bi bi-wifi fs-4"></i></div>
                  <div class="card-brand-logo" id="previewBrandLogo">
                    <span class="badge bg-white text-dark fw-bold px-2 py-1">VISA</span>
                  </div>
                </div>
                <div class="card-visual-number my-3" id="previewCardNumber">
                  4242 &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; 4242
                </div>
                <div class="card-visual-bottom d-flex justify-content-between align-items-end">
                  <div>
                    <small class="card-label">CARDHOLDER</small>
                    <div class="card-val" id="previewCardholder">ALEX JOHNSON</div>
                  </div>
                  <div>
                    <small class="card-label">EXPIRES</small>
                    <div class="card-val" id="previewCardExp">12/28</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Square Sandbox Payment Terminal Form -->
            <div class="square-card-terminal p-4 rounded-4 bg-white border shadow-sm mb-4">
              
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-credit-card-2-front-fill text-primary fs-5"></i>
                  <strong class="text-dark">Square Card Sandbox (Official SDK)</strong>
                </div>
                <div class="d-flex gap-1 align-items-center">
                  <span class="badge bg-light text-dark border">VISA</span>
                  <span class="badge bg-light text-dark border">MASTERCARD</span>
                  <span class="badge bg-light text-dark border">AMEX</span>
                </div>
              </div>

              <!-- Quick Demo Fill Button -->
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="small text-muted"><i class="bi bi-info-circle me-1"></i> Sandbox Test Credentials</span>
                <button type="button" id="btnSquareDemoFill" class="btn btn-outline-success btn-sm rounded-pill fw-bold px-3">
                  <i class="bi bi-magic me-1"></i> Quick Fill Sandbox Card
                </button>
              </div>

              <!-- Official Square Web Payments SDK Card Mount Container -->
              <div id="card-container" class="mb-3"></div>

              <!-- Manual / Fallback Card Fields -->
              <form id="sqCardForm">
                <div id="squareManualFields">
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-muted mb-1">Card Number</label>
                    <div class="input-group">
                      <span class="input-group-text bg-white"><i class="bi bi-credit-card text-muted"></i></span>
                      <input type="text" class="form-control font-monospace" id="sqCardNumber" placeholder="4242 •••• •••• 4242" maxlength="19" value="4242 4242 4242 4242" required>
                      <span class="input-group-text bg-white border-start-0" id="sqCardBrandBadge">Square Sandbox Visa</span>
                    </div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted mb-1">Expiry Date (MM/YY)</label>
                      <input type="text" class="form-control font-monospace" id="sqCardExp" placeholder="MM/YY" maxlength="5" value="12/28" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label small fw-bold text-muted mb-1">CVV / CVC Security Code</label>
                      <input type="password" class="form-control font-monospace" id="sqCardCvv" placeholder="123" maxlength="4" value="789" required>
                    </div>
                  </div>

                  <div class="row g-3 mb-4">
                    <div class="col-md-7">
                      <label class="form-label small fw-bold text-muted mb-1">Cardholder Name</label>
                      <input type="text" class="form-control" id="sqCardholder" placeholder="Name on card" value="Alex Johnson" required>
                    </div>
                    <div class="col-md-5">
                      <label class="form-label small fw-bold text-muted mb-1">Billing Postal Code</label>
                      <input type="text" class="form-control font-monospace" id="sqCardZip" placeholder="90210" value="90210">
                    </div>
                  </div>
                </div>

                <input type="hidden" id="sqCardNonce" value="">

                <button type="submit" id="btnSquarePaySubmit" class="square-pay-btn btn-lg w-100 py-3 shadow">
                  <i class="bi bi-lock-fill"></i>
                  <span>Pay <span id="sqPayBtnAmount">$780.00</span> via Square SDK</span>
                </button>
              </form>

            </div>

            <div class="text-center text-muted small">
              <i class="bi bi-shield-lock-fill text-success me-1"></i> Square Official SDK Active &bull; 256-Bit SSL Encrypted &bull; Automated receipt dispatched via SMTP
            </div>

          </div>
        </div>

        <!-- ====================================================================
             STAGE 4: PAYMENT CONFIRMED & OFFICIAL INVOICE RECEIPT
             ==================================================================== -->
        <div id="sqStageSuccess" style="display: none;" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
          <div class="card-header bg-dark text-white p-4 border-0 text-center">
            <div class="display-3 text-success mb-2">
              <i class="bi bi-check-circle-fill"></i>
            </div>
            <h2 class="fw-bold mb-1 text-white">Square Payment Successful!</h2>
            <p class="text-white-50 mb-0">Your driving package booking is confirmed and paid in full.</p>
          </div>

          <div class="card-body p-4 p-md-5">
            <p class="text-muted text-center mb-4">
              An official payment receipt has been dispatched to your email via SMTP. Your Grade-A instructor is reserved for your chosen dates.
            </p>

            <!-- Printable Receipt Box -->
            <div class="square-receipt-box mb-4 p-4 rounded-4 bg-light border" id="printableReceipt">
              <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <div>
                  <h5 class="fw-bold mb-0 text-dark">DriveMaster Driving Academy</h5>
                  <span class="small text-muted">Official Payment Receipt &bull; Square Sandbox</span>
                </div>
                <span class="badge bg-success px-3 py-2 fs-6">PAID IN FULL</span>
              </div>

              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Square Sandbox Tx ID:</span>
                <span class="font-monospace fw-bold text-dark" id="sqReceiptTxId">sq_sandbox_tx_...</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Booking Reference:</span>
                <span class="font-monospace fw-bold text-primary" id="sqReceiptRef">DM-SQ-...</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Enrolled Course:</span>
                <strong id="sqReceiptCourse" class="text-dark">Standard Pass Course</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Student Name:</span>
                <span id="sqReceiptName" class="fw-bold">Alex Johnson</span>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Payment Gateway &amp; Card:</span>
                <span id="sqReceiptMethod">Square Sandbox Visa (•••• 4242)</span>
              </div>
              <div class="d-flex justify-content-between mb-2" id="sqReceiptUrlRow" style="display: none;">
                <span class="text-muted">Square Hosted Receipt:</span>
                <a href="#" target="_blank" id="sqReceiptUrlLink" class="text-success fw-bold text-decoration-none">Open Official Receipt <i class="bi bi-box-arrow-up-right ms-1"></i></a>
              </div>
              <div class="d-flex justify-content-between pt-3 mt-2 border-top">
                <span class="fs-5 fw-bold text-dark">Total Amount Paid:</span>
                <strong class="text-success fs-4 font-monospace" id="sqReceiptTotal">$780.00</strong>
              </div>
            </div>

            <!-- Receipt Action Buttons -->
            <div class="row g-3">
              <div class="col-sm-4">
                <button type="button" class="btn btn-outline-dark w-100 py-3 rounded-pill fw-bold" onclick="window.print()">
                  <i class="bi bi-printer me-1"></i> Print Receipt
                </button>
              </div>
              <div class="col-sm-4">
                <a href="/admin/enrollments.php" target="_blank" class="btn btn-outline-success w-100 py-3 rounded-pill fw-bold">
                  <i class="bi bi-speedometer2 me-1"></i> View in Admin
                </a>
              </div>
              <div class="col-sm-4">
                <a href="courses" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                  <i class="bi bi-arrow-left me-1"></i> Browse Courses
                </a>
              </div>
            </div>

          </div>
        </div>

      </div>

      <!-- RIGHT 4 COLUMNS: Sticky Order Summary & Trust Guarantee -->
      <div class="col-lg-4">
        <div class="sticky-top" style="top: 24px;">

          <!-- Order Summary Card -->
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white p-4 border-bottom">
              <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-primary"></i> Order Summary
              </h5>
            </div>
            <div class="card-body p-4">
              
              <!-- Item Details Row -->
              <div class="d-flex gap-3 align-items-center mb-3 pb-3 border-bottom" id="summaryItemRow">
                <div class="rounded-3 bg-light border p-2 flex-shrink-0 text-center" style="width: 60px; height: 60px;">
                  <i class="bi bi-car-front-fill text-success fs-2"></i>
                </div>
                <div class="flex-grow-1">
                  <strong class="text-dark d-block" id="summaryItemTitle">Standard Pass Course</strong>
                  <span class="small text-muted" id="summaryItemSub">20 Hours Complete &bull; Dual-Control</span>
                </div>
                <span class="fw-bold font-monospace fs-5 text-dark" id="summaryItemPrice">$780</span>
              </div>

              <!-- Cost Breakdown -->
              <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Package Subtotal:</span>
                <span class="fw-bold text-dark font-monospace" id="summarySubtotal">$780.00</span>
              </div>
              <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Admin Slot Verification:</span>
                <span class="badge bg-success-subtle text-success">FREE</span>
              </div>
              <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Fuel &amp; Comprehensive Insurance:</span>
                <span class="badge bg-success-subtle text-success">INCLUDED</span>
              </div>
              <div class="d-flex justify-content-between mb-2 small" id="summaryDiscountRow" style="display: none;">
                <span class="text-muted">Promotional Discount:</span>
                <span class="text-success fw-bold font-monospace" id="summaryDiscountAmount">-$0.00</span>
              </div>
              
              <hr class="my-3">

              <div class="d-flex justify-content-between align-items-baseline mb-2">
                <span class="fw-bold text-dark fs-5">Estimated Total:</span>
                <div class="text-end">
                  <span class="fs-3 fw-bold text-primary font-monospace" id="summaryTotal">$780.00</span>
                  <small class="text-muted d-block" style="font-size: 0.72rem;">All taxes &amp; fees included</small>
                </div>
              </div>

            </div>
          </div>

          <!-- Square Sandbox Guarantee Card -->
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
            <div class="card-body p-4">
              <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-shield-check text-success fs-5"></i> Square Sandbox Protection
              </h6>
              
              <ul class="list-unstyled mb-0 small text-muted d-grid gap-2">
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success mt-1"></i>
                  <span>Official Square Web Payments SDK integration</span>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success mt-1"></i>
                  <span>2-Step admin approval protects slot reservations</span>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success mt-1"></i>
                  <span>Instant email dispatch via SMTP to your inbox</span>
                </li>
                <li class="d-flex align-items-start gap-2">
                  <i class="bi bi-check-circle-fill text-success mt-1"></i>
                  <span>DVSA Grade-A Certified driving instructors</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Need Help Widget -->
          <div class="card border-0 bg-dark text-white rounded-4 shadow-sm p-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-headset text-warning me-2"></i>Need Assistance?</h6>
            <p class="small text-white-50 mb-3">Have questions about your booking or test sandbox payment? Our support desk is available 7 days a week.</p>
            <div class="d-flex align-items-center gap-2 text-warning fw-bold mb-1">
              <i class="bi bi-telephone-fill"></i>
              <a href="tel:+66120003456" class="text-warning text-decoration-none">+66 12 000 3456</a>
            </div>
            <div class="small text-white-50">
              <i class="bi bi-envelope-fill me-1"></i> info@skdrivingschool.com
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>
