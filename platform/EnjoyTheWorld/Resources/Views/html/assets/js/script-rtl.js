/*
Author       : DreamsTechnologies
Template Name: DreamsTour - Bootstrap Template
*/

(function () {
    "use strict";
    
    // Banner Slider
    if ($(".banner-slider-rtl").length > 0) {
      $(".banner-slider-rtl").owlCarousel({
        loop: true,
        margin: 0,
        nav: false,
        dots: true,
        autoplay: false,
        smartSpeed: 2000,
        rtl: true,
        navText: [
          "<i class='fa-solid fa-arrow-left'></i>",
          "<i class='fa-solid fa-arrow-right'></i>",
        ],
        responsive: {
          0: {
            items: 1,
          },
          550: {
            items: 1,
          },
          1200: {
            items: 1,
          },
        },
      });
    }

    // Destination Slider
    if ($(".destination-sliders").length > 0) {
      $(".destination-sliders").owlCarousel({
        loop: true,
        margin: 24,
        nav: true,
        dots: false,
        autoplay: false,
        smartSpeed: 2000,
        rtl: true,
        navText: [
          "<i class='fa-solid fa-chevron-left'></i>",
          "<i class='fa-solid fa-chevron-right'></i>",
        ],
        responsive: {
          0: {
            items: 1,
          },
          576: {
            items: 2,
          },
          992: {
            items: 3,
          },
          1200: {
            items: 4,
          },
        },
      });
    }

    // place Slider
    if ($(".place-slider-rtl").length > 0) {
      $(".place-slider-rtl").owlCarousel({
        loop: false,
        margin: 24,
        nav: true,
        dots: false,
        smartSpeed: 2000,
        autoplay: false,
        rtl: true,
        navText: [
          "<i class='isax isax-arrow-left-2'></i>",
          "<i class='isax isax-arrow-right-3'></i>",
        ],
        responsive: {
          0: {
            items: 1,
          },
          550: {
            items: 1,
          },
          768: {
            items: 2,
          },
          992: {
            items: 3,
          },
          1200: {
            items: 3,
          },
          1400: {
            items: 4,
          },
        },
      });
    }

    // Image Carousel
    if ($(".img-slider-rtl").length > 0) {
      $(".img-slider-rtl").owlCarousel({
        loop: true,
        margin: 20,
        nav: true,
        dots: true,
        smartSpeed: 2000,
        autoplay: false,
        rtl: true,
        navText: [
          '<i class="fa-solid fa-chevron-left"></i>',
          '<i class="fa-solid fa-chevron-right"></i>',
        ],
        responsive: {
          0: {
            items: 1,
          },
          550: {
            items: 1,
          },
          768: {
            items: 1,
          },
          1000: {
            items: 1,
          },
        },
      });
    }

   // Experts Slider
   if ($(".experts-slider-rtl").length > 0) {
    $(".experts-slider-rtl").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
      rtl: true,
      navText: [
        "<i class='fa-solid fa-chevron-left'></i>",
        "<i class='fa-solid fa-chevron-right'></i>",
      ],
      responsive: {
        0: {
          items: 1,
        },
        576: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
      },
    });
  }

  // Client Slider
  if ($(".client-slider-rtl").length > 0) {
    $(".client-slider-rtl").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: false,
      autoplay: true,
      smartSpeed: 2000,
      rtl: true,
      navText: [
        "<i class='fa-solid fa-chevron-left'></i>",
        "<i class='fa-solid fa-chevron-right'></i>",
      ],
      responsive: {
        0: {
          items: 2,
        },
        576: {
          items: 3,
        },
        992: {
          items: 4,
        },
        1200: {
          items: 5,
        },
        1400: {
          items: 7,
        },
      },
    });
  }

  // Testimonial Slider
  if ($(".testimonial-slider-rtl").length > 0) {
    $(".testimonial-slider-rtl").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
      rtl: true,
      navText: [
        "<i class='isax isax-arrow-left-2'></i>",
        "<i class='isax isax-arrow-right-3'></i>",
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        1200: {
          items: 3,
        },
      },
    });
  }

})(jQuery);