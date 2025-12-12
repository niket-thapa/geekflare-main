(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var RichText = wp.blockEditor.RichText;
  var TextControl = wp.components.TextControl;
  var Button = wp.components.Button;
  var CheckboxControl = wp.components.CheckboxControl;
  var SelectControl = wp.components.SelectControl;
  var Spinner = wp.components.Spinner;
  var __ = wp.i18n.__;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useRef = wp.element.useRef;

  // Available field options for columns
  var FIELD_OPTIONS = [
    { value: "tagline", label: "Tagline" },
    { value: "pricing_summary", label: "Pricing Summary" },
    { value: "our_rating", label: "Rating" },
    { value: "has_free_plan", label: "Free Plan" },
    { value: "has_free_trial", label: "Free Trial" },
    { value: "has_demo", label: "Demo" },
    { value: "open_source", label: "Open Source" },
    { value: "ai_powered", label: "AI-Powered" },
    { value: "award", label: "Award" },
    { value: "custom_note", label: "Product Description" },
  ];

  registerBlockType("main/summary-table", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var products = attributes.products || [];
      var selectedFields = attributes.selectedFields || [];
      var columns = attributes.columns || [];
      var lastColumnConfig = attributes.lastColumnConfig || {
        buttonText: "Try Now",
        urlSource: "affiliate",
        customUrl: "",
      };

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "main-summary-table-editor",
      });

      // Search state
      var searchState = useState("");
      var searchTerm = searchState[0];
      var setSearchTerm = searchState[1];

      var resultsState = useState([]);
      var searchResults = resultsState[0];
      var setSearchResults = resultsState[1];

      var searchingState = useState(false);
      var isSearching = searchingState[0];
      var setIsSearching = searchingState[1];

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

      // UI state for collapsible sections
      // Auto-show search when block is first added (no products yet)
      var showSearchState = useState(products.length === 0);
      var showSearch = showSearchState[0];
      var setShowSearch = showSearchState[1];

      // Ref for search input to auto-focus
      var searchInputId =
        "summary-table-search-input-" + (props.clientId || "default");
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

      var showFieldsState = useState(false);
      var showFields = showFieldsState[0];
      var setShowFields = showFieldsState[1];

      var showCustomColumnsState = useState(false);
      var showCustomColumns = showCustomColumnsState[0];
      var setShowCustomColumns = showCustomColumnsState[1];

      var showLastColumnState = useState(false);
      var showLastColumn = showLastColumnState[0];
      var setShowLastColumn = showLastColumnState[1];

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

      // Helper function to get default width for a field
      function getDefaultColumnWidth(field) {
        if (field === "tagline") {
          return "180px";
        } else if (field === "custom_note") {
          return "200px";
        }
        return "120px";
      }

      // Initialize columns from selectedFields if columns is empty
      // Only run on initial mount, not on every selectedFields change
      useEffect(
        function () {
          if (columns.length === 0 && selectedFields.length > 0) {
            var newColumns = selectedFields.map(function (field) {
              var fieldOption = FIELD_OPTIONS.find(function (opt) {
                return opt.value === field;
              });
              return {
                type: "field",
                field: field,
                label: fieldOption ? fieldOption.label : field,
                width: getDefaultColumnWidth(field),
              };
            });
            setAttributes({ columns: newColumns });
          }
        },
        [] // Only run once on mount
      );

      // Search products function
      function searchProducts(term) {
        if (!term || term.length < 2) {
          setSearchResults([]);
          return;
        }

        setIsSearching(true);

        apiFetch({
          path:
            "/wp/v2/products?search=" +
            encodeURIComponent(term) +
            "&per_page=20",
        })
          .then(function (results) {
            setSearchResults(results);
            setIsSearching(false);
          })
          .catch(function (error) {
            setIsSearching(false);
            setSearchResults([]);
          });
      }

      // Add product
      function addProduct(product) {
        var newProducts = products.slice();
        var exists = newProducts.some(function (p) {
          return p.id === product.id;
        });

        if (!exists) {
          // Initialize custom column values for this product
          var customColumnValues = {};
          columns.forEach(function (col) {
            if (col.type === "custom" && col.id) {
              customColumnValues[col.id] = "";
              // Also update the column's values object
              if (!col.values) {
                col.values = {};
              }
              col.values[product.id] = "";
            }
          });

          newProducts.push({
            id: product.id,
            name: product.title.rendered,
            customValues: customColumnValues,
          });

          // Update columns to include this product in their values
          var newColumns = columns.map(function (col) {
            if (col.type === "custom" && col.id) {
              var newCol = Object.assign({}, col);
              if (!newCol.values) {
                newCol.values = {};
              }
              newCol.values = Object.assign({}, newCol.values);
              if (!newCol.values[product.id]) {
                newCol.values[product.id] = "";
              }
              return newCol;
            }
            return col;
          });

          setAttributes({
            products: newProducts,
            columns: newColumns,
          });
        }

        setSearchTerm("");
        setSearchResults([]);
        setShowSearch(false);
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
                // Initialize custom column values for this product
                var customColumnValues = {};
                columns.forEach(function (col) {
                  if (col.type === "custom" && col.id) {
                    customColumnValues[col.id] = "";
                  }
                });

                newProducts.push({
                  id: product.id,
                  name: product.title
                    ? product.title
                    : product.name || "Product",
                  customValues: customColumnValues,
                });
              }
            });

            // Update columns to include new products in their values
            var newColumns = columns.map(function (col) {
              if (col.type === "custom" && col.id) {
                var newCol = Object.assign({}, col);
                if (!newCol.values) {
                  newCol.values = {};
                }
                newCol.values = Object.assign({}, newCol.values);
                newProducts.forEach(function (product) {
                  if (!newCol.values[product.id]) {
                    newCol.values[product.id] = "";
                  }
                });
                return newCol;
              }
              return col;
            });

            setAttributes({
              products: newProducts,
              columns: newColumns,
            });
            setSelectedCategory("");
            setIsLoadingProducts(false);
          })
          .catch(function (error) {
            console.error("Error fetching products by category:", error);
            setIsLoadingProducts(false);
          });
      };

      // Remove product
      function removeProduct(index) {
        var newProducts = products.slice();
        newProducts.splice(index, 1);
        setAttributes({ products: newProducts });
      }

      // Toggle field selection
      function toggleField(fieldValue) {
        var newSelectedFields = selectedFields.slice();
        var index = newSelectedFields.indexOf(fieldValue);

        if (index > -1) {
          // Unchecking - remove from selectedFields and columns
          newSelectedFields.splice(index, 1);
          // Remove from columns too - make sure we remove ALL instances of this field
          var newColumns = columns.filter(function (col) {
            // Keep custom columns and field columns that don't match this field
            return col.type !== "field" || col.field !== fieldValue;
          });
          setAttributes({
            selectedFields: newSelectedFields,
            columns: newColumns,
          });
        } else {
          // Checking - add to selectedFields and columns (only if not already exists)
          // First check if column already exists in columns array
          var columnExists = columns.some(function (col) {
            return col.type === "field" && col.field === fieldValue;
          });

          if (!columnExists) {
            // Column doesn't exist, add it
            newSelectedFields.push(fieldValue);
            var fieldOption = FIELD_OPTIONS.find(function (opt) {
              return opt.value === fieldValue;
            });
            var newColumns = columns.slice();
            newColumns.push({
              type: "field",
              field: fieldValue,
              label: fieldOption ? fieldOption.label : fieldValue,
              width: getDefaultColumnWidth(fieldValue),
            });
            setAttributes({
              selectedFields: newSelectedFields,
              columns: newColumns,
            });
          } else {
            // Column already exists in columns but not in selectedFields
            // This shouldn't happen, but sync it anyway
            newSelectedFields.push(fieldValue);
            setAttributes({
              selectedFields: newSelectedFields,
            });
          }
        }
      }

      // Add custom column
      function addCustomColumn() {
        var customColumnId = "custom_" + Date.now();
        var newColumns = columns.slice();
        var initialValues = {};

        // Initialize values for all products
        products.forEach(function (product) {
          initialValues[product.id] = "";
        });

        newColumns.push({
          type: "custom",
          id: customColumnId,
          label: "Custom Column",
          values: initialValues,
          width: "120px",
        });

        // Also initialize values in products if they don't exist
        var newProducts = products.map(function (product) {
          var newProduct = Object.assign({}, product);
          if (!newProduct.customValues) {
            newProduct.customValues = {};
          }
          newProduct.customValues = Object.assign({}, newProduct.customValues);
          if (!newProduct.customValues[customColumnId]) {
            newProduct.customValues[customColumnId] = "";
          }
          return newProduct;
        });

        setAttributes({
          columns: newColumns,
          products: newProducts,
        });
      }

      // Update custom column value for a product
      function updateCustomColumnValue(columnId, productId, value) {
        var newColumns = columns.map(function (col) {
          if (col.type === "custom" && col.id === columnId) {
            var newCol = Object.assign({}, col);
            if (!newCol.values) {
              newCol.values = {};
            }
            newCol.values = Object.assign({}, newCol.values);
            newCol.values[productId] = value;
            return newCol;
          }
          return col;
        });

        // Also update in products
        var newProducts = products.map(function (product) {
          if (product.id === productId) {
            var newProduct = Object.assign({}, product);
            if (!newProduct.customValues) {
              newProduct.customValues = {};
            }
            newProduct.customValues = Object.assign(
              {},
              newProduct.customValues
            );
            newProduct.customValues[columnId] = value;
            return newProduct;
          }
          return product;
        });

        setAttributes({
          columns: newColumns,
          products: newProducts,
        });
      }

      // Update column label
      function updateColumnLabel(index, label) {
        var newColumns = columns.slice();
        newColumns[index].label = label;
        setAttributes({ columns: newColumns });
      }

      // Update column width
      function updateColumnWidth(index, width) {
        var newColumns = columns.slice();
        if (!newColumns[index]) {
          return;
        }
        // Remove width if empty, otherwise set it
        if (!width || width.trim() === "") {
          delete newColumns[index].width;
        } else {
          var widthValue = width.trim();
          // Remove any existing "px" suffix to normalize
          widthValue = widthValue.replace(/px$/i, "");

          // If it's a valid number (integer or decimal), add px
          if (/^\d+(\.\d+)?$/.test(widthValue)) {
            widthValue = widthValue + "px";
            newColumns[index].width = widthValue;
          } else {
            // Invalid format, don't update
            return;
          }
        }
        setAttributes({ columns: newColumns });
      }

      // Remove column
      function removeColumn(index) {
        var newColumns = columns.slice();
        var removedColumn = newColumns[index];
        newColumns.splice(index, 1);

        if (removedColumn && removedColumn.type === "field") {
          // Remove from selectedFields too - remove ALL instances
          var newSelectedFields = selectedFields.filter(function (field) {
            return field !== removedColumn.field;
          });
          setAttributes({
            columns: newColumns,
            selectedFields: newSelectedFields,
          });
        } else {
          // For custom columns, just remove the column
          setAttributes({
            columns: newColumns,
          });
        }
      }

      // Move column up
      function moveColumnUp(index) {
        if (index === 0) {
          return; // Already at the top
        }
        var newColumns = columns.slice();
        var temp = newColumns[index];
        newColumns[index] = newColumns[index - 1];
        newColumns[index - 1] = temp;
        setAttributes({ columns: newColumns });
      }

      // Move column down
      function moveColumnDown(index) {
        if (index === columns.length - 1) {
          return; // Already at the bottom
        }
        var newColumns = columns.slice();
        var temp = newColumns[index];
        newColumns[index] = newColumns[index + 1];
        newColumns[index + 1] = temp;
        setAttributes({ columns: newColumns });
      }

      // Add column (insert after index)
      function addColumnAfter(index) {
        // This will be handled by selecting a new field
        // For now, we'll just show a message or allow reordering
      }

      // Update last column config
      function updateLastColumnConfig(key, value) {
        var newConfig = Object.assign({}, lastColumnConfig);
        newConfig[key] = value;
        setAttributes({ lastColumnConfig: newConfig });
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

      // Render table preview
      function renderTablePreview() {
        if (products.length === 0) {
          return el(
            "div",
            {
              className: "summary-table-placeholder",
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
              "No products selected. Use the search above to add products.",
              "main"
            )
          );
        }

        return el(
          "div",
          { className: "summary-table-preview-wrapper" },
          el(
            "div",
            {
              className:
                "overflow-auto rounded-2xl md:rounded-3xl border border-gray-200",
              style: { margin: "20px 0" },
            },
            el(
              "table",
              {
                className: "product-compare-table border-none w-full m-0",
                style: { borderCollapse: "collapse" },
              },
              el(
                "thead",
                {},
                el(
                  "tr",
                  {},
                  // First column - Product (fixed)
                  el(
                    "th",
                    {
                      className:
                        "bg-gray-100 whitespace-nowrap text-left p-4 text-xs font-bold text-gray-500 uppercase",
                      style: { border: "1px solid #e5e7eb" },
                    },
                    "Product"
                  ),
                  // Dynamic columns
                  columns.map(function (column, colIndex) {
                    var columnWidth = column.width || "auto";
                    var widthStyle = {};
                    if (columnWidth && columnWidth !== "auto") {
                      widthStyle.width = columnWidth;
                      widthStyle.minWidth = columnWidth;
                    }
                    return el(
                      "th",
                      {
                        key:
                          column.type === "custom" ? column.id : column.field,
                        className:
                          "bg-gray-100 whitespace-nowrap text-left p-4 text-xs font-bold text-gray-500 uppercase",
                        style: Object.assign(
                          {
                            border: "1px solid #e5e7eb",
                            position: "relative",
                          },
                          widthStyle
                        ),
                      },
                      el(
                        "div",
                        {
                          style: {
                            display: "flex",
                            alignItems: "center",
                            gap: "4px",
                            flexWrap: "wrap",
                          },
                        },
                        el(
                          "div",
                          {
                            style: {
                              display: "flex",
                              flexDirection: "column",
                              gap: "2px",
                              marginRight: "4px",
                            },
                          },
                          el(
                            Button,
                            {
                              isSmall: true,
                              onClick: function () {
                                moveColumnUp(colIndex);
                              },
                              disabled: colIndex === 0,
                              style: {
                                minWidth: "auto",
                                padding: "2px 4px",
                                height: "16px",
                                lineHeight: "1",
                                fontSize: "10px",
                              },
                              title: __("Move up", "main"),
                            },
                            "↑"
                          ),
                          el(
                            Button,
                            {
                              isSmall: true,
                              onClick: function () {
                                moveColumnDown(colIndex);
                              },
                              disabled: colIndex === columns.length - 1,
                              style: {
                                minWidth: "auto",
                                padding: "2px 4px",
                                height: "16px",
                                lineHeight: "1",
                                fontSize: "10px",
                              },
                              title: __("Move down", "main"),
                            },
                            "↓"
                          )
                        ),
                        el(RichText, {
                          tagName: "span",
                          value: column.label,
                          onChange: function (value) {
                            updateColumnLabel(colIndex, value);
                          },
                          placeholder: __("Column label...", "main"),
                          allowedFormats: [],
                          style: {
                            flex: 1,
                            minWidth: "80px",
                            cursor: "text",
                            display: "inline-block",
                          },
                          className: "column-label-editable",
                        }),
                        column.type === "custom" &&
                          el(
                            "span",
                            {
                              style: {
                                fontSize: "10px",
                                color: "#6b7280",
                                fontWeight: "normal",
                              },
                            },
                            "(Custom)"
                          ),
                        el(
                          Button,
                          {
                            isSmall: true,
                            isDestructive: true,
                            onClick: function () {
                              removeColumn(colIndex);
                            },
                            style: {
                              minWidth: "auto",
                              padding: "2px 6px",
                              height: "20px",
                              fontSize: "10px",
                            },
                            title: __("Remove column", "main"),
                          },
                          "×"
                        )
                      )
                    );
                  }),
                  // Last column - Action (fixed)
                  el(
                    "th",
                    {
                      className:
                        "bg-gray-100 whitespace-nowrap text-left p-4 text-xs font-bold text-gray-500 uppercase",
                      style: { border: "1px solid #e5e7eb" },
                    },
                    lastColumnConfig.buttonText || "Action"
                  )
                )
              ),
              el(
                "tbody",
                {},
                products.map(function (product, rowIndex) {
                  return el(
                    "tr",
                    { key: product.id },
                    // First column - Product
                    el(
                      "td",
                      {
                        className: "p-0",
                        style: { border: "1px solid #e5e7eb" },
                      },
                      el(
                        "div",
                        {
                          className: "flex gap-2 items-center px-4 py-3",
                        },
                        el(
                          "div",
                          { style: { fontWeight: "600" } },
                          product.name
                        )
                      )
                    ),
                    // Dynamic columns
                    columns.map(function (column) {
                      var displayValue = "";
                      if (column.type === "custom") {
                        // Get custom column value for this product
                        displayValue =
                          (product.customValues &&
                            product.customValues[column.id]) ||
                          (column.values && column.values[product.id]) ||
                          "";
                      } else {
                        // Field-based column - show placeholder
                        displayValue = "[" + column.label + "]";
                      }

                      var columnWidth = column.width || "auto";
                      var widthStyle = {};
                      if (columnWidth && columnWidth !== "auto") {
                        widthStyle.width = columnWidth;
                        widthStyle.minWidth = columnWidth;
                      }

                      return el(
                        "td",
                        {
                          key:
                            column.type === "custom" ? column.id : column.field,
                          className: "p-0",
                          style: Object.assign(
                            {
                              border: "1px solid #e5e7eb",
                              padding: "12px",
                            },
                            widthStyle
                          ),
                        },
                        column.type === "custom"
                          ? el(RichText, {
                              tagName: "div",
                              value: displayValue,
                              onChange: function (value) {
                                updateCustomColumnValue(
                                  column.id,
                                  product.id,
                                  value
                                );
                              },
                              placeholder: __("Click to edit...", "main"),
                              allowedFormats: [],
                              style: {
                                fontSize: "14px",
                                color: "#6b7280",
                                minHeight: "20px",
                                cursor: "text",
                              },
                              className: "custom-column-editable",
                            })
                          : el(
                              "div",
                              { style: { fontSize: "14px", color: "#6b7280" } },
                              displayValue
                            )
                      );
                    }),
                    // Last column - Action
                    el(
                      "td",
                      {
                        className: "p-0",
                        style: { border: "1px solid #e5e7eb" },
                      },
                      el(
                        "div",
                        {
                          className: "px-4 py-3",
                        },
                        el(
                          "a",
                          {
                            href: "#",
                            className: "btn btn--primary rounded-full",
                            style: {
                              display: "inline-block",
                              padding: "8px 16px",
                              fontSize: "14px",
                            },
                          },
                          lastColumnConfig.buttonText || "Try Now"
                        )
                      )
                    )
                  );
                })
              )
            )
          )
        );
      }

      return el(
        "div",
        blockProps,
        // Sidebar Block Settings
        el(
          InspectorControls,
          {},
          // Field Selection in Sidebar
          el(
            PanelBody,
            {
              title: __("Select Fields", "main"),
              initialOpen: false,
            },
            el(
              "p",
              {
                style: {
                  fontSize: "12px",
                  color: "#6b7280",
                  marginBottom: "12px",
                },
              },
              __(
                "Select which product fields to display as columns in the table.",
                "main"
              )
            ),
            el(
              "div",
              {
                style: {
                  display: "flex",
                  flexDirection: "column",
                  gap: "8px",
                },
              },
              FIELD_OPTIONS.map(function (option) {
                return el(CheckboxControl, {
                  key: option.value,
                  label: option.label,
                  checked: selectedFields.indexOf(option.value) > -1,
                  onChange: function (checked) {
                    toggleField(option.value);
                  },
                });
              })
            ),
            columns.length > 0 &&
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
                  __("Column Settings:", "main")
                ),
                columns
                  .filter(function (col) {
                    return col.type !== "custom";
                  })
                  .map(function (column, index) {
                    // Find actual index in full columns array
                    var actualIndex = columns.findIndex(function (c) {
                      return c.type === "field" && c.field === column.field;
                    });
                    return el(
                      "div",
                      {
                        key: column.field,
                        style: {
                          marginBottom: "12px",
                          padding: "12px",
                          background: "#fff",
                          borderRadius: "6px",
                          border: "1px solid #e5e7eb",
                        },
                      },
                      el(TextControl, {
                        label: __("Column Label", "main"),
                        value: column.label,
                        onChange: function (value) {
                          updateColumnLabel(actualIndex, value);
                        },
                        style: { marginBottom: "8px" },
                      }),
                      el(TextControl, {
                        label: __("Width (px)", "main"),
                        value: column.width || "",
                        onChange: function (value) {
                          updateColumnWidth(actualIndex, value);
                        },
                        placeholder: __("e.g., 200 or 200px", "main"),
                        help: __(
                          "Set column width in pixels. Enter number only (e.g., 200) or with px (e.g., 200px). Leave empty for auto.",
                          "main"
                        ),
                      })
                    );
                  })
              )
          ),
          // Custom Columns in Sidebar
          el(
            PanelBody,
            {
              title: __("Custom Columns", "main"),
              initialOpen: false,
            },
            el(
              "p",
              {
                style: {
                  fontSize: "12px",
                  color: "#6b7280",
                  marginBottom: "12px",
                },
              },
              __(
                "Add custom columns with manual data entry for each product.",
                "main"
              )
            ),
            el(
              Button,
              {
                variant: "primary",
                onClick: addCustomColumn,
                isSmall: true,
                style: { marginBottom: "16px" },
              },
              __("+ Add Custom Column", "main")
            ),
            columns
              .filter(function (col) {
                return col.type === "custom";
              })
              .map(function (column, colIndex) {
                // Find the actual index in the full columns array
                var actualIndex = columns.findIndex(function (c) {
                  return c.type === "custom" && c.id === column.id;
                });

                return el(
                  "div",
                  {
                    key: column.id,
                    style: {
                      marginBottom: "16px",
                      padding: "12px",
                      background: "#fff",
                      borderRadius: "6px",
                      border: "1px solid #e5e7eb",
                    },
                  },
                  el(TextControl, {
                    label: __("Column Label", "main"),
                    value: column.label,
                    onChange: function (value) {
                      updateColumnLabel(actualIndex, value);
                    },
                    style: { marginBottom: "8px" },
                  }),
                  el(TextControl, {
                    label: __("Width (px)", "main"),
                    value: column.width || "",
                    onChange: function (value) {
                      updateColumnWidth(actualIndex, value);
                    },
                    placeholder: __("e.g., 200 or 200px", "main"),
                    help: __(
                      "Set column width in pixels. Enter number only (e.g., 200) or with px (e.g., 200px). Leave empty for auto.",
                      "main"
                    ),
                    style: { marginBottom: "8px" },
                  }),
                  el(
                    Button,
                    {
                      isSmall: true,
                      isDestructive: true,
                      onClick: function () {
                        removeColumn(actualIndex);
                      },
                    },
                    __("Remove Column", "main")
                  )
                );
              }),
            columns.filter(function (col) {
              return col.type === "custom";
            }).length === 0 &&
              el(
                "p",
                {
                  style: {
                    fontSize: "12px",
                    color: "#9ca3af",
                    fontStyle: "italic",
                    textAlign: "center",
                    padding: "20px",
                  },
                },
                __("No custom columns yet. Click above to add one.", "main")
              )
          ),
          // Last Column Configuration in Sidebar
          el(
            PanelBody,
            {
              title: __("Last Column (Action)", "main"),
              initialOpen: false,
            },
            el(TextControl, {
              label: __("Button Text", "main"),
              value: lastColumnConfig.buttonText,
              onChange: function (value) {
                updateLastColumnConfig("buttonText", value);
              },
              placeholder: __("Try Now", "main"),
              style: { marginBottom: "12px" },
            }),
            el(SelectControl, {
              label: __("URL Source", "main"),
              value: lastColumnConfig.urlSource,
              options: [
                {
                  label: __("Affiliate / CTA URL from CPT", "main"),
                  value: "affiliate",
                },
                { label: __("Website URL from CPT", "main"), value: "website" },
                { label: __("Custom URL", "main"), value: "custom" },
              ],
              onChange: function (value) {
                updateLastColumnConfig("urlSource", value);
              },
              style: { marginBottom: "12px" },
            }),
            lastColumnConfig.urlSource === "custom" &&
              el(TextControl, {
                label: __("Custom URL", "main"),
                value: lastColumnConfig.customUrl,
                onChange: function (value) {
                  updateLastColumnConfig("customUrl", value);
                },
                placeholder: __("https://example.com", "main"),
                type: "url",
              })
          )
        ),
        // Main Content Area - Product Search and Table Preview
        el(
          "div",
          {
            className: "summary-table-main-content",
            style: {
              padding: "20px 0",
            },
          },
          // Product Search in Main Area
          el(
            "div",
            {
              style: {
                marginBottom: "20px",
                padding: "16px",
                background: "#f9fafb",
                borderRadius: "8px",
                border: "1px solid #e5e7eb",
              },
            },
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
              el(
                "div",
                {
                  style: {
                    marginBottom: "12px",
                  },
                },
                el(
                  "h3",
                  {
                    style: {
                      fontSize: "14px",
                      fontWeight: "600",
                      color: "#374151",
                      marginBottom: "8px",
                    },
                  },
                  __("Add Products", "main")
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

              el(TextControl, {
                label: __("Search Products", "main"),
                value: searchTerm,
                onChange: function (value) {
                  setSearchTerm(value);
                  searchProducts(value);
                },
                placeholder: __("Type to search...", "main"),
                help: __("Type at least 2 characters", "main"),
                style: { marginBottom: "12px" },
              }),
              isSearching &&
                el(
                  "p",
                  {
                    style: {
                      fontStyle: "italic",
                      color: "#666",
                      fontSize: "12px",
                      marginBottom: "8px",
                    },
                  },
                  __("Searching...", "main")
                ),
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
                      marginBottom: "12px",
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
                    __("Selected Products:", "main") + " " + products.length
                  ),
                  products.map(function (product, index) {
                    return el(
                      "div",
                      {
                        key: product.id,
                        style: {
                          padding: "8px",
                          background: "#fff",
                          borderRadius: "4px",
                          marginBottom: "6px",
                          border: "1px solid #e5e7eb",
                          display: "flex",
                          justifyContent: "space-between",
                          alignItems: "center",
                          fontSize: "13px",
                        },
                      },
                      el("span", {}, product.name),
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
            )
          ),
          // Table Preview
          renderTablePreview()
        )
      );
    },

    save: function () {
      return null; // Rendered via PHP
    },
  });
})(window.wp);
