(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var Button = wp.components.Button;
  var CheckboxControl = wp.components.CheckboxControl;
  var SelectControl = wp.components.SelectControl;
  var __ = wp.i18n.__;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;

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
    { value: "custom_note", label: "Custom Note" },
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
      }

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
              "No products selected. Use the sidebar to search and add products.",
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
                className:
                  "product-compare-table border-none w-full table-fixed",
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
                      widthStyle.maxWidth = columnWidth;
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
                        el(
                          "span",
                          {
                            style: {
                              flex: 1,
                              minWidth: "80px",
                            },
                          },
                          column.label
                        ),
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
                        widthStyle.maxWidth = columnWidth;
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
                          ? el(
                              "div",
                              { style: { fontSize: "14px", color: "#6b7280" } },
                              displayValue ||
                                el(
                                  "em",
                                  { style: { color: "#9ca3af" } },
                                  "Click to edit"
                                )
                            )
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
        el(
          InspectorControls,
          {},
          // Product Selection Panel
          el(
            PanelBody,
            { title: __("Product Selection", "main"), initialOpen: true },
            el(TextControl, {
              label: __("Search Products", "main"),
              value: searchTerm,
              onChange: function (value) {
                setSearchTerm(value);
                searchProducts(value);
              },
              placeholder: __("Type to search...", "main"),
              help: __("Type at least 2 characters to search", "main"),
            }),

            isSearching &&
              el(
                "p",
                {
                  style: {
                    fontStyle: "italic",
                    color: "#666",
                    fontSize: "12px",
                  },
                },
                __("Searching...", "main")
              ),

            searchResults.length > 0 &&
              el(
                "div",
                {
                  className: "product-search-results",
                  style: {
                    marginTop: "12px",
                    maxHeight: "200px",
                    overflowY: "auto",
                    border: "1px solid #ddd",
                    borderRadius: "4px",
                    padding: "4px",
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
                  style: { fontSize: "12px", color: "#666", marginTop: "8px" },
                },
                __("Type at least 2 characters to search", "main")
              ),

            products.length > 0 &&
              el(
                "div",
                { style: { marginTop: "16px" } },
                el(
                  "strong",
                  { style: { display: "block", marginBottom: "8px" } },
                  __("Selected Products:", "main")
                ),
                products.map(function (product, index) {
                  return el(
                    "div",
                    {
                      key: product.id,
                      style: {
                        padding: "8px",
                        background: "#f3f4f6",
                        borderRadius: "4px",
                        marginBottom: "4px",
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                      },
                    },
                    el("span", { style: { fontSize: "13px" } }, product.name),
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

          // Field Selection Panel
          el(
            PanelBody,
            {
              title: __("Select Fields to Display", "main"),
              initialOpen: true,
            },
            el(
              "p",
              {
                style: {
                  fontSize: "12px",
                  color: "#666",
                  marginBottom: "12px",
                },
              },
              __(
                "Select which product fields to display as columns in the table.",
                "main"
              )
            ),
            FIELD_OPTIONS.map(function (option) {
              return el(CheckboxControl, {
                key: option.value,
                label: option.label,
                checked: selectedFields.indexOf(option.value) > -1,
                onChange: function (checked) {
                  toggleField(option.value);
                },
              });
            }),

            columns.length > 0 &&
              el(
                "div",
                {
                  style: {
                    marginTop: "16px",
                    paddingTop: "16px",
                    borderTop: "1px solid #ddd",
                  },
                },
                el(
                  "strong",
                  { style: { display: "block", marginBottom: "8px" } },
                  __("Column Settings:", "main")
                ),
                columns.map(function (column, index) {
                  if (column.type === "custom") {
                    return null; // Custom columns handled in separate panel
                  }
                  return el(
                    "div",
                    {
                      key: column.field,
                      style: {
                        marginBottom: "16px",
                        padding: "12px",
                        background: "#f9fafb",
                        borderRadius: "6px",
                        border: "1px solid #e5e7eb",
                      },
                    },
                    el(TextControl, {
                      label: __("Column Label", "main"),
                      value: column.label,
                      onChange: function (value) {
                        updateColumnLabel(index, value);
                      },
                      style: { marginBottom: "8px" },
                    }),
                    el(TextControl, {
                      label: __("Width (px)", "main"),
                      value: column.width || "",
                      onChange: function (value) {
                        updateColumnWidth(index, value);
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

          // Custom Columns Panel
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
                  color: "#666",
                  marginBottom: "12px",
                },
              },
              __(
                "Add custom columns with manual data entry for each product. Use the ↑↓ arrows in the table header or sidebar to reorder columns.",
                "main"
              )
            ),
            el(
              Button,
              {
                variant: "primary",
                onClick: addCustomColumn,
                style: { marginBottom: "16px", width: "100%" },
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

                // Find all custom column indices to determine if we can move up/down
                var customColumnIndices = columns
                  .map(function (c, idx) {
                    return c.type === "custom" ? idx : -1;
                  })
                  .filter(function (idx) {
                    return idx !== -1;
                  });
                var isFirstCustom =
                  customColumnIndices.indexOf(actualIndex) === 0;
                var isLastCustom =
                  customColumnIndices.indexOf(actualIndex) ===
                  customColumnIndices.length - 1;

                return el(
                  "div",
                  {
                    key: column.id,
                    style: {
                      marginBottom: "20px",
                      padding: "12px",
                      background: "#f9fafb",
                      borderRadius: "6px",
                      border: "1px solid #e5e7eb",
                    },
                  },
                  el(
                    "div",
                    {
                      style: {
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "flex-start",
                        marginBottom: "12px",
                        gap: "8px",
                      },
                    },
                    el(
                      "div",
                      {
                        style: {
                          display: "flex",
                          flexDirection: "column",
                          gap: "4px",
                          marginRight: "4px",
                        },
                      },
                      el(
                        Button,
                        {
                          isSmall: true,
                          onClick: function () {
                            moveColumnUp(actualIndex);
                          },
                          disabled: actualIndex === 0,
                          style: {
                            minWidth: "auto",
                            padding: "4px 6px",
                            height: "24px",
                            lineHeight: "1",
                          },
                          title: __("Move column up", "main"),
                        },
                        "↑"
                      ),
                      el(
                        Button,
                        {
                          isSmall: true,
                          onClick: function () {
                            moveColumnDown(actualIndex);
                          },
                          disabled: actualIndex === columns.length - 1,
                          style: {
                            minWidth: "auto",
                            padding: "4px 6px",
                            height: "24px",
                            lineHeight: "1",
                          },
                          title: __("Move column down", "main"),
                        },
                        "↓"
                      )
                    ),
                    el(
                      "div",
                      { style: { flex: 1 } },
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
                    ),
                    el(
                      Button,
                      {
                        isSmall: true,
                        isDestructive: true,
                        onClick: function () {
                          removeColumn(actualIndex);
                        },
                        style: { marginLeft: "8px" },
                      },
                      __("Remove", "main")
                    )
                  ),
                  products.length > 0 &&
                    el(
                      "div",
                      { style: { marginTop: "12px" } },
                      el(
                        "strong",
                        {
                          style: {
                            display: "block",
                            marginBottom: "8px",
                            fontSize: "12px",
                          },
                        },
                        __("Values for each product:", "main")
                      ),
                      products.map(function (product) {
                        var currentValue =
                          (product.customValues &&
                            product.customValues[column.id]) ||
                          (column.values && column.values[product.id]) ||
                          "";

                        return el(
                          "div",
                          {
                            key: product.id,
                            style: { marginBottom: "8px" },
                          },
                          el(TextControl, {
                            label: product.name,
                            value: currentValue,
                            onChange: function (value) {
                              updateCustomColumnValue(
                                column.id,
                                product.id,
                                value
                              );
                            },
                            placeholder: __("Enter value...", "main"),
                          })
                        );
                      })
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

          // Last Column Configuration Panel
          el(
            PanelBody,
            {
              title: __("Last Column (Action Button)", "main"),
              initialOpen: false,
            },
            el(TextControl, {
              label: __("Button Text", "main"),
              value: lastColumnConfig.buttonText,
              onChange: function (value) {
                updateLastColumnConfig("buttonText", value);
              },
              placeholder: __("Try Now", "main"),
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

        // Table Preview
        renderTablePreview()
      );
    },

    save: function () {
      return null; // Rendered via PHP
    },
  });
})(window.wp);
