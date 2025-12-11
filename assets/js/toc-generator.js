/**
 * TOC Generator
 *
 * Handles active state tracking and smooth scroll for TOC.
 * Works with existing accordion JavaScript.
 *
 * @package Main
 */

(function () {
  "use strict";

  // Wait for DOM
  // if (document.readyState === "loading") {
  //   document.addEventListener("DOMContentLoaded", init);
  // } else {
  //   init();
  // }

  function init() {
    const tocItems = document.querySelectorAll(".toc-item");
    const headings = document.querySelectorAll(
      ".entry-content h2[id], .entry-content h3[id]"
    );

    if (!tocItems.length || !headings.length) {
      return;
    }

    // Smooth scroll to section
    tocItems.forEach((item) => {
      item.addEventListener("click", function (e) {
        e.preventDefault();

        const targetId = this.getAttribute("href").substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
          const offset = 100;
          const elementPosition = targetElement.getBoundingClientRect().top;
          const offsetPosition = elementPosition + window.pageYOffset - offset;

          window.scrollTo({
            top: offsetPosition,
            behavior: "smooth",
          });

          setActiveItems(targetId);
        }
      });
    });

    // Track active section on scroll
    let ticking = false;

    window.addEventListener("scroll", function () {
      if (!ticking) {
        window.requestAnimationFrame(function () {
          updateActiveSection();
          ticking = false;
        });
        ticking = true;
      }
    });

    // Initial check on load and after a short delay (to ensure layout is settled)
    updateActiveSection();
    setTimeout(updateActiveSection, 100);

    function updateActiveSection() {
      const scrollPosition = window.scrollY + 150;
      const windowHeight = window.innerHeight;
      const documentHeight = document.documentElement.scrollHeight;

      // Check if we're at the bottom of the page (with generous threshold)
      const isAtBottom = window.scrollY + windowHeight >= documentHeight - 50;

      let currentHeading = null;

      // Find the current heading based on scroll position
      headings.forEach((heading) => {
        const headingTop = heading.offsetTop;

        if (scrollPosition >= headingTop) {
          currentHeading = heading;
        }
      });

      // If at bottom, always use the last heading to ensure it activates
      if (isAtBottom && headings.length > 0) {
        currentHeading = headings[headings.length - 1];
      }

      if (currentHeading) {
        const currentId = currentHeading.getAttribute("id");
        setActiveItems(currentId);
      }
    }

    function setActiveItems(targetId) {
      // Find the target TOC item using href
      const targetItem = document.querySelector(
        `.toc-item[href="#${targetId}"]`
      );

      if (!targetItem) {
        return;
      }

      // Remove active class and styles from all items
      tocItems.forEach((tocItem) => {
        tocItem.classList.remove(
          "toc-item--active",
          "bg-eva-prime-50",
          "font-semibold"
        );
        tocItem.classList.add("bg-gray-50", "font-medium");

        const arrow = tocItem.querySelector("svg");
        if (arrow) {
          arrow.classList.add("opacity-0");
        }
      });

      // Determine if target is h2 or h3
      const targetHeading = document.getElementById(targetId);
      if (!targetHeading) {
        return;
      }

      const isH3 = targetHeading.tagName.toLowerCase() === "h3";

      if (isH3) {
        // For h3, find and activate parent h2 as well
        let parentH2 = null;
        const allH2s = document.querySelectorAll(".entry-content h2[id]");

        // Find the parent h2 (the last h2 before this h3)
        allH2s.forEach((h2) => {
          if (h2.offsetTop < targetHeading.offsetTop) {
            parentH2 = h2;
          }
        });

        // Activate parent h2
        if (parentH2) {
          const parentId = parentH2.getAttribute("id");
          const parentItem = document.querySelector(
            `.toc-item[href="#${parentId}"]`
          );

          if (parentItem) {
            parentItem.classList.add(
              "toc-item--active",
              "bg-eva-prime-50",
              "font-semibold"
            );
            parentItem.classList.remove("bg-gray-50", "font-medium");

            const parentArrow = parentItem.querySelector("svg");
            if (parentArrow) {
              parentArrow.classList.remove("opacity-0");
            }
          }
        }
      }

      // Activate the current item (h2 or h3)
      targetItem.classList.add(
        "toc-item--active",
        "bg-eva-prime-50",
        "font-semibold"
      );
      targetItem.classList.remove("bg-gray-50", "font-medium");

      const arrow = targetItem.querySelector("svg");
      if (arrow) {
        arrow.classList.remove("opacity-0");
      }
    }
  }
})();
