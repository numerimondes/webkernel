/*
Author       : DreamsTechnologies
Template Name: DreamsTour - Bootstrap Template
*/

(function () {
  "use strict";

  // Mobile Menu Js
  $("#mobile-menu").meanmenu({
    meanMenuContainer: ".mobile-menu",
    meanScreenWidth: "991",
    meanExpand: ['<i class="ti ti-plus"></i>'],
  });

  // Sidebar Toggle Js
  $(".offcanvas-close,.offcanvas-overlay").on("click", function () {
    $(".offcanvas-info").removeClass("show");
    $(".offcanvas-overlay").removeClass("overlay-open");
  });
  $(".sidebar-menu").on("click", function () {
    $(".offcanvas-info").addClass("show");
    $(".offcanvas-overlay").addClass("overlay-open");
  });

  // Body Overlay Js
  $(".body-overlay").on("click", function () {
    $(".offcanvas__area").removeClass("offcanvas-opened");
    $(".df-search-area").removeClass("opened");
    $(".body-overlay").removeClass("opened");
  });

  // Sticky Header
  $(window).scroll(function () {
    if ($(this).scrollTop() > 130) {
      $("header").addClass("fixed");
    } else {
      $("header").removeClass("fixed");
    }
  });

  new WOW().init();

  // Banner Slider
  if ($(".banner-slider").length > 0) {
    $(".banner-slider").owlCarousel({
      loop: true,
      margin: 0,
      nav: false,
      dots: true,
      autoplay: false,
      smartSpeed: 2000,
      animateOut: "slideOutUp",
      animateIn: "slideIntUp",
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
  if ($(".destination-slider").length > 0) {
    $(".destination-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
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

  // Cruise Slider
  if ($(".cruise-slider").length > 0) {
    $(".cruise-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
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
          items: 3,
        },
        1200: {
          items: 6,
        },
      },
    });
  }

  // Experts Slider
  if ($(".experts-slider").length > 0) {
    $(".experts-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
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
  if ($(".client-slider").length > 0) {
    $(".client-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: false,
      autoplay: true,
      smartSpeed: 2000,
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
  if ($(".testimonial-slider").length > 0) {
    $(".testimonial-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
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
  
  // Tour Gallery Slider
  if ($(".tour-gallery-slider").length > 0) {
    $(".tour-gallery-slider").owlCarousel({
      loop: false,
      margin: 8,
      nav: false,
      dots: false,
      autoplay: false,
      responsive: {
        0: {
          items: 2
        },
        550: {
          items: 4
        },
        992: {
          items: 4
        },
        1200: {
          items: 5
        },
        1400: {
          items: 6
        }
      }
    })
  }

  // Vertical Slider
  if ($('.vertical-slider').length > 0) {
    $('.slider-for').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      asNavFor: '.slider-nav'
    });
    $('.slider-nav').slick({
      slidesToShow: 4,
      slidesToScroll: 1,
      vertical: true,
      asNavFor: '.slider-for',
      dots: false,
      arrows: true,
      focusOnSelect: true,
      verticalSwiping: true,
      prevArrow: "<span class='slick-next'><i class='fa-solid fa-chevron-down'></i></span>",
      nextArrow: "<span class='slick-prev'><i class='fa-solid fa-chevron-up'></i></span>",
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 3,
          }
        },
        {
          breakpoint: 768,
        },
        {
          breakpoint: 580,
          settings: {
            slidesToShow: 2,
          }
        },
        {
          breakpoint: 0,
          settings: {
            vertical: false,
            slidesToShow: 1,
          }
        }
      ]
    });
  }

  //Tours slider  
  if ($(".tours-slider").length > 0) {
    $(".tours-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
      navText: [
        "<i class='fa-solid fa-chevron-left'></i>",
        "<i class='fa-solid fa-chevron-right'></i>",
      ],
      responsive: {
        0: {
          items: 1  ,
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
      },
    });
  }

	// Toggle Password
	if($('.toggle-password').length > 0) {
		$(document).on('click', '.toggle-password', function() {
			$(this).find('i').toggleClass("isax-eye-slash isax-eye");
			var input = $(this).parent().find(".pass-input");
			if (input.attr("type") == "password") {
				input.attr("type", "text");
			} else {
				input.attr("type", "password");
			}
		});
	}

	// More View
	if ($('.more-view').length > 0) {
		const buttons = document.querySelectorAll('.more-view');
		const containers = document.querySelectorAll('.more-content');	
		buttons.forEach((button, index) => {
			let isLess = true;
			const container = containers[index];	
			function viewMoreLess() {
				if (isLess) {
					isLess = false;
					container.style.height = container.scrollHeight + 'px'; 
					button.innerHTML = "Show Less";
				} else {
					isLess = true;
					container.style.height = '148px';
					button.innerHTML = "Show More";
				}
			}	
			button.addEventListener('click', viewMoreLess);
		});
	}
	
	// Stick Sidebar
	if ($(window).width() > 1199) {
		if ($('.theiaStickySidebar').length > 0) {
			$('.theiaStickySidebar').theiaStickySidebar({
				additionalMarginTop: 30
			});
		}
	}

	// Service Slider
	if ($('.service-carousel').length > 0) {
		$(document).ready(function () {
			var sync1 = $("#large-img");
			var sync2 = $("#small-img");
			var slidesPerPage = 5; // Number of thumbnails per page
			var syncedSecondary = true;
			sync1.owlCarousel({
				items: 1,
				slideSpeed: 2000,
				smartSpeed: 2000,
				nav: true,
				autoplay: false,
				dots: false,
				loop: true,
				navText: [
					'<i class="fa-solid fa-chevron-left"></i>',
					'<i class="fa-solid fa-chevron-right"></i>'
				],
				responsiveRefreshRate: 200,
			}).on('changed.owl.carousel', syncPosition);
			sync2.owlCarousel({
				items: slidesPerPage,
				dots: false,
				nav: true,
				margin: 10,
				smartSpeed: 1000,
				smartSpeed: 2000,
				slideBy: slidesPerPage,
				responsiveRefreshRate: 100,
				navText: [
					'<i class="fa-solid fa-chevron-left"></i>',
					'<i class="fa-solid fa-chevron-right"></i>'
				],
			}).on('initialized.owl.carousel', function () {
				sync2.find(".owl-item").eq(0).addClass("current");
			}).on('changed.owl.carousel', syncPosition2);
			function syncPosition(el) {
				var count = el.item.count - 1;
				var current = Math.round(el.item.index - (el.item.count / 2) - .5);
				if (current < 0) {
					current = count;
				}
				if (current > count) {
					current = 0;
				}
				sync2
					.find(".owl-item")
					.removeClass("current")
					.eq(current)
					.addClass("current");

				var onscreen = sync2.find('.owl-item.active').length - 1;
				var start = sync2.find('.owl-item.active').first().index();
				var end = sync2.find('.owl-item.active').last().index();

				if (current > end) {
					sync2.data('owl.carousel').to(current, 100, true);
				}
				if (current < start) {
					sync2.data('owl.carousel').to(current - onscreen, 100, true);
				}
			}
			function syncPosition2(el) {
				if (syncedSecondary) {
					var number = el.item.index;
					sync1.data('owl.carousel').to(number, 100, true);
				}
			}
			sync2.on("click", ".owl-item", function (e) {
				e.preventDefault();
				var number = $(this).index();
				sync1.data('owl.carousel').to(number, 300, true);
			});
		});
	}

  // Car Slider  
  if ($(".cars-slider").length > 0) {
    $(".cars-slider").owlCarousel({
      loop: false,
      margin: 24,
      nav: false,
      dots: true,
      smartSpeed: 2000,
      autoplay: false,
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
        769: {
          items: 2,
        },
        993: {
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

  // place Slider
  if ($(".place-slider").length > 0) {
    $(".place-slider").owlCarousel({
      loop: false,
      margin: 24,
      nav: true,
      dots: false,
      smartSpeed: 2000,
      autoplay: false,
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
  if ($(".img-slider").length > 0) {
    $(".img-slider").owlCarousel({
      loop: true,
      margin: 20,
      nav: true,
      dots: true,
      smartSpeed: 2000,
      autoplay: false,
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

  // JQuery counterUp
  if ($(".counter").length > 0) {
    $(".counter").counterUp({
      delay: 10,
      time: 2000,
    });
    $(".counter").addClass("animated fadeInDownBig");
  }

  // Fligt Check
  $(".banner-form .form-check-input").on("change", function (e) {
    var radioval = $(this).val();
    if (radioval === "roundtrip") {
      $(".round-drip").show();
      $(".multi-trip").hide();
      $(".normal-trip").show();
    } else if (radioval === "multiway") {
      $(".round-drip").hide();
      $(".multi-trip").show();
      $(".normal-trip").hide();
    } else {
      $(".round-drip").hide();
      $(".multi-trip").hide();
      $(".normal-trip").show();
    }
  });

  // Fligt Check
  $(".banner-form .form-check-input").on("change", function (e) {
    var radioval = $(this).val();
    if (radioval === "different-drop") {
      $(".return-drop").show();
      $(".dropoff-time").show();
      $(".hourly-time").hide();
      $(".pickup-airport").hide();
      $(".from-location").show();
    } else if (radioval === "airport") {
      $(".return-drop").hide();
      $(".dropoff-time").hide();
      $(".hourly-time").hide();
      $(".to-location").show();
      $(".pickup-airport").show();
      $(".from-location").hide();
    } else if (radioval === "hourly-drop") {
      $(".return-drop").hide();
      $(".dropoff-time").hide();
      $(".to-location").hide();
      $(".hourly-time").show();
      $(".pickup-airport").hide();
      $(".from-location").show();
    } else {
      $(".return-drop").hide();
      $(".dropoff-time").hide();
      $(".hourly-time").hide();
      $(".pickup-airport").hide();
    }
  });

  // Fav Icon
  if ($(".fav-icon").length > 0) {
    $(".fav-icon").on("click", function () {
      $(this).toggleClass("selected");
    });
  }

  // View More
  if ($(".more-view").length > 0) {
    const buttons = document.querySelectorAll(".more-view");
    const containers = document.querySelectorAll(".more-content");
    buttons.forEach((button, index) => {
      let isLess = true;
      const container = containers[index];
      function viewMoreLess() {
        if (isLess) {
          isLess = false;
          container.style.height = container.scrollHeight + "px";
          button.innerHTML = "Show Less";
        } else {
          isLess = true;
          container.style.height = "148px";
          button.innerHTML = "Show More";
        }
      }
      button.addEventListener("click", viewMoreLess);
    });
  }

  // Stick Sidebar
  if ($(window).width() > 1199) {
    if ($(".theiaStickySidebar").length > 0) {
      $(".theiaStickySidebar").theiaStickySidebar({
        // Settings
        additionalMarginTop: 30,
      });
    }
  }

  // Scroll Top
  $(window).scroll(function () {
    var scroll = $(window).scrollTop();
    if (scroll >= 500) {
      $(".back-to-top-icon").addClass("show");
    } else {
      $(".back-to-top-icon").removeClass("show");
    }
  });

  // Datepicker
  if ($(".datetimepicker").length > 0) {
    $(".datetimepicker").datetimepicker({
      format: "DD-MM-YYYY",
      icons: {
        up: "fas fa-angle-up",
        down: "fas fa-angle-down",
        next: "fas fa-angle-right",
        previous: "fas fa-angle-left",
      },
    });
  }

  // Timepicker
  if ($(".timepicker").length > 0) {
    $(".timepicker").datetimepicker({
      format: "hh:mm A",
      icons: {
        up: "fa fa-angle-up",
        down: "fa fa-angle-down",
        next: "fa fa-angle-right",
        previous: "fa fa-angle-left",
      },
    });
  }

  //Increment Decrement Numberes
  $(".quantity-right-plus").on("click", function (e) {
    e.preventDefault();
    var input = $(this).closest(".custom-increment").find(".input-number");
    var quantity = parseInt(input.val());
    // Increment the quantity
    input.val(quantity + 1);
  });

  $(".quantity-left-minus").on("click", function (e) {   
     e.preventDefault();
    var input = $(this).closest(".custom-increment").find(".input-number");
    var quantity = parseInt(input.val());
    // Decrement the quantity, but not below 0
    if (quantity > 0) {
      input.val(quantity - 1);
    }
  });
	 //Increment Decrement Numberes

	$('.quantity-right-plus').on('click', function (e) {
		e.preventDefault();		
		var input = $(this).closest('.custom-increment').find('.input-number');
		var quantity = parseInt(input.val());		
		// Increment the quantity
		input.val(quantity + 1);
	});

	$('.quantity-left-minus').on('click', function (e) {
		e.preventDefault();		
		var input = $(this).closest('.custom-increment').find('.input-number');
		var quantity = parseInt(input.val());		
		// Decrement the quantity, but not below 0
		if (quantity > 0) {
			input.val(quantity - 1);
		}
	});

	// More View
	if ($('.more-btn').length > 0) {
		const buttons = document.querySelectorAll('.more-btn');
		const containers = document.querySelectorAll('.more-info');	
		buttons.forEach((button, index) => {
			let isLess = true;
			const container = containers[index];	
			function viewMoreLess() {
				if (isLess) {
					isLess = false;
					container.style.height = container.scrollHeight + 'px'; 
					button.innerHTML = "Show Less";
				} else {
					isLess = true;
					container.style.height = '70px';
					button.innerHTML = "Show More";
				}
			}	
			button.addEventListener('click', viewMoreLess);
		});
	}

  // Policy Toggle
  if ($('.toggle-btn').length > 0) {
    $('.toggle-btn').on('click', function () {
      const policyWrap = $(this).closest('.policy-wrap');
      const policyInfo = policyWrap.find('.policy-info');
      policyInfo.slideToggle('hide');
      policyWrap.toggleClass('expanded');      
      $(this).text(policyInfo.hasClass('hide') ? 'Show More' : 'Show Less');
    });
  }

  // Accordion class
  if ($('.faq-accordion').length > 0) {
    $('.accordion-item').on('show.bs.collapse', function () {
      $(this).parent().addClass('show');
    });
    $('.accordion-item .collapse').on('hide.bs.collapse', function () {
      $(this).parent().removeClass('show');
    });
  }
  // Accordion class
  if ($('.faq-cards-six').length > 0) {
    $('.accordion-item .collapse').on('show.bs.collapse', function () {
      $(this).parent().addClass('show');
    });
    $('.accordion-item .collapse').on('hide.bs.collapse', function () {
      $(this).parent().removeClass('show');
    });
  }

  if ($('.faq-four').length > 0) {
    $('.accordion-item .collapse').on('show.bs.collapse', function () {
      $(this).parent().addClass('show');
    });
    $('.accordion-item .collapse').on('hide.bs.collapse', function () {
      $(this).parent().removeClass('show');
    });
  }
  
  // Blog Slider
  if ($(".blog-slider").length > 0) {
    $(".blog-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
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
          items: 3,
        },
      },
    });
  }

  //Add Credit Card
  $('#open-folder').on('click', function() {
    $('#folder-input').click();
  });

  // Handle folder selection
  $('#folder-input').on('change', function(event) {
    // Do something with the selected folder
    var files = event.target.files;
    console.log('Selected files:', files);
  });

  // Select 2	
  if ($('.select2').length > 0) {
    $(".select2").select2();
  }

  if ($('.select').length > 0) {
    $('.select').select2({
      minimumResultsForSearch: -1,
      width: '100%'
    });
  }

  // Add Highlight
  $(".add-highlight").on('click', function () {			
  var addcontent =
    '<div class="col-md-12 highlight-info">'+
      '<div class="mb-3">'+
        '<label class="form-label">Highlights</label>'+
        '<div class="d-flex align-items-center">' +
        '<input type="text" class="form-control">'+
        '<a class="text-danger trash-icon d-flex align-items-center justify-content-center ms-3"><i class="isax isax-trash"></i></a>'
        '</div>'
      '</div>'+
    '</div>';
    $(".add-highlight-info").append(addcontent);
    $('.select').select2({
      minimumResultsForSearch: -1,
      width: '100%'
    });
    return false;		  
  });

  $(".add-highlight-info").on('click','.trash-icon', function () {
    $(this).closest('.highlight-info').remove();
    return false;
  });

  // Remove Gallery
  $(document).on("click", ".gallery-trash", function () {
    $(this).parent().hide();
  });
  
  $('.user-sidebar ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');

  // Image file upload
  function readURL(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('#blah').attr('src', e.target.result);
      }

      reader.readAsDataURL(input.files[0]);
    }
  }
  $("#imgInp").change(function(){
    readURL(this);
  });

  // Date Range Picker
  if($('.bookingrange').length > 0) {
    var start = moment().subtract(6, 'days');
    var end = moment();
    function booking_range(start, end) {
      $('.bookingrange span').html(start.format('M/D/YYYY') + ' - ' + end.format('M/D/YYYY'));
    }
    $('.bookingrange').daterangepicker({
      startDate: start,
      endDate: end,
      ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Year': [moment().startOf('year'), moment().endOf('year')],
        'Next Year': [moment().add(1, 'year').startOf('year'), moment().add(1, 'year').endOf('year')]
      }
    }, booking_range);
    booking_range(start, end);
  }

  //Dashboard Sidebar
  $('.user-sidebar a').on('click', function(e) {
    if($(this).parent().hasClass('submenu')) {
      e.preventDefault();
    }
    if(!$(this).hasClass('subdrop')) {
      $('ul', $(this).parents('ul:first')).hide(350);
      $('a', $(this).parents('ul:first')).removeClass('subdrop');
      $(this).next('ul').show(350);
      $(this).addClass('subdrop');
    } else if($(this).hasClass('subdrop')) {
      $(this).removeClass('subdrop');
      $(this).next('ul').hide(350);
    }
  });

  // Link Account
  if ($(".link-account").length > 0) {
    $(".link-account .integration-card").on("click", function () {
      $(this).toggleClass("selected");
    });
  }

  // Reference to the file input and file name display elements
  const $fileUpload = $('#fileUpload');
  const $fileNameDisplay = $('#fileName');

  // Display file name when a file is selected
  $fileUpload.on('change', () => {
      if ($fileUpload[0].files.length > 0) {
          $fileNameDisplay.text($fileUpload[0].files[0].name);
      } else {
          $fileNameDisplay.text("No file chosen");
      }
  });

  // Open file input when the upload button is clicked
  $('.upload-btn').on('click', () => {
      $fileUpload.click();
  });
 
  // Tab List
  $("ul.add-tab-list li a").on('click', function(e) {
    $("ul.add-tab-list li a").removeClass("active");
    $(this).addClass("active");
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: ($(target).offset().top - 90)
        }, 500);
  });

  $("ul.active-tab-list li a").on('click', function(e) {
  $("ul.active-tab-list li a").removeClass("active");
  $(this).addClass("active");
      e.preventDefault();
      var target = $(this).attr('href');
      $('html, body').animate({
          scrollTop: ($(target).offset().top - 90)
      }, 500);
  });    

  // Home Vertical Slider
  if ($('.home-vertical-slider').length > 0) {
    $('.slider-fors').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      asNavFor: '.slider-nav'
    });
    $('.slider-nav').slick({
      slidesToShow: 3,
      slidesToScroll: 1,
      vertical: true,
      asNavFor: '.slider-fors',
      dots: false,
      arrows: false,
      focusOnSelect: true,
      verticalSwiping: true,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 3,
          }
        },
        {
          breakpoint: 768,
        },
        {
          breakpoint: 580,
          settings: {
            slidesToShow: 2,
          }
        },
        {
          breakpoint: 0,
          settings: {
            vertical: false,
            slidesToShow: 1,
          }
        }
      ]
    });
  }

  // Travelers Slider
  if ($(".travelers-slider").length > 0) {
    $(".travelers-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
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
        1300: {
          items: 4,
        },
        1400: {
          items: 5,
        },
      },
    });
  }
  
  // Client Slider
  if ($(".client-slider-two").length > 0) {
    $(".client-slider-two").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: false,
      autoplay: true,
      smartSpeed: 2000,
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
          items: 6,
        },
      },
    });
  }

  // Offer Slider
  if ($(".offer-slider").length > 0) {
    $(".offer-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: true,
      dots: false,
      navContainer: '.slide-nav',
      autoplay: false,
      smartSpeed: 2000,
      navText: [
        "<i class='fa-solid fa-chevron-left'></i>",
        "<i class='fa-solid fa-chevron-right'></i>",
      ],
      responsive: {
        0: {
          items: 1,
        },
        576: {
          items: 1,
        },
        1400: {
          items: 1,
        },
      },
    });
  }  

  // Location Slider
  if ($(".location-slider").length > 0) {
    $(".location-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: true,
      autoplay: false,
      smartSpeed: 2000,
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
          items: 5,
        },
      },
    });
  }

  // Testimonial Slider
  if ($(".testimonial-slider-three").length > 0) {
    $(".testimonial-slider-three").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: true,
      autoplay: false,
      smartSpeed: 2000,
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
          items: 3.5,
        },
      },
    });
  }

  // Add Class to Menu
  if ($(".megamenu").length > 0) {
    const megamenuItem = document.querySelector('.megamenu');
    const megamenuContent = document.querySelector('.main-nav');
    megamenuItem.addEventListener('mouseenter', () => {
      megamenuContent.classList.add('active');
    });
    megamenuItem.addEventListener('mouseleave', () => {
      megamenuContent.classList.remove('active');
    });
  }

  // Horizontal Slide
  document.addEventListener("DOMContentLoaded", function () {
    const scrollers = document.querySelectorAll(".horizontal-slide");
    scrollers.forEach((scroller) => {
      scroller.setAttribute("data-animated", true);
      const scrollerInner = scroller.querySelector(".slide-list");
      const scrollerContent = Array.from(scrollerInner.children);
      scrollerContent.forEach((item) => {
        const duplicatedItem = item.cloneNode(true);
        duplicatedItem.setAttribute("aria-hidden", true);
        scrollerInner.appendChild(duplicatedItem);
      });
    });
  });
  
  // Popular Hotel
  if ($(".popular-hotel-slider").length > 0) {
    $(".popular-hotel-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
      responsive: {
        0: {
          items: 1,
        },
        576: {
          items: 1,
        },
        991: {
          items: 3,
        },
        1300: {
          items: 3,
        },
        1400: {
          items: 4,
        },
      },
    });
  }

	var $slimScrolls = $('.slimscroll');

	// Sidebar Slimscroll

	if ($slimScrolls.length > 0) {
		$slimScrolls.slimScroll({
			height: 'auto',
			width: '100%',
			position: 'right',
			size: '7px',
			color: '#ccc',
			wheelStep: 10,
			touchScrollStep: 100
		});
		var wHeight = $(window).height();
		$slimScrolls.height(wHeight);
		$('.left-sidebar .slimScrollDiv, .sidebar-menu .slimScrollDiv, .sidebar-menu .slimScrollDiv').height(wHeight);
		$('.right-sidebar .slimScrollDiv').height(wHeight - 30);
		$('.chat .slimScrollDiv').height(wHeight - 70);
		$('.chat.settings-main .slimScrollDiv').height(wHeight);
		$('.right-sidebar.video-right-sidebar .slimScrollDiv').height(wHeight - 90);
		$(window).resize(function () {
			var rHeight = $(window).height();
			$slimScrolls.height(rHeight);
			$('.left-sidebar .slimScrollDiv, .sidebar-menu .slimScrollDiv, .sidebar-menu .slimScrollDiv').height(rHeight);
			$('.right-sidebar .slimScrollDiv').height(wHeight - 30);
			$('.chat .slimScrollDiv').height(rHeight - 70);
			$('.chat.settings-main .slimScrollDiv').height(wHeight);
			$('.right-sidebar.video-right-sidebar .slimScrollDiv').height(wHeight - 90);
		});
	}

	// layout content remove
	$('.chat-close').on('click', function () {
		$(".chat").removeClass('show');
	});

	$("#search-contact").on("keyup", function () {
		var value = $(this).val().toLowerCase();
		$("#chatsidebar ul li").filter(function () {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});

	$(".left_sides").on('click', function () {
		if ($(window).width() <= 991) {
			$('.sidebar-group').removeClass('hide-left-sidebar');
			$('.sidebar-menu').removeClass('d-none');
		}
	});
	$(".chat-user-list").on('click', function () {
		if ($(window).width() <= 767) {
			$('.left-sidebar').addClass('hide-left-sidebar');
			$('.sidebar-menu').addClass('d-none');
		}
	});

	jQuery(window).on('load resize', function () {

		// Variable Declarations

		var right_sidebar = $('.right-sidebar').width();
		var left_sidebar = $('.left-sidebar').width();
		var chat_bar = $('.chat').width();
		var win_width = $(window).width();

		$(".user-list-item:not(body.status-page .user-list-item, body.voice-call-page .user-list-item)").on('click', function () {
			if ($(window).width() < 992) {
				$('.left-sidebar').addClass('hide-left-sidebar');
				$('.chat').addClass('show-chatbar');
			}

		});

		$(".group-left-setting").on('click', function () {
			$('.right_side_group').addClass('show-right-sidebar');
			$('.right_side_group').removeClass('hide-right-sidebar');
			$('.right-side-contact').addClass('hide-right-sidebar');
			$('.chat-options ').addClass('chat-small');
		});
		$(".remove-group-message").on('click', function () {
			$('.right_side_group').addClass('hide-right-sidebar');
			$('.right_side_group').removeClass('show-right-sidebar');
			$('.chat-options ').removeClass('chat-small');
			if ($(window).width() > 991 && $(window).width() < 1201) {
				$(".chat").css('margin-left', 0);
			}
			if ($(window).width() < 992) {
				$('.chat').removeClass('hide-chatbar');
			}
		});

		$(".star-message-left").on('click', function () {
			$('.right_side_star').addClass('show-right-sidebar');
			$('.right_side_star').removeClass('hide-right-sidebar');
			$('.right-side-contact').addClass('hide-right-sidebar');
			$('.right-side-contact').removeClass('show-right-sidebar');
			$('.chat-options ').addClass('chat-small');
		});
		$(".remove-star-message").on('click', function () {
			$('.right_side_star').addClass('hide-right-sidebar');
			$('.right_side_star').removeClass('show-right-sidebar');
			$('.chat-options ').removeClass('chat-small');
			if ($(window).width() > 991 && $(window).width() < 1201) {
				$(".chat").css('margin-left', 0);
			}
			if ($(window).width() < 992) {
				$('.chat').removeClass('hide-chatbar');
			}
		});

		$(".message-info-left").on('click', function () {
			$('.right_sidebar_info').addClass('show-right-sidebar');
			$('.right_sidebar_info').removeClass('hide-right-sidebar');
			$('.right-side-contact').addClass('hide-right-sidebar');
			$('.right-side-contact').removeClass('show-right-sidebar');
			$('.right_side_star').addClass('hide-right-sidebar');
			$('.right_side_star').removeClass('show-right-sidebar');
			$('.right_side_group').addClass('hide-right-sidebar');
			$('.right_side_group').removeClass('show-right-sidebar');
			$('.chat-options ').addClass('chat-small');
			if ($(window).width() > 991 && $(window).width() < 1201) {
				$(".chat:not(.right_sidebar_info .chat)").css('margin-left', - chat_bar);
			}
			if ($(window).width() < 992) {
				$('.chat:not(.right_sidebar_info .chat)').addClass('hide-chatbar');
			}
		});
		$(".remove-message-info").on('click', function () {
			$('.right_sidebar_info').addClass('hide-right-sidebar');
			$('.right_sidebar_info').removeClass('show-right-sidebar');
			$('.chat-options ').removeClass('chat-small');
			if ($(window).width() > 991 && $(window).width() < 1201) {
				$(".chat").css('margin-left', 0);
			}
			if ($(window).width() < 992) {
				$('.chat').removeClass('hide-chatbar');
			}
		});

		$(".dream_profile_menu").on('click', function () {
			$('.right-side-contact').addClass('show-right-sidebar');
			$('.right-side-contact').removeClass('hide-right-sidebar');
			$('.right_sidebar_info').addClass('hide-right-sidebar');
			$('.right_sidebar_info').removeClass('show-right-sidebar');
			$('.right_side_star').addClass('hide-right-sidebar');
			$('.right_side_star').removeClass('show-right-sidebar');
			$('.video-right-sidebar').addClass('show-right-sidebar');
			$('.video-right-sidebar').removeClass('hide-right-sidebar');
			$('.chat-options ').addClass('chat-small');
			if ($(window).width() > 991 && $(window).width() < 1201) {
				$(".chat:not(.right-side-contact .chat)").css('margin-left', - chat_bar);
				$(".chat:not(.right_side_star .chat)").css('margin-left', - chat_bar);
				$('.left-sidebar').hide();
				$('.chat').css("margin-left", "0");

			}
			if ($(window).width() < 992) {
				$('.chat:not(.right-side-contact .chat)').addClass('hide-chatbar');
				$('.chat:not(.right_side_star .chat)').addClass('hide-chatbar');
			}
		});

		$(".close_profile").on('click', function () {
			$('.right-side-contact').addClass('hide-right-sidebar');
			$('.right-side-contact').removeClass('show-right-sidebar');
			$('.video-right-sidebar').addClass('hide-right-sidebar');
			$('.video-right-sidebar').removeClass('show-right-sidebar');
			$('.chat-options ').removeClass('chat-small');
			if ($(window).width() > 991 && $(window).width() < 1201) {
				$(".chat").css('margin-left', 0);
			}
			if ($(window).width() < 992) {
				$('.chat').removeClass('hide-chatbar');
			}
		});
		$(".nav-tabs a").on('click', function () {
			$(this).tab('show');
		});
		$(".chat-header .left_side i, .page-header .left_side i").on('click', function () {
			$('.left-sidebar').removeClass('hide-left-sidebar');
			$('.chat').removeClass('show-chatbar');
			$('.sidebar-menu').removeClass('d-none');
		});

	});

	// Emoj Add
	if ($('.emoj-action').length > 0) {
		$(".emoj-action").on('click', function () {
			$('.emoj-group-list').toggle();
		});
	}
	if ($('.emoj-action-foot').length > 0) {
		$(".emoj-action-foot").on('click', function () {
			$('.emoj-group-list-foot').toggle();
		});
	}

	// Rightside Accordian
	$('.accordion-col .accordion-title').on('click', function () {
		$(this).next().slideToggle();
		$(this).toggleClass('active');
	});

	// Tooltip
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	})

	// Datetimepicker Date
	if ($('.datetimepicker').length > 0) {
		$('.datetimepicker').datetimepicker({
			format: 'DD-MM-YYYY',
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		});
	}

	// Chat Search Visible
	$('.chat-search-btn').on('click', function () {
		$('.chat-search').toggleClass('visible-chat');
	});
	$('.close-btn-chat').on('click', function () {
		$('.chat-search').removeClass('visible-chat');
	});
	$(".chat-search .form-control").on("keyup", function () {
		var value = $(this).val().toLowerCase();
		$(".chat .chat-body .messages .chats").filter(function () {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	});

  // header 
	$('.sidebar-menu').on('click', function () {
		$('body').addClass('overflow-hidden');
	});
  $('.offcanvas-overlay').on('click', function () {
		$('body').removeClass('overflow-hidden');
	});
  $('.offcanvas-close').on('click', function () {
		$('body').removeClass('overflow-hidden');
	});
	// Chat Search Visible
	$('.user-chat-search-btn').on('click', function () {
		$('.user-chat-search').addClass('visible-chat');
	});
	$('.user-close-btn-chat').on('click', function () {
		$('.user-chat-search').removeClass('visible-chat');
	});

	// Otp Verfication
	$('.digit-group').find('input').each(function () {
		$(this).attr('maxlength', 1);
		$(this).on('keyup', function (e) {
			var parent = $($(this).parent());
			if (e.keyCode === 8 || e.keyCode === 37) {
				var prev = parent.find('input#' + $(this).data('previous'));
				if (prev.length) {
					$(prev).select();
				}
			} else if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 65 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode === 39) {
				var next = parent.find('input#' + $(this).data('next'));
				if (next.length) {
					$(next).select();
				} else {
					if (parent.data('autosubmit')) {
						parent.submit();
					}
				}
			}
		});
	});

	$('.digit-group input').on('keyup', function () {
		var self = $(this);
		if (self.val() != '') {
			self.addClass('active');
		} else {
			self.removeClass('active');
		}
	});

	// Mute Audio
	if ($('.mute-bt').length > 0) {
		$(".mute-bt").on('click', function () {
			if ($(this).hasClass("stop")) {
				$(this).removeClass("stop");
				$(".mute-bt i").removeClass("ti-microphone-off");
				$(".mute-bt i").addClass("ti-microphone");
				$(this).attr("data-bs-original-title", "Mute Audio");
				$(".join-video.user-active .more-icon").removeClass("mic-view");
				$(".action-info.vid-view li .mute-mic i").removeClass("ti-microphone-off");
				$(".action-info.vid-view li .mute-mic i").addClass("ti-microphone");
			}
			else {
				$(this).addClass("stop");
				$(".mute-bt i").removeClass("ti-microphone");
				$(".mute-bt i").addClass("ti-microphone-off");
				$(this).attr("data-bs-original-title", "Unmute Audio");
				$(".join-video.user-active .more-icon").addClass("mic-view");
				$(".add-list .user-active .action-info").addClass("vid-view");
				$(".action-info.vid-view li .mute-mic i").removeClass("ti-microphone");
				$(".action-info.vid-view li .mute-mic i").addClass("ti-microphone-off");
			}
		});
	}

	// Mute Video
	if ($('.mute-video').length > 0) {
		$(".mute-video").on('click', function () {
			if ($(this).hasClass("stop")) {
				$(this).removeClass("stop");
				$(".mute-video i").removeClass("ti-video-off");
				$(".mute-video i").addClass("ti-video");
				$(".mini-video-view").removeClass("no-video");
				$(".join-call .join-video").removeClass("video-hide");
				$(".video-avatar").removeClass("active");
				$(this).attr("data-bs-original-title", "Stop Camera");
				$(".meeting .join-video.user-active").removeClass("video-hide");
				$(".join-video.user-active .more-icon").removeClass("vid-view");
				$(".action-info.vid-view li .mute-vid i").removeClass("feather-video-off");
				$(".action-info.vid-view li .mute-vid i").addClass("feather-video");
			}
			else {
				$(this).addClass("stop");
				$(".mute-video i").removeClass("ti-video");
				$(".mute-video i").addClass("ti-video-off");
				$(".mini-video-view").addClass("no-video");
				$(".join-call .join-video").addClass("video-hide");
				$(".video-avatar").addClass("active");
				$(this).attr("data-bs-original-title", "Start Camera");
				$(".meeting .join-video.user-active").addClass("video-hide");
				$(".add-list .user-active .action-info").addClass("vid-view");
				$(".action-info.vid-view li .mute-vid i").removeClass("ti-video");
				$(".action-info.vid-view li .mute-vid i").addClass("ti-video-off");
			}
		});
	}

	if ($('.main-wrapper').length > 0) {
		document.getElementsByClassName("main-wrapper")[0].style.visibility = "visible";
	}

	// Reply Button
	if ($('.reply-button').length > 0) {
		var replyButton = document.querySelectorAll('.reply-button');
		var replyContent = document.querySelector('.reply-content');
		var replyDiv = document.querySelector('.reply-chat');
		document.querySelector('.close-replay').onclick = function () {
			replyDiv.classList.remove("d-flex");
			replyDiv.classList.add("d-none");
		}
		replyButton.forEach(function (button) {
			button.addEventListener('click', handleButtonClick);
		});
		function handleButtonClick(event) {
			replyDiv.classList.add("d-flex");
			replyDiv.classList.remove("d-none");
			replyContent.innerHTML = "Thank you for your support";
		}
	}

  // Categories Slider
  if ($(".categories-slider-six").length > 0) {
    $(".categories-slider-six").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: true,
      autoplay: false,
      smartSpeed: 2000,
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        1300: {
          items: 4,
        },
        1400: {
          items: 5,
        },
      },
    });
  }

  // Providers Slider
  if ($(".providers-slider").length > 0) {
    $(".providers-slider").owlCarousel({
      loop: true,
      margin: 24,
      nav: false,
      dots: true,
      autoplay: false,
      smartSpeed: 2000,
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        1300: {
          items: 3,
        },
        1400: {
          items: 4,
        },
      },
    });
  }

  // Coming Soon
	if ($('.comming-soon-pg').length > 0) {
		// Get html elements
		let day = document.querySelector('.days');
		let hour = document.querySelector('.hours');
		let minute = document.querySelector('.minutes');
		let second = document.querySelector('.seconds');
		function setCountdown() {

			// Set countdown date
			let countdownDate = new Date('Dec 30, 2024 16:00:00').getTime();

			// Update countdown every second
			let updateCount = setInterval(function () {

				// Get today's date and time
				let todayDate = new Date().getTime();

				// Get distance between now and countdown date
				let distance = countdownDate - todayDate;

				let days = Math.floor(distance / (1000 * 60 * 60 * 24));

				let hours = Math.floor(distance % (1000 * 60 * 60 * 24) / (1000 * 60 * 60));

				let minutes = Math.floor(distance % (1000 * 60 * 60) / (1000 * 60));

				let seconds = Math.floor(distance % (1000 * 60) / 1000);

				// Display values in html elements
				day.textContent = days;
				hour.textContent = hours;
				minute.textContent = minutes;
				second.textContent = seconds;

				// if countdown expires
				if (distance < 0) {
					clearInterval(updateCount);
					document.querySelector(".comming-soon-pg").innerHTML = '<h1>EXPIRED</h1>'
				}
			}, 1000)
		}
		setCountdown()
	}

  if($('.read-more').length > 0) {
		$(".more-text").hide();
		$(".more-link").on("click", function() {
			$(this).addClass("less");
	 		$(this).text($(this).text() === "Show Less" ? "Show More" : "Show Less");
	 		$(".more-text").slideToggle(900);
		});	  	
	}

  //Add Additional Service

  $(".add-service").on('click', function () {
			
    var addservice ='<div class="row service-info">'+
    '<div class="col-md-6">'+
    '<div class="mb-3">'+
    '<label class="form-label">Name of the Service</label>'+
    '<input type="text" class="form-control">'+
    '</div>'+
    '</div>'+
    '<div class="col-md-6">'+
      '<div class="mb-3">'+
        '<label class="form-label">Price</label>'+
        '<div class="d-flex align-items-center">' +
        '<input type="text" class="form-control">'+
        '<a class="text-danger trash-icon d-flex align-items-center justify-content-center ms-3"><i class="isax isax-trash"></i></a>'
        '</div>'
      '</div>'+
      '</div>'+
    '</div>';
  
    $(".add-service-info").append(addservice);

    $('.select').select2({
      minimumResultsForSearch: -1,
      width: '100%'
    });

    return false;		
  
  });
  
  $(".add-service-info").on('click','.trash-icon', function () {
		$(this).closest('.service-info').remove();
		return false;
  });

  // Loader	
  $('#loader-wrapper').show();
  setTimeout(function () {
      $("#loader-wrapper").hide();
  }, 300);

  // Initially hide the chat window on screens smaller than 991px
  if ($(window).width() <= 991) {
    $('.chat-window-long').hide();
  }

  // Toggle chat window visibility on .chat-member click
  $('.chat-member').on('click', function () {
      if ($(window).width() <= 991) {
        $('.chat-window-long').toggle();
        $('.chat-cont-left').toggle();
        if ($('.chat-window-long').is(':visible')) {
          $('.chat-member').not(this).hide();
        } else {
          $('.chat-member').show();
        }
      }
  });
  $('#back_user_list').on('click', function () {
    if ($(window).width() < 991) {
      $('.chat-window-long').toggle();
      $('.chat-cont-left').toggle();
      if ($('.chat-member ').is(':visible')) {
        $('.chat-window-long').not(this).hide();
      } else {
        $('.chat-member').show();
      }
    }
  });

  // Custom Country Code Selector
  if ($('#phone').length > 0) {
    var input = document.querySelector("#phone");
    window.intlTelInput(input, {
      utilsScript: "assets/plugins/intltelinput/js/utils.js",
    });
  }

  if ($('#phone1').length > 0) {
    var input = document.querySelector("#phone1");
    window.intlTelInput(input, {
      utilsScript: "assets/plugins/intltelinput/js/utils.js",
    });
  }  

  // Scroll Active 
  if ($('.add-tab-list').length > 0) {
    const sections = $(".card");
    const sidebarLinks = $(".add-tab-list a");
    $(window).on("scroll", function () {
      let scrollPosition = $(this).scrollTop();
      sections.each(function () {
          const sectionTop = $(this).offset().top - 60; // Adjust for header or offset
          const sectionBottom = sectionTop + $(this).outerHeight();
          if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
              const currentId = $(this).attr("id");
              sidebarLinks.removeClass("active");
              $(`.add-tab-list a[href="#${currentId}"]`).addClass("active");
          }
      });
    });
    // Smooth scroll animation on link click
    sidebarLinks.on("click", function (e) {
        e.preventDefault();
        const targetId = $(this).attr("href");
        const targetOffset = $(targetId).offset().top - 50; 
        $("html, body").animate(
            { scrollTop: targetOffset },
            500, 
            "swing", 
            function () {
                // Add active class after scroll animation completes
                sidebarLinks.removeClass("active");
                $(`.add-tab-list a[href="${targetId}"]`).addClass("active");
            }
        );
    });
  }

  // Payment
  $(".payment-form .form-check-input").on("change", function (e) {
    var radioval = $(this).val();
    if (radioval === "credit-card") {
      $(".credit-card-details").show();
      $(".paypal-details").hide();
      $(".stripe-details").hide();
    } else if (radioval === "paypal") {
      $(".credit-card-details").hide();
      $(".paypal-details").show();
      $(".stripe-details").hide();
    } else {
      $(".credit-card-details").hide();
      $(".paypal-details").hide();
      $(".stripe-details").show();
    }
  });

  // Room Slider
  if ($(".room-slider").length > 0) {
    $(".room-slider").owlCarousel({
      loop: true,
      margin: 0,
      nav: true,
      dots: false,
      autoplay: false,
      smartSpeed: 2000,
      navText: [
        "<i class='fa-solid fa-chevron-left'></i>",
        "<i class='fa-solid fa-chevron-right'></i>",
      ],
      responsive: {
        0: {
          items: 1,
        },
      },
    });
  }

  // Add Comment
	$('.add-reply').on('click', function () {
		$('.review-reply').toggleClass('show');
	});

  document.addEventListener("DOMContentLoaded", function () {
    // Initialize .snow-editor
    document.querySelectorAll(".snow-editor").forEach((el) => {
      new Quill(el, {
        theme: "snow",
        modules: {
          toolbar: [
            ["bold", "italic", "underline"],
            [{ header: [null, 1, 2, 3, 4, 5, 6] }],
            [{ list: "ordered" }, { list: "bullet" }],
            ["link", "image", "video"]
          ]
        }
      });
    });
  });


})(jQuery);

