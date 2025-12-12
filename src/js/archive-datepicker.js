/**
 * Archive Date Range Picker
 *
 * Initializes Flatpickr date range picker for archive search.
 *
 * @package Main
 * @since 1.0.0
 */

(function () {
  "use strict";

  /**
   * Initialize date range picker
   */
  const initArchiveDatePicker = () => {
    const dateRangeInput = document.getElementById("dateRangeSearch");
    const dateStartInput = document.getElementById("archiveDateStart");
    const dateEndInput = document.getElementById("archiveDateEnd");

    // Check if required element exists
    if (!dateRangeInput) {
      console.warn("Archive DatePicker: dateRangeSearch element not found");
      return;
    }

    // Check if flatpickr is loaded, wait for it if needed
    if (typeof flatpickr === "undefined") {
      // Retry if flatpickr is not yet loaded
      let retries = 0;
      const maxRetries = 50;
      const retryInterval = 100;

      const retryTimer = setInterval(() => {
        retries++;
        if (typeof flatpickr !== "undefined") {
          clearInterval(retryTimer);
          initArchiveDatePicker();
        } else if (retries >= maxRetries) {
          clearInterval(retryTimer);
          console.error(
            "Archive DatePicker: flatpickr library not loaded after",
            maxRetries * retryInterval,
            "ms"
          );
        }
      }, retryInterval);
      return;
    }

    // Set default dates from hidden inputs if available
    const defaultDates = [];
    if (dateStartInput?.value) {
      defaultDates.push(dateStartInput.value);
    }
    if (dateEndInput?.value) {
      defaultDates.push(dateEndInput.value);
    }

    /**
     * Check if search function is ready
     */
    function isSearchReady() {
      return (
        window.mainArchiveSearch &&
        typeof window.mainArchiveSearch.performSearch === "function" &&
        window.mainArchiveSearch.performSearch
          .toString()
          .indexOf("not yet initialized") === -1
      );
    }

    /**
     * Trigger search function with retry mechanism
     */
    function triggerSearch() {
      const searchInput = document.getElementById("articlesSearch");
      const searchQuery = searchInput?.value?.trim() || "";

      // Try to perform search immediately
      if (isSearchReady()) {
        window.mainArchiveSearch.performSearch(searchQuery);
        return;
      }

      // Retry if search function is not yet available
      let retries = 0;
      const maxRetries = 20;
      const retryInterval = 25;

      const retryTimer = setInterval(() => {
        retries++;
        if (isSearchReady()) {
          window.mainArchiveSearch.performSearch(searchQuery);
          clearInterval(retryTimer);
        } else if (retries >= maxRetries) {
          clearInterval(retryTimer);
        }
      }, retryInterval);
    }

    // Initialize Flatpickr
    try {
      const fpInstance = flatpickr(dateRangeInput, {
        mode: "range",
        dateFormat: "Y-m-d",
        allowInput: false,
        clickOpens: true,
        defaultDate: defaultDates.length > 0 ? defaultDates : undefined,
        onChange: function (selectedDates, dateStr, instance) {
          // Update hidden inputs with selected dates
          if (selectedDates.length >= 1 && dateStartInput) {
            const startDate = instance.formatDate(selectedDates[0], "Y-m-d");
            dateStartInput.value = startDate;
          }

          if (selectedDates.length === 2 && dateEndInput) {
            const endDate = instance.formatDate(selectedDates[1], "Y-m-d");
            dateEndInput.value = endDate;
          }

          // Clear dates if selection is cleared
          if (selectedDates.length === 0) {
            if (dateStartInput) dateStartInput.value = "";
            if (dateEndInput) dateEndInput.value = "";
          }

          // Trigger search when both dates are selected or when dates are cleared
          if (selectedDates.length === 2 || selectedDates.length === 0) {
            triggerSearch();
          }
        },
      });

      // Store instance for potential cleanup
      if (fpInstance) {
        dateRangeInput._flatpickr = fpInstance;
      }
    } catch (error) {
      console.error("Archive DatePicker: Error initializing Flatpickr", error);
    }
  };

  // Expose function globally for manual initialization if needed
  window.initArchiveDatePicker = initArchiveDatePicker;

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initArchiveDatePicker);
  } else {
    // DOM already loaded, initialize immediately
    initArchiveDatePicker();
  }

  // Also listen for custom event in case search is ready first
  window.addEventListener("mainArchiveSearchReady", function () {
    // Re-initialize if datepicker wasn't ready before
    if (
      document.getElementById("dateRangeSearch") &&
      !document.getElementById("dateRangeSearch")._flatpickr
    ) {
      initArchiveDatePicker();
    }
  });
})();
