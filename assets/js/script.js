// script.js - Updated with corrected URLs

// ========== Navigation Menu ==========
const menus = document.querySelector("nav ul");
const header = document.querySelector("header");
const menuBtn = document.querySelector(".menu-btn");
const closeBtn = document.querySelector(".close-btn");

menuBtn.addEventListener("click", () => {
  menus.classList.add("display");
});

closeBtn.addEventListener("click", () => {
  menus.classList.remove("display");
});

// ========== Scroll Sticky Navbar ==========
window.addEventListener("scroll", () => {
  if (document.documentElement.scrollTop > 20) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
});

// ========== Preloader Script ==========
window.addEventListener("load", () => {
  const preloader = document.getElementById("preloader");

  // Add fade-out class to preloader
  preloader.classList.add("fade-out");

  // After animation completes, hide preloader and show content
  setTimeout(() => {
    preloader.style.display = "none";
    document.body.classList.add("loaded");
  }, 1200);
});

// ========== Static Numbers Counter ==========
const countersEl = document.querySelectorAll(".numbers");

countersEl.forEach((counter) => {
  counter.textContent = "0";

  const updateCounter = () => {
    const target = +counter.getAttribute("data-ceil");
    const current = +counter.textContent;

    if (current < target) {
      const increment = Math.ceil(target / 50);
      counter.textContent = Math.min(current + increment, target);
      setTimeout(updateCounter, 50);
    }
  };

  // Start counter when element is in viewport
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        updateCounter();
        observer.unobserve(entry.target);
      }
    });
  });

  observer.observe(counter);
});

// ========== Travel Search Functionality ==========
document.addEventListener("DOMContentLoaded", function () {
  const searchForm = document.getElementById("travel-search-form");

  if (searchForm) {
    searchForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Get form values
      const destination = document.getElementById("destination").value.trim();
      const tripType = document.getElementById("trip-type").value;
      const duration = document.getElementById("duration").value;
      const travelers = document.getElementById("travelers").value;

      // Check if destination is filled
      if (!destination) {
        alert("Please enter a destination");
        return;
      }

      // Show loading/processing message
      const searchBtn = document.getElementById("search-btn");
      const originalText = searchBtn.innerHTML;
      searchBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin"></i> Processing...';
      searchBtn.disabled = true;

      // Redirect based on trip type
      setTimeout(() => {
        if (tripType === "tour") {
          window.location.href = "excursions.html";
        } else if (tripType === "flight") {
          bookFlight(destination);
        } else if (tripType === "hotel") {
          bookHotel(destination);
        } else if (tripType === "package") {
          bookPackage(destination, duration, travelers);
        }

        // Reset button after a short delay
        setTimeout(() => {
          searchBtn.innerHTML = originalText;
          searchBtn.disabled = false;
        }, 1000);
      }, 500);
    });
  }
});

// ========== Corrected Booking Functions ==========
function bookFlight(destination) {
  const cityName = getCityName(destination);

  // CORRECTED: Use proper Skyscanner flight search URL
  const url = `https://www.skyscanner.com/transport/flights/mor/${getAirportCode(cityName)}/`;

  console.log("Flight booking URL:", url);
  window.open(url, "_blank", "noopener,noreferrer");
}

function bookHotel(destination) {
  const cityName = getCityName(destination);

  // Using Booking.com with Morocco filter
  const url = `https://www.booking.com/searchresults.html?ss=${encodeURIComponent(cityName)}+Morocco&dest_type=city`;

  console.log("Hotel booking URL:", url);
  window.open(url, "_blank", "noopener,noreferrer");
}

function bookTour(tourType) {
  // Redirect to your own tours page
  if (tourType === "Sahara") {
    window.location.href = "excursions.html#desert";
  } else {
    window.location.href = "excursions.html";
  }
}

function bookPackage(destination, duration, travelers) {
  const cityName = getCityName(destination);

  // Use Expedia with Morocco packages
  const url = `https://www.expedia.com/Destinations-In-Morocco.d201.Flight-Package-Deals`;

  console.log("Package booking URL:", url);
  window.open(url, "_blank", "noopener,noreferrer");
}

// Helper function to extract city name from destination input
function getCityName(destination) {
  // Remove "Morocco" or any country suffix
  const city = destination.replace(/,.*/g, "").trim();

  // Map common variations
  const cityMap = {
    marrakech: "Marrakech",
    casablanca: "Casablanca",
    agadir: "Agadir",
    tangier: "Tangier",
    rabat: "Rabat",
    fez: "Fez",
    chefchaouen: "Chefchaouen",
    essaouira: "Essaouira",
    sahara: "Sahara Desert",
    "sahara desert": "Sahara Desert",
    "atlas mountains": "Atlas Mountains",
  };

  const lowerCity = city.toLowerCase();
  return cityMap[lowerCity] || city;
}

// Get correct airport codes for Skyscanner
function getAirportCode(cityName) {
  const airportCodes = {
    Marrakech: "rak",
    Casablanca: "cmn",
    Agadir: "aga",
    Tangier: "tng",
    Rabat: "rba",
    Fez: "fez",
    Essaouira: "esu",
    Chefchaouen: "ttu", // Tetouan is closest
    "Sahara Desert": "rak", // Default to Marrakech for Sahara
    "Atlas Mountains": "rak", // Default to Marrakech
  };

  return airportCodes[cityName] || "rak"; // Default to Marrakech
}

// ========== Quick Links Functions (for the buttons) ==========
function setupQuickLinks() {
  // Day Tours button already has href="excursions.html"

  // Setup flight quick link
  const flightBtn = document.querySelector(
    '.quick-link-btn[onclick*="bookFlight"]',
  );
  if (flightBtn) {
    flightBtn.addEventListener("click", function (e) {
      e.preventDefault();
      bookFlight("Marrakech");
    });
  }

  // Setup hotel quick link
  const hotelBtn = document.querySelector(
    '.quick-link-btn[onclick*="bookHotel"]',
  );
  if (hotelBtn) {
    hotelBtn.addEventListener("click", function (e) {
      e.preventDefault();
      bookHotel("Marrakech");
    });
  }

  // Setup desert tour quick link
  const desertBtn = document.querySelector(
    '.quick-link-btn[onclick*="bookTour"]',
  );
  if (desertBtn) {
    desertBtn.addEventListener("click", function (e) {
      e.preventDefault();
      bookTour("Sahara");
    });
  }
}

// ========== Back to Top Button ==========
const backToTopBtn = document.getElementById("backToTop");

if (backToTopBtn) {
  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      backToTopBtn.style.display = "block";
    } else {
      backToTopBtn.style.display = "none";
    }
  });

  backToTopBtn.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });
}

// ========== Initialize everything when DOM is loaded ==========
document.addEventListener("DOMContentLoaded", function () {
  console.log("Travelo Morocco - Script initialized");

  // Setup quick links
  setupQuickLinks();

  // Trigger counters when page loads
  const counters = document.querySelectorAll(".numbers");
  if (counters.length > 0) {
    counters.forEach((counter) => {
      const target = +counter.getAttribute("data-ceil");
      const duration = 2000;
      const step = Math.ceil(target / (duration / 50));

      let current = 0;
      const timer = setInterval(() => {
        current += step;
        if (current >= target) {
          counter.textContent = target;
          clearInterval(timer);
        } else {
          counter.textContent = current;
        }
      }, 50);
    });
  }

  // Debug: Log all form elements
  const form = document.getElementById("travel-search-form");
  if (form) {
    console.log("Search form found, elements:", {
      destination: document.getElementById("destination"),
      tripType: document.getElementById("trip-type"),
      duration: document.getElementById("duration"),
      travelers: document.getElementById("travelers"),
    });
  }
});
