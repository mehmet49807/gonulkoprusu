const toggleButton = document.querySelector(".menu-toggle");
const navigation = document.querySelector(".nav-links");
const year = document.querySelector("#year");

if (year) {
  year.textContent = String(new Date().getFullYear());
}

if (toggleButton && navigation) {
  toggleButton.addEventListener("click", () => {
    const isOpen = navigation.dataset.open === "true";
    navigation.dataset.open = String(!isOpen);
    toggleButton.setAttribute("aria-expanded", String(!isOpen));
  });

  navigation.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      navigation.dataset.open = "false";
      toggleButton.setAttribute("aria-expanded", "false");
    });
  });
}
