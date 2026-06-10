(function () {
  "use strict";

  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  /* Yıl */
  const yearEl = $("#year");
  if (yearEl) yearEl.textContent = new Date().getFullYear();

  /* Mobil menü */
  const navToggle = $("#navToggle");
  const mobileNav = $("#mobileNav");
  if (navToggle && mobileNav) {
    navToggle.addEventListener("click", () => {
      const open = navToggle.getAttribute("aria-expanded") === "true";
      navToggle.setAttribute("aria-expanded", String(!open));
      mobileNav.hidden = open;
    });
    $$("a", mobileNav).forEach((a) =>
      a.addEventListener("click", () => {
        navToggle.setAttribute("aria-expanded", "false");
        mobileNav.hidden = true;
      })
    );
  }

  /* Bağış tutarı seçimi */
  const amountBtns = $$(".amount");
  const customAmount = $("#customAmount");
  const donateBtnText = $("#donateBtnText");
  let selectedAmount = 500;

  const formatTL = (n) => "₺" + Number(n).toLocaleString("tr-TR");

  const updateDonateBtn = () => {
    if (donateBtnText) {
      donateBtnText.textContent = selectedAmount
        ? `${formatTL(selectedAmount)} Bağış Yap`
        : "Bağış Yap";
    }
  };

  amountBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      amountBtns.forEach((b) => b.classList.remove("is-active"));
      btn.classList.add("is-active");
      selectedAmount = Number(btn.dataset.amount);
      if (customAmount) customAmount.value = "";
      updateDonateBtn();
    });
  });

  if (customAmount) {
    customAmount.addEventListener("input", () => {
      const val = Number(customAmount.value);
      if (val > 0) {
        amountBtns.forEach((b) => b.classList.remove("is-active"));
        selectedAmount = val;
      }
      updateDonateBtn();
    });
  }

  /* Bağış formu gönderimi (demo) */
  const donateForm = $("#donateForm");
  const donateSuccess = $("#donateSuccess");
  if (donateForm) {
    donateForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const name = $("#donorName");
      const email = $("#donorEmail");
      if (!name.value.trim() || !email.checkValidity()) {
        (name.value.trim() ? email : name).focus();
        return;
      }
      if (donateSuccess) {
        donateSuccess.hidden = false;
        donateSuccess.textContent = `Teşekkürler ${name.value.trim().split(" ")[0]}! ${formatTL(
          selectedAmount
        )} bağışınız için onay e-postası gönderildi. 💚`;
      }
      donateForm.reset();
      amountBtns.forEach((b) => b.classList.remove("is-active"));
      const def = donateForm.querySelector('.amount[data-amount="500"]');
      if (def) def.classList.add("is-active");
      selectedAmount = 500;
      updateDonateBtn();
    });
  }

  /* Bülten formu (demo) */
  const newsletterForm = $("#newsletterForm");
  const newsletterSuccess = $("#newsletterSuccess");
  if (newsletterForm) {
    newsletterForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = $("#newsletterEmail");
      if (!email.checkValidity()) {
        email.focus();
        return;
      }
      if (newsletterSuccess) {
        newsletterSuccess.hidden = false;
        newsletterSuccess.textContent = "Abone oldunuz! İyilik hikâyeleri yolda. 🙌";
      }
      newsletterForm.reset();
    });
  }

  /* Sayaç animasyonu */
  const animateCount = (el) => {
    const target = Number(el.dataset.count);
    const numEl = $(".stat-num", el);
    if (!numEl || isNaN(target)) return;
    const duration = 1600;
    const start = performance.now();
    const step = (now) => {
      const p = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      numEl.textContent = Math.floor(eased * target).toLocaleString("tr-TR");
      if (p < 1) requestAnimationFrame(step);
      else numEl.textContent = target.toLocaleString("tr-TR") + "+";
    };
    requestAnimationFrame(step);
  };

  /* IntersectionObserver: sayaç + reveal */
  const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  const statEls = $$(".stat");
  if ("IntersectionObserver" in window && !reduceMotion) {
    const statObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            animateCount(entry.target);
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.4 }
    );
    statEls.forEach((el) => statObserver.observe(el));
  } else {
    statEls.forEach((el) => {
      const numEl = $(".stat-num", el);
      if (numEl) numEl.textContent = Number(el.dataset.count).toLocaleString("tr-TR") + "+";
    });
  }

  /* Reveal on scroll */
  const revealTargets = $$(".section, .hero-card, .card, .quote, .stat");
  revealTargets.forEach((el) => el.classList.add("reveal"));
  if ("IntersectionObserver" in window && !reduceMotion) {
    const revealObserver = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    revealTargets.forEach((el) => revealObserver.observe(el));
  } else {
    revealTargets.forEach((el) => el.classList.add("is-visible"));
  }

  updateDonateBtn();
})();
