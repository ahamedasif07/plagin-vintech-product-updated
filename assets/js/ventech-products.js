/* Ventech Products — Frontend JS */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {

    // ── Nav dropdown click toggle (mobile support) ──────────
    var dropdowns = document.querySelectorAll('.ventech-has-dropdown');

    dropdowns.forEach(function (item) {
      var link = item.querySelector('.ventech-nav-products-link');
      if (!link) return;

      link.addEventListener('click', function (e) {
        var dropdown = item.querySelector('.ventech-cat-dropdown');
        if (!dropdown) return;

        // On mobile: toggle instead of hover
        if (window.innerWidth <= 768) {
          e.preventDefault();
          item.classList.toggle('active');
        }
      });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.ventech-has-dropdown')) {
        dropdowns.forEach(function (item) {
          item.classList.remove('active');
        });
      }
    });

    // ── Smooth card hover effects ───────────────────────────
    var cards = document.querySelectorAll('.ventech-product-card');
    cards.forEach(function (card) {
      card.addEventListener('mouseenter', function () {
        this.style.willChange = 'transform';
      });
      card.addEventListener('mouseleave', function () {
        this.style.willChange = 'auto';
      });
    });

  });
})();
