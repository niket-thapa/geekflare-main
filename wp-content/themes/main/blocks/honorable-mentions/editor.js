(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;
  var TextControl = wp.components.TextControl;
  var Button = wp.components.Button;
  var Spinner = wp.components.Spinner;
  var __ = wp.i18n.__;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useRef = wp.element.useRef;

  registerBlockType("main/honorable-mentions", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var products = attributes.products || [];
      var heading = attributes.heading || "Honorable Mentions";
      var designType = attributes.designType || "honorable-mentions";

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "honorable-mentions-editor",
      });

      var searchTermState = useState("");
      var searchTerm = searchTermState[0];
      var setSearchTerm = searchTermState[1];

      var searchResultsState = useState([]);
      var searchResults = searchResultsState[0];
      var setSearchResults = searchResultsState[1];

      var productDataCacheState = useState({});
      var productDataCache = productDataCacheState[0];
      var setProductDataCache = productDataCacheState[1];

      // Category states
      var categoriesState = useState([]);
      var categories = categoriesState[0];
      var setCategories = categoriesState[1];

      var selectedCategoryState = useState("");
      var selectedCategory = selectedCategoryState[0];
      var setSelectedCategory = selectedCategoryState[1];

      var isLoadingCategoriesState = useState(false);
      var isLoadingCategories = isLoadingCategoriesState[0];
      var setIsLoadingCategories = isLoadingCategoriesState[1];

      var isLoadingProductsState = useState(false);
      var isLoadingProducts = isLoadingProductsState[0];
      var setIsLoadingProducts = isLoadingProductsState[1];

      // Auto-show search when block is first added (no products yet)
      var showSearchState = useState(products.length === 0);
      var showSearch = showSearchState[0];
      var setShowSearch = showSearchState[1];

      // Ref for search input to auto-focus
      var searchInputId =
        "honorable-mentions-search-input-" + (props.clientId || "default");
      var searchInputRef = useRef(null);
      var hasAutoFocusedRef = useRef(false); // Track if we've already auto-focused

      // Auto-show search when products list becomes empty
      useEffect(
        function () {
          if (products.length === 0) {
            setShowSearch(true);
          }
        },
        [products.length]
      );

      // Function to focus the search input
      var focusSearchInput = function () {
        // Try multiple strategies to find and focus the input
        var strategies = [
          // Strategy 1: Use ref if available
          function () {
            if (searchInputRef.current) {
              var input =
                searchInputRef.current.querySelector('input[type="text"]') ||
                searchInputRef.current.querySelector("input") ||
                searchInputRef.current.querySelector(
                  ".components-text-control__input"
                );
              return input;
            }
            return null;
          },
          // Strategy 2: Find by container ID
          function () {
            var container = document.getElementById(searchInputId);
            if (container) {
              var input =
                container.querySelector('input[type="text"]') ||
                container.querySelector("input") ||
                container.querySelector(".components-text-control__input");
              return input;
            }
            return null;
          },
          // Strategy 3: Find by placeholder text
          function () {
            var inputs = document.querySelectorAll('input[type="text"]');
            for (var i = 0; i < inputs.length; i++) {
              var placeholder = inputs[i].getAttribute("placeholder") || "";
              if (placeholder.toLowerCase().indexOf("type to search") !== -1) {
                return inputs[i];
              }
            }
            return null;
          },
        ];

        // Try each strategy
        for (var i = 0; i < strategies.length; i++) {
          var input = strategies[i]();
          if (input && input.offsetParent !== null) {
            // Input is visible
            try {
              input.focus();
              // Verify focus worked
              if (document.activeElement === input) {
                return true;
              }
            } catch (e) {
              // Continue to next strategy
            }
          }
        }
        return false;
      };

      // Auto-focus search input only once on initial mount (when block is first added)
      useEffect(
        function () {
          // Only auto-focus if:
          // 1. We haven't auto-focused before
          // 2. Search is shown
          // 3. No products are selected (new block)
          if (
            !hasAutoFocusedRef.current &&
            showSearch &&
            products.length === 0
          ) {
            // Use requestAnimationFrame for better timing
            var frameId = requestAnimationFrame(function () {
              // Try immediate focus
              if (focusSearchInput()) {
                hasAutoFocusedRef.current = true; // Mark as focused
                return;
              }

              // If immediate focus fails, try with delays
              var attempts = [50, 150, 300, 500, 800];
              var attemptIndex = 0;

              var tryFocus = function () {
                if (attemptIndex < attempts.length) {
                  setTimeout(function () {
                    if (focusSearchInput()) {
                      hasAutoFocusedRef.current = true; // Mark as focused
                      return; // Success, stop trying
                    }
                    attemptIndex++;
                    tryFocus();
                  }, attempts[attemptIndex] -
                    (attemptIndex > 0 ? attempts[attemptIndex - 1] : 0));
                }
              };

              tryFocus();
            });

            return function () {
              cancelAnimationFrame(frameId);
            };
          }
        },
        [] // Only run once on mount
      );

      // Fetch product categories on mount
      useEffect(function () {
        if (categories.length > 0) return;

        setIsLoadingCategories(true);
        apiFetch({
          path: "/wp/v2/product-category?per_page=100&orderby=name&order=asc",
        })
          .then(function (results) {
            setCategories(results || []);
            setIsLoadingCategories(false);
          })
          .catch(function (error) {
            console.error("Error fetching categories:", error);
            setCategories([]);
            setIsLoadingCategories(false);
          });
      }, []);

      // Fetch product data for ratings, logos, and descriptions
      useEffect(
        function () {
          products.forEach(function (product) {
            if (!productDataCache[product.id]) {
              apiFetch({
                path: "/main/v1/product-data/" + product.id,
              })
                .then(function (data) {
                  if (data) {
                    setProductDataCache(function (prevCache) {
                      var newCache = Object.assign({}, prevCache);
                      newCache[product.id] = data;
                      return newCache;
                    });
                  }
                })
                .catch(function (error) {
                  console.error(
                    "Error fetching product data for ID " + product.id + ":",
                    error
                  );
                  setProductDataCache(function (prevCache) {
                    var newCache = Object.assign({}, prevCache);
                    newCache[product.id] = null;
                    return newCache;
                  });
                });
            }
          });
        },
        [
          products
            .map(function (p) {
              return p.id;
            })
            .join(","),
        ]
      );

      function searchProducts(term) {
        if (!term || term.length < 2) {
          setSearchResults([]);
          return;
        }

        apiFetch({
          path:
            "/wp/v2/products?search=" +
            encodeURIComponent(term) +
            "&per_page=10",
        })
          .then(function (results) {
            setSearchResults(results || []);
          })
          .catch(function (error) {
            setSearchResults([]);
          });
      }

      function addProduct(product) {
        var newProducts = products.slice();
        var exists = newProducts.some(function (p) {
          return p.id === product.id;
        });

        if (!exists) {
          newProducts.push({
            id: product.id,
            name: product.title.rendered,
            rank: newProducts.length + 4,
            readReviewUrl: "",
            readReviewText: "Read Review",
          });
          setAttributes({ products: newProducts });
        }

        setSearchTerm("");
        setSearchResults([]);
        // Keep search open and auto-focus for adding more products
        // setShowSearch(false);

        // Auto-focus search input after adding product
        setTimeout(function () {
          focusSearchInput();
        }, 100);
      }

      // Handle category selection and bulk add products
      var handleCategoryChange = function (categoryId) {
        setSelectedCategory(categoryId);
        if (!categoryId || categoryId === "") {
          return;
        }

        setIsLoadingProducts(true);

        apiFetch({
          path: "/main/v1/products-by-category/" + categoryId + "?limit=100",
        })
          .then(function (productsList) {
            if (
              !productsList ||
              !Array.isArray(productsList) ||
              productsList.length === 0
            ) {
              setIsLoadingProducts(false);
              return;
            }

            var newProducts = products.slice();
            var existingIds = newProducts.map(function (p) {
              return p.id;
            });

            productsList.forEach(function (product) {
              if (
                product &&
                product.id &&
                existingIds.indexOf(product.id) === -1
              ) {
                newProducts.push({
                  id: product.id,
                  name: product.title
                    ? product.title
                    : product.name || "Product",
                  rank: newProducts.length + 4,
                  readReviewUrl: "",
                  readReviewText: "Read Review",
                });
              }
            });

            setAttributes({ products: newProducts });
            setSelectedCategory("");
            setIsLoadingProducts(false);
          })
          .catch(function (error) {
            console.error("Error fetching products by category:", error);
            setIsLoadingProducts(false);
          });
      };

      function removeProduct(index) {
        var newProducts = products.slice();
        newProducts.splice(index, 1);
        setAttributes({ products: newProducts });
      }

      function updateRank(index, value) {
        var newProducts = products.slice();
        newProducts[index].rank = value;
        setAttributes({ products: newProducts });
      }

      function updateReadReviewUrl(index, value) {
        var newProducts = products.slice();
        if (!newProducts[index].readReviewUrl) {
          newProducts[index].readReviewUrl = "";
        }
        newProducts[index].readReviewUrl = value;
        setAttributes({ products: newProducts });
      }

      function updateReadReviewText(index, value) {
        var newProducts = products.slice();
        if (!newProducts[index].readReviewText) {
          newProducts[index].readReviewText = "";
        }
        newProducts[index].readReviewText = value;
        setAttributes({ products: newProducts });
      }

      // Render badge (number or rating)
      function renderBadge(product, index) {
        var productData = productDataCache[product.id];

        // If data is still loading, show a placeholder
        if (!productData) {
          return el(
            "div",
            {
              className:
                "flex justify-center items-center py-1 px-3.5 bg-gray-100 rounded-full",
              style: { minWidth: "40px", height: "28px" },
            },
            el(Spinner, { style: { width: "16px", height: "16px" } })
          );
        }

        var rating =
          productData && productData.rating ? productData.rating : null;

        if (designType === "top-alternatives" && rating && rating > 0) {
          // Show rating badge
          return el(
            "div",
            {
              className:
                "flex justify-center items-center py-[0.1875rem] px-2 bg-[#FFFAEB] rounded-full gap-1",
            },
            el(
              "svg",
              {
                className: "w-3.5 md:w-4 h-auto",
                xmlns: "http://www.w3.org/2000/svg",
                width: "14",
                height: "14",
                fill: "none",
                viewBox: "0 0 14 14",
              },
              el("path", {
                fill: "#f79009",
                d: "m12.813 6.28-2.46 2.124.749 3.176a.897.897 0 0 1-1.34.975L7 10.855l-2.763 1.7A.897.897 0 0 1 2.9 11.58l.752-3.176-2.46-2.123a.9.9 0 0 1 .51-1.578l3.226-.26L6.17 1.43a.895.895 0 0 1 1.656 0L9.07 4.443l3.226.26a.9.9 0 0 1 .513 1.578z",
              })
            ),
            el(
              "span",
              {
                className:
                  "text-xs md:text-sm font-semibold text-gray-800 -mb-0.5",
              },
              rating.toFixed(1)
            )
          );
        } else {
          // Show editable number badge
          var rankValue = product.rank || "";
          if (!rankValue) {
            // Show placeholder for empty rank
            return el(
              "div",
              {
                className:
                  "flex justify-center items-center py-1 px-3.5 bg-gray-100 rounded-full",
              },
              el(RichText, {
                tagName: "span",
                value: "",
                onChange: function (value) {
                  // Remove # and any whitespace, keep only numbers
                  var numValue = value.replace(/[^0-9]/g, "");
                  updateRank(index, numValue);
                },
                placeholder: "#",
                allowedFormats: [],
                className:
                  "text-sm md:text-base font-semibold leading-5 md:leading-6 text-gray-800",
                style: {
                  minWidth: "30px",
                  display: "inline-block",
                  cursor: "text",
                },
              })
            );
          }
          return el(
            "div",
            {
              className:
                "flex justify-center items-center py-1 px-3.5 bg-gray-100 rounded-full",
            },
            el(RichText, {
              tagName: "span",
              value: "#" + rankValue,
              onChange: function (value) {
                // Remove # and any whitespace, keep only numbers
                var numValue = value.replace(/[^0-9]/g, "");
                updateRank(index, numValue);
              },
              placeholder: "#",
              allowedFormats: [],
              className:
                "text-sm md:text-base font-semibold leading-5 md:leading-6 text-gray-800",
              style: {
                minWidth: "30px",
                display: "inline-block",
                cursor: "text",
              },
            })
          );
        }
      }

      // Prepare category options
      var categoryOptions = [
        {
          label: __("Select a category to add products...", "main"),
          value: "",
        },
      ].concat(
        categories.map(function (category) {
          return {
            label: category.name + " (" + category.count + ")",
            value: String(category.id),
          };
        })
      );

      return el(
        "div",
        blockProps,
        // Sidebar Block Settings
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            {
              title: __("Block Settings", "main"),
              initialOpen: true,
            },
            el(SelectControl, {
              label: __("Design Type", "main"),
              value: designType,
              options: [
                {
                  label: __("Honorable Mentions", "main"),
                  value: "honorable-mentions",
                },
                {
                  label: __("Top Alternatives", "main"),
                  value: "top-alternatives",
                },
              ],
              onChange: function (value) {
                setAttributes({ designType: value });
              },
            })
          )
        ),
        el(
          "section",
          {
            id: "honorable-mentions",
            className: "flex flex-col gap-7 md:gap-8 pb-8 md:pb-12 lg:pb-20",
          },

          // Product Search and Selection
          el(
            "div",
            {
              style: {
                padding: "16px",
                background: "#f9fafb",
                borderRadius: "8px",
                border: "1px solid #e5e7eb",
                marginBottom: "24px",
              },
            },
            el(
              "div",
              {
                style: {
                  display: "flex",
                  alignItems: "center",
                  gap: "12px",
                  marginBottom: "12px",
                },
              },
              products.length > 0 &&
                el(
                  "span",
                  {
                    style: {
                      fontSize: "14px",
                      color: "#6b7280",
                    },
                  },
                  __("Products:", "main") + " " + products.length
                )
            ),

            // Category Selection
            isLoadingCategories
              ? el(
                  "div",
                  {
                    style: {
                      display: "flex",
                      alignItems: "center",
                      gap: "8px",
                      padding: "8px 0",
                      marginBottom: "16px",
                    },
                  },
                  el(Spinner, {}),
                  el(
                    "span",
                    { style: { fontSize: "13px", color: "#757575" } },
                    __("Loading categories...", "main")
                  )
                )
              : el(
                  "div",
                  { style: { marginBottom: "16px" } },
                  el(SelectControl, {
                    label: __("Add by Category", "main"),
                    value: selectedCategory,
                    options: categoryOptions,
                    onChange: handleCategoryChange,
                    disabled: isLoadingProducts,
                    help: __(
                      "Select a category to add all its products at once",
                      "main"
                    ),
                  })
                ),

            isLoadingProducts &&
              el(
                "div",
                {
                  style: {
                    display: "flex",
                    alignItems: "center",
                    gap: "8px",
                    marginBottom: "16px",
                  },
                },
                el(Spinner, {}),
                el(
                  "span",
                  { style: { fontSize: "13px", color: "#757575" } },
                  __("Adding products...", "main")
                )
              ),

            el(
              "div",
              {
                id: searchInputId,
                ref: function (element) {
                  searchInputRef.current = element;
                  // Try to focus immediately when element is available, but only once
                  if (
                    element &&
                    showSearch &&
                    !hasAutoFocusedRef.current &&
                    products.length === 0
                  ) {
                    setTimeout(function () {
                      var input =
                        element.querySelector('input[type="text"]') ||
                        element.querySelector("input") ||
                        element.querySelector(
                          ".components-text-control__input"
                        );
                      if (input && document.activeElement !== input) {
                        input.focus();
                        hasAutoFocusedRef.current = true; // Mark as focused
                      }
                    }, 50);
                  }
                },
              },
              el(TextControl, {
                label: __("Search Products", "main"),
                value: searchTerm,
                onChange: function (value) {
                  setSearchTerm(value);
                  searchProducts(value);
                },
                placeholder: __("Type to search...", "main"),
                style: { marginBottom: "12px" },
              }),

              searchResults.length > 0 &&
                el(
                  "div",
                  {
                    style: {
                      maxHeight: "200px",
                      overflowY: "auto",
                      border: "1px solid #e5e7eb",
                      borderRadius: "4px",
                      padding: "8px",
                      background: "#fff",
                    },
                  },
                  searchResults.map(function (result) {
                    return el(
                      Button,
                      {
                        key: result.id,
                        onClick: function () {
                          addProduct(result);
                        },
                        variant: "secondary",
                        style: {
                          marginBottom: "4px",
                          display: "block",
                          width: "100%",
                          textAlign: "left",
                        },
                      },
                      result.title.rendered
                    );
                  })
                ),

              searchTerm.length > 0 &&
                searchTerm.length < 2 &&
                el(
                  "p",
                  {
                    style: {
                      fontSize: "12px",
                      color: "#9ca3af",
                      fontStyle: "italic",
                      marginTop: "8px",
                    },
                  },
                  __("Type at least 2 characters to search", "main")
                )
            ),

            products.length > 0 &&
              el(
                "div",
                {
                  style: {
                    marginTop: "16px",
                    paddingTop: "16px",
                    borderTop: "1px solid #e5e7eb",
                  },
                },
                el(
                  "div",
                  {
                    style: {
                      fontSize: "13px",
                      fontWeight: "600",
                      color: "#374151",
                      marginBottom: "12px",
                    },
                  },
                  __("Selected Products:", "main")
                ),
                products.map(function (product, index) {
                  var productData = productDataCache[product.id];
                  var permalink =
                    productData && productData.permalink
                      ? productData.permalink
                      : "#";

                  return el(
                    "div",
                    {
                      key: product.id,
                      style: {
                        padding: "12px",
                        background: "#fff",
                        borderRadius: "6px",
                        marginBottom: "8px",
                        border: "1px solid #e5e7eb",
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "flex-start",
                        gap: "12px",
                      },
                    },
                    el(
                      "div",
                      {
                        style: {
                          flex: 1,
                        },
                      },
                      el(
                        "div",
                        {
                          style: {
                            fontSize: "14px",
                            fontWeight: "600",
                            color: "#111827",
                            marginBottom: "4px",
                          },
                        },
                        product.name
                      ),
                      designType === "top-alternatives" &&
                        el(
                          "div",
                          {
                            style: {
                              display: "flex",
                              flexDirection: "column",
                              gap: "8px",
                              marginTop: "8px",
                            },
                          },
                          el(TextControl, {
                            label: __("Link Text", "main"),
                            value: product.readReviewText || "",
                            onChange: function (value) {
                              updateReadReviewText(index, value);
                            },
                            placeholder: __("Read Review", "main"),
                            style: { marginBottom: "0" },
                          }),
                          el(TextControl, {
                            label: __("Link URL", "main"),
                            value: product.readReviewUrl || permalink,
                            onChange: function (value) {
                              updateReadReviewUrl(index, value);
                            },
                            placeholder: permalink,
                            type: "url",
                            style: { marginBottom: "0" },
                          })
                        )
                    ),
                    el(
                      Button,
                      {
                        isSmall: true,
                        isDestructive: true,
                        onClick: function () {
                          removeProduct(index);
                        },
                      },
                      __("Remove", "main")
                    )
                  );
                })
              )
          ),

          // Products Display
          products.length === 0
            ? el(
                "p",
                {
                  style: {
                    padding: "40px",
                    textAlign: "center",
                    background: "#f9fafb",
                    borderRadius: "8px",
                    color: "#6b7280",
                    fontStyle: "italic",
                  },
                },
                __(
                  "No products selected. Click 'Add Product' above to search and add products.",
                  "main"
                )
              )
            : el(
                "div",
                { className: "flex flex-col md:grid md:grid-cols-2 gap-4" },
                products.map(function (product, index) {
                  var productData = productDataCache[product.id];
                  var customNote =
                    productData && productData.custom_note
                      ? productData.custom_note
                      : productData && productData.tagline
                      ? productData.tagline
                      : "";
                  var logo =
                    productData && productData.logo ? productData.logo : "";
                  var permalink =
                    productData && productData.permalink
                      ? productData.permalink
                      : "#";

                  // Check if product data is still loading
                  var isLoading = !productDataCache[product.id];

                  return el(
                    "article",
                    {
                      key: product.id,
                      className:
                        "flex flex-col gap-6 p-5 bg-white border border-gray-200 rounded-2xl flex-1 relative group",
                    },
                    // Remove button (appears on hover)
                    el(
                      "div",
                      {
                        className: "honorable-mentions-remove-btn",
                        style: {
                          position: "absolute",
                          top: "8px",
                          right: "8px",
                          zIndex: 10,
                        },
                      },
                      el(
                        Button,
                        {
                          isSmall: true,
                          isDestructive: true,
                          onClick: function () {
                            removeProduct(index);
                          },
                        },
                        "×"
                      )
                    ),
                    // Loading indicator
                    isLoading &&
                      el(
                        "div",
                        {
                          style: {
                            display: "flex",
                            alignItems: "center",
                            gap: "8px",
                            padding: "8px 0",
                          },
                        },
                        el(Spinner, {}),
                        el(
                          "span",
                          { style: { fontSize: "12px", color: "#6b7280" } },
                          __("Loading product data...", "main")
                        )
                      ),
                    // Header with Logo and Badge
                    !isLoading &&
                      el(
                        "div",
                        {
                          className: "flex justify-between items-center gap-4",
                        },
                        logo
                          ? el(
                              "div",
                              {
                                className:
                                  "w-8 h-8 [&_img]:w-full [&_img]:h-auto",
                              },
                              el("img", {
                                src: logo,
                                alt: product.name,
                                width: "32",
                                height: "32",
                                loading: "lazy",
                                class: "m-0",
                              })
                            )
                          : el("div", {
                              className: "w-8 h-8",
                              style: {
                                background: "#f3f4f6",
                                borderRadius: "4px",
                              },
                            }),
                        renderBadge(product, index)
                      ),

                    // Product Info
                    !isLoading &&
                      el(
                        "div",
                        { className: "flex flex-col gap-1.5 md:gap-2" },
                        el(
                          "h3",
                          {
                            className:
                              "text-base md:text-xl font-semibold leading-6 md:leading-7 text-gray-800",
                          },
                          product.name
                        ),
                        customNote &&
                          el(
                            "div",
                            {
                              className:
                                "text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p md:tracking-1p text-gray-500 line-clamp-2",
                            },
                            customNote
                          ),
                        designType === "top-alternatives" &&
                          (product.readReviewText || product.readReviewUrl) &&
                          el(
                            "a",
                            {
                              href: product.readReviewUrl || permalink,
                              className:
                                "flex items-center gap-1 text-sm font-semibold leading-5 text-eva-prime-600 hover:text-primary transition-colors",
                              onClick: function (e) {
                                e.preventDefault();
                              },
                              style: { cursor: "text" },
                            },
                            el(RichText, {
                              tagName: "span",
                              value: product.readReviewText || "Read Review",
                              onChange: function (value) {
                                updateReadReviewText(index, value);
                              },
                              allowedFormats: [],
                              placeholder: __("Read Review", "main"),
                            }),
                            el(
                              "svg",
                              {
                                className: "w-4 h-4 flex-shrink-0",
                                xmlns: "http://www.w3.org/2000/svg",
                                width: "16",
                                height: "16",
                                fill: "none",
                                viewBox: "0 0 16 16",
                              },
                              el("path", {
                                stroke: "#e84300",
                                strokeLinecap: "round",
                                strokeLinejoin: "round",
                                strokeWidth: "1.5",
                                d: "M3.333 8h9.333M8.667 12l4-4M8.667 4l4 4",
                              })
                            )
                          )
                      )
                  );
                })
              )
        )
      );
    },

    save: function () {
      return null;
    },
  });
})(window.wp);
