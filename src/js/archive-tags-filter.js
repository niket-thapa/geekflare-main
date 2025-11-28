/**
 * Archive Tags Filter Scroll Functionality
 *
 * Handles the scroll button for the tags filter on archive pages.
 * Shows/hides the scroll button based on overflow and viewport width.
 *
 * @package Main
 * @since 1.0.0
 */

/**
 * Initialize tags filter scroll functionality
 *
 * Sets up the scroll button behavior for the tags filter container.
 * Only shows the button on desktop when content overflows.
 */
const initTagsFilterScroll = () => {
  const tagsFilter = document.querySelector(".tags-filter");
  const scrollButton = document.getElementById("tagsFilterScrollBtn");

  if (!tagsFilter || !scrollButton) {
    return;
  }

  const MOBILE_BREAKPOINT = 768;
  const GRADIENT_WIDTH = 72;

  // Add padding-right to account for gradient overlay so items can scroll past it
  tagsFilter.style.paddingRight = `${GRADIENT_WIDTH}px`;

  /**
   * Check if content overflows and update button visibility
   */
  const checkOverflow = () => {
    const hasOverflow = tagsFilter.scrollWidth > tagsFilter.clientWidth;
    const isMobile = window.innerWidth < MOBILE_BREAKPOINT;

    if (hasOverflow && !isMobile) {
      scrollButton.style.opacity = "1";
      scrollButton.style.pointerEvents = "auto";
    } else {
      scrollButton.style.opacity = "0";
      scrollButton.style.pointerEvents = "none";
    }
  };

  /**
   * Scroll to the next section of tags
   */
  const scrollToNext = () => {
    const scrollAmount = tagsFilter.clientWidth * 0.8; // Scroll 80% of visible width
    const currentScroll = tagsFilter.scrollLeft;
    const maxScroll = tagsFilter.scrollWidth - tagsFilter.clientWidth;

    // Calculate next scroll position
    let nextScroll = currentScroll + scrollAmount;

    // If we're near the end, scroll to the end (scroll padding will handle gradient spacing)
    if (nextScroll >= maxScroll - 10) {
      nextScroll = maxScroll;
    }

    tagsFilter.scrollTo({
      left: nextScroll,
      behavior: "smooth",
    });

    // Update button visibility after scroll
    setTimeout(() => {
      checkOverflow();
    }, 300);
  };

  // Initial check
  checkOverflow();

  // Check on resize with debounce
  let resizeTimeout;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(() => {
      checkOverflow();
    }, 150);
  });

  // Check on scroll (in case content changes)
  tagsFilter.addEventListener("scroll", () => {
    checkOverflow();
  });

  // Button click handler
  scrollButton.addEventListener("click", scrollToNext);
};

// Initialize when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initTagsFilterScroll);
} else {
  // DOM is already ready
  initTagsFilterScroll();
}
