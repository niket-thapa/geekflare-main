/**
 * Archive Page Search Functionality
 *
 * Handles AJAX search on archive pages with debouncing and smooth updates.
 * Provides a seamless search experience without page reloads.
 *
 * @package Main
 * @since 1.0.0
 */

(function () {
  "use strict";

  /**
   * Initialize archive search functionality
   */
  const initArchiveSearch = () => {
    const searchForm = document.querySelector(".archive-search-form");
    const searchInput = document.getElementById("articlesSearch");
    const postsGrid = document.getElementById("archive-posts-grid");
    const pagination = document.querySelector(".main-pagination");

    if (!searchForm || !searchInput || !postsGrid) {
      return;
    }

    // Get current archive context from form data attributes
    const getArchiveContext = () => {
      const context = {
        category: null,
        tag: null,
        author: null,
      };

      // Get from form data attributes (set by PHP)
      if (searchForm) {
        const categoryId = searchForm.dataset.categoryId;
        const tagId = searchForm.dataset.tagId;
        const authorId = searchForm.dataset.authorId;

        if (categoryId) {
          context.category = parseInt(categoryId, 10);
        }

        if (tagId) {
          context.tag = parseInt(tagId, 10);
        }

        if (authorId) {
          context.author = parseInt(authorId, 10);
        }
      }

      return context;
    };

    /**
     * Get date range from hidden inputs
     */
    const getDateRange = () => {
      const dateStartInput = document.getElementById("archiveDateStart");
      const dateEndInput = document.getElementById("archiveDateEnd");

      return {
        start: dateStartInput?.value?.trim() || "",
        end: dateEndInput?.value?.trim() || "",
      };
    };

    /**
     * Perform AJAX search
     */
    const performSearch = (searchQuery, page = 1) => {
      if (!postsGrid) {
        return;
      }

      // Show loading state
      postsGrid.classList.add("is-loading");
      postsGrid.style.opacity = "0.6";
      postsGrid.style.pointerEvents = "none";

      // Get archive context
      const context = getArchiveContext();

      // Build API URL
      const apiUrl = new URL(
        `${window.wpApiSettings.root}main/v1/archive-search`,
        window.location.origin
      );
      apiUrl.searchParams.append("search", searchQuery || "");
      apiUrl.searchParams.append("paged", page);

      // Get date range
      const dateRange = getDateRange();
      if (dateRange.start) {
        apiUrl.searchParams.append("date_start", dateRange.start);
      }
      if (dateRange.end) {
        apiUrl.searchParams.append("date_end", dateRange.end);
      }

      // Add archive context filters
      if (context.category) {
        apiUrl.searchParams.append("category", context.category);
      }

      if (context.tag) {
        apiUrl.searchParams.append("tag", context.tag);
      }

      if (context.author) {
        apiUrl.searchParams.append("author", context.author);
      }

      // Fetch search results
      fetch(apiUrl.toString(), {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            // Update posts grid
            postsGrid.innerHTML = data.posts_html;

            // Update pagination if exists
            if (pagination && data.max_pages > 1) {
              // Could update pagination here, but for now just show/hide
              pagination.style.display = data.max_pages > 1 ? "block" : "none";
            } else if (pagination) {
              pagination.style.display = "none";
            }

            // Update URL without reload (optional)
            const url = new URL(window.location.href);
            if (searchQuery) {
              url.searchParams.set("archive_search", searchQuery);
            } else {
              url.searchParams.delete("archive_search");
            }

            // Update date range in URL
            const dateRange = getDateRange();
            if (dateRange.start) {
              url.searchParams.set("archive_date_start", dateRange.start);
            } else {
              url.searchParams.delete("archive_date_start");
            }
            if (dateRange.end) {
              url.searchParams.set("archive_date_end", dateRange.end);
            } else {
              url.searchParams.delete("archive_date_end");
            }

            url.searchParams.delete("paged"); // Reset to page 1 on new search
            window.history.replaceState({}, "", url.toString());

            // Scroll to top of results
            postsGrid.scrollIntoView({ behavior: "smooth", block: "start" });
          }
        })
        .catch((error) => {
          postsGrid.innerHTML = `
						<div class="col-span-full text-center py-12">
							<p class="text-gray-500 text-lg font-medium">
								${
                  window.mainArchiveSearch?.errorMessage ||
                  "An error occurred while searching. Please try again."
                }
							</p>
						</div>
					`;
        })
        .finally(() => {
          // Remove loading state
          postsGrid.classList.remove("is-loading");
          postsGrid.style.opacity = "1";
          postsGrid.style.pointerEvents = "auto";
        });
    };

    // Debounce function
    let debounceTimeout;
    const debounce = (func, wait) => {
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(debounceTimeout);
          func(...args);
        };
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(later, wait);
      };
    };

    // Debounced search function (wait 500ms after user stops typing)
    const debouncedSearch = debounce((query) => {
      if (query.trim().length >= 2 || query.trim().length === 0) {
        performSearch(query.trim());
      }
    }, 500);

    // Handle input events
    searchInput.addEventListener("input", (e) => {
      const query = e.target.value.trim();

      // If query is cleared, reload page to show all posts
      if (query.length === 0) {
        const url = new URL(window.location.href);
        url.searchParams.delete("archive_search");
        url.searchParams.delete("paged");
        window.location.href = url.toString();
        return;
      }

      // Only search if query is 2+ characters
      if (query.length >= 1) {
        debouncedSearch(query);
      }
    });

    // Handle form submission (for non-JS fallback)
    searchForm.addEventListener("submit", (e) => {
      const query = searchInput.value.trim();

      // If JS is enabled, prevent default and do AJAX search
      if (query.length >= 2) {
        e.preventDefault();
        debouncedSearch(query);
      }
      // Otherwise let form submit normally (non-JS fallback)
    });

    // Handle Enter key (immediate search without debounce)
    searchInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query.length >= 2 || query.length === 0) {
          performSearch(query);
        }
      }
    });

    // Make performSearch function accessible for datepicker
    // Expose immediately so datepicker can use it
    window.mainArchiveSearch = window.mainArchiveSearch || {};
    window.mainArchiveSearch.performSearch = performSearch;
    window.mainArchiveSearch.getSearchQuery = () =>
      searchInput?.value?.trim() || "";

    // Dispatch custom event to notify that search is ready
    window.dispatchEvent(new CustomEvent("mainArchiveSearchReady"));
  };

  // Initialize performSearch placeholder immediately so datepicker knows it exists
  window.mainArchiveSearch = window.mainArchiveSearch || {};

  // Placeholder that will be replaced when initArchiveSearch runs
  if (!window.mainArchiveSearch.performSearch) {
    window.mainArchiveSearch.performSearch = function () {
      // Archive search not yet initialized
    };
  }

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initArchiveSearch);
  } else {
    initArchiveSearch();
  }
})();
