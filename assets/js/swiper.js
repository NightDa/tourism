// swiper.js - Swiper Initializations
document.addEventListener("DOMContentLoaded", function () {
  // Check if Swiper is available
  if (typeof Swiper === "undefined") {
    console.error("Swiper is not loaded!");
    return;
  }

  console.log("Initializing Swipers...");

  // ========== Main Hero Slider ==========
  const heroSlider = document.querySelector(".slide-container");
  if (heroSlider) {
    console.log("Found hero slider, initializing...");

    // Clean and simple configuration
    const heroSwiper = new Swiper(".slide-container", {
      // Basic settings
      direction: "horizontal",
      loop: true,

      // Speed and timing
      speed: 800,
      autoplay: {
        delay: 5000,
        disableOnInteraction: true, // Changed to true for better UX
      },

      // Effects
      effect: "fade",
      fadeEffect: {
        crossFade: true,
      },

      // Pagination
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
        dynamicBullets: true,
      },

      // Touch controls
      touchRatio: 1,
      simulateTouch: true,
      grabCursor: true,

      // No navigation arrows
      navigation: false,

      // Responsive breakpoints if needed
      breakpoints: {
        320: {
          speed: 600,
        },
      },

      // Callbacks for debugging
      on: {
        init: function () {
          console.log("Hero Swiper initialized successfully");
        },
        slideChange: function () {
          console.log("Slide changed to: " + this.activeIndex);
        },
      },
    });

    console.log("Hero Swiper instance created:", heroSwiper);
  } else {
    console.error("Hero slider not found! Selector: .slide-container");
  }

  // ========== Top Destinations Slider ==========
  const destSlider = document.querySelector(".destination-swiper");
  if (destSlider) {
    console.log("Found destinations slider, initializing...");

    const destinationSwiper = new Swiper(".destination-swiper", {
      slidesPerView: 3,
      spaceBetween: 30,
      loop: true,
      grabCursor: true,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      speed: 600,
      autoHeight: false,
      pagination: {
        el: ".swiper2-pagination",
        clickable: true,
      },
      breakpoints: {
        320: {
          slidesPerView: 1,
          spaceBetween: 15,
        },
        640: {
          slidesPerView: 2,
          spaceBetween: 20,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 30,
        },
      },
    });
  }

  // ========== Travel Cities Sliders ==========
  const citySliders = document.querySelectorAll(".city-swiper");
  if (citySliders.length > 0) {
    console.log(`Found ${citySliders.length} city slider(s), initializing...`);

    citySliders.forEach((swiperEl) => {
      new Swiper(swiperEl, {
        slidesPerView: 2,
        spaceBetween: 20,
        loop: true,
        grabCursor: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        speed: 600,
        breakpoints: {
          320: {
            slidesPerView: 1,
            spaceBetween: 10,
          },
          768: {
            slidesPerView: 2,
            spaceBetween: 15,
          },
        },
      });
    });
  }

  console.log("All Swiper initializations attempted");
});
