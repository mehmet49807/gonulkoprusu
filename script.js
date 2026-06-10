const navToggle = document.querySelector("[data-nav-toggle]");
const nav = document.querySelector("[data-nav]");

function setMenuState(isOpen) {
  document.body.classList.toggle("nav-open", isOpen);
  navToggle.setAttribute("aria-expanded", String(isOpen));
  navToggle.setAttribute("aria-label", isOpen ? "Menüyü kapat" : "Menüyü aç");
}

navToggle.addEventListener("click", () => {
  const isOpen = navToggle.getAttribute("aria-expanded") === "true";
  setMenuState(!isOpen);
});

nav.addEventListener("click", (event) => {
  if (!(event.target instanceof HTMLAnchorElement)) {
    return;
  }

  const isOpen = document.body.classList.contains("nav-open");

  if (!isOpen) {
    return;
  }

  const href = event.target.getAttribute("href");
  const destination = href?.startsWith("#")
    ? document.getElementById(href.slice(1))
    : null;

  if (destination) {
    event.preventDefault();
    setMenuState(false);
    window.setTimeout(() => {
      destination.scrollIntoView({ behavior: "smooth" });
    }, 180);
  } else {
    setMenuState(false);
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    setMenuState(false);
  }
});

document.addEventListener("click", (event) => {
  const isOpen = navToggle.getAttribute("aria-expanded") === "true";
  const clickedMenu = nav.contains(event.target);
  const clickedToggle = navToggle.contains(event.target);

  if (isOpen && !clickedMenu && !clickedToggle) {
    setMenuState(false);
  }
});
