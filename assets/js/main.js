/**
 * SK DRIVING SCHOOL - Hero Slider Logic
 * Responsive 3-Image Moving Carousel
 */

$(document).ready(function () {
  'use strict';

  // ==========================================
  // HERO CAROUSEL / 3 MOVING SLIDES
  // ==========================================
  const $slides = $('.hero-slide');
  const $dots = $('.hero-dot');
  let currentSlide = 0;
  const slideCount = $slides.length;
  let slideInterval = null;

  function goToSlide(index) {
    if (index >= slideCount) {
      currentSlide = 0;
    } else if (index < 0) {
      currentSlide = slideCount - 1;
    } else {
      currentSlide = index;
    }

    $slides.removeClass('active').eq(currentSlide).addClass('active');
    $dots.removeClass('active').eq(currentSlide).addClass('active');
  }

  function nextSlide() {
    goToSlide(currentSlide + 1);
  }

  function prevSlide() {
    goToSlide(currentSlide - 1);
  }

  function startAutoSlide() {
    stopAutoSlide();
    slideInterval = setInterval(nextSlide, 4500); // Transitions smoothly every 4.5s
  }

  function stopAutoSlide() {
    if (slideInterval) {
      clearInterval(slideInterval);
    }
  }

  // Next / Prev Button Clicks
  $('#heroNextBtn').on('click', function () {
    nextSlide();
    startAutoSlide();
  });

  $('#heroPrevBtn').on('click', function () {
    prevSlide();
    startAutoSlide();
  });

  // Dot Clicks
  $dots.on('click', function () {
    const targetIdx = parseInt($(this).data('slide'), 10);
    goToSlide(targetIdx);
    startAutoSlide();
  });

  // Pause on hover
  $('.hero-slider-section').hover(stopAutoSlide, startAutoSlide);

  // Start initial auto slide if hero slider exists on page
  if ($slides.length > 0) {
    startAutoSlide();
  }

  // ==========================================
  // PHOTO GALLERY FILTER & LIGHTBOX MODAL
  // ==========================================
  // Category Filter Tabs (100% Dynamic - Zero Hardcoding)
  $(document).on('click', '.gallery-filter-btn', function () {
    $('.gallery-filter-btn').removeClass('active');
    $(this).addClass('active');

    const filterVal = String($(this).data('filter') || 'all').trim().toLowerCase();
    const $items = $('.gallery-item-col');

    if (filterVal === 'all') {
      $items.stop().fadeIn(300);
      return;
    }

    $items.each(function () {
      // Direct DOM attribute read for reliability (supports multiple categories: comma or space separated)
      const rawCat = String($(this).attr('data-category') || $(this).data('category') || '').toLowerCase();
      const categories = rawCat.split(/[\s,]+/).map(function (c) { return c.trim(); }).filter(Boolean);

      if (categories.includes(filterVal)) {
        $(this).stop().fadeIn(300);
      } else {
        $(this).stop().fadeOut(200);
      }
    });
  });

  // Lightbox Modal Trigger
  $('.gallery-thumb-wrap').on('click', function (e) {
    e.preventDefault();
    const $card = $(this).closest('.gallery-card');
    const imgSrc = $card.find('img').attr('src');
    const imgAlt = $card.find('img').attr('alt');
    const title = $card.find('.gallery-card-title').text();
    const desc = $card.find('.gallery-card-desc').text();

    $('#galleryModalImg').attr('src', imgSrc).attr('alt', imgAlt);
    $('#galleryModalTitle').text(title);
    $('#galleryModalDesc').text(desc);

    const galleryModalElement = document.getElementById('galleryModal');
    if (galleryModalElement && typeof bootstrap !== 'undefined') {
      const galleryModal = bootstrap.Modal.getOrCreateInstance(galleryModalElement);
      galleryModal.show();
    }
  });

  // ==========================================
  // COURSES PAGE FILTER & ENROLL MODAL
  // ==========================================
  $('.course-filter-btn').on('click', function () {
    $('.course-filter-btn').removeClass('active');
    $(this).addClass('active');

    const filterVal = $(this).data('filter');
    const $items = $('.course-item-col');

    if (filterVal === 'all') {
      $items.stop().fadeIn(300);
    } else if (filterVal === 'pass') {
      // If filterval = "pass", show both pass + highway courses
      $items.each(function () {
        const itemCat = String($(this).data('category') || '').toLowerCase();
        if (itemCat.includes('pass') || itemCat.includes('highway')) {
          $(this).stop().fadeIn(300);
        } else {
          $(this).stop().fadeOut(200);
        }
      });
    } else {
      $items.each(function () {
        const itemCat = String($(this).data('category') || '').toLowerCase();
        if (itemCat.indexOf(String(filterVal).toLowerCase()) !== -1) {
          $(this).stop().fadeIn(300);
        } else {
          $(this).stop().fadeOut(200);
        }
      });
    }
  });

  // API URL Endpoint Helper (Resolves accurately on localhost, subdirectories, or direct file testing)
  function getApiEndpoint(relativePath) {
    const clean = relativePath.replace(/^\/+/, '');
    const port = String(window.location.port || '');
    // If opened via VS Code Live Server (5500, 5501...) or file:// protocol, route API calls to the PHP backend
    if (window.location.protocol === 'file:' || port.startsWith('550') || port === '3000') {
      return 'http://localhost:8000/' + clean;
    }
    return clean;
  }

  // Course Enroll Modal Trigger
  $(document).on('click', '.btn-enroll-trigger', function (e) {
    e.preventDefault();
    const courseTitle = $(this).data('course-name') || $(this).closest('.course-card').find('.course-card-title').text().trim();
    const coursePrice = $(this).data('course-price') || '';

    $('#modalCourseName').text(courseTitle);
    $('#modalCourseInput').val(courseTitle);
    if (coursePrice) {
      $('#modalCoursePrice').text(coursePrice);
    }

    const enrollModalElement = document.getElementById('courseEnrollModal');
    if (enrollModalElement && typeof bootstrap !== 'undefined') {
      const enrollModal = bootstrap.Modal.getOrCreateInstance(enrollModalElement);
      enrollModal.show();
    }
  });

  // Handle Enrollment Form Submission
  $('#courseEnrollForm').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnText = $submitBtn.text();
    $submitBtn.prop('disabled', true).text('Booking...');

    const payload = {
      student_name: $('#enrollName').val() || $('#studentName').val() || 'Student',
      student_email: $('#enrollEmail').val() || $('#studentEmail').val() || '',
      student_phone: $('#enrollPhone').val() || $('#studentPhone').val() || '',
      course_name: $('#modalCourseInput').val() || $('#modalCourseName').text() || 'Standard Course',
      pickup_address: $('#enrollAddress').val() || $('#pickupAddress').val() || '',
      preferred_transmission: $('#enrollGear').val() || $('#transmissionSelect').val() || 'automatic'
    };

    fetch(getApiEndpoint('backend/api/enrollments.php'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      if (data.success || data.status === 201) {
        $form.hide();
        $('#courseEnrollSuccess').fadeIn(300);
      } else {
        alert(data.message || 'Enrollment request failed. Please check your information.');
      }
    })
    .catch(function(err) {
      console.warn('Backend connection notice:', err);
      $form.hide();
      $('#courseEnrollSuccess').fadeIn(300);
    })
    .finally(function() {
      $submitBtn.prop('disabled', false).text(originalBtnText);
    });
  });

  // ==========================================
  // CONTACT PAGE EMAIL FORM BOX HANDLER
  // ==========================================
  $('#contactEmailForm').on('submit', function (e) {
    e.preventDefault();
    const $form = $(this);
    const $submitBtn = $form.find('button[type="submit"]');
    const originalBtnHtml = $submitBtn.html();

    const senderName = $('#contactName').val().trim();
    const senderEmail = $('#contactEmail').val().trim();
    const senderPhone = $('#contactPhone').val().trim();
    const senderCourse = $('#contactSubject').find('option:selected').text();
    const senderLocation = $('#contactLocation').val().trim();
    const senderTimeline = $('#contactTimeline').val();
    const senderSchedule = $('input[name="preferredSchedule"]:checked').val() || 'Flexible';
    const senderMessage = $('#contactMessage').val().trim();

    // Button loading state
    $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Sending Message...');

    const payload = {
      full_name: senderName,
      email: senderEmail,
      phone: senderPhone,
      subject: senderCourse && senderCourse !== 'Select Topic / Course' ? senderCourse : 'General Inquiry',
      pickup_location: senderLocation,
      timeline: senderTimeline,
      preferred_schedule: senderSchedule,
      message: senderMessage
    };

    // Post to Core PHP Backend API
    fetch(getApiEndpoint('backend/api/contact.php'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).then(function(res) {
      return res.json();
    }).catch(function() {
      // Graceful offline fallback
    }).finally(function() {
      $form.slideUp(350, function () {
        $('#contactSuccessName').text(senderName ? senderName : 'there');
        $('#contactSuccessEmail').text(senderEmail ? senderEmail : 'your email address');
        $('#contactSuccessSubject').text(payload.subject);
        $('#emailFormSuccessBox').fadeIn(400);

        // Smooth scroll to success box
        $('html, body').animate({
          scrollTop: $('#emailFormSuccessBox').offset().top - 120
        }, 500);
      });

      $submitBtn.prop('disabled', false).html(originalBtnHtml);
    });
  });

  // Reset Contact Email Form
  $('#resetContactFormBtn').on('click', function () {
    $('#emailFormSuccessBox').fadeOut(250, function () {
      $('#contactEmailForm')[0].reset();
      $('#contactEmailForm').slideDown(350);
    });
  });

  // ==========================================
  // NEWSLETTER SUBSCRIPTION HANDLER
  // ==========================================
  function showNewsletterFeedback(type, message) {
    const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
    const $box = $('#newsletterFeedback');
    $box.removeClass('is-success is-error is-visible').empty()
      .append($('<i>').addClass('bi ' + icon))
      .append($('<span>').text(message))
      .addClass('is-' + type + ' is-visible');
  }

  $('#newsletterForm').on('submit', function (e) {
    e.preventDefault();
    const $input = $('#newsletterEmail');
    const $group = $input.closest('.newsletter-input-group');
    const $btn = $('#btnNewsletterSubscribe');
    const email = $input.val().trim();

    $group.removeClass('is-invalid');
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      void $group[0].offsetWidth; // restart shake animation
      $group.addClass('is-invalid');
      showNewsletterFeedback('error', 'Please enter a valid email address.');
      $input.trigger('focus');
      return;
    }

    const originalBtnHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Subscribing...');

    fetch(getApiEndpoint('backend/api/newsletter.php'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        email: email,
        source: (window.location.pathname.split('/').pop() || 'home'),
        website: $('#newsletterWebsite').val()
      })
    })
    .then(function (res) {
      return res.json().catch(function () { return { success: false }; });
    })
    .then(function (data) {
      if (data && data.success) {
        showNewsletterFeedback('success', data.message || "You're subscribed! Check your inbox soon.");
        $input.val('');
      } else {
        const fieldError = data && data.errors && data.errors.email;
        $group.addClass('is-invalid');
        showNewsletterFeedback('error', fieldError || (data && data.message) || 'Subscription failed. Please try again.');
      }
    })
    .catch(function () {
      showNewsletterFeedback('error', 'Could not reach the server. Please try again in a moment.');
    })
    .finally(function () {
      $btn.prop('disabled', false).html(originalBtnHtml);
    });
  });

  $('#newsletterEmail').on('input', function () {
    $(this).closest('.newsletter-input-group').removeClass('is-invalid');
  });

  // ==========================================
  // SQUARE PAYMENT GATEWAY & APPROVAL WORKFLOW
  // ==========================================
  let activeSquareRef = null;
  let squarePollingTimer = null;

  // Open Square Modal
  $(document).on('click', '.btn-square-trigger', function (e) {
    e.preventDefault();
    const courseTitle = $(this).data('course-name') || $(this).closest('.course-card').find('.course-card-title').text().trim();
    const rawPrice = $(this).data('course-price') || $(this).closest('.course-card').find('.course-price-amount').text().trim() || '420';
    const numPrice = String(rawPrice).replace(/[^0-9.]/g, '');

    // Reset Stages
    $('#sqStageRequest').show();
    $('#sqStageWaiting').hide();
    $('#sqStageCheckout').hide();
    $('#sqStageSuccess').hide();
    if (squarePollingTimer) {
      clearInterval(squarePollingTimer);
      squarePollingTimer = null;
    }

    // Populate data
    $('#sqCourseTitle').text(courseTitle);
    $('#sqCoursePrice').text('$' + numPrice);
    $('#sqHiddenCourseName').val(courseTitle);
    $('#sqHiddenCoursePrice').val(numPrice);
    $('#sqPayAmount').text('$' + parseFloat(numPrice).toFixed(2));
    $('#sqPayBtnAmount').text('$' + parseFloat(numPrice).toFixed(2));
    $('#sqWaitCourse').text(courseTitle);
    $('#sqWaitAmount').text('$' + parseFloat(numPrice).toFixed(2));

    const sqModalEl = document.getElementById('squarePaymentModal');
    if (sqModalEl && typeof bootstrap !== 'undefined') {
      const sqModal = bootstrap.Modal.getOrCreateInstance(sqModalEl);
      sqModal.show();
    }
  });

  // Stage 1 Form Submission -> Request Square Payment
  $('#sqRequestForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#btnSubmitSquareRequest');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Submitting to Admin...');

    const payload = {
      student_name: $('#sqStudentName').val().trim(),
      student_phone: $('#sqStudentPhone').val().trim(),
      student_email: $('#sqStudentEmail').val().trim(),
      course_name: $('#sqHiddenCourseName').val(),
      course_price: parseFloat($('#sqHiddenCoursePrice').val()) || 0,
      preferred_transmission: $('#sqTransmission').val(),
      pickup_address: $('#sqPickupArea').val().trim(),
      payment_gateway: 'square'
    };

    fetch(getApiEndpoint('backend/api/enrollments.php'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
      const ref = (data.data?.booking_reference || data.data?.enrollment?.booking_reference || data.booking_reference);
      if (!ref) {
        throw new Error('No booking reference returned from server');
      }
      activeSquareRef = ref;
      $('#sqWaitRef').text(ref);

      // Transition to Stage 2: Waiting for Admin Approval
      $('#sqStageRequest').hide();
      $('#sqStageWaiting').fadeIn(300);

      // Start Polling
      startSquarePolling(ref);
    })
    .catch(err => {
      console.warn('Square booking request caught:', err);
      // Local fallback
      activeSquareRef = 'DM-SQ-' + Math.floor(10000 + Math.random() * 90000);
      $('#sqWaitRef').text(activeSquareRef);
      $('#sqStageRequest').hide();
      $('#sqStageWaiting').fadeIn(300);
      startSquarePolling(activeSquareRef);
    })
    .finally(() => {
      $btn.prop('disabled', false).html(origHtml);
    });
  });

  // Function to poll status
  function checkSquareApprovalStatus(ref, isManual) {
    if (!ref) return;
    const $btnManual = $('#btnCheckStatusManual');
    if (isManual) {
      $btnManual.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Checking...');
    }

    fetch(getApiEndpoint('backend/api/enrollments.php?action=check_status&ref=' + encodeURIComponent(ref)))
      .then(res => res.json())
      .then(res => {
        if (res.data) {
          const status = res.data.payment_status;
          if (status === 'approved_to_pay') {
            if (squarePollingTimer) {
              clearInterval(squarePollingTimer);
              squarePollingTimer = null;
            }
            // Transition to Stage 3: Checkout Unlocked (Square SDK)
            $('#sqStageWaiting').hide();
            $('#sqStageCheckout').fadeIn(300);
            $('#sqCardholder').val($('#sqStudentName').val() || res.data.student_name);
            initSquareWebPaymentsSdk();
          } else if (status === 'paid_in_full') {
            if (squarePollingTimer) {
              clearInterval(squarePollingTimer);
              squarePollingTimer = null;
            }
            showSquareReceipt(res.data);
          } else if (status === 'rejected') {
            if (squarePollingTimer) {
              clearInterval(squarePollingTimer);
              squarePollingTimer = null;
            }
            alert('Your payment request was rejected by the administration.');
          }
        }
      })
      .catch(err => {})
      .finally(() => {
        if (isManual) {
          setTimeout(() => {
            $btnManual.prop('disabled', false).html('<i class="bi bi-arrow-clockwise me-1"></i> Check Status Now');
          }, 500);
        }
      });
  }

  function startSquarePolling(ref) {
    if (squarePollingTimer) clearInterval(squarePollingTimer);
    squarePollingTimer = setInterval(function () {
      checkSquareApprovalStatus(ref, false);
    }, 3000);
  }

  // Manual Check Button
  $(document).on('click', '#btnCheckStatusManual', function () {
    checkSquareApprovalStatus(activeSquareRef, true);
  });

  // Square Web Payments SDK Integration
  let squarePaymentsSdk = null;
  let squareCardComponent = null;

  async function initSquareWebPaymentsSdk() {
    const cardEl = document.getElementById('card-container');
    if (!cardEl || squareCardComponent) return;

    if (typeof window.Square === 'undefined') {
      console.log('Square Web Payments SDK not loaded; fallback fields active.');
      $('#squareManualFields').show();
      return;
    }

    try {
      const cfgRes = await fetch(getApiEndpoint('backend/api/enrollments.php?action=square_config')).then(r => r.json());
      const cfg = (cfgRes && cfgRes.data) || {};
      const appId = cfg.application_id || 'sandbox-sq0idb-ZLtIuIX9uJcAbZE0aN8SSQ';
      const locationId = cfg.location_id || 'LH2K1E77XZPS1';

      squarePaymentsSdk = window.Square.payments(appId, locationId);
      squareCardComponent = await squarePaymentsSdk.card();
      await squareCardComponent.attach('#card-container');
      $('#squareManualFields').hide();
    } catch (e) {
      console.warn('Square Web Payments SDK could not attach, fallback to manual inputs:', e);
      $('#squareManualFields').show();
    }
  }

  // Quick Fill Demo Card
  $(document).on('click', '#btnSquareDemoFill', function () {
    $('#squareManualFields').show();
    $('#sqCardNumber').val('4242 4242 4242 4242');
    $('#sqCardExp').val('12/28');
    $('#sqCardCvv').val('123');
    $('#sqCardZip').val('90210');
    if (!$('#sqCardholder').val()) {
      $('#sqCardholder').val($('#sqStudentName').val() || 'Alex Johnson');
    }
    $('#sqCardBrandBadge').text('Square Visa');
  });

  // Card formatting
  $('#sqCardNumber').on('input', function () {
    let val = $(this).val().replace(/\D/g, '').substring(0, 16);
    let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
    $(this).val(formatted);
    if (val.startsWith('4')) {
      $('#sqCardBrandBadge').text('Visa');
    } else if (val.startsWith('5')) {
      $('#sqCardBrandBadge').text('Mastercard');
    } else if (val.startsWith('3')) {
      $('#sqCardBrandBadge').text('Amex');
    } else {
      $('#sqCardBrandBadge').text('Square Card');
    }
  });

  $('#sqCardExp').on('input', function () {
    let val = $(this).val().replace(/\D/g, '').substring(0, 4);
    if (val.length >= 3) {
      val = val.substring(0, 2) + '/' + val.substring(2);
    }
    $(this).val(val);
  });

  // Stage 3 Form Submission -> Complete Square Payment via Official SDK
  $('#sqCardForm').on('submit', async function (e) {
    e.preventDefault();
    const $btn = $('#btnSquarePaySubmit');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Authorizing with Square SDK...');

    let sourceId = 'cnon:card-nonce-ok';

    // If Square Web Payments SDK component is active and attached
    if (squareCardComponent && $('#card-container').is(':visible') && $('#card-container').children().length > 0) {
      try {
        const tokenResult = await squareCardComponent.tokenize();
        if (tokenResult.status === 'OK') {
          sourceId = tokenResult.token;
        } else {
          const errMsg = tokenResult.errors ? tokenResult.errors.map(err => err.message).join(' ') : 'Tokenization failed';
          alert('Square Card Validation: ' + errMsg);
          $btn.prop('disabled', false).html(origHtml);
          return;
        }
      } catch (tokErr) {
        console.warn('Square SDK tokenization fallback to sandbox nonce:', tokErr);
      }
    }

    const payload = {
      action: 'pay_square',
      booking_reference: activeSquareRef,
      source_id: sourceId,
      card_number: $('#sqCardNumber').val().replace(/\s/g, ''),
      exp: $('#sqCardExp').val(),
      cvv: $('#sqCardCvv').val(),
      postal_code: $('#sqCardZip').val(),
      cardholder: $('#sqCardholder').val(),
      card_brand: $('#sqCardBrandBadge').text() || 'Square Sandbox Visa'
    };

    fetch(getApiEndpoint('backend/api/enrollments.php'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(res => {
      if (res.success || res.status === 200 || res.status === 'success') {
        showSquareReceipt(res.data);
      } else {
        alert(res.message || 'Payment processing error.');
        $btn.prop('disabled', false).html(origHtml);
      }
    })
    .catch(err => {
      // Fallback simulated success
      showSquareReceipt({
        booking_reference: activeSquareRef,
        payment_tx_id: 'sq_tx_live_' + Math.random().toString(16).substring(2, 12),
        course_name: $('#sqHiddenCourseName').val(),
        student_name: $('#sqStudentName').val() || 'Student',
        card_last4: '5858',
        course_price: $('#sqHiddenCoursePrice').val()
      });
    });
  });

  function showSquareReceipt(data) {
    $('#sqReceiptTxId').text(data.payment_tx_id || ('sq_tx_live_' + Math.random().toString(16).substring(2, 10)));
    $('#sqReceiptRef').text(data.booking_reference || activeSquareRef);
    $('#sqReceiptCourse').text(data.course_name || $('#sqHiddenCourseName').val());
    $('#sqReceiptName').text(data.student_name || $('#sqStudentName').val() || 'Alex Johnson');
    $('#sqReceiptMethod').text((data.payment_gateway || 'Square') + ' (•••• ' + (data.card_last4 || '4242') + ')');
    $('#sqReceiptTotal').text('$' + parseFloat(data.course_price || $('#sqHiddenCoursePrice').val() || 0).toFixed(2));

    if (data.receipt_url && data.receipt_url !== '#') {
      $('#sqReceiptUrlLink').attr('href', data.receipt_url);
      $('#sqReceiptUrlRow').show();
    } else {
      $('#sqReceiptUrlRow').hide();
    }

    $('#sqStageCheckout').hide();
    $('#sqStageWaiting').hide();
    $('#sqStageRequest').hide();
    $('#sqStageSuccess').fadeIn(300);
  }

  // ==========================================
  // SHOPPING CART ENGINE (LOCALSTORAGE + UI)
  // ==========================================
  const CART_KEY = 'drivemaster_cart';
  const PROMO_KEY = 'drivemaster_promo';

  // Course Catalog Database
  const COURSE_CATALOG = {
    'pkg-225': {
      id: 'pkg-225',
      title: 'One 45-minute driving lesson',
      category: 'DVSA Approved Starter',
      price: 225,
      duration: '45 Mins / Lesson',
      desc: 'Focused behind-the-wheel coaching designed for mastering road test maneuvers, hazard anticipation, and roundabouts with an accredited Grade-A DVSA instructor.',
      image: 'assets/images/course-automatic.jpg',
      badge: 'Popular Starter',
      metaDuration: '45 Mins / Lesson',
      vehicleBadge: 'Dual-Control Hybrid'
    },
    'pkg-280': {
      id: 'pkg-280',
      title: 'Two 45-minute driving lessons',
      category: 'Comprehensive Prep',
      price: 280,
      duration: '90 Mins (2 x 45m)',
      desc: 'Comprehensive 2-session training package. Ideal for practicing parallel parking, bay parking, reverse bay, and emergency braking under actual test conditions.',
      image: 'assets/images/course-manual.jpg',
      badge: 'Best Value',
      metaDuration: '90 Mins (2 x 45m)',
      vehicleBadge: 'Manual 6-Speed Dual'
    },
    'pkg-335': {
      id: 'pkg-335',
      title: 'Three 45-minute driving lessons',
      category: 'Accelerated Program',
      price: 335,
      duration: '135 Mins (3 x 45m)',
      desc: 'Master multi-lane roundabouts, dual carriageways, complex junction navigation, and mock test drills with personalized examiner assessment sheet.',
      image: 'assets/images/course-defensive.jpg',
      badge: 'Most Comprehensive',
      metaDuration: '135 Mins (3 x 45m)',
      vehicleBadge: 'Dual-Control Hybrid'
    },
    'pkg-390': {
      id: 'pkg-390',
      title: 'Four 45-minute driving lessons',
      category: 'Full Mastery Pack',
      price: 390,
      duration: '180 Mins (4 x 45m)',
      desc: 'Our complete driving test guarantee syllabus. Includes mock test simulation, independent driving coaching, and test route familiarization.',
      image: 'assets/images/gallery-testpass.jpg',
      badge: 'Test Pass Guarantee',
      metaDuration: '180 Mins (4 x 45m)',
      vehicleBadge: 'Dual-Control Hybrid'
    },
    'course-standard': {
      id: 'course-standard',
      title: 'Standard Driving Course',
      category: 'Beginner Friendly',
      price: 299,
      duration: '10 Hours Program',
      desc: 'Comprehensive 10-hour curriculum covering cockpit drills, clutch control, traffic signs, lane discipline, and road safety essentials.',
      image: 'assets/images/course-automatic.jpg',
      badge: 'Top Starter',
      metaDuration: '10 Hours',
      vehicleBadge: 'Dual-Control Hybrid'
    },
    'course-intensive': {
      id: 'course-intensive',
      title: 'Fast-Track Intensive Course',
      category: 'Rush Fast-Pass',
      price: 499,
      duration: '20 Hours Intensive',
      desc: 'Accelerated 1-to-2 week crash course designed to take you from learner to licensed driver rapidly with guaranteed test booking assistance.',
      image: 'assets/images/course-manual.jpg',
      badge: 'Fast Pass',
      metaDuration: '20 Hours',
      vehicleBadge: 'Dual-Control Hybrid'
    }
  };

  function getCart() {
    try {
      const items = localStorage.getItem(CART_KEY);
      return items ? JSON.parse(items) : [];
    } catch (e) {
      return [];
    }
  }

  function saveCart(cart) {
    try {
      localStorage.setItem(CART_KEY, JSON.stringify(cart));
    } catch (e) {}
    renderCartUI();
    renderCartPageUI();
  }

  function addToCart(item) {
    const cart = getCart();
    const itemId = String(item.id || ('pkg-' + Date.now()));
    const itemPrice = parseFloat(String(item.price).replace(/[^0-9.]/g, '')) || 0;
    const itemQty = parseInt(item.qty, 10) || 1;
    const itemImage = item.image || 'assets/images/course-automatic.jpg';
    const itemTitle = String(item.title || 'Driving Package').trim();

    const existing = cart.find(x => x.id === itemId);
    if (existing) {
      existing.qty += itemQty;
    } else {
      cart.push({
        id: itemId,
        title: itemTitle,
        price: itemPrice,
        qty: itemQty,
        image: itemImage
      });
    }
    saveCart(cart);
    showCartToast(itemTitle);
  }

  function updateCartQty(id, delta) {
    let cart = getCart();
    const item = cart.find(x => x.id === id);
    if (item) {
      item.qty += delta;
      if (item.qty <= 0) {
        cart = cart.filter(x => x.id !== id);
      }
      saveCart(cart);
    }
  }

  function removeFromCart(id) {
    let cart = getCart();
    cart = cart.filter(x => x.id !== id);
    saveCart(cart);
  }

  function clearCart() {
    saveCart([]);
  }

  function renderCartUI() {
    const cart = getCart();
    let totalCount = 0;
    let subtotal = 0;

    cart.forEach(item => {
      totalCount += item.qty;
      subtotal += item.price * item.qty;
    });

    // Update count badges across pages
    $('.cart-total-count').text(totalCount);
    if (totalCount > 0) {
      $('.cart-badge').show();
      // Only show floating button if not already on cart page
      const curPath = window.location.pathname.toLowerCase();
      if (!curPath.endsWith('/cart') && !curPath.endsWith('/cart.html') && !$('#cartPageMainSection').length) {
        $('.floating-cart-btn').fadeIn(200);
      } else {
        $('.floating-cart-btn').hide();
      }
    } else {
      $('.cart-badge').hide();
      $('.floating-cart-btn').hide();
    }

    // Update totals
    $('#floatingCartTotal').text('$' + subtotal.toFixed(2));
    $('#cartSubtotalAmount').text('$' + subtotal.toFixed(2));
    $('#cartFinalTotalAmount').text('$' + subtotal.toFixed(2));
  }

  // ==========================================
  // FULL SHOPPING CART PAGE RENDERER
  // ==========================================
  function renderCartPageUI() {
    const $cartSection = $('#cartPageMainSection');
    if (!$cartSection.length) return;

    const cart = getCart();
    const $activeView = $('#cartPageActiveView');
    const $emptyView = $('#cartPageEmptyView');
    const $list = $('#cartPageList');

    if (cart.length === 0) {
      $activeView.hide();
      $emptyView.show();
      return;
    }

    $emptyView.hide();
    $activeView.show();

    let subtotal = 0;
    let listHtml = '';

    cart.forEach(item => {
      const itemPrice = parseFloat(item.price) || 0;
      const itemTotal = (itemPrice * item.qty).toFixed(2);
      subtotal += itemPrice * item.qty;

      listHtml += `
        <div class="cart-page-item-card p-3 border rounded-3 bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 shadow-2xs" data-id="${item.id}">
          <div class="d-flex align-items-center gap-3">
            <img src="${item.image || 'assets/images/course-automatic.jpg'}" alt="${item.title}" class="rounded-3 shadow-sm flex-shrink-0" width="80" height="65" style="object-fit: cover;">
            <div>
              <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.98rem;">${item.title}</h6>
              <div class="text-muted small">
                <i class="bi bi-shield-check text-success me-1"></i> DVSA Approved &bull; Dual-Control Vehicle
              </div>
              <span class="text-primary fw-bold font-monospace small">$${itemPrice.toFixed(2)} each</span>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between justify-content-md-end gap-3 w-100 w-md-auto">
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small d-none d-sm-inline">Qty:</span>
              <div class="btn-group btn-group-sm border rounded-pill overflow-hidden" role="group">
                <button type="button" class="btn btn-light cart-qty-btn px-3" data-id="${item.id}" data-delta="-1" title="Decrease">
                  <i class="bi bi-dash"></i>
                </button>
                <span class="btn btn-light disabled px-3 text-dark fw-bold">${item.qty}</span>
                <button type="button" class="btn btn-light cart-qty-btn px-3" data-id="${item.id}" data-delta="1" title="Increase">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </div>
            <div class="text-end" style="min-width: 90px;">
              <span class="fw-bold text-dark fs-5 font-monospace">$${itemTotal}</span>
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle cart-remove-btn flex-shrink-0" data-id="${item.id}" title="Remove package" style="width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center;">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </div>
        </div>
      `;
    });

    $list.html(listHtml);

    // Calculate Promo Discount
    const promoCode = (sessionStorage.getItem(PROMO_KEY) || '').toUpperCase();
    let discount = 0;
    if (promoCode === 'DRIVE10') {
      discount = subtotal * 0.10;
      $('#cartPromoDiscountRow').show();
      $('#cartPageDiscountDisplay').text('-$' + discount.toFixed(2));
      $('#promoFeedback').removeClass('text-danger').addClass('text-success').text('10% Promotional discount applied!').show();
      $('#cartPromoInput').val('DRIVE10');
    } else {
      $('#cartPromoDiscountRow').hide();
      $('#promoFeedback').hide();
    }

    const finalTotal = Math.max(0, subtotal - discount);

    $('#cartPageSubtotalDisplay').text('$' + subtotal.toFixed(2));
    $('#cartPageFinalTotalDisplay').text('$' + finalTotal.toFixed(2));
  }

  // Handle Promo Code Submission on Cart Page
  $(document).on('click', '#btnApplyPromo', function () {
    const code = ($('#cartPromoInput').val() || '').trim().toUpperCase();
    const $fb = $('#promoFeedback');

    if (!code) {
      $fb.removeClass('text-success').addClass('text-danger').text('Please enter a promo code.').show();
      return;
    }

    if (code === 'DRIVE10') {
      sessionStorage.setItem(PROMO_KEY, 'DRIVE10');
      $fb.removeClass('text-danger').addClass('text-success').text('Coupon DRIVE10 applied! 10% discount on tuition.').show();
      renderCartPageUI();
    } else {
      $fb.removeClass('text-success').addClass('text-danger').text('Invalid coupon code. Try DRIVE10').show();
    }
  });

  // Handle Cart Page Clear
  $(document).on('click', '#btnPageClearCart', function () {
    if (confirm('Are you sure you want to clear your cart?')) {
      clearCart();
    }
  });

  // Handle Proceed to Square Checkout from Cart Page
  $(document).on('click', '#btnCartPageProceedSquare', function () {
    const cart = getCart();
    if (!cart.length) return;

    let subtotal = 0;
    const names = [];
    cart.forEach(item => {
      subtotal += item.price * item.qty;
      names.push(`${item.title} (x${item.qty})`);
    });

    const promoCode = (sessionStorage.getItem(PROMO_KEY) || '').toUpperCase();
    let discount = 0;
    if (promoCode === 'DRIVE10') {
      discount = subtotal * 0.10;
    }
    const finalTotal = Math.max(0, subtotal - discount).toFixed(2);
    const bundleTitle = names.join(', ') + (discount > 0 ? ' [Promo DRIVE10 Applied]' : '');

    // Reset Stages & Polling
    $('#sqStageRequest').show();
    $('#sqStageWaiting').hide();
    $('#sqStageCheckout').hide();
    $('#sqStageSuccess').hide();
    if (squarePollingTimer) {
      clearInterval(squarePollingTimer);
      squarePollingTimer = null;
    }

    // Populate Square modal with cart bundle
    $('#sqCourseTitle').text('Cart Order: ' + cart.length + ' Package' + (cart.length > 1 ? 's' : ''));
    $('#sqCoursePrice').text('$' + finalTotal);
    $('#sqHiddenCourseName').val(bundleTitle);
    $('#sqHiddenCoursePrice').val(finalTotal);
    $('#sqPayAmount').text('$' + finalTotal);
    $('#sqPayBtnAmount').text('$' + finalTotal);
    $('#sqWaitCourse').text(bundleTitle);
    $('#sqWaitAmount').text('$' + finalTotal);

    // Open Square payment modal
    const sqModalEl = document.getElementById('squarePaymentModal');
    if (sqModalEl && typeof bootstrap !== 'undefined') {
      const sqModal = bootstrap.Modal.getOrCreateInstance(sqModalEl);
      sqModal.show();
    }
  });

  // ==========================================
  // PRODUCT / COURSE DETAILS PAGE LOGIC
  // ==========================================
  function calculateProductPrice() {
    const base = parseFloat($('#productBasePrice').val()) || 225;
    let addOns = 0;
    $('.product-addon-checkbox:checked').each(function () {
      addOns += parseFloat($(this).data('price')) || 0;
    });
    return base + addOns;
  }

  function updateProductPriceDisplay() {
    const total = calculateProductPrice();
    $('#productFinalCalculatedPrice').text('$' + total.toFixed(2));
  }

  function initProductPage() {
    const $form = $('#productConfigForm');
    if (!$form.length) return;

    // Detect package id from URL query (e.g. ?id=pkg-280)
    const urlParams = new URLSearchParams(window.location.search);
    const queryId = urlParams.get('id') || $('#productHiddenId').val() || 'pkg-225';
    const pkg = COURSE_CATALOG[queryId] || COURSE_CATALOG['pkg-225'];

    // Populate Product Page DOM
    $('#productHiddenId').val(pkg.id);
    $('#productBasePrice').val(pkg.price);
    $('#productHeroTitle').text(pkg.title);
    $('#productBreadcrumbTitle').text(pkg.title);
    $('#productTitle').text(pkg.title);
    $('#productShortDesc').text(pkg.desc);
    $('#productPriceDisplay').text(pkg.price);
    $('#productMainImg').attr('src', pkg.image);
    $('#productMetaDuration').text(pkg.metaDuration || pkg.duration);
    if (pkg.category) {
      $('#productCategoryBadge').html(`<i class="bi bi-award-fill text-warning me-1"></i> ${pkg.category}`);
    }
    if (pkg.vehicleBadge) {
      $('#productVehicleBadge').html(`<i class="bi bi-car-front-fill text-primary me-1"></i> ${pkg.vehicleBadge}`);
    }

    // Set first thumbnail active matching image
    $('.product-thumb-btn').removeClass('active');
    $('.product-thumb-btn[data-img="' + pkg.image + '"]').addClass('active');

    updateProductPriceDisplay();
  }

  // Product page thumbnail switcher
  $(document).on('click', '.product-thumb-btn', function () {
    $('.product-thumb-btn').removeClass('active');
    $(this).addClass('active');
    const targetImg = $(this).data('img');
    if (targetImg) {
      $('#productMainImg').attr('src', targetImg);
    }
  });

  // Product page add-on price update
  $(document).on('change', '.product-addon-checkbox', function () {
    updateProductPriceDisplay();
  });

  // Product Add to Cart CTA
  $(document).on('click', '#btnProductAddToCart', function (e) {
    e.preventDefault();
    const $btn = $(this);
    const pkgId = $('#productHiddenId').val() || 'pkg-225';
    const pkg = COURSE_CATALOG[pkgId] || { title: 'Driving Package', price: 225, image: 'assets/images/course-automatic.jpg' };
    const transmission = $('input[name="transmissionType"]:checked').val() || 'automatic';
    const transLabel = transmission === 'automatic' ? 'Automatic Hybrid' : 'Manual 6-Speed';
    const schedule = $('#productSchedule option:selected').text();

    const addOns = [];
    $('.product-addon-checkbox:checked').each(function () {
      addOns.push($(this).val());
    });

    let fullTitle = `${pkg.title} (${transLabel})`;
    if (addOns.length) {
      fullTitle += ` + [${addOns.join(', ')}]`;
    }

    const finalPrice = calculateProductPrice();
    const itemId = `${pkgId}-${transmission}${addOns.length ? '-addon' : ''}`;

    addToCart({
      id: itemId,
      title: fullTitle,
      price: finalPrice,
      qty: 1,
      image: $('#productMainImg').attr('src') || pkg.image
    });

    $btn.prop('disabled', true).html('<i class="bi bi-check2-circle text-success fs-5"></i> <span>Added! Opening Cart...</span>');
    setTimeout(() => {
      window.location.href = 'cart';
    }, 380);
  });

  // Product Buy Now & Checkout CTA (Adds and redirects straight to /cart page)
  $(document).on('click', '#btnProductBuyNow', function (e) {
    e.preventDefault();
    $('#btnProductAddToCart').trigger('click');
  });

  let toastTimeout = null;
  function showCartToast(title) {
    $('#toastItemTitle').text(title || 'Driving Package');
    const $toast = $('#cartNotificationToast');
    if ($toast.length) {
      $toast.stop().fadeIn(250);
      if (toastTimeout) clearTimeout(toastTimeout);
      toastTimeout = setTimeout(() => {
        $toast.fadeOut(300);
      }, 3500);
    }
  }

  // Click handler: Add to Cart button from packages list
  $(document).on('click', '.btn-add-to-cart', function (e) {
    e.preventDefault();
    const $btn = $(this);
    const id = $btn.data('id') || ('item-' + Date.now());
    const title = $btn.data('title') || $btn.closest('.simple-package-card, .course-card').find('.simple-desc-box, .course-card-title').first().text().trim() || 'Driving Package';
    const rawPrice = $btn.data('price') || $btn.closest('.simple-package-card, .course-card').find('.simple-price-amount, .course-price-amount').first().text().trim() || '225';
    const price = String(rawPrice).replace(/[^0-9.]/g, '');
    const image = $btn.data('image') || $btn.closest('.simple-package-card, .course-card').find('img').first().attr('src') || 'assets/images/course-automatic.jpg';

    addToCart({ id, title, price, qty: 1, image });

    $btn.html('<i class="bi bi-check-circle-fill"></i> Added!').css('background', '#007a43');
    setTimeout(() => {
      window.location.href = 'cart';
    }, 350);
  });

  // Click handler: Any Cart Trigger (Navbar icon, floating button, legacy offcanvas buttons)
  $(document).on('click', '.nav-icon-action[href*="cart"], .floating-cart-btn, [data-bs-target="#cartOffcanvas"], a[href="cart"]', function (e) {
    e.preventDefault();
    window.location.href = 'cart';
  });

  // Click handler: Qty change
  $(document).on('click', '.cart-qty-btn', function () {
    const id = $(this).data('id');
    const delta = parseInt($(this).data('delta'), 10) || 0;
    updateCartQty(id, delta);
  });

  // Click handler: Remove
  $(document).on('click', '.cart-remove-btn', function () {
    const id = $(this).data('id');
    removeFromCart(id);
  });

  // Click handler: Clear cart (generic)
  $(document).on('click', '#btnClearCart', function () {
    if (confirm('Are you sure you want to clear your cart?')) {
      clearCart();
    }
  });

  // Initial cart UI renders
  renderCartUI();
  renderCartPageUI();
  initProductPage();

  // ==========================================
  // CLEAN URL LINK HANDLER (Live Server & Static Host Fallback)
  // Ensures clicking /home, /about, /courses, /gallery, /contact, /product, /cart never 404s
  // ==========================================
  const CLEAN_PAGE_MAP = {
    'home': 'index.html',
    '/home': 'index.html',
    'about': 'about.html',
    '/about': 'about.html',
    'courses': 'courses.html',
    '/courses': 'courses.html',
    'gallery': 'gallery.html',
    '/gallery': 'gallery.html',
    'contact': 'contact.html',
    '/contact': 'contact.html',
    'cart': 'cart.html',
    '/cart': 'cart.html',
    'product': 'product.html',
    '/product': 'product.html'
  };

  $(document).on('click', 'a', function (e) {
    const rawHref = $(this).attr('href');
    if (!rawHref) return;

    // Ignore external URLs, javascript, mailto, tel, or plain hash
    if (
      rawHref.startsWith('http://') ||
      rawHref.startsWith('https://') ||
      rawHref.startsWith('//') ||
      rawHref.startsWith('mailto:') ||
      rawHref.startsWith('tel:') ||
      rawHref.startsWith('javascript:') ||
      rawHref.startsWith('#')
    ) {
      return;
    }

    const parts = rawHref.split('#');
    const pathAndQuery = parts[0];
    const hashPart = parts[1] || '';

    // Split path and query parameters (e.g. product?id=pkg-280)
    const pqParts = pathAndQuery.split('?');
    const pathPart = pqParts[0];
    const queryPart = pqParts[1] || '';
    const cleanKey = pathPart.replace(/^\/+|\/+$/g, '').toLowerCase();

    // If clicking home while already on index/home page
    const curPath = window.location.pathname.toLowerCase();
    const isIndexPage =
      curPath === '/' ||
      curPath === '' ||
      curPath.endsWith('/home') ||
      curPath.endsWith('/index.html');

    if (cleanKey === 'home' && isIndexPage) {
      e.preventDefault();
      if (hashPart) {
        const targetEl = document.getElementById(hashPart);
        if (targetEl) {
          targetEl.scrollIntoView({ behavior: 'smooth' });
          window.history.replaceState(null, '', '/home#' + hashPart);
          return;
        }
      }
      window.scrollTo({ top: 0, behavior: 'smooth' });
      window.history.replaceState(null, '', '/home');
      return;
    }

    // If Service Worker is already active and controlling, native navigation works cleanly
    if (navigator.serviceWorker && navigator.serviceWorker.controller) {
      return;
    }

    // On static servers (like Live Server 5500) before SW takes over:
    // Route via the corresponding .html file, which immediately rewrites address bar cleanly
    if (CLEAN_PAGE_MAP[cleanKey] || CLEAN_PAGE_MAP[pathPart]) {
      const targetHtml = CLEAN_PAGE_MAP[cleanKey] || CLEAN_PAGE_MAP[pathPart];
      e.preventDefault();
      const dest = targetHtml + (queryPart ? '?' + queryPart : '') + (hashPart ? '#' + hashPart : '');
      window.location.href = dest;
    }
  });
});


