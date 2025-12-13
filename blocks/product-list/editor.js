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
  var ToggleControl = wp.components.ToggleControl;
  var __ = wp.i18n.__;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;
  var useRef = wp.element.useRef;

  var ALLOWED_BLOCKS = ["main/product-item"];

  registerBlockType("main/product-list", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var clientId = props.clientId;

      var blockProps = useBlockProps({
        className: "product-list-editor flex flex-col gap-7.5 pb-3 md:pb-4",
      });

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

      var dragState = useState({
        isDragging: false,
        dragIndex: null,
        hoverIndex: null,
      });
      var dragInfo = dragState[0];
      var setDragInfo = dragState[1];

      var containerRef = useRef(null);
      var itemRefs = useRef([]);
      var dragRef = useRef({
        active: false,
        pointerId: null,
        startY: 0,
        dragIndex: null,
        hoverIndex: null,
        element: null,
      });

      var _useDispatch = useDispatch("core/block-editor");
      var insertBlocks = _useDispatch.insertBlocks;
      var removeBlock = _useDispatch.removeBlock;
      var moveBlockToPosition = _useDispatch.moveBlockToPosition;

      var getBlock = useSelect(function (select) {
        return select("core/block-editor").getBlock;
      }, []);

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

      var existingProductIds = productBlocks
        .map(function (innerBlock) {
          return innerBlock.attributes && innerBlock.attributes.productId
            ? innerBlock.attributes.productId
            : 0;
        })
        .filter(function (id) {
          return id > 0;
        });

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
            setCategories([]);
            setIsLoadingCategories(false);
          });
      }, []);

      useEffect(
        function () {
          if (!searchQuery || searchQuery.length < 2) {
            setSearchResults([]);
            setIsSearching(false);
            return;
          }

          setIsSearching(true);

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
                setSearchResults([]);
                setIsSearching(false);
              });
          }, 300);

          return function () {
            clearTimeout(timer);
          };
        },
        [searchQuery]
      );

      // ============================================
      // POINTER EVENTS BASED DRAG AND DROP
      // ============================================

      var findHoverIndex = function (mouseY) {
        var items = itemRefs.current;
        for (var i = 0; i < items.length; i++) {
          var item = items[i];
          if (!item) continue;
          var rect = item.getBoundingClientRect();
          var midY = rect.top + rect.height / 2;
          if (mouseY < midY) {
            return i;
          }
        }
        return items.length - 1;
      };

      var handlePointerDown = function (e, index) {
        if (e.button !== 0) return;
        if (e.target.closest("button")) return;

        e.preventDefault();
        e.stopPropagation();

        e.target.setPointerCapture(e.pointerId);

        dragRef.current = {
          active: true,
          pointerId: e.pointerId,
          startY: e.clientY,
          dragIndex: index,
          hoverIndex: index,
          element: e.target,
          visualDragging: false,
        };

        // Show highlight immediately on press
        setDragInfo({
          isDragging: true,
          dragIndex: index,
          hoverIndex: index,
        });

        document.body.style.cursor = "grabbing";
        document.body.style.userSelect = "none";
      };

      var handlePointerMove = function (e) {
        var data = dragRef.current;
        if (!data.active) return;

        var deltaY = Math.abs(e.clientY - data.startY);

        // Mark as visual dragging after 5px (for the actual move operation)
        if (!data.visualDragging && deltaY > 5) {
          data.visualDragging = true;
        }

        // Always update hover index while active
        var newHoverIndex = findHoverIndex(e.clientY);
        if (data.hoverIndex !== newHoverIndex) {
          data.hoverIndex = newHoverIndex;
          setDragInfo({
            isDragging: true,
            dragIndex: data.dragIndex,
            hoverIndex: newHoverIndex,
          });
        }
      };

      var handlePointerUp = function (e) {
        var data = dragRef.current;

        if (!data.active) return;

        if (data.element && data.pointerId !== null) {
          try {
            data.element.releasePointerCapture(data.pointerId);
          } catch (err) {}
        }

        document.body.style.cursor = "";
        document.body.style.userSelect = "";

        // Only perform move if user actually dragged (moved more than 5px)
        if (data.visualDragging && data.dragIndex !== null) {
          var finalHoverIndex = findHoverIndex(e.clientY);

          if (finalHoverIndex !== data.dragIndex && moveBlockToPosition) {
            var blockToMove = productBlocks[data.dragIndex];
            if (blockToMove) {
              try {
                moveBlockToPosition(
                  blockToMove.clientId,
                  clientId,
                  clientId,
                  finalHoverIndex
                );
              } catch (err) {}
            }
          }
        }

        dragRef.current = {
          active: false,
          pointerId: null,
          startY: 0,
          dragIndex: null,
          hoverIndex: null,
          element: null,
          visualDragging: false,
        };

        setDragInfo({
          isDragging: false,
          dragIndex: null,
          hoverIndex: null,
        });
      };

      var handlePointerCancel = function (e) {
        handlePointerUp(e);
      };

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
            setIsLoadingProducts(false);
          });
      };

      var handleAddProduct = function (productId) {
        if (existingProductIds.indexOf(parseInt(productId, 10)) !== -1) {
          return;
        }

        var currentBlock = getBlock ? getBlock(clientId) : null;
        if (!currentBlock || !insertBlocks) return;

        var newBlock = wp.blocks.createBlock("main/product-item", {
          productId: productId,
        });

        insertBlocks([newBlock], currentBlock.innerBlocks.length, clientId);

        setSearchQuery("");
        setSearchResults([]);
      };

      var handleRemoveProduct = function (blockId) {
        if (removeBlock) {
          removeBlock(blockId);
        }
      };

      var handleMoveUp = function (index) {
        if (index === 0 || !moveBlockToPosition) return;

        var blockToMove = productBlocks[index];
        if (!blockToMove) return;

        moveBlockToPosition(
          blockToMove.clientId,
          clientId,
          clientId,
          index - 1
        );
      };

      var handleMoveDown = function (index) {
        if (index === productBlocks.length - 1 || !moveBlockToPosition) return;

        var blockToMove = productBlocks[index];
        if (!blockToMove) return;

        moveBlockToPosition(
          blockToMove.clientId,
          clientId,
          clientId,
          index + 1
        );
      };

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

      useEffect(
        function () {
          itemRefs.current = itemRefs.current.slice(0, productBlocks.length);
        },
        [productBlocks.length]
      );

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            {
              title: __("Block Settings", "main"),
              initialOpen: true,
            },
            el(ToggleControl, {
              label: __("Show Product Number", "main"),
              checked:
                attributes.showProductNumber !== undefined
                  ? attributes.showProductNumber
                  : true,
              onChange: function (value) {
                setAttributes({ showProductNumber: value });
              },
              help: __(
                "Show or hide the product number badge on each product item",
                "main"
              ),
            })
          )
        ),

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

        productBlocks.length > 0 &&
          el(
            "div",
            {
              ref: containerRef,
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
                style: {
                  fontSize: "12px",
                  color: "#757575",
                  marginBottom: "12px",
                  fontStyle: "italic",
                },
              },
              __("Drag items to reorder, or use arrow buttons", "main")
            ),
            el(
              "div",
              {
                className: "product-sort-container",
                style: {
                  display: "flex",
                  flexDirection: "column",
                  gap: "8px",
                },
              },
              productBlocks.map(function (block, index) {
                var isDragging =
                  dragInfo.isDragging && dragInfo.dragIndex === index;
                var isHoverTarget =
                  dragInfo.isDragging &&
                  dragInfo.hoverIndex === index &&
                  dragInfo.dragIndex !== index;

                return el(
                  "div",
                  {
                    key: block.clientId,
                    ref: function (element) {
                      itemRefs.current[index] = element;
                    },
                    onPointerDown: function (e) {
                      handlePointerDown(e, index);
                    },
                    onPointerMove: handlePointerMove,
                    onPointerUp: handlePointerUp,
                    onPointerCancel: handlePointerCancel,
                    style: {
                      display: "flex",
                      justifyContent: "space-between",
                      alignItems: "center",
                      padding: "8px 12px",
                      backgroundColor: isHoverTarget
                        ? "#dbeafe"
                        : isDragging
                        ? "#fef3c7"
                        : "#f0f0f1",
                      borderRadius: "4px",
                      fontSize: "13px",
                      cursor: dragInfo.isDragging ? "grabbing" : "grab",
                      opacity: isDragging ? 0.8 : 1,
                      userSelect: "none",
                      border: isHoverTarget
                        ? "2px dashed #3b82f6"
                        : isDragging
                        ? "2px solid #f59e0b"
                        : "2px solid transparent",
                      transition: "background-color 0.1s, border-color 0.1s",
                      boxSizing: "border-box",
                      position: "relative",
                      touchAction: "none",
                    },
                  },
                  el(
                    "span",
                    {
                      style: {
                        marginRight: "8px",
                        cursor: dragInfo.isDragging ? "grabbing" : "grab",
                        fontSize: "16px",
                        color: "#757575",
                        userSelect: "none",
                        padding: "0 4px",
                      },
                    },
                    "⋮⋮"
                  ),
                  el(
                    "span",
                    {
                      style: {
                        flex: 1,
                        pointerEvents: "none",
                        userSelect: "none",
                      },
                    },
                    "#" +
                      (index + 1) +
                      " - " +
                      __("Product ID: ", "main") +
                      (block.attributes.productId || "N/A")
                  ),
                  el(
                    "div",
                    {
                      onPointerDown: function (e) {
                        e.stopPropagation();
                      },
                      style: {
                        display: "flex",
                        gap: "4px",
                        alignItems: "center",
                      },
                    },
                    el(Button, {
                      icon: "arrow-up-alt2",
                      isSmall: true,
                      disabled: index === 0,
                      onClick: function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        handleMoveUp(index);
                      },
                      label: __("Move Up", "main"),
                      style: {
                        minWidth: "30px",
                      },
                    }),
                    el(Button, {
                      icon: "arrow-down-alt2",
                      isSmall: true,
                      disabled: index === productBlocks.length - 1,
                      onClick: function (e) {
                        e.stopPropagation();
                        e.preventDefault();
                        handleMoveDown(index);
                      },
                      label: __("Move Down", "main"),
                      style: {
                        minWidth: "30px",
                      },
                    }),
                    el(
                      Button,
                      {
                        isDestructive: true,
                        isSmall: true,
                        onClick: function (e) {
                          e.stopPropagation();
                          e.preventDefault();
                          handleRemoveProduct(block.clientId);
                        },
                      },
                      __("Remove", "main")
                    )
                  )
                );
              })
            )
          ),

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
