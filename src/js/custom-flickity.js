import Flickity from "flickity";

document.addEventListener("DOMContentLoaded", function () {
  // Add 200ms delay before initializing Flickity carousels
  setTimeout(function () {
    document.querySelectorAll("[data-flickity]").forEach((carousel) => {
      try {
        // Get Flickity options from data attribute
        const optionsAttr = carousel.getAttribute("data-flickity");
        if (!optionsAttr) return;

        // Parse JSON options
        let options = {};
        try {
          options = JSON.parse(optionsAttr);
        } catch (e) {
          return;
        }

        // Initialize Flickity (Flickity stores the instance on the element as element.flickity)
        const flkty = new Flickity(carousel, options);

        // Check if data-pagination is enabled
        if (carousel.getAttribute("data-pagination") === "true") {
          // Create and insert pagination element
          const counter = document.createElement("div");
          counter.className = "flickity-pagination";
          counter.style.pointerEvents = "none";
          carousel.appendChild(counter);

          const updatePagination = () => {
            const current = flkty.selectedIndex + 1;
            const total = flkty.slides.length;
            counter.textContent = `${current}/${total}`;
          };

          flkty.on("select", updatePagination);
          updatePagination();
        }
      } catch (error) {
        // Silently fail if Flickity initialization fails
      }
    });

    // Set up control buttons for each button individually
    document
      .querySelectorAll("[data-flickity-control][data-flickity-target]")
      .forEach((button) => {
        const control = button.getAttribute("data-flickity-control");
        const targetSelector = button.getAttribute("data-flickity-target");

        if (!control || !targetSelector) return;

        button.addEventListener("click", (event) => {
          event.preventDefault();

          // Find the target carousel element using the selector from the button
          const target = document.querySelector(targetSelector);
          if (!target) return;

          // Get the Flickity instance from the carousel element (Flickity stores it as element.flickity)
          const instance = Flickity.data(target);

          if (!instance) return;

          if (control === "prev") {
            instance.previous();
          } else if (control === "next") {
            instance.next();
          }
        });
      });
  }, 200); // 200ms delay before initialization
});
