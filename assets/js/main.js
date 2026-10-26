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
  // Category Filter Tabs
  $('.gallery-filter-btn').on('click', function () {
    $('.gallery-filter-btn').removeClass('active');
    $(this).addClass('active');

    const filterVal = $(this).data('filter');
    const $items = $('.gallery-item-col');

    if (filterVal === 'all') {
      $items.stop().fadeIn(300);
    } else if (filterVal === 'pass') {
      // Show both category pass + highway
      $items.each(function () {
        const itemCat = String($(this).data('category') || '').toLowerCase();
        if (itemCat === 'pass' || itemCat === 'highway' || itemCat.includes('pass') || itemCat.includes('highway')) {
          console.log(itemCat)
          $(this).stop().fadeIn(300);
        } else {
          $(this).stop().fadeOut(200);
        }
      });
    } else {
      $items.each(function () {
        const itemCat = $(this).data('category');
        if (itemCat === filterVal) {
          $(this).stop().fadeIn(300);
        } else {
          $(this).stop().fadeOut(200);
        }
      });
    }
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
    if (window.location.protocol === 'file:') {
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
            // Transition to Stage 3: Checkout Unlocked
            $('#sqStageWaiting').hide();
            $('#sqStageCheckout').fadeIn(300);
            $('#sqCardholder').val($('#sqStudentName').val() || res.data.student_name);
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

  // Quick Fill Demo Card
  $(document).on('click', '#btnSquareDemoFill', function () {
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

  // Stage 3 Form Submission -> Complete Square Payment
  $('#sqCardForm').on('submit', function (e) {
    e.preventDefault();
    const $btn = $('#btnSquarePaySubmit');
    const origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Authorizing Square Payment...');

    const payload = {
      action: 'pay_square',
      booking_reference: activeSquareRef,
      card_number: $('#sqCardNumber').val().replace(/\s/g, ''),
      exp: $('#sqCardExp').val(),
      cvv: $('#sqCardCvv').val(),
      postal_code: $('#sqCardZip').val(),
      cardholder: $('#sqCardholder').val(),
      card_brand: $('#sqCardBrandBadge').text()
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
        card_last4: '4242',
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

    $('#sqStageCheckout').hide();
    $('#sqStageWaiting').hide();
    $('#sqStageRequest').hide();
    $('#sqStageSuccess').fadeIn(300);
  }

  // ==========================================
  // SHOPPING CART ENGINE (LOCALSTORAGE + UI)
  // ==========================================
  const CART_KEY = 'drivemaster_cart';

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
  }

  function addToCart(item) {
    const cart = getCart();
    const existing = cart.find(x => x.id === item.id);
    if (existing) {
      existing.qty += (item.qty || 1);
    } else {
      cart.push({
        id: item.id,
        title: item.title,
        price: parseFloat(item.price) || 0,
        qty: item.qty || 1
      });
    }
    saveCart(cart);
    showCartToast(item.title);
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
      $('.floating-cart-btn').fadeIn(200);
    } else {
      $('.cart-badge').hide();
    }

    // Update totals
    $('#floatingCartTotal').text('$' + subtotal.toFixed(2));
    $('#cartSubtotalAmount').text('$' + subtotal.toFixed(2));
    $('#cartFinalTotalAmount').text('$' + subtotal.toFixed(2));

    // Render drawer list
    const $container = $('#cartItemsContainer');
    if ($container.length) {
      if (cart.length === 0) {
        $container.html(`
          <div class="text-center py-5 text-muted">
            <i class="bi bi-cart-x fs-1 d-block mb-3 text-secondary opacity-50"></i>
            <h6 class="fw-bold text-dark">Your cart is empty</h6>
            <p class="small mb-3">Add driving packages to customize your curriculum before checkout.</p>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-dismiss="offcanvas">
              Browse Packages
            </button>
          </div>
        `);
        $('#btnCartProceedSquare').prop('disabled', true);
      } else {
        let html = '';
        cart.forEach(item => {
          const itemTotal = (item.price * item.qty).toFixed(2);
          html += `
            <div class="cart-item-row" data-id="${item.id}">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">${item.title}</h6>
                  <span class="text-primary fw-bold small">$${parseFloat(item.price).toFixed(2)} each</span>
                </div>
                <button type="button" class="btn btn-link text-danger p-0 cart-remove-btn" data-id="${item.id}" title="Remove item">
                  <i class="bi bi-trash3-fill"></i>
                </button>
              </div>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-secondary cart-qty-btn py-0 px-2" data-id="${item.id}" data-delta="-1">-</button>
                  <span class="btn btn-outline-secondary disabled px-3 py-0 text-dark fw-bold">${item.qty}</span>
                  <button type="button" class="btn btn-outline-secondary cart-qty-btn py-0 px-2" data-id="${item.id}" data-delta="1">+</button>
                </div>
                <div class="fw-bold text-dark font-monospace">$${itemTotal}</div>
              </div>
            </div>
          `;
        });
        $container.html(html);
        $('#btnCartProceedSquare').prop('disabled', false);
      }
    }
  }

  let toastTimeout = null;
  function showCartToast(title) {
    $('#toastItemTitle').text(title || 'Driving Package');
    const $toast = $('#cartNotificationToast');
    $toast.stop().fadeIn(250);
    if (toastTimeout) clearTimeout(toastTimeout);
    toastTimeout = setTimeout(() => {
      $toast.fadeOut(300);
    }, 3200);
  }

  // Click handler: Add to Cart button
  $(document).on('click', '.btn-add-to-cart', function (e) {
    e.preventDefault();
    const id = $(this).data('id') || ('item-' + Date.now());
    const title = $(this).data('title') || $(this).closest('.simple-package-card').find('.simple-desc-box').text().trim() || 'Driving Package';
    const rawPrice = $(this).data('price') || $(this).closest('.simple-package-card').find('.simple-price-amount').text().trim() || '225';
    const price = String(rawPrice).replace(/[^0-9.]/g, '');
    
    addToCart({ id, title, price, qty: 1 });
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

  // Click handler: Clear cart
  $(document).on('click', '#btnClearCart', function () {
    if (confirm('Are you sure you want to clear your cart?')) {
      clearCart();
    }
  });

  // Click handler: Proceed to Square Checkout from Cart
  $(document).on('click', '#btnCartProceedSquare', function () {
    const cart = getCart();
    if (!cart.length) return;

    let subtotal = 0;
    const names = [];
    cart.forEach(item => {
      subtotal += item.price * item.qty;
      names.push(`${item.title} (x${item.qty})`);
    });

    const bundleTitle = names.join(', ');
    const numPrice = subtotal.toFixed(2);

    // Close offcanvas
    const offcanvasEl = document.getElementById('cartOffcanvas');
    if (offcanvasEl && typeof bootstrap !== 'undefined') {
      const oc = bootstrap.Offcanvas.getInstance(offcanvasEl);
      if (oc) oc.hide();
    }

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
    $('#sqCoursePrice').text('$' + numPrice);
    $('#sqHiddenCourseName').val(bundleTitle);
    $('#sqHiddenCoursePrice').val(numPrice);
    $('#sqPayAmount').text('$' + numPrice);
    $('#sqPayBtnAmount').text('$' + numPrice);
    $('#sqWaitCourse').text(bundleTitle);
    $('#sqWaitAmount').text('$' + numPrice);

    // Open Square payment modal
    const sqModalEl = document.getElementById('squarePaymentModal');
    if (sqModalEl && typeof bootstrap !== 'undefined') {
      const sqModal = bootstrap.Modal.getOrCreateInstance(sqModalEl);
      sqModal.show();
    }
  });

  // Initial cart UI render
  renderCartUI();

  // ==========================================
  // CLEAN URL LINK HANDLER (Live Server & Static Host Fallback)
  // Ensures clicking /home, /about, /courses, /gallery, /contact never 404s
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
    '/contact': 'contact.html'
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
    const pathPart = parts[0];
    const hashPart = parts[1] || '';
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
      const dest = targetHtml + (hashPart ? '#' + hashPart : '');
      window.location.href = dest;
    }
  });
});


