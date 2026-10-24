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
    } else {
      $items.each(function () {
        const itemCat = $(this).data('category') || '';
        if (itemCat.indexOf(filterVal) !== -1) {
          $(this).stop().fadeIn(300);
        } else {
          $(this).stop().fadeOut(200);
        }
      });
    }
  });

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
      student_name: $('#studentName').val() || 'Student',
      student_email: $('#studentEmail').val() || '',
      student_phone: $('#studentPhone').val() || '',
      course_name: $('#modalCourseInput').val() || $('#modalCourseName').text() || 'Standard Course',
      pickup_address: $('#pickupAddress').val() || '',
      preferred_transmission: $('#transmissionSelect').val() || 'automatic'
    };

    fetch('/backend/api/enrollments.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    }).catch(function() {
      // Fallback if backend offline
    }).finally(function() {
      $form.hide();
      $('#courseEnrollSuccess').fadeIn(300);
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
    fetch('/backend/api/contact.php', {
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
});

