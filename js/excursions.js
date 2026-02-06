// Filter functionality for excursions
document.addEventListener("DOMContentLoaded", function () {
  const filterButtons = document.querySelectorAll(".filter-btn");
  const excursionCards = document.querySelectorAll(".excursion-card");
  const loadMoreBtn = document.getElementById("loadMoreBtn");

  // Initially show all cards
  let visibleCards = 6;

  // Show only first 6 cards
  excursionCards.forEach((card, index) => {
    if (index >= visibleCards) {
      card.style.display = "none";
    }
  });

  // Filter functionality
  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Update active button
      filterButtons.forEach((btn) => btn.classList.remove("active"));
      this.classList.add("active");

      const filter = this.getAttribute("data-filter");

      excursionCards.forEach((card) => {
        const category = card.getAttribute("data-category");

        if (filter === "all" || category === filter) {
          card.style.display = "block";
        } else {
          card.style.display = "none";
        }
      });
    });
  });

  // Load more functionality
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", function () {
      visibleCards += 6;

      excursionCards.forEach((card, index) => {
        if (index < visibleCards) {
          card.style.display = "block";
        }
      });

      // Hide button if all cards are visible
      if (visibleCards >= excursionCards.length) {
        loadMoreBtn.style.display = "none";
      }
    });
  }

  // Add active class to current page in nav
  const currentPage = window.location.pathname.split("/").pop();
  const navLinks = document.querySelectorAll("nav ul li a");

  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");
    }
  });

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");
      if (targetId === "#") return;

      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 100,
          behavior: "smooth",
        });
      }
    });
  });
});
