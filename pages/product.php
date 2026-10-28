<?php
/**
 * DriveMaster Driving Academy - Product / Course Details Page
 */
$pkgId = htmlspecialchars($_GET['id'] ?? 'pkg-225');

$catalog = [
    'pkg-225' => [
        'id' => 'pkg-225',
        'title' => 'One 45-minute driving lesson',
        'price' => 225,
        'desc' => 'Focused behind-the-wheel coaching designed for mastering road test maneuvers, hazard anticipation, and roundabouts with an accredited Grade-A DVSA instructor.',
        'image' => 'assets/images/course-automatic.jpg',
        'duration' => '45 Mins / Lesson',
        'category' => 'DVSA Approved'
    ],
    'pkg-280' => [
        'id' => 'pkg-280',
        'title' => 'Two 45-minute driving lessons',
        'price' => 280,
        'desc' => 'Comprehensive 2-session training package. Ideal for practicing parallel parking, bay parking, reverse bay, and emergency braking under actual test conditions.',
        'image' => 'assets/images/course-manual.jpg',
        'duration' => '90 Mins (2 x 45m)',
        'category' => 'Comprehensive Prep'
    ],
    'pkg-335' => [
        'id' => 'pkg-335',
        'title' => 'Three 45-minute driving lessons',
        'price' => 335,
        'desc' => 'Master multi-lane roundabouts, dual carriageways, complex junction navigation, and mock test drills with personalized examiner assessment sheet.',
        'image' => 'assets/images/course-defensive.jpg',
        'duration' => '135 Mins (3 x 45m)',
        'category' => 'Accelerated Program'
    ],
    'pkg-390' => [
        'id' => 'pkg-390',
        'title' => 'Four 45-minute driving lessons',
        'price' => 390,
        'desc' => 'Our complete driving test guarantee syllabus. Includes mock test simulation, independent driving coaching, and test route familiarization.',
        'image' => 'assets/images/gallery-testpass.jpg',
        'duration' => '180 Mins (4 x 45m)',
        'category' => 'Full Mastery Pack'
    ],
    'course-standard' => [
        'id' => 'course-standard',
        'title' => 'Standard Driving Course',
        'price' => 299,
        'desc' => 'Comprehensive 10-hour curriculum covering cockpit drills, clutch control, traffic signs, lane discipline, and road safety essentials.',
        'image' => 'assets/images/course-automatic.jpg',
        'duration' => '10 Hours Program',
        'category' => 'Beginner Friendly'
    ],
    'course-intensive' => [
        'id' => 'course-intensive',
        'title' => 'Fast-Track Intensive Course',
        'price' => 499,
        'desc' => 'Accelerated 1-to-2 week crash course designed to take you from learner to licensed driver rapidly with guaranteed test booking assistance.',
        'image' => 'assets/images/course-manual.jpg',
        'duration' => '20 Hours Intensive',
        'category' => 'Rush Fast-Pass'
    ]
];

$pkg = $catalog[$pkgId] ?? $catalog['pkg-225'];
?>

<!-- ==========================================================================
     PAGE INNER HERO / BANNER
     ========================================================================== -->
<section class="page-banner text-center">
  <div class="container position-relative">
    <span class="section-pill-badge mb-3 bg-white text-dark border-0 shadow-sm">
      <i class="bi bi-stars text-warning"></i> Certified Driving Curriculum
    </span>
    <h1 class="page-banner-title text-white" id="productHeroTitle"><?= htmlspecialchars($pkg['title']) ?></h1>
    <p class="page-banner-desc">
      Detailed syllabus breakdown, vehicle specifications, flexible transmission options, and personalized instructor coaching tailored for your driving test pass.
    </p>
    <nav aria-label="breadcrumb">
      <div class="page-breadcrumb">
        <a href="home"><i class="bi bi-house-door-fill me-1"></i> Home</a>
        <i class="bi bi-chevron-right small text-white-50"></i>
        <a href="courses">Our Packages</a>
        <i class="bi bi-chevron-right small text-white-50"></i>
        <span id="productBreadcrumbTitle"><?= htmlspecialchars($pkg['title']) ?></span>
      </div>
    </nav>
  </div>
</section>

<!-- ==========================================================================
     MAIN PRODUCT DETAIL SECTION
     ========================================================================== -->
<section class="product-detail-section section-padding py-5">
  <div class="container">
    <div class="row g-5 align-items-start">

      <!-- Left Column: Gallery & Vehicle Visuals -->
      <div class="col-lg-6">
        <div class="product-gallery-card border rounded-4 p-3 bg-white shadow-sm position-sticky" style="top: 100px;">
          <!-- Main Display Image with Badges -->
          <div class="product-main-img-wrap rounded-4 overflow-hidden position-relative mb-3">
            <img src="<?= htmlspecialchars($pkg['image']) ?>" id="productMainImg" alt="Driving Lesson Package" class="img-fluid w-100 object-fit-cover" style="height: 380px; transition: transform 0.4s ease;">
            
            <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-2">
              <span class="badge bg-dark text-white px-3 py-2 rounded-pill shadow-sm" id="productCategoryBadge">
                <i class="bi bi-award-fill text-warning me-1"></i> <?= htmlspecialchars($pkg['category']) ?>
              </span>
              <span class="badge bg-success text-white px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-check-circle-fill me-1"></i> 98.4% Pass Rate
              </span>
            </div>

            <div class="position-absolute bottom-0 end-0 m-3">
              <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm font-monospace fw-bold" id="productVehicleBadge">
                <i class="bi bi-car-front-fill text-primary me-1"></i> Dual-Control Hybrid
              </span>
            </div>
          </div>

          <!-- Thumbnails Switcher -->
          <div class="d-flex gap-2">
            <button type="button" class="product-thumb-btn border rounded-3 p-1 active" data-img="assets/images/course-automatic.jpg">
              <img src="assets/images/course-automatic.jpg" alt="Cockpit View" class="rounded-2" width="75" height="55" style="object-fit: cover;">
            </button>
            <button type="button" class="product-thumb-btn border rounded-3 p-1" data-img="assets/images/course-manual.jpg">
              <img src="assets/images/course-manual.jpg" alt="Manual Gearbox" class="rounded-2" width="75" height="55" style="object-fit: cover;">
            </button>
            <button type="button" class="product-thumb-btn border rounded-3 p-1" data-img="assets/images/gallery-parking.jpg">
              <img src="assets/images/gallery-parking.jpg" alt="Manoeuvre Drills" class="rounded-2" width="75" height="55" style="object-fit: cover;">
            </button>
            <button type="button" class="product-thumb-btn border rounded-3 p-1" data-img="assets/images/course-defensive.jpg">
              <img src="assets/images/course-defensive.jpg" alt="Highway Motorway" class="rounded-2" width="75" height="55" style="object-fit: cover;">
            </button>
          </div>

          <!-- Key Highlights Quick Bar -->
          <div class="row g-2 mt-4 pt-3 border-top text-center">
            <div class="col-4">
              <div class="p-2 rounded-3 bg-light">
                <i class="bi bi-clock-fill text-primary fs-5 d-block mb-1"></i>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Lesson Length</small>
                <strong class="small text-dark" id="productMetaDuration"><?= htmlspecialchars($pkg['duration']) ?></strong>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded-3 bg-light">
                <i class="bi bi-shield-lock-fill text-success fs-5 d-block mb-1"></i>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Car For Test</small>
                <strong class="small text-dark">Included</strong>
              </div>
            </div>
            <div class="col-4">
              <div class="p-2 rounded-3 bg-light">
                <i class="bi bi-geo-alt-fill text-danger fs-5 d-block mb-1"></i>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Pickup Service</small>
                <strong class="small text-dark">Door-to-Door</strong>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Product Info, Options & Pricing -->
      <div class="col-lg-6">
        <div class="product-info-wrap">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold text-uppercase" style="font-size: 0.75rem;">
              <i class="bi bi-fire me-1"></i> Top Rated Package
            </span>
            <div class="text-warning small d-flex align-items-center gap-1">
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <i class="bi bi-star-fill"></i>
              <strong class="text-dark ms-1">4.9/5</strong>
              <span class="text-muted">(342 pass reviews)</span>
            </div>
          </div>

          <h2 class="display-6 fw-bold text-dark mb-2" id="productTitle"><?= htmlspecialchars($pkg['title']) ?></h2>
          
          <p class="text-muted lead fs-6 mb-4" id="productShortDesc">
            <?= htmlspecialchars($pkg['desc']) ?>
          </p>

          <!-- Price Header Box -->
          <div class="p-3 rounded-4 bg-light border d-flex justify-content-between align-items-center mb-4">
            <div>
              <span class="text-muted small d-block">All-Inclusive Tuition Price:</span>
              <div class="d-flex align-items-baseline gap-1">
                <span class="fs-4 fw-bold text-primary">$</span>
                <span class="display-5 fw-bold text-primary" id="productPriceDisplay"><?= $pkg['price'] ?></span>
                <span class="text-muted small">.00</span>
                <span class="badge bg-success-subtle text-success ms-2 font-monospace">Tax &amp; Fuel Included</span>
              </div>
            </div>
            <div class="text-end">
              <span class="badge bg-dark text-white rounded-pill px-3 py-2">
                <i class="bi bi-shield-check text-warning me-1"></i> Rush Slot Protection
              </span>
            </div>
          </div>

          <!-- Product Configuration Form -->
          <form id="productConfigForm">
            <input type="hidden" id="productHiddenId" value="<?= htmlspecialchars($pkg['id']) ?>">
            <input type="hidden" id="productBasePrice" value="<?= $pkg['price'] ?>">

            <!-- 1. Transmission Option -->
            <div class="mb-4">
              <label class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                <span>1. Select Transmission Type:</span>
                <span class="text-muted small">Dual-Control Equipped</span>
              </label>
              <div class="row g-2">
                <div class="col-6">
                  <input type="radio" class="btn-check" name="transmissionType" id="transAuto" value="automatic" checked autocomplete="off">
                  <label class="btn btn-outline-dark w-100 p-3 rounded-3 text-start d-flex align-items-center gap-2" for="transAuto">
                    <i class="bi bi-gear-wide fs-4 text-primary"></i>
                    <div>
                      <strong class="d-block small">Automatic (Hybrid)</strong>
                      <span class="text-muted" style="font-size: 0.72rem;">Stress-free, dual control</span>
                    </div>
                  </label>
                </div>
                <div class="col-6">
                  <input type="radio" class="btn-check" name="transmissionType" id="transManual" value="manual" autocomplete="off">
                  <label class="btn btn-outline-dark w-100 p-3 rounded-3 text-start d-flex align-items-center gap-2" for="transManual">
                    <i class="bi bi-speedometer fs-4 text-success"></i>
                    <div>
                      <strong class="d-block small">Manual (6-Speed)</strong>
                      <span class="text-muted" style="font-size: 0.72rem;">Full Category B License</span>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <!-- 2. Schedule Preference -->
            <div class="mb-4">
              <label for="productSchedule" class="form-label fw-bold text-dark">
                2. Preferred Training Window:
              </label>
              <select class="form-select rounded-3 py-2" id="productSchedule">
                <option value="morning">Morning Shift (7:00 AM – 11:00 AM)</option>
                <option value="afternoon" selected>Afternoon Shift (1:00 PM – 5:00 PM)</option>
                <option value="evening">Evening Shift (5:00 PM – 8:00 PM - Night Lighting)</option>
                <option value="weekend">Weekend Priority Intensive</option>
              </select>
            </div>

            <!-- 3. Optional Value-Add Upgrades -->
            <div class="mb-4">
              <label class="form-label fw-bold text-dark mb-2">
                3. Recommended Add-On Certifications (Optional):
              </label>
              <div class="d-flex flex-column gap-2">
                <div class="form-check p-3 border rounded-3 bg-white">
                  <input class="form-check-input ms-0 me-3 product-addon-checkbox" type="checkbox" id="addonTheory" data-price="35" value="Theory Test App">
                  <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="addonTheory">
                    <div>
                      <strong class="d-block text-dark small">Official DVSA Theory Test Simulator + Hazard App</strong>
                      <span class="text-muted" style="font-size: 0.75rem;">Instant access with 1,200+ mock questions &amp; clips</span>
                    </div>
                    <span class="badge bg-light text-primary border font-monospace">+ $35.00</span>
                  </label>
                </div>

                <div class="form-check p-3 border rounded-3 bg-white">
                  <input class="form-check-input ms-0 me-3 product-addon-checkbox" type="checkbox" id="addonMock" data-price="65" value="Mock Exam Simulation">
                  <label class="form-check-label d-flex justify-content-between align-items-center w-100" for="addonMock">
                    <div>
                      <strong class="d-block text-dark small">Complete Mock Driving Test with Official Scoring Sheet</strong>
                      <span class="text-muted" style="font-size: 0.75rem;">Simulated test route under real examiner conditions</span>
                    </div>
                    <span class="badge bg-light text-primary border font-monospace">+ $65.00</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Total Bar & CTA Actions -->
            <div class="p-3 border rounded-4 bg-white shadow-sm mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold text-muted small">Cart Package Total:</span>
                <span class="fs-4 fw-bold text-primary font-monospace" id="productFinalCalculatedPrice">$225.00</span>
              </div>

              <div class="row g-2">
                <div class="col-sm-6">
                  <button type="button" class="btn btn-outline-primary w-100 py-3 fw-bold rounded-pill d-flex align-items-center justify-content-center gap-2" id="btnProductAddToCart">
                    <i class="bi bi-cart-plus-fill fs-5"></i>
                    <span>Add to Cart</span>
                  </button>
                </div>
                <div class="col-sm-6">
                  <button type="button" class="btn btn-primary w-100 py-3 fw-bold rounded-pill d-flex align-items-center justify-content-center gap-2 shadow-sm" id="btnProductBuyNow">
                    <i class="bi bi-lightning-charge-fill fs-5 text-warning"></i>
                    <span>Buy Now &amp; Checkout</span>
                  </button>
                </div>
              </div>
            </div>
          </form>

          <!-- Guarantees & Perks -->
          <div class="row g-3 text-muted small">
            <div class="col-6 d-flex align-items-center gap-2">
              <i class="bi bi-shield-check text-success fs-5"></i>
              <span>Official Square 2-Step Protected Checkout</span>
            </div>
            <div class="col-6 d-flex align-items-center gap-2">
              <i class="bi bi-arrow-repeat text-primary fs-5"></i>
              <span>Free Reschedule up to 24 Hours Prior</span>
            </div>
          </div>

        </div>
      </div>

    </div>

    <!-- Syllabus & Tabs Section -->
    <div class="mt-5 pt-4 border-top">
      <ul class="nav nav-pills justify-content-center gap-2 mb-4" id="productTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active px-4 py-2 rounded-pill fw-bold" id="tab-curriculum-tab" data-bs-toggle="pill" data-bs-target="#tab-curriculum" type="button" role="tab">
            <i class="bi bi-journal-check me-1"></i> Course Curriculum
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link px-4 py-2 rounded-pill fw-bold" id="tab-fleet-tab" data-bs-toggle="pill" data-bs-target="#tab-fleet" type="button" role="tab">
            <i class="bi bi-car-front me-1"></i> Vehicle Specifications
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link px-4 py-2 rounded-pill fw-bold" id="tab-reviews-tab" data-bs-toggle="pill" data-bs-target="#tab-reviews" type="button" role="tab">
            <i class="bi bi-chat-quote me-1"></i> Verified Reviews (340+)
          </button>
        </li>
      </ul>

      <div class="tab-content bg-white p-4 p-md-5 border rounded-4 shadow-sm" id="productTabContent">
        <!-- Curriculum Tab -->
        <div class="tab-pane fade show active" id="tab-curriculum" role="tabpanel">
          <div class="row g-4">
            <div class="col-md-6">
              <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-compass-fill text-primary me-2"></i>What You Will Master</h5>
              <ul class="list-unstyled d-flex flex-column gap-2 text-muted">
                <li><i class="bi bi-check2-circle text-success me-2"></i> Cockpit setup, blind-spot mirror alignment, and dashboard safety indicators.</li>
                <li><i class="bi bi-check2-circle text-success me-2"></i> Smooth clutch control, bite-point balance, and stall prevention on hill starts.</li>
                <li><i class="bi bi-check2-circle text-success me-2"></i> All mandatory maneuvers: Parallel park, bay park, pull up on the right.</li>
                <li><i class="bi bi-check2-circle text-success me-2"></i> Complex multi-lane roundabouts, spiral lane discipline, and junction anticipation.</li>
              </ul>
            </div>
            <div class="col-md-6">
              <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-award text-success me-2"></i>Included In Every Package</h5>
              <ul class="list-unstyled d-flex flex-column gap-2 text-muted">
                <li><i class="bi bi-check2-circle text-success me-2"></i> Fully insured dual-control vehicle for practical driving training.</li>
                <li><i class="bi bi-check2-circle text-success me-2"></i> Door-to-door pickup &amp; drop-off across your local service postcode.</li>
                <li><i class="bi bi-check2-circle text-success me-2"></i> Fast-track test date booking assistance with our priority office team.</li>
                <li><i class="bi bi-check2-circle text-success me-2"></i> Electronic driver log &amp; progress scorecard after every session.</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Fleet Tab -->
        <div class="tab-pane fade" id="tab-fleet" role="tabpanel">
          <div class="row align-items-center g-4">
            <div class="col-md-6">
              <h5 class="fw-bold text-dark mb-3">Modern Dual-Control Hybrid Fleet</h5>
              <p class="text-muted">
                Safety and confidence start with the right machine. DriveMaster operates brand new dual-control vehicles equipped with auxiliary brake and clutch pedals for instructor intervention, high-definition forward and rear safety cameras, and power steering for ultra-light maneuvers.
              </p>
              <div class="d-flex gap-3">
                <div class="badge bg-light text-dark border p-2 rounded-3">Heated Mirrors</div>
                <div class="badge bg-light text-dark border p-2 rounded-3">Rear Parking Sensors</div>
                <div class="badge bg-light text-dark border p-2 rounded-3">Hill Start Assist</div>
                <div class="badge bg-light text-dark border p-2 rounded-3">Eco Hybrid Engine</div>
              </div>
            </div>
            <div class="col-md-6 text-center">
              <img src="assets/images/course-automatic.jpg" alt="Fleet Vehicle" class="img-fluid rounded-4 shadow-sm" style="max-height: 260px; object-fit: cover;">
            </div>
          </div>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="tab-reviews" role="tabpanel">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="p-4 border rounded-3 bg-light h-100">
                <div class="text-warning mb-2">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="text-muted small fst-italic">"Passed my test first time with only 2 minors! My instructor was incredibly patient and taught me parking tricks that made the exam a breeze."</p>
                <div class="fw-bold small text-dark mt-2">— Sarah M., Standard Pass Graduate</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-4 border rounded-3 bg-light h-100">
                <div class="text-warning mb-2">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="text-muted small fst-italic">"The mock test exam was identical to the actual DVSA test route. By test day, I had zero nerves. Highly recommended academy!"</p>
                <div class="fw-bold small text-dark mt-2">— James R., Intensive Student</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-4 border rounded-3 bg-light h-100">
                <div class="text-warning mb-2">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p class="text-muted small fst-italic">"Automatic lessons in their hybrid cars are so smooth. Booking and paying via Square was seamless and confirmed immediately."</p>
                <div class="fw-bold small text-dark mt-2">— Emily K., Automatic Pass</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
