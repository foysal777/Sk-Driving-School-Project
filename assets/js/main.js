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

  // Start initial auto slide
  startAutoSlide();

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
});
