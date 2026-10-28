<?php
/**
 * DriveMaster Driving Academy - Dedicated Full Shopping Cart Page
 */
?>

<!-- ==========================================================================
     PAGE INNER HERO / BANNER
     ========================================================================== -->
<section class="page-banner text-center">
  <div class="container position-relative">
    <span class="section-pill-badge mb-3 bg-white text-dark border-0 shadow-sm">
      <i class="bi bi-bag-check-fill text-success"></i> Secure Course Booking
    </span>
    <h1 class="page-banner-title text-white">Your Training Cart</h1>
    <p class="page-banner-desc">
      Review your selected driving lesson packages, adjust session hours, apply promotional discounts, and proceed to our 2-step protected Square checkout.
    </p>
    <nav aria-label="breadcrumb">
      <div class="page-breadcrumb">
        <a href="home"><i class="bi bi-house-door-fill me-1"></i> Home</a>
        <i class="bi bi-chevron-right small text-white-50"></i>
        <a href="courses">Courses</a>
        <i class="bi bi-chevron-right small text-white-50"></i>
        <span>Shopping Cart</span>
      </div>
    </nav>
  </div>
</section>

<!-- ==========================================================================
     MAIN FULL-PAGE CART CONTENT
     ========================================================================== -->
<section class="cart-page-section section-padding py-5" id="cartPageMainSection">
  <div class="container">

    <!-- Active Cart Layout (Hidden if empty) -->
    <div id="cartPageActiveView">
      <div class="row g-4 align-items-start">

        <!-- Left 8 Columns: Cart Items Table & Coupon -->
        <div class="col-lg-8">
          <div class="card border rounded-4 shadow-sm p-4 bg-white mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
              <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-cart3 text-primary"></i> Selected Packages 
                <span class="badge bg-primary rounded-pill cart-total-count" style="font-size: 0.8rem;">0</span>
              </h4>
              <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" id="btnPageClearCart">
                <i class="bi bi-trash3 me-1"></i> Clear Cart
              </button>
            </div>

            <!-- Dynamic Cart Items Container -->
            <div id="cartPageList" class="d-flex flex-column gap-3">
              <!-- Rendered via main.js renderCartPage() -->
            </div>

            <!-- Cart Table Bottom Controls -->
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-4 mt-3 border-top">
              <a href="courses" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Continue Browsing Packages
              </a>
              <span class="text-muted small">
                <i class="bi bi-shield-check text-success me-1"></i> Door-to-door pickup included with all lessons
              </span>
            </div>
          </div>

          <!-- Promo Code Card -->
          <div class="card border rounded-4 shadow-sm p-4 bg-white">
            <div class="row align-items-center g-3">
              <div class="col-md-7">
                <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                  <i class="bi bi-ticket-perforated-fill text-warning"></i> Promotional Discount Code
                </h6>
                <p class="text-muted small mb-0">
                  Have a promotional code from our newsletter or student discount? Apply below. (Try: <strong class="text-primary font-monospace">DRIVE10</strong> for 10% off)
                </p>
              </div>
              <div class="col-md-5">
                <div class="input-group">
                  <input type="text" class="form-control rounded-start-pill text-uppercase font-monospace" id="cartPromoInput" placeholder="PROMO CODE">
                  <button class="btn btn-dark rounded-end-pill px-3 fw-bold" type="button" id="btnApplyPromo">
                    Apply
                  </button>
                </div>
                <div id="promoFeedback" class="small mt-1" style="display: none;"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right 4 Columns: Sticky Order Summary & Square Checkout -->
        <div class="col-lg-4">
          <div class="card border-0 rounded-4 shadow-sm p-4 bg-light position-sticky" style="top: 100px;">
            <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">Booking Summary</h5>

            <div class="d-flex justify-content-between mb-2 text-muted">
              <span>Tuition Subtotal:</span>
              <strong class="text-dark font-monospace" id="cartPageSubtotalDisplay">$0.00</strong>
            </div>

            <div class="d-flex justify-content-between mb-2 text-muted" id="cartPromoDiscountRow" style="display: none;">
              <span class="text-success"><i class="bi bi-tag-fill me-1"></i> Promo Discount (10%):</span>
              <strong class="text-success font-monospace" id="cartPageDiscountDisplay">-$0.00</strong>
            </div>

            <div class="d-flex justify-content-between mb-2 text-muted">
              <span>DVSA Registration Fee:</span>
              <span class="badge bg-success-subtle text-success">FREE ($0.00)</span>
            </div>

            <div class="d-flex justify-content-between mb-3 text-muted">
              <span>Vehicle Fuel &amp; Insurance:</span>
              <span class="badge bg-success-subtle text-success">INCLUDED</span>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-baseline mb-4">
              <span class="fs-5 fw-bold text-dark">Estimated Total:</span>
              <div class="text-end">
                <span class="fs-3 fw-bold text-primary font-monospace" id="cartPageFinalTotalDisplay">$0.00</span>
                <small class="text-muted d-block" style="font-size: 0.72rem;">All taxes &amp; fees included</small>
              </div>
            </div>

            <!-- Primary Checkout Button -->
            <button type="button" class="btn btn-primary w-100 py-3 fw-bold rounded-pill shadow d-flex align-items-center justify-content-center gap-2 mb-3" id="btnCartPageProceedSquare">
              <i class="bi bi-square-fill text-warning"></i>
              <span>Proceed to Square Checkout</span>
            </button>

            <!-- Trust & Security Badges -->
            <div class="p-3 bg-white border rounded-3 text-muted small">
              <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                <div>
                  <strong>Official Square Payment Sandbox</strong>
                  <div style="font-size: 0.72rem;">2-Step admin approval ensures verified vehicle availability</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-envelope-check-fill text-primary fs-5"></i>
                <div>
                  <strong>Instant Electronic Receipt</strong>
                  <div style="font-size: 0.72rem;">Official invoice sent via SMTP directly to your inbox</div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>

    <!-- Empty Cart Fallback View -->
    <div id="cartPageEmptyView" style="display: none;" class="text-center py-5">
      <div class="py-5 bg-white border rounded-4 shadow-sm p-4 max-w-600 mx-auto" style="max-width: 600px;">
        <div class="mb-4">
          <div class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded-circle" style="width: 100px; height: 100px;">
            <i class="bi bi-cart-x fs-1 text-secondary opacity-75"></i>
          </div>
        </div>
        <h3 class="fw-bold text-dark mb-2">Your Cart is Currently Empty</h3>
        <p class="text-muted mb-4">
          You haven't selected any driving lesson packages yet. Explore our DVSA approved curriculum and add a package to customize your tuition.
        </p>
        <div class="d-flex gap-3 justify-content-center">
          <a href="courses" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
            <i class="bi bi-compass me-1"></i> Browse Driving Packages
          </a>
          <a href="home" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">
            <i class="bi bi-house me-1"></i> Back to Home
          </a>
        </div>
      </div>
    </div>

  </div>
</section>
