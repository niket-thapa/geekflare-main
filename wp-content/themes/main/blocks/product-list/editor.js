(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var InnerBlocks = wp.blockEditor.InnerBlocks;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var SelectControl = wp.components.SelectControl;
  var Button = wp.components.Button;
  var Spinner = wp.components.Spinner;
  var TextControl = wp.components.TextControl;
  var __ = wp.i18n.__;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;

  var ALLOWED_BLOCKS = ["main/product-item"];

  registerBlockType("main/product-list", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var clientId = props.clientId;

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className:
          "product-list-editor flex flex-col gap-7.5 pb-8 md:pb-12 lg:pb-20",
      });

      // State for category selection and products
      var categoriesState = useState([]);
      var categories = categoriesState[0];
      var setCategories = categoriesState[1];

      var selectedCategoryState = useState("");
      var selectedCategory = selectedCategoryState[0];
      var setSelectedCategory = selectedCategoryState[1];

      var searchQueryState = useState("");
      var searchQuery = searchQueryState[0];
      var setSearchQuery = searchQueryState[1];

      var searchResultsState = useState([]);
      var searchResults = searchResultsState[0];
      var setSearchResults = searchResultsState[1];

      var isLoadingCategoriesState = useState(false);
      var isLoadingCategories = isLoadingCategoriesState[0];
      var setIsLoadingCategories = isLoadingCategoriesState[1];

      var isLoadingProductsState = useState(false);
      var isLoadingProducts = isLoadingProductsState[0];
      var setIsLoadingProducts = isLoadingProductsState[1];

      var isSearchingState = useState(false);
      var isSearching = isSearchingState[0];
      var setIsSearching = isSearchingState[1];

      // Get dispatch functions
      var dispatch = useDispatch("core/block-editor");
      var insertBlocks = dispatch ? dispatch.insertBlocks : null;
      var removeBlock = dispatch ? dispatch.removeBlock : null;

      var getBlock = useSelect
        ? useSelect(function (select) {
            return select("core/block-editor").getBlock;
          }, [])
        : null;

      // Get existing product blocks
      var productBlocks = useSelect(
        function (select) {
          var blockEditor = select("core/block-editor");
          if (!blockEditor) return [];

          var block = blockEditor.getBlock(clientId);
          if (!block || !block.innerBlocks) return [];

          return block.innerBlocks;
        },
        [clientId]
      );

      // Get existing product IDs to avoid duplicates
      var existingProductIds = productBlocks
        .map(function (innerBlock) {
          return innerBlock.attributes && innerBlock.attributes.productId
            ? innerBlock.attributes.productId
            : 0;
        })
        .filter(function (id) {
          return id > 0;
        });

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

      // Live search effect - triggers when searchQuery changes
      useEffect(
        function () {
          if (!searchQuery || searchQuery.length < 2) {
            setSearchResults([]);
            setIsSearching(false);
            return;
          }

          setIsSearching(true);

          // Debounce search
          var timer = setTimeout(function () {
            apiFetch({
              path:
                "/wp/v2/products?search=" +
                encodeURIComponent(searchQuery) +
                "&per_page=20",
            })
              .then(function (results) {
                setSearchResults(results || []);
                setIsSearching(false);
              })
              .catch(function (error) {
                console.error("Error searching products:", error);
                setSearchResults([]);
                setIsSearching(false);
              });
          }, 300); // 300ms debounce

          return function () {
            clearTimeout(timer);
          };
        },
        [searchQuery]
      );

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
          .then(function (products) {
            if (
              !products ||
              !Array.isArray(products) ||
              products.length === 0
            ) {
              setIsLoadingProducts(false);
              return;
            }

            var currentBlock = getBlock ? getBlock(clientId) : null;
            if (!currentBlock || !insertBlocks) {
              setIsLoadingProducts(false);
              return;
            }

            var newProducts = products.filter(function (product) {
              return (
                product &&
                product.id &&
                existingProductIds.indexOf(parseInt(product.id, 10)) === -1
              );
            });

            if (newProducts.length === 0) {
              setIsLoadingProducts(false);
              return;
            }

            var blocksToInsert = newProducts.map(function (product) {
              return wp.blocks.createBlock("main/product-item", {
                productId: product.id,
              });
            });

            insertBlocks(
              blocksToInsert,
              currentBlock.innerBlocks.length,
              clientId
            );

            setSelectedCategory("");
            setIsLoadingProducts(false);
          })
          .catch(function (error) {
            console.error("Error fetching products by category:", error);
            setIsLoadingProducts(false);
          });
      };

      // Handle adding a single product from search
      var handleAddProduct = function (productId) {
        if (existingProductIds.indexOf(parseInt(productId, 10)) !== -1) {
          return; // Already added
        }

        var currentBlock = getBlock ? getBlock(clientId) : null;
        if (!currentBlock || !insertBlocks) return;

        var newBlock = wp.blocks.createBlock("main/product-item", {
          productId: productId,
        });

        insertBlocks([newBlock], currentBlock.innerBlocks.length, clientId);

        // Clear search
        setSearchQuery("");
        setSearchResults([]);
      };

      // Handle removing a product
      var handleRemoveProduct = function (blockId) {
        if (removeBlock) {
          removeBlock(blockId);
        }
      };

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
        // Bulk Add Section
        el(
          "div",
          {
            className: "bulk-add-section",
            style: {
              backgroundColor: "#f0f0f1",
              padding: "16px",
              borderRadius: "4px",
              marginBottom: "20px",
              border: "1px solid #dcdcde",
            },
          },
          el(
            "h3",
            {
              style: {
                margin: "0 0 12px 0",
                fontSize: "14px",
                fontWeight: "600",
                color: "#1e1e1e",
              },
            },
            __("Add Products", "main")
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

          // Search Section (Live AJAX Search)
          el(
            "div",
            { style: { borderTop: "1px solid #dcdcde", paddingTop: "16px" } },
            el(
              "label",
              {
                style: {
                  display: "block",
                  marginBottom: "8px",
                  fontSize: "11px",
                  fontWeight: "500",
                  textTransform: "uppercase",
                  color: "#1e1e1e",
                },
              },
              __("Search Products", "main")
            ),
            el(
              "div",
              { style: { position: "relative" } },
              el(TextControl, {
                value: searchQuery,
                onChange: setSearchQuery,
                placeholder: __("Type to search products...", "main"),
                style: { marginBottom: "0" },
              }),
              isSearching &&
                el(
                  "div",
                  {
                    style: {
                      position: "absolute",
                      right: "8px",
                      top: "50%",
                      transform: "translateY(-50%)",
                    },
                  },
                  el(Spinner, {})
                )
            ),

            // Search Results Dropdown (Click item to add)
            searchResults.length > 0 &&
              el(
                "div",
                {
                  style: {
                    marginTop: "4px",
                    maxHeight: "250px",
                    overflowY: "auto",
                    border: "1px solid #dcdcde",
                    borderRadius: "4px",
                    backgroundColor: "#fff",
                    boxShadow: "0 2px 6px rgba(0,0,0,0.1)",
                  },
                },
                searchResults.map(function (product) {
                  var isAdded = existingProductIds.indexOf(product.id) !== -1;
                  return el(
                    "div",
                    {
                      key: product.id,
                      onClick: function () {
                        if (!isAdded) handleAddProduct(product.id);
                      },
                      style: {
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                        padding: "10px 12px",
                        borderBottom: "1px solid #f0f0f1",
                        cursor: isAdded ? "not-allowed" : "pointer",
                        transition: "background-color 0.2s",
                        opacity: isAdded ? 0.6 : 1,
                      },
                      onMouseEnter: function (e) {
                        if (!isAdded) {
                          e.currentTarget.style.backgroundColor = "#f6f7f7";
                        }
                      },
                      onMouseLeave: function (e) {
                        e.currentTarget.style.backgroundColor = "transparent";
                      },
                    },
                    el(
                      "span",
                      { style: { fontSize: "13px", flex: 1 } },
                      product.title.rendered
                    ),
                    isAdded &&
                      el(
                        "span",
                        {
                          style: {
                            fontSize: "12px",
                            color: "#757575",
                            fontStyle: "italic",
                          },
                        },
                        __("Added", "main")
                      )
                  );
                })
              ),

            // Minimum character message
            searchQuery &&
              searchQuery.length > 0 &&
              searchQuery.length < 2 &&
              el(
                "div",
                {
                  style: {
                    marginTop: "8px",
                    padding: "8px 12px",
                    backgroundColor: "#f9fafb",
                    border: "1px solid #e5e7eb",
                    borderRadius: "4px",
                    fontSize: "12px",
                    color: "#6b7280",
                    fontStyle: "italic",
                  },
                },
                __("Type at least 2 characters to search", "main")
              ),

            // No results message
            searchQuery &&
              searchQuery.length >= 2 &&
              !isSearching &&
              searchResults.length === 0 &&
              el(
                "div",
                {
                  style: {
                    marginTop: "8px",
                    padding: "12px",
                    backgroundColor: "#fff",
                    border: "1px solid #dcdcde",
                    borderRadius: "4px",
                    fontSize: "13px",
                    color: "#757575",
                    textAlign: "center",
                  },
                },
                __("No products found matching your search.", "main")
              )
          )
        ),

        // Added Products List with Remove Option
        productBlocks.length > 0 &&
          el(
            "div",
            {
              style: {
                backgroundColor: "#fff",
                border: "1px solid #dcdcde",
                borderRadius: "4px",
                padding: "16px",
                marginBottom: "20px",
              },
            },
            el(
              "h4",
              {
                style: {
                  margin: "0 0 12px 0",
                  fontSize: "13px",
                  fontWeight: "600",
                  color: "#1e1e1e",
                },
              },
              __("Added Products (" + productBlocks.length + ")", "main")
            ),
            el(
              "div",
              {
                style: { display: "flex", flexDirection: "column", gap: "8px" },
              },
              productBlocks.map(function (block) {
                return el(
                  "div",
                  {
                    key: block.clientId,
                    style: {
                      display: "flex",
                      justifyContent: "space-between",
                      alignItems: "center",
                      padding: "8px 12px",
                      backgroundColor: "#f0f0f1",
                      borderRadius: "4px",
                      fontSize: "13px",
                    },
                  },
                  el(
                    "span",
                    {},
                    __("Product ID: ", "main") +
                      (block.attributes.productId || "N/A")
                  ),
                  el(
                    Button,
                    {
                      isDestructive: true,
                      isSmall: true,
                      onClick: function () {
                        handleRemoveProduct(block.clientId);
                      },
                    },
                    __("Remove", "main")
                  )
                );
              })
            )
          ),

        // Products Preview Section
        el(
          "div",
          {
            className: "products-preview-section",
            style: {
              border: "2px dashed #dcdcde",
              borderRadius: "4px",
              padding: "20px",
              backgroundColor: "#fafafa",
            },
          },
          el(
            "div",
            {
              style: {
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                marginBottom: "16px",
                paddingBottom: "12px",
                borderBottom: "1px solid #dcdcde",
              },
            },
            el(
              "h4",
              {
                style: {
                  margin: "0",
                  fontSize: "14px",
                  fontWeight: "600",
                  color: "#1e1e1e",
                },
              },
              __("Products Preview", "main")
            ),
            productBlocks.length === 0 &&
              el(
                "span",
                {
                  style: {
                    fontSize: "12px",
                    color: "#757575",
                    fontStyle: "italic",
                  },
                },
                __("Add products using the controls above", "main")
              )
          ),

          // InnerBlocks - Visible for preview
          el(
            "div",
            {
              className: "product-list-inner-blocks",
              style: {
                minHeight: productBlocks.length === 0 ? "100px" : "auto",
              },
            },
            el(InnerBlocks, {
              allowedBlocks: ALLOWED_BLOCKS,
              templateLock: false,
              renderAppender: false,
            })
          ),

          // Empty state
          productBlocks.length === 0 &&
            el(
              "div",
              {
                style: {
                  textAlign: "center",
                  padding: "40px 20px",
                  color: "#757575",
                },
              },
              el(
                "p",
                {
                  style: {
                    margin: "0 0 8px 0",
                    fontSize: "14px",
                    fontWeight: "500",
                  },
                },
                __("No products added yet", "main")
              ),
              el(
                "p",
                {
                  style: {
                    margin: "0",
                    fontSize: "13px",
                  },
                },
                __(
                  "Use the category selector or search above to add products",
                  "main"
                )
              )
            )
        )
      );
    },

    save: function (props) {
      var InnerBlocks = wp.blockEditor.InnerBlocks;
      return el(InnerBlocks.Content, {});
    },
  });
})(window.wp);
