  <!-- ==========================================================================
       PAGE INNER HERO / BANNER
       ========================================================================== -->
  <section class="page-banner text-center">
    <div class="container position-relative">
      <span class="section-pill-badge mb-3 bg-white text-dark border-0 shadow-sm">
        <i class="bi bi-camera-fill text-success"></i> Training Academy Moments
      </span>
      <h1 class="page-banner-title text-white">Our Photo Gallery</h1>
      <p class="page-banner-desc">
        Take a look at real behind-the-wheel driving sessions, student test-pass celebrations, precision parking manoeuvres, and modern dual-control training cars.
      </p>
      <nav aria-label="breadcrumb">
        <div class="page-breadcrumb">
          <a href="home"><i class="bi bi-house-door-fill"></i> Home</a>
          <i class="bi bi-chevron-right small text-white-50"></i>
          <span>Photo Gallery</span>
        </div>
      </nav>
    </div>
  </section>

  <!-- ==========================================================================
       GALLERY SECTION
       ========================================================================== -->
  <section class="gallery-section section-padding" id="gallery">
    <div class="container">
      <!-- Filter Buttons -->
      <div class="gallery-filter-wrap">
        <button type="button" class="gallery-filter-btn active" data-filter="all">All Photos</button>
        <button type="button" class="gallery-filter-btn" data-filter="pass" >Student Passes</button>
        <button type="button" class="gallery-filter-btn" data-filter="lessons">In-Car Lessons</button>
        <button type="button" class="gallery-filter-btn" data-filter="manoeuvres">Parking &amp; Manoeuvres</button>
        <button type="button" class="gallery-filter-btn" data-filter="highway">Highway Driving</button>
      </div>

      <!-- Gallery Grid -->
      <div class="row g-4 align-items-stretch" id="galleryGrid">
        <!-- Item 1: Student Pass Celebration -->
        <div class="col-lg-4 col-md-6 gallery-item-col" data-category="pass highway">
          <div class="gallery-card">
            <div class="gallery-thumb-wrap">
              <img src="assets/images/gallery-pass.jpg" alt="Student First-Time Test Pass" loading="lazy">
              <span class="gallery-tag-pill">
                <i class="bi bi-trophy-fill text-warning"></i> Test Pass
              </span>
              <div class="gallery-zoom-overlay">
                <div class="gallery-zoom-btn gallery-trigger-btn" title="View Full Photo">
                  <i class="bi bi-arrows-fullscreen"></i>
                </div>
                <h4 class="gallery-overlay-title">First-Time Test Pass</h4>
                <p class="gallery-overlay-desc">Proud student with her official pass certificate.</p>
              </div>
            </div>
            <div class="gallery-card-info">
              <h3 class="gallery-card-title">First-Time Test Pass Success</h3>
              <p class="gallery-card-desc">
                Celebrating a clean practical driving test pass with our certified instructor on the very first attempt.
              </p>
            </div>
          </div>
        </div>

        <!-- Item 2: Precision Parking Manoeuvre -->
        <div class="col-lg-4 col-md-6 gallery-item-col" data-category="manoeuvres,highway">
          <div class="gallery-card">
            <div class="gallery-thumb-wrap">
              <img src="assets/images/gallery-parking.jpg" alt="Parallel Parking Pad Drill" loading="lazy">
              <span class="gallery-tag-pill">
                <i class="bi bi-cone-striped text-warning"></i> Manoeuvres
              </span>
              <div class="gallery-zoom-overlay">
                <div class="gallery-zoom-btn gallery-trigger-btn" title="View Full Photo">
                  <i class="bi bi-arrows-fullscreen"></i>
                </div>
                <h4 class="gallery-overlay-title">Precision Parking Pad</h4>
                <p class="gallery-overlay-desc">Dedicated cone drills for bay &amp; parallel parking.</p>
              </div>
            </div>
            <div class="gallery-card-info">
              <h3 class="gallery-card-title">Bay &amp; Parallel Parking Practice</h3>
              <p class="gallery-card-desc">
                Instructor-guided spatial control drills ensuring flawless parking during the official DVSA driving exam.
              </p>
            </div>
          </div>
        </div>

        <!-- Item 3: Automatic Dual-Control Cockpit Drill -->
        <div class="col-lg-4 col-md-6 gallery-item-col" data-category="lessons">
          <div class="gallery-card">
            <div class="gallery-thumb-wrap">
              <img src="assets/images/course-automatic.jpg" alt="Automatic Transmission In-Car Lesson" loading="lazy">
              <span class="gallery-tag-pill">
                <i class="bi bi-gear-wide text-info"></i> In-Car Lesson
              </span>
              <div class="gallery-zoom-overlay">
                <div class="gallery-zoom-btn gallery-trigger-btn" title="View Full Photo">
                  <i class="bi bi-arrows-fullscreen"></i>
                </div>
                <h4 class="gallery-overlay-title">Automatic In-Car Drill</h4>
                <p class="gallery-overlay-desc">Cockpit setup, steering balance, and blind-spot mirrors.</p>
              </div>
            </div>
            <div class="gallery-card-info">
              <h3 class="gallery-card-title">Automatic Dual-Control Training</h3>
              <p class="gallery-card-desc">
                Stress-free beginner sessions focusing on steering coordination and smooth acceleration in traffic.
              </p>
            </div>
          </div>
        </div>

        <!-- Item 4: Manual Transmission Clutch Control -->
        <div class="col-lg-4 col-md-6 gallery-item-col" data-category="lessons">
          <div class="gallery-card">
            <div class="gallery-thumb-wrap">
              <img src="assets/images/course-manual.jpg" alt="Manual Gear Clutch Instruction" loading="lazy">
              <span class="gallery-tag-pill">
                <i class="bi bi-speedometer2 text-success"></i> Manual Control
              </span>
              <div class="gallery-zoom-overlay">
                <div class="gallery-zoom-btn gallery-trigger-btn" title="View Full Photo">
                  <i class="bi bi-arrows-fullscreen"></i>
                </div>
                <h4 class="gallery-overlay-title">Clutch &amp; Gear Transitions</h4>
                <p class="gallery-overlay-desc">Mastering the bite point and stall prevention.</p>
              </div>
            </div>
            <div class="gallery-card-info">
              <h3 class="gallery-card-title">Clutch &amp; Shifting Mastery</h3>
              <p class="gallery-card-desc">
                Tackle hill starts, roundabouts, and precise downshifting with total confidence under patient instructor coaching.
              </p>
            </div>
          </div>
        </div>

        <!-- Item 5: High-Speed Motorway & Dual-Carriageway -->
        <div class="col-lg-4 col-md-6 gallery-item-col" data-category="highway">
          <div class="gallery-card">
            <div class="gallery-thumb-wrap">
              <img src="assets/images/course-defensive.jpg" alt="High Speed Highway Driving Session" loading="lazy">
              <span class="gallery-tag-pill">
                <i class="bi bi-signpost-split-fill text-danger"></i> Highway
              </span>
              <div class="gallery-zoom-overlay">
                <div class="gallery-zoom-btn gallery-trigger-btn" title="View Full Photo">
                  <i class="bi bi-arrows-fullscreen"></i>
                </div>
                <h4 class="gallery-overlay-title">Motorway Training</h4>
                <p class="gallery-overlay-desc">Slip road merging, lane discipline, and hazard safety.</p>
              </div>
            </div>
            <div class="gallery-card-info">
              <h3 class="gallery-card-title">Motorway &amp; Highway Experience</h3>
              <p class="gallery-card-desc">
                Advanced road awareness, multi-lane overtaking, and high-speed braking in adverse weather conditions.
              </p>
            </div>
          </div>
        </div>

        <!-- Item 6: Patient 1-on-1 Instructor Coaching -->
        <div class="col-lg-4 col-md-6 gallery-item-col" data-category="lessons pass">
          <div class="gallery-card">
            <div class="gallery-thumb-wrap">
              <img src="assets/images/about-instructor.jpg" alt="Certified Instructor 1-on-1 Coaching" loading="lazy">
              <span class="gallery-tag-pill">
                <i class="bi bi-award-fill text-primary"></i> Certified Instructor
              </span>
              <div class="gallery-zoom-overlay">
                <div class="gallery-zoom-btn gallery-trigger-btn" title="View Full Photo">
                  <i class="bi bi-arrows-fullscreen"></i>
                </div>
                <h4 class="gallery-overlay-title">Certified DVSA Instructors</h4>
                <p class="gallery-overlay-desc">Patient, encouraging 1-on-1 personalized lessons.</p>
              </div>
            </div>
            <div class="gallery-card-info">
              <h3 class="gallery-card-title">Dedicated 1-on-1 Instructor Care</h3>
              <p class="gallery-card-desc">
                Friendly guidance that puts nervous beginners at ease, tailored to each student's personal learning pace.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- ==========================================================================
       GALLERY LIGHTBOX MODAL
       ========================================================================== -->
  <div class="modal fade gallery-modal" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
        <div class="modal-body">
          <div class="modal-img-wrap">
            <img src="" id="galleryModalImg" alt="Gallery Preview" class="img-fluid">
          </div>
          <div class="modal-caption">
            <div>
              <h4 class="h5 mb-1 text-white" id="galleryModalTitle">Photo Preview</h4>
              <p class="mb-0 text-white-50 small" id="galleryModalDesc"></p>
            </div>
            <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

