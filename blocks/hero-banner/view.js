/**
 * Hero Banner Block Frontend JavaScript
 *
 * @package Main
 * @since 1.0.0
 */

(function() {
  'use strict';

  const initBannerSearchSuggestions = () => {
    const input = document.getElementById("bannerSearch");

    if (!input) return;

    const suggestionButtons = Array.from(
      document.querySelectorAll(".search-suggestions-item")
    );

    if (!suggestionButtons.length) return;

    suggestionButtons.forEach((button) => {
      button.addEventListener("click", () => {
        const value = button.dataset.value || button.textContent.trim();
        input.value = value;
        input.focus();

        suggestionButtons.forEach((btn) => {
          btn.classList.remove("active");
          btn.setAttribute("aria-pressed", "false");
        });

        button.classList.add("active");
        button.setAttribute("aria-pressed", "true");
      });
    });
  };

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBannerSearchSuggestions);
  } else {
    initBannerSearchSuggestions();
  }

})();

