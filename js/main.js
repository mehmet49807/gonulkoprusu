/* Gönül Köprüsü — etkileşim katmanı */
(function () {
  "use strict";

  /* ----- Sticky header gölgesi ----- */
  const header = document.getElementById("header");
  const onScroll = () => header.classList.toggle("is-scrolled", window.scrollY > 8);
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ----- Mobil menü ----- */
  const nav = document.getElementById("nav");
  const navToggle = document.getElementById("navToggle");

  navToggle.addEventListener("click", () => {
    const open = nav.classList.toggle("is-open");
    navToggle.classList.toggle("is-open", open);
    navToggle.setAttribute("aria-expanded", String(open));
  });

  nav.addEventListener("click", (e) => {
    if (e.target.matches(".nav__link")) {
      nav.classList.remove("is-open");
      navToggle.classList.remove("is-open");
      navToggle.setAttribute("aria-expanded", "false");
    }
  });

  /* ----- Scroll reveal ----- */
  const revealEls = document.querySelectorAll(".reveal");
  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          revealObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );
  revealEls.forEach((el) => revealObserver.observe(el));

  /* ----- Sayaç animasyonu ----- */
  const counters = document.querySelectorAll(".stat__num[data-count]");
  const formatter = new Intl.NumberFormat("tr-TR");

  const animateCount = (el) => {
    const target = parseInt(el.dataset.count, 10);
    const duration = 1600;
    const start = performance.now();

    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = formatter.format(Math.round(target * eased));
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  };

  const counterObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          counterObserver.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.4 }
  );
  counters.forEach((el) => counterObserver.observe(el));

  /* ----- Gönüllü formu ----- */
  const form = document.getElementById("volunteerForm");
  const note = document.getElementById("formNote");

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    if (!form.checkValidity()) {
      note.textContent = "Lütfen zorunlu alanları eksiksiz doldurun.";
      note.style.color = "#c2410c";
      form.reportValidity();
      return;
    }

    const name = form.adsoyad.value.trim().split(" ")[0] || "Gönüllümüz";
    note.textContent = `Teşekkürler ${name}! Başvurun alındı, en kısa sürede seninle iletişime geçeceğiz. 💛`;
    note.style.color = "";
    form.reset();
  });
})();
