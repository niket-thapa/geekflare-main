(function () {
  "use strict";

  // Wait for DOM to be ready
  document.addEventListener("DOMContentLoaded", function () {
    initProductFilter();
  });

  function initProductFilter() {
    const filterForm = document.getElementById("filter-content");
    const clearButton = filterForm?.querySelector('button[type="reset"]');
    const clearButtonWrap = filterForm?.querySelector(".btn_clear_wrap");
    const resultsCountWrap = filterForm?.querySelector(".filter-results-count");
    const resultsCountSpan = document.getElementById("results-count");
    const showMoreBtn = document.getElementById("show-more-features");
    const showLessBtn = document.getElementById("show-less-features");
    const hiddenFeatures = document.getElementById("hidden-features");
    const productsContainer = document.getElementById("products");

    if (!filterForm) return;

    // Show/Hide more features
    if (showMoreBtn && hiddenFeatures) {
      showMoreBtn.addEventListener("click", function () {
        hiddenFeatures.style.display = "block";
        showMoreBtn.style.display = "none";
        if (showLessBtn) {
          showLessBtn.style.display = "block";
        }
      });
    }

    if (showLessBtn && hiddenFeatures) {
      showLessBtn.addEventListener("click", function () {
        hiddenFeatures.style.display = "none";
        if (showMoreBtn) {
          showMoreBtn.style.display = "block";
        }
        showLessBtn.style.display = "none";
      });
    }

    // Listen to all filter inputs
    const filterInputs = filterForm.querySelectorAll(
      'input[type="checkbox"], input[type="radio"]'
    );

    filterInputs.forEach(function (input) {
      input.addEventListener("change", function () {
        applyFilters();
        updateClearButtonVisibility();
      });
    });

    // Clear filters
    if (clearButton) {
      clearButton.addEventListener("click", function (e) {
        e.preventDefault();
        resetFilters();
      });
    }

    // Scroll to products section
    function scrollToProducts() {
      if (productsContainer) {
        const elementPosition = productsContainer.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - 50;

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        });
      }
    }

    // Apply filters function
    function applyFilters() {
      const products = document.querySelectorAll(
        ".buying_guide_item[data-product-filter]"
      );

      // Get selected filters
      const selectedPricing =
        filterForm.querySelector('input[name="pricing"]:checked')?.value ||
        "all";
      const selectedBestSuited = Array.from(
        filterForm.querySelectorAll('input[name="best-suited[]"]:checked')
      ).map((cb) => cb.value);
      const selectedFeatures = Array.from(
        filterForm.querySelectorAll('input[name="features[]"]:checked')
      ).map((cb) => cb.value);

      let visibleCount = 0;

      products.forEach(function (product) {
        try {
          const filterDataAttr = product.getAttribute("data-product-filter");

          // Decode HTML entities if needed
          const decodedData = filterDataAttr.replace(/&quot;/g, '"');
          const filterData = JSON.parse(decodedData);

          let shouldShow = true;

          // Filter by pricing
          if (selectedPricing !== "all") {
            shouldShow =
              shouldShow && matchesPricing(filterData.pricing, selectedPricing);
          }

          // Filter by Best Suited For (OR logic - match any selected)
          if (selectedBestSuited.length > 0) {
            shouldShow =
              shouldShow &&
              matchesAnyTerm(filterData.bestSuited, selectedBestSuited);
          }

          // Filter by Features (OR logic - match any selected)
          if (selectedFeatures.length > 0) {
            shouldShow =
              shouldShow &&
              matchesAnyTerm(filterData.features, selectedFeatures);
          }

          // Show/hide product
          if (shouldShow) {
            product.style.display = "";
            visibleCount++;
          } else {
            product.style.display = "none";
          }
        } catch (error) {
          console.error("Error parsing product filter data:", error, product);
        }
      });

      // Update results count
      updateResultsCount(visibleCount);

      // Scroll to products section
      scrollToProducts();
    }

    // Check if pricing matches
    function matchesPricing(productPricing, selectedRange) {
      // If product has no pricing value, don't show it in any pricing filter
      if (
        productPricing === null ||
        productPricing === undefined ||
        productPricing === ""
      ) {
        return false;
      }

      // Extract numeric value from string (handles "$21", "21", etc.)
      let price = productPricing;

      // If it's a string, extract the number
      if (typeof productPricing === "string") {
        // Remove any non-numeric characters except decimal point
        const numericString = productPricing.replace(/[^\d.]/g, "");
        price = parseFloat(numericString);
      } else {
        price = parseFloat(productPricing);
      }

      // If we couldn't get a valid number, return false
      if (isNaN(price)) {
        return false;
      }

      switch (selectedRange) {
        case "free":
          return price === 0;
        case "0-10":
          return price > 0 && price < 10;
        case "10-20":
          return price >= 10 && price <= 20;
        case "20+":
          return price > 20;
        default:
          return true;
      }
    }

    // Check if any term matches (OR logic)
    function matchesAnyTerm(productTerms, selectedTerms) {
      if (!Array.isArray(productTerms) || productTerms.length === 0) {
        return false;
      }

      return selectedTerms.some(function (selectedTerm) {
        return productTerms.includes(selectedTerm);
      });
    }

    // Update results count
    function updateResultsCount(count) {
      if (resultsCountSpan && resultsCountWrap) {
        resultsCountSpan.textContent = count;
        resultsCountWrap.style.display = "block";
      }
    }

    // Update clear button visibility
    function updateClearButtonVisibility() {
      const hasActiveFilters = isAnyFilterActive();
      if (clearButtonWrap) {
        clearButtonWrap.style.display = hasActiveFilters ? "block" : "none";
      }
    }

    // Check if any filter is active
    function isAnyFilterActive() {
      const pricingRadio = filterForm.querySelector(
        'input[name="pricing"]:checked'
      );
      const hasPricingFilter = pricingRadio && pricingRadio.value !== "all";

      const hasBestSuitedFilter =
        filterForm.querySelectorAll('input[name="best-suited[]"]:checked')
          .length > 0;
      const hasFeaturesFilter =
        filterForm.querySelectorAll('input[name="features[]"]:checked').length >
        0;

      return hasPricingFilter || hasBestSuitedFilter || hasFeaturesFilter;
    }

    // Reset all filters
    function resetFilters() {
      // Reset pricing to "all"
      const allPricingRadio = filterForm.querySelector(
        'input[name="pricing"][value="all"]'
      );
      if (allPricingRadio) {
        allPricingRadio.checked = true;
      }

      // Uncheck all checkboxes
      filterForm
        .querySelectorAll('input[type="checkbox"]')
        .forEach(function (checkbox) {
          checkbox.checked = false;
        });

      // Show all products
      const products = document.querySelectorAll(
        ".buying_guide_item[data-product-filter]"
      );
      products.forEach(function (product) {
        product.style.display = "";
      });

      // Update UI
      updateClearButtonVisibility();
      if (resultsCountWrap) {
        resultsCountWrap.style.display = "none";
      }

      // Scroll to products section
      scrollToProducts();
    }

    // Initial state
    updateClearButtonVisibility();
  }
})();
