/**
 * Main Theme JavaScript
 *
 * @package Main
 * @since 1.0.0
 */

// Import custom Flickity initialization (Flickity CSS is enqueued separately via functions.php)
import "./custom-flickity.js";

import PhotoSwipe from "photoswipe";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import "photoswipe/style.css";

const lightbox = new PhotoSwipeLightbox({
  pswpModule: () => import("photoswipe"),
});

(function () {
  "use strict";

  const initHeaderLangMenu = () => {
    const menu = document.querySelector(".header-lang-menu");

    if (!menu) return;

    const toggle = menu.querySelector(".header-lang-menu__toggle");
    const list = menu.querySelector(".header-lang-menu__list");
    const flagImg = toggle?.querySelector(".header-lang-menu__flag-img");
    const isoTarget = toggle?.querySelector(".iso-name");

    if (!toggle || !list || !flagImg || !isoTarget) return;

    const closeMenu = () => {
      menu.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
    };

    const openMenu = () => {
      menu.classList.add("is-open");
      toggle.setAttribute("aria-expanded", "true");
    };

    const toggleMenu = () => {
      if (menu.classList.contains("is-open")) {
        closeMenu();
      } else {
        openMenu();
      }
    };

    toggle.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleMenu();
    });

    list.addEventListener("click", (event) => {
      const option = event.target.closest(".header-lang-menu__option");

      if (!option) return;

      const { iso, label, flag } = option.dataset;

      if (!iso || !flag) return;

      flagImg.src = flag;
      flagImg.alt = `${label || iso} flag`;
      isoTarget.textContent = iso;

      list
        .querySelectorAll(".header-lang-menu__option")
        .forEach((btn) => btn.classList.remove("is-active"));

      option.classList.add("is-active");

      closeMenu();
    });

    document.addEventListener("click", (event) => {
      if (!menu.contains(event.target)) {
        closeMenu();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeMenu();
      }
    });
  };

  const initHeaderSearch = () => {
    const container = document.querySelector(".header-search");

    if (!container) return;

    const toggle = container.querySelector(".header-search__toggle");
    const form = container.querySelector(".header-search__form");
    const input = container.querySelector(".header-search__input");

    if (!toggle || !form || !input) return;

    const closeSearch = () => {
      container.classList.remove("header-search--active");
      toggle.setAttribute("aria-expanded", "false");
      form.setAttribute("aria-hidden", "true");
    };

    const openSearch = () => {
      container.classList.add("header-search--active");
      toggle.setAttribute("aria-expanded", "true");
      form.setAttribute("aria-hidden", "false");
      requestAnimationFrame(() => input.focus());
    };

    const toggleSearch = () => {
      if (container.classList.contains("header-search--active")) {
        closeSearch();
      } else {
        openSearch();
      }
    };

    toggle.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleSearch();
    });

    document.addEventListener("click", (event) => {
      if (!container.contains(event.target)) {
        closeSearch();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeSearch();
      }
    });
  };

  const initMobileSubmenus = () => {
    const mainMenu = document.querySelector(".main-menu");

    if (!mainMenu) return () => {};

    const submenuItems = Array.from(
      mainMenu.querySelectorAll(".menu-item-has-children")
    );

    if (!submenuItems.length) return () => {};

    const desktopMedia = window.matchMedia("(min-width: 1024px)");

    const getSubmenu = (item) => item.querySelector(":scope > .submenu");

    const openItem = (item) => {
      const submenu = getSubmenu(item);
      if (!submenu) return;

      const targetHeight = submenu.scrollHeight;
      submenu.style.height = `${targetHeight}px`;

      const handleOpenEnd = (event) => {
        if (event.propertyName !== "height") return;
        submenu.style.height = "auto";
        submenu.removeEventListener("transitionend", handleOpenEnd);
      };

      submenu.addEventListener("transitionend", handleOpenEnd);
      item.classList.add("submenu-open");
    };

    const closeItem = (item) => {
      if (!item.classList.contains("submenu-open")) return;

      const submenu = getSubmenu(item);
      if (!submenu) return;

      if (submenu.style.height === "" || submenu.style.height === "auto") {
        submenu.style.height = `${submenu.scrollHeight}px`;
      }

      requestAnimationFrame(() => {
        submenu.style.height = "0px";
      });

      const handleCloseEnd = (event) => {
        if (event.propertyName !== "height") return;
        submenu.style.height = "";
        submenu.removeEventListener("transitionend", handleCloseEnd);
      };

      submenu.addEventListener("transitionend", handleCloseEnd);
      item.classList.remove("submenu-open");
    };

    const closeAll = () => {
      submenuItems.forEach((item) => closeItem(item));
    };

    submenuItems.forEach((item) => {
      const trigger = item.querySelector(":scope > a");
      const submenu = getSubmenu(item);

      if (!trigger || !submenu) return;

      trigger.addEventListener("click", (event) => {
        if (desktopMedia.matches) return;

        event.preventDefault();

        const isOpen = item.classList.contains("submenu-open");

        if (isOpen) {
          closeItem(item);
          return;
        }

        submenuItems.forEach((otherItem) => {
          if (otherItem !== item) {
            closeItem(otherItem);
          }
        });

        openItem(item);
      });
    });

    desktopMedia.addEventListener("change", () => {
      if (desktopMedia.matches) {
        closeAll();
      }
    });

    return closeAll;
  };

  const initMobileMenuToggle = (resetSubmenus = () => {}) => {
    const toggleButton = document.querySelector(".mobile_menu_toggle");
    const mainMenu = document.querySelector(".main-menu");
    const body = document.body;

    if (!toggleButton || !mainMenu) return;

    const closeMenu = () => {
      body.classList.remove("main-menu-active");
      resetSubmenus();
    };

    const toggleMenu = () => {
      body.classList.toggle("main-menu-active");
    };

    toggleButton.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleMenu();
    });

    document.addEventListener("click", (event) => {
      if (!body.classList.contains("main-menu-active")) return;

      const target = event.target;

      if (mainMenu.contains(target) || toggleButton.contains(target)) {
        return;
      }

      closeMenu();
    });

    window.addEventListener("resize", () => {
      if (window.innerWidth >= 1024) {
        closeMenu();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeMenu();
      }
    });
  };

  const initTableOfContents = () => {
    const tocContainer = document.querySelector("aside.table-of-contents");
    if (!tocContainer) return;

    const tocHeader = tocContainer.querySelector(".toc-header");
    const tocContent = document.getElementById("toc-content");
    const tocChevron = tocHeader?.querySelector(".toc-chevron");
    const filterHeader = tocContainer.querySelector(".filter-header");
    const filterContent = document.getElementById("filter-content");
    const filterChevron = filterHeader?.querySelector(".filter-chevron");

    const sections = [
      {
        id: "toc",
        header: tocHeader,
        content: tocContent,
        chevron: tocChevron,
        openOnDesktop: true,
        openOnMobile: false,
      },
      {
        id: "filter",
        header: filterHeader,
        content: filterContent,
        chevron: filterChevron,
        openOnDesktop: false,
        openOnMobile: false,
      },
    ].filter((section) => section.header && section.content);

    const setSectionState = (section, shouldOpen, { animate = true } = {}) => {
      if (!section.header || !section.content) return;

      const panel = section.content;
      const header = section.header;
      const chevron = section.chevron;

      header.setAttribute("aria-expanded", String(shouldOpen));

      if (chevron) {
        chevron.classList.toggle("rotate-180", shouldOpen);
      }

      const isOpen = panel.classList.contains("is-open");

      if (shouldOpen === isOpen) {
        if (!shouldOpen) {
          panel.style.height = "0px";
        } else {
          panel.style.height = "auto";
        }
        return;
      }

      const openPanel = () => {
        panel.classList.add("is-open");

        const targetHeight = panel.scrollHeight;

        if (!animate) {
          panel.style.height = "auto";
          return;
        }

        panel.style.height = "0px";

        requestAnimationFrame(() => {
          panel.style.height = `${targetHeight}px`;
        });

        const onTransitionEnd = (event) => {
          if (event.propertyName === "height") {
            panel.style.height = "auto";
            panel.removeEventListener("transitionend", onTransitionEnd);
          }
        };

        panel.addEventListener("transitionend", onTransitionEnd);
      };

      const closePanel = () => {
        if (!animate) {
          panel.classList.remove("is-open");
          panel.style.height = "0px";
          return;
        }

        const currentHeight = panel.scrollHeight;
        panel.style.height = `${currentHeight}px`;

        panel.offsetHeight; // force reflow

        requestAnimationFrame(() => {
          panel.style.height = "0px";
        });

        const onTransitionEnd = (event) => {
          if (event.propertyName === "height") {
            panel.classList.remove("is-open");
            panel.style.height = "0px";
            panel.removeEventListener("transitionend", onTransitionEnd);
          }
        };

        panel.addEventListener("transitionend", onTransitionEnd);
      };

      if (shouldOpen) {
        openPanel();
      } else {
        closePanel();
      }
    };

    const closeOtherSections = (currentSectionId) => {
      sections.forEach((section) => {
        if (section.id !== currentSectionId) {
          setSectionState(section, false);
        }
      });
    };

    const updateSectionStates = () => {
      const isDesktop = window.innerWidth >= 1024;

      sections.forEach((section) => {
        const shouldOpen = isDesktop
          ? section.openOnDesktop
          : section.openOnMobile;

        setSectionState(section, shouldOpen, { animate: false });
      });
    };

    // Initialize states
    updateSectionStates();

    // Update on resize
    window.addEventListener("resize", () => {
      updateSectionStates();
    });

    // Attach toggle handlers
    sections.forEach((section) => {
      section.header.addEventListener("click", () => {
        const isExpanded =
          section.header.getAttribute("aria-expanded") === "true";
        const shouldOpen = !isExpanded;

        if (shouldOpen) {
          closeOtherSections(section.id);
        }

        setSectionState(section, shouldOpen);
      });
    });

    // Smooth scroll for anchor links
    const tocItems = tocContainer.querySelectorAll(".toc-item");
    const tocNestedItems = tocContainer.querySelectorAll(".toc-nested-item");
    const allTocLinks = [...tocItems, ...tocNestedItems];

    // Create a mapping of nested items to their parent items
    const nestedToParentMap = new Map();
    tocNestedItems.forEach((nested) => {
      // Find the previous .toc-item sibling (the parent heading)
      let previousElement =
        nested.closest(".toc-nested")?.previousElementSibling;

      // Walk backwards until we find a .toc-item
      while (previousElement) {
        if (previousElement.classList.contains("toc-item")) {
          nestedToParentMap.set(nested, previousElement);
          break;
        }
        previousElement = previousElement.previousElementSibling;
      }
    });

    // Scroll-based active state management variables
    let isManualScroll = false;
    let scrollTimeout = null;
    let rafId = null;

    // Helper function to update active state
    const updateActiveState = (activeLink) => {
      // Remove active state from all items
      tocItems.forEach((item) => {
        item.classList.remove("toc-item--active");
        item.classList.add("bg-gray-50");
        item.classList.remove("bg-eva-prime-50");

        // Also reset the SVG opacity
        const svg = item.querySelector("svg");
        if (svg) svg.classList.add("opacity-0");
      });

      // Reset all nested items
      tocNestedItems.forEach((item) => {
        item.classList.remove("text-eva-prime-600");
        item.classList.add("text-gray-800");
      });

      if (!activeLink) return;

      // If it's a nested item, also update parent styling
      if (activeLink.classList.contains("toc-nested-item")) {
        activeLink.classList.remove("text-gray-800");
        activeLink.classList.add("text-eva-prime-600");

        // Keep parent highlighted when nested item is active
        const parentItem = nestedToParentMap.get(activeLink);
        if (parentItem) {
          parentItem.classList.add("toc-item--active");
          parentItem.classList.remove("bg-gray-50");
          parentItem.classList.add("bg-eva-prime-50");

          // Show the parent's SVG arrow
          const svg = parentItem.querySelector("svg");
          if (svg) svg.classList.remove("opacity-0");
        }
      } else {
        // If it's a main item
        activeLink.classList.add("toc-item--active");
        activeLink.classList.remove("bg-gray-50");
        activeLink.classList.add("bg-eva-prime-50");

        // Show the SVG arrow
        const svg = activeLink.querySelector("svg");
        if (svg) svg.classList.remove("opacity-0");
      }
    };

    const getViewportThreshold = () => {
      // 30% from bottom means 70% from top of viewport
      return window.innerHeight * 0.5;
    };

    const findActiveHeading = () => {
      const threshold = getViewportThreshold();
      const viewportHeight = window.innerHeight;

      // Get all headings with their positions
      const headings = Array.from(allTocLinks)
        .map((link) => {
          const href = link.getAttribute("href");
          if (!href || !href.startsWith("#")) return null;

          const target = document.querySelector(href);
          if (!target) return null;

          const rect = target.getBoundingClientRect();
          const top = rect.top;
          const bottom = rect.bottom;

          return {
            link,
            element: target,
            top,
            bottom,
            isNested: link.classList.contains("toc-nested-item"),
            distanceFromThreshold: top - threshold,
          };
        })
        .filter((item) => item !== null);

      if (headings.length === 0) return null;

      // Sort by position from top (ascending)
      headings.sort((a, b) => a.top - b.top);

      let activeHeading = null;

      // Special case: If we're at the very bottom of the page, activate the last heading
      const isAtBottom =
        window.innerHeight + window.scrollY >=
        document.documentElement.scrollHeight - 10;

      if (isAtBottom) {
        // Return the last heading (prefer nested if available)
        const lastHeading = headings[headings.length - 1];
        return lastHeading.link;
      }

      // Find the last heading that has crossed the threshold (30% from bottom)
      for (let i = headings.length - 1; i >= 0; i--) {
        const heading = headings[i];

        // Check if heading's top has passed the threshold
        if (heading.top <= threshold) {
          activeHeading = heading.link;
          break;
        }
      }

      // If no heading has passed the threshold yet, use the first visible one
      if (!activeHeading) {
        for (let i = 0; i < headings.length; i++) {
          const heading = headings[i];
          if (heading.bottom > 0 && heading.top < viewportHeight) {
            activeHeading = heading.link;
            break;
          }
        }
      }

      // Final fallback: return the first heading
      return activeHeading || headings[0].link;
    };

    const updateActiveStateOnScroll = () => {
      if (isManualScroll) return;

      const activeLink = findActiveHeading();
      if (activeLink) {
        // Check if this link is already active
        const isNested = activeLink.classList.contains("toc-nested-item");
        let isCurrentlyActive = false;

        if (isNested) {
          // For nested items, check if it has the active class
          isCurrentlyActive =
            activeLink.classList.contains("text-eva-prime-600");
        } else {
          // For main items, check if it has the active class
          isCurrentlyActive = activeLink.classList.contains("toc-item--active");
        }

        if (!isCurrentlyActive) {
          updateActiveState(activeLink);
        }
      }
    };

    const handleScroll = () => {
      if (isManualScroll) return;

      // Cancel previous animation frame
      if (rafId) {
        cancelAnimationFrame(rafId);
      }

      // Use requestAnimationFrame for smoother updates
      rafId = requestAnimationFrame(() => {
        // Debounce with a small delay
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
          updateActiveStateOnScroll();
        }, 50);
      });
    };

    // Click handler for TOC links
    allTocLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        const href = link.getAttribute("href");

        if (href && href.startsWith("#")) {
          const target = document.querySelector(href);

          if (target) {
            e.preventDefault();

            // Calculate offset based on device
            const isMobile = window.innerWidth <= 768; // Adjust breakpoint as needed
            let offset;

            if (isMobile) {
              const mobileSidebar = document.querySelector(
                ".mobile_sidebar_wrap"
              );
              const sidebarHeight = mobileSidebar
                ? mobileSidebar.offsetHeight
                : 0;
              offset = sidebarHeight + 25;
            } else {
              offset = 50;
            }

            // Calculate target position with offset
            const targetPosition =
              target.getBoundingClientRect().top + window.pageYOffset - offset;

            // Smooth scroll to position
            window.scrollTo({
              top: targetPosition,
              behavior: "smooth",
            });

            // Temporarily disable scroll-based updates to prevent conflicts
            isManualScroll = true;
            updateActiveState(link);

            setTimeout(() => {
              isManualScroll = false;
              // Update after manual scroll completes
              setTimeout(() => {
                updateActiveStateOnScroll();
              }, 100);
            }, 1000);
          }
        }
      });
    });

    // Listen to scroll events
    window.addEventListener("scroll", handleScroll, { passive: true });

    // Also listen to resize to recalculate on viewport changes
    window.addEventListener("resize", () => {
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        updateActiveStateOnScroll();
      }, 100);
    });

    // Initial active state on page load
    setTimeout(() => {
      updateActiveStateOnScroll();
    }, 200);
  };

  const initFAQ = () => {
    const faqItems = document.querySelectorAll(".schema-faq-section");

    faqItems.forEach((item, index) => {
      const question = item.querySelector(".schema-faq-question");
      const answer = item.querySelector(".schema-faq-answer");

      if (!question || !answer) return;

      // Set initial state for first item (open) and others (closed)
      const isFirstItem = index === 0;

      if (isFirstItem) {
        item.classList.add("is-open");
        question.setAttribute("aria-expanded", "true");
      } else {
        item.classList.remove("is-open");
        question.setAttribute("aria-expanded", "false");
      }

      // Make question clickable
      question.style.cursor = "pointer";
      question.setAttribute("role", "button");
      question.setAttribute("tabindex", "0");

      const toggleFAQ = () => {
        const isExpanded = question.getAttribute("aria-expanded") === "true";
        const shouldOpen = !isExpanded;

        // Update button state
        question.setAttribute("aria-expanded", String(shouldOpen));

        // Toggle classes
        if (shouldOpen) {
          item.classList.add("is-open");
        } else {
          item.classList.remove("is-open");
        }
      };

      question.addEventListener("click", toggleFAQ);

      // Keyboard accessibility
      question.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          toggleFAQ();
        }
      });
    });
  };

  const initAuthorInfoToggle = () => {
    const authorWraps = document.querySelectorAll(".post-author-wrap");

    if (!authorWraps.length) return;

    const isMobile = () => window.innerWidth < 1024;

    authorWraps.forEach((wrap) => {
      const infoDropdown = wrap.querySelector(".author-info-drop");

      if (!infoDropdown) return;

      // Toggle on click for mobile only
      const handleClick = (event) => {
        if (!isMobile()) return;

        // Don't close if clicking inside the dropdown
        if (infoDropdown.contains(event.target)) {
          return;
        }

        event.stopPropagation();
        wrap.classList.toggle("is-active");
      };

      wrap.addEventListener("click", handleClick);

      // Close dropdown when clicking outside
      document.addEventListener("click", (event) => {
        if (!isMobile()) return;

        if (
          !wrap.contains(event.target) &&
          wrap.classList.contains("is-active")
        ) {
          wrap.classList.remove("is-active");
        }
      });

      // Close on window resize to desktop
      window.addEventListener("resize", () => {
        if (!isMobile() && wrap.classList.contains("is-active")) {
          wrap.classList.remove("is-active");
        }
      });

      // Close on escape key for mobile
      document.addEventListener("keydown", (event) => {
        if (
          event.key === "Escape" &&
          isMobile() &&
          wrap.classList.contains("is-active")
        ) {
          wrap.classList.remove("is-active");
        }
      });
    });
  };

  const initDisclosureToggle = () => {
    const disclosureButtons = document.querySelectorAll(
      ".post-meta-disclosure"
    );

    if (!disclosureButtons.length) return;

    const isMobile = () => window.innerWidth < 1024;

    disclosureButtons.forEach((button) => {
      const container = button.closest(".post-meta-info");
      const metaBar = button.closest(".post-meta-bar");
      const disclosureDropdown = container?.querySelector(".disclosure-drop");

      if (!disclosureDropdown || !container - 1056 || !metaBar) return;

      // Calculate and set dynamic top position relative to .post-meta-bar
      const setDynamicPosition = () => {
        const metaBarRect = metaBar.getBoundingClientRect();
        const buttonRect = button.getBoundingClientRect();

        // Calculate button's position relative to .post-meta-bar
        const buttonTopRelative = buttonRect.top - metaBarRect.top;
        const buttonHeight = button.offsetHeight;

        // Top position = button's top position relative to meta-bar + button height + 20px
        const topPosition = buttonTopRelative + buttonHeight + 20;
        disclosureDropdown.style.top = `${topPosition}px`;
      };

      // Set initial position
      setDynamicPosition();

      // Update position on window resize
      window.addEventListener("resize", setDynamicPosition);

      // Toggle on click for mobile only
      const handleClick = (event) => {
        if (!isMobile()) return;

        // Don't close if clicking inside the dropdown
        if (disclosureDropdown.contains(event.target)) {
          return;
        }

        event.stopPropagation();
        container.classList.toggle("is-active");
      };

      container.addEventListener("click", handleClick);

      // Close dropdown when clicking outside
      document.addEventListener("click", (event) => {
        if (!isMobile()) return;

        if (
          !container.contains(event.target) &&
          container.classList.contains("is-active")
        ) {
          container.classList.remove("is-active");
        }
      });

      // Close on window resize to desktop
      window.addEventListener("resize", () => {
        if (!isMobile() && container.classList.contains("is-active")) {
          container.classList.remove("is-active");
        }
      });

      // Close on escape key for mobile
      document.addEventListener("keydown", (event) => {
        if (
          event.key === "Escape" &&
          isMobile() &&
          container.classList.contains("is-active")
        ) {
          container.classList.remove("is-active");
        }
      });
    });
  };

  // Calculate and set header/footer heights as CSS variables
  const setHeaderFooterHeights = () => {
    const header = document.getElementById("site-header");
    const footer = document.getElementById("site-footer");
    const root = document.documentElement;

    if (header) {
      const headerHeight = header.offsetHeight;
      root.style.setProperty("--header-height", `${headerHeight}px`);
    }

    if (footer) {
      const footerHeight = footer.offsetHeight;
      root.style.setProperty("--footer-height", `${footerHeight}px`);
    }
  };

  // Initialize all header functionality when DOM is ready
  const init = () => {
    const resetSubmenus = initMobileSubmenus();
    initMobileMenuToggle(resetSubmenus);
    initHeaderSearch();
    initHeaderLangMenu();
    initTableOfContents();
    initFAQ();
    initAuthorInfoToggle();
    initDisclosureToggle();

    // Set header and footer heights
    setHeaderFooterHeights();

    // Update heights on window resize
    let resizeTimeout;
    window.addEventListener("resize", () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => {
        setHeaderFooterHeights();
      }, 100);
    });

    document.querySelectorAll(".pswp-single").forEach((img) => {
      img.style.cursor = "pointer";

      img.addEventListener("click", () => {
        const pswp = new PhotoSwipe({
          dataSource: [
            {
              src: img.dataset.full,
              width: parseInt(img.dataset.w),
              height: parseInt(img.dataset.h),
            },
          ],
          index: 0,
        });

        pswp.init();
      });
    });
  };

  // Run on DOMContentLoaded
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
