const menus = document.querySelector("nav ul");
const header = document.querySelector("header");
const menuBtn = document.querySelector(".menu-btn");
const closeBtn = document.querySelector(".close-btn"); // fixed selector

menuBtn.addEventListener("click", () => {
  menus.classList.add("display");
});

closeBtn.addEventListener("click", () => {
  menus.classList.remove("display"); // this is the correct class to remove
});

//scroll sticky navbar
window.addEventListener("scroll", () => {
  if (document.documentElement.scrollTop > 20) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
});

// static numbers
const countersEl = document.querySelectorAll(".numbers");

countersEl.forEach((counters) => {
  counters.textContent = 0;

  function incrementCounters() {
    let currentNum = +counters.textContent;
    const dataCeil = +counters.getAttribute("data-ceil"); // Convert to number
    const increment = dataCeil / 25;

    currentNum = Math.ceil(currentNum + increment);

    if (currentNum < dataCeil) {
      counters.textContent = currentNum;
      setTimeout(incrementCounters, 70);
    } else {
      counters.textContent = dataCeil;
    }
  }

  incrementCounters();
});
// ========== Preloader Script ==========
window.addEventListener("load", () => {
  const preloader = document.getElementById("preloader");
  const pageContent = document.getElementById("page-content");

  preloader.classList.add("fade-out");

  setTimeout(() => {
    preloader.style.display = "none";
    pageContent.classList.add("show");
  }, 1200); // match transition time
});
