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
});
