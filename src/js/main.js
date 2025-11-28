/**
 * Main Theme JavaScript
 *
 * @package Main
 * @since 1.0.0
 */

// Import custom Flickity initialization (Flickity CSS is enqueued separately via functions.php)
import "./custom-flickity.js";

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

    allTocLinks.forEach((link) => {
      link.addEventListener("click", (e) => {
        const href = link.getAttribute("href");

        if (href && href.startsWith("#")) {
          const target = document.querySelector(href);

          if (target) {
            e.preventDefault();

            target.scrollIntoView({ behavior: "smooth", block: "start" });

            // Update active state - remove from all items
            tocItems.forEach((item) => {
              item.classList.remove("toc-item--active");
              item.classList.add("bg-gray-50");
              item.classList.remove("bg-eva-prime-50");
            });

            // If it's a nested item, also update parent styling
            if (link.classList.contains("toc-nested-item")) {
              tocNestedItems.forEach((item) => {
                item.classList.remove("text-eva-prime-600");
                item.classList.add("text-gray-800");
              });

              link.classList.remove("text-gray-800");
              link.classList.add("text-eva-prime-600");
            } else {
              // If it's a main item, reset nested items
              tocNestedItems.forEach((item) => {
                item.classList.remove("text-eva-prime-600");
                item.classList.add("text-gray-800");
              });

              link.classList.add("toc-item--active");
              link.classList.remove("bg-gray-50");
              link.classList.add("bg-eva-prime-50");
            }
          }
        }
      });
    });
  };

  // Initialize all header functionality when DOM is ready
  const init = () => {
    const resetSubmenus = initMobileSubmenus();
    initMobileMenuToggle(resetSubmenus);
    initHeaderSearch();
    initHeaderLangMenu();
    initTableOfContents();
  };

  // Run on DOMContentLoaded
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
