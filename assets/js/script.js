//AOS animation
const hero_section = document.querySelectorAll(
  '.hero-section [data-aos="fade-up"]'
);
hero_section.forEach((element, index) => {
  element.setAttribute("data-aos-delay", `${index * 100}`);
});

const footer = document.querySelectorAll('.footer [data-aos="fade-up"]');
footer.forEach((element, index) => {
  element.setAttribute("data-aos-delay", `${index * 100}`);
});

//Change background, logo and text color of navigation menu when scroll off the top
window.addEventListener("scroll", function () {
  const header = document.querySelector("header");
  const nav_menu__button = document.querySelectorAll(".nav-menu__button span");
  const site_logo = document.getElementById("site-logo");

  if (window.scrollY > 50) {
    header.classList.add("bg-white");
    header.classList.remove("text-white");
    header.classList.add("shadow-md");

    site_logo.src="../assets/img/real-estate-logo.png";

    nav_menu__button.forEach((span) => {
      span.style.backgroundColor = "black";
    });
  } else {
    header.classList.remove("bg-white");
    header.classList.add("text-white");
    header.classList.remove("shadow-md");

    site_logo.src="../assets/img/real-estate-white-logo.png";

    nav_menu__button.forEach((span) => {
      span.style.backgroundColor = "white";
    });
  }
});

//Back to the top button
const backToTopButton = document.getElementById("backToTop");

window.onscroll = function () {
  if (
    document.body.scrollTop > 100 ||
    document.documentElement.scrollTop > 100
  ) {
    backToTopButton.classList.remove("hidden");
  } else {
    backToTopButton.classList.add("hidden");
  }
};

backToTopButton.onclick = function () {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
};
