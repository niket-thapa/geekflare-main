(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var InnerBlocks = wp.blockEditor.InnerBlocks;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var TextControl = wp.components.TextControl;
  var ToggleControl = wp.components.ToggleControl;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useRef = wp.element.useRef;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;

  var ALLOWED_BLOCKS = [
    "core/paragraph",
    "core/heading",
    "core/list",
    "core/image",
    "main/info-box",
    "core/table",
    "main/pros-cons",
    "main/key-features",
    "main/score-breakdown",
    "main/awards",
  ];

  registerBlockType("main/product-item", {
    edit: function (props) {
      props = props || {};
      if (!wp || !wp.data || !wp.blockEditor || !wp.element || !wp.components) {
        return el("div", {}, __("Loading…", "main"));
      }
      var attributes = props.attributes || {};
      var setAttributes = props.setAttributes || function () {};
      var clientId = props.clientId;
      var productId = attributes.productId || 0;

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className:
          "buying_guide_item bg-white border border-gray-200 rounded-2xl md:rounded-3xl relative " +
          (attributes.isHighlighted
            ? "mt-[1.3125rem] md:mt-0"
            : "mt-[1.3125rem] md:mt-0"),
      });
      var previewUpdateLogId = "product-update-log-preview-" + clientId;

      // Get all product-item blocks and their product IDs from parent
      var siblingProducts = useSelect(
        function (select) {
          var blockEditor = select("core/block-editor");
          if (!blockEditor) return { blocks: [], productIds: [] };

          var block = blockEditor.getBlock(clientId);
          if (!block) return { blocks: [], productIds: [] };

          var parentClientId = blockEditor.getBlockParents(clientId)[0];
          if (!parentClientId) return { blocks: [], productIds: [] };

          var parentBlock = blockEditor.getBlock(parentClientId);
          if (!parentBlock || !parentBlock.innerBlocks)
            return { blocks: [], productIds: [] };

          // Find all product-item blocks in the parent
          var productItemBlocks = parentBlock.innerBlocks.filter(function (
            innerBlock
          ) {
            return innerBlock.name === "main/product-item";
          });

          // Extract product IDs from sibling blocks (excluding current block)
          var productIds = productItemBlocks
            .filter(function (innerBlock) {
              return innerBlock.clientId !== clientId;
            })
            .map(function (innerBlock) {
              return innerBlock.attributes && innerBlock.attributes.productId
                ? innerBlock.attributes.productId
                : 0;
            })
            .filter(function (id) {
              return id > 0;
            });

          // Find index of current block
          var index = productItemBlocks.findIndex(function (innerBlock) {
            return innerBlock.clientId === clientId;
          });

          return {
            blocks: productItemBlocks,
            productIds: productIds,
            blockIndex: index >= 0 ? index + 1 : 1,
          };
        },
        [clientId]
      );

      var blockIndex = siblingProducts.blockIndex || 1;
      var existingProductIds = siblingProducts.productIds || [];

      // CORRECTED: Properly destructure useState
      var searchState = useState("");
      var searchTerm = searchState[0];
      var setSearchTerm = searchState[1];

      var resultsState = useState([]);
      var searchResults = resultsState[0];
      var setSearchResults = resultsState[1];

      var productState = useState(null);
      var productData = productState[0];
      var setProductData = productState[1];

      var logoUrlState = useState("");
      var logoUrl = logoUrlState[0];
      var setLogoUrl = logoUrlState[1];

      var duplicateErrorState = useState("");
      var duplicateError = duplicateErrorState[0];
      var setDuplicateError = duplicateErrorState[1];

      // UI state for collapsible sections
      // Auto-show search when no product is selected (new block)
      var showSearchState = useState(!productId);
      var showSearch = showSearchState[0];
      var setShowSearch = showSearchState[1];

      // Track if user has manually set the product number
      var manuallySetNumberState = useState(false);
      var manuallySetNumber = manuallySetNumberState[0];
      var setManuallySetNumber = manuallySetNumberState[1];

      // Unique ID for search input
      var searchInputId = "product-search-input-" + clientId;
      var searchInputRef = useRef(null);
      var hasAutoFocusedRef = useRef(false); // Track if we've already auto-focused

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
          // Strategy 4: Find input in the same block context
          function () {
            var blockElement = document.querySelector(
              '[data-block="' + clientId + '"]'
            );
            if (blockElement) {
              var input = blockElement.querySelector('input[type="text"]');
              return input;
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

      // Auto-show search when productId is cleared or block is first created
      useEffect(
        function () {
          if (!productId) {
            setShowSearch(true);
          }
        },
        [productId]
      );

      // Auto-focus search input only once on initial mount (when block is first added)
      useEffect(
        function () {
          // Only auto-focus if:
          // 1. We haven't auto-focused before
          // 2. Search is shown
          // 3. No product is selected (new block)
          if (!hasAutoFocusedRef.current && showSearch && !productId) {
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

      // Auto-update product number when block index changes (only if not manually set)
      useEffect(
        function () {
          // Only auto-update if:
          // 1. We have a valid blockIndex
          // 2. The current productNumber doesn't match blockIndex
          // 3. User hasn't manually set a custom number
          if (
            blockIndex &&
            attributes.productNumber !== blockIndex &&
            !manuallySetNumber
          ) {
            setAttributes({ productNumber: blockIndex });
          }
        },
        [blockIndex, manuallySetNumber]
      );

      // Load product data when productId changes
      useEffect(
        function () {
          if (productId) {
            // Use standard REST API and build product data manually
            apiFetch({ path: "/wp/v2/products/" + productId + "?_embed" })
              .then(function (post) {
                if (post && post.id) {
                  // Build product data object from REST API response
                  var meta = post && post.meta ? post.meta : {};
                  var productName =
                    meta && meta.product_name
                      ? meta.product_name
                      : post.title && post.title.rendered
                      ? post.title.rendered
                      : "";
                  var tagline = meta && meta.tagline ? meta.tagline : "";
                  var websiteUrl =
                    meta && meta.website_url
                      ? meta.website_url
                      : post.link
                      ? post.link
                      : "";
                  var logoId =
                    meta && meta.product_logo
                      ? parseInt(meta.product_logo, 10)
                      : 0;

                  // Build product data object
                  var postTitle =
                    post.title && post.title.rendered
                      ? post.title.rendered
                      : "";
                  var postLink = post.link || "";
                  // Get update logs from meta
                  var updateLogsJson =
                    meta && meta.product_update_logs
                      ? meta.product_update_logs
                      : "[]";
                  var updateLogs = [];
                  try {
                    var parsed = JSON.parse(updateLogsJson);
                    if (Array.isArray(parsed)) {
                      updateLogs = parsed;
                    }
                  } catch (e) {
                    updateLogs = [];
                  }

                  // Get score breakdown from meta
                  var scoreBreakdownJson =
                    meta && meta.score_breakdown ? meta.score_breakdown : "[]";
                  var scoreBreakdown = [];
                  try {
                    var parsedScore = JSON.parse(scoreBreakdownJson);
                    if (Array.isArray(parsedScore)) {
                      scoreBreakdown = parsedScore;
                    }
                  } catch (e) {
                    scoreBreakdown = [];
                  }

                  var productDataObj = {
                    id: post.id,
                    name: productName || postTitle,
                    title: postTitle,
                    tagline: tagline,
                    logo: "",
                    logo_attachment_id: logoId,
                    website_url: websiteUrl,
                    permalink: postLink,
                    ai_powered: !!(meta && meta.ai_powered),
                    open_source: !!(meta && meta.open_source),
                    has_free_plan: !!(meta && meta.has_free_plan),
                    has_free_trial: !!(meta && meta.has_free_trial),
                    has_demo: !!(meta && meta.has_demo),
                    update_logs: updateLogs,
                    score_breakdown: scoreBreakdownJson, // Store as JSON string for auto-loading
                    product_features: [], // Will be populated from taxonomy
                    features_loaded: false, // Flag to track if features have been loaded
                  };

                  // Get product features from the REST response (via custom REST field)
                  var productFeaturesData =
                    post.product_features_data &&
                    Array.isArray(post.product_features_data)
                      ? post.product_features_data
                      : [];

                  if (productFeaturesData.length > 0) {
                    var featureNames = productFeaturesData
                      .map(function (term) {
                        return term.name || "";
                      })
                      .filter(function (name) {
                        return name.length > 0;
                      });
                    productDataObj.product_features = featureNames;
                  }

                  // Mark features as loaded (data comes directly from REST response)
                  productDataObj.features_loaded = true;
                  setProductData(productDataObj);

                  // Fetch logo if we have a logo ID
                  if (logoId && logoId > 0) {
                    apiFetch({ path: "/wp/v2/media/" + logoId })
                      .then(function (attachment) {
                        if (attachment && attachment.source_url) {
                          setLogoUrl(attachment.source_url);
                          // Update product data with logo URL (preserve existing data)
                          setProductData(function (prevData) {
                            return Object.assign(
                              {},
                              prevData || productDataObj,
                              {
                                logo: attachment.source_url,
                              }
                            );
                          });
                        } else {
                          setLogoUrl("");
                        }
                      })
                      .catch(function () {
                        setLogoUrl("");
                      });
                  } else {
                    setLogoUrl("");
                  }
                }
              })
              .catch(function (fallbackError) {
                try {
                  setProductData(null);
                  setLogoUrl("");
                } catch (e) {
                  // Silently handle state errors
                }
              });
          } else {
            setProductData(null);
            setLogoUrl("");
          }
        },
        [productId]
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
            setSearchResults(results);
          })
          .catch(function (error) {
            setSearchResults([]);
          });
      }

      function selectProduct(product) {
        if (!product || !product.id) {
          return;
        }

        // Check if this product is already added in another block
        // Allow selecting the same product if it's already in the current block
        var productIdNum = parseInt(product.id, 10);
        var currentProductId = attributes.productId || 0;

        // Only check for duplicates if it's a different product than the current one
        if (
          productIdNum !== currentProductId &&
          existingProductIds.indexOf(productIdNum) !== -1
        ) {
          // Product already exists in another block
          setDuplicateError(
            __(
              "This product is already in the list. Please select a different product.",
              "main"
            )
          );
          // Clear error after 5 seconds
          setTimeout(function () {
            setDuplicateError("");
          }, 5000);
          return;
        }

        // Clear any previous errors
        setDuplicateError("");
        setAttributes({ productId: product.id });
        setProductData(null);
        setLogoUrl("");
        setSearchTerm("");
        setSearchResults([]);
        setShowSearch(false);
      }

      // Get update logs from product data
      var updateLogs =
        productData && Array.isArray(productData.update_logs)
          ? productData.update_logs.filter(function (entry) {
              return entry && (entry.date || entry.description);
            })
          : [];

      // Get show_update_logs from block attribute (defaults to true)
      var showUpdateLogs =
        attributes.showUpdateLogs !== undefined
          ? attributes.showUpdateLogs
          : true;
      var shouldShowUpdateLog = showUpdateLogs && updateLogs.length > 0;

      // Get block editor dispatch for inserting blocks
      var insertBlocks = useDispatch
        ? useDispatch("core/block-editor").insertBlocks
        : null;
      var getBlock = useSelect
        ? useSelect(function (select) {
            return select("core/block-editor").getBlock;
          }, [])
        : null;

      // Track if we've already auto-loaded blocks for this product
      var hasAutoLoadedState = useState(false);
      var hasAutoLoaded = hasAutoLoadedState[0];
      var setHasAutoLoaded = hasAutoLoadedState[1];

      // Auto-load sub-blocks when product is selected and product data is loaded
      useEffect(
        function () {
          // Only proceed if we have a product ID, product data is loaded, and we haven't already loaded blocks
          if (!productId || !productData || !insertBlocks || !getBlock) {
            return;
          }

          // Wait for features to be loaded before auto-loading blocks
          if (!productData.features_loaded) {
            return;
          }

          // Skip if we've already loaded blocks for this product
          if (hasAutoLoaded) {
            return;
          }

          // Check if the required sub-blocks already exist
          var currentBlock = getBlock(clientId);
          var existingBlockNames = [];
          if (
            currentBlock &&
            currentBlock.innerBlocks &&
            currentBlock.innerBlocks.length > 0
          ) {
            existingBlockNames = currentBlock.innerBlocks.map(function (block) {
              return block.name;
            });
          }

          // Check which blocks we need to add
          var needsScoreBreakdown =
            existingBlockNames.indexOf("main/score-breakdown") === -1;
          var needsKeyFeatures =
            existingBlockNames.indexOf("main/key-features") === -1;
          var needsProsCons =
            existingBlockNames.indexOf("main/pros-cons") === -1;

          // If all blocks already exist, mark as loaded and return
          if (!needsScoreBreakdown && !needsKeyFeatures && !needsProsCons) {
            setHasAutoLoaded(true);
            return;
          }

          // Get score breakdown from product meta
          var scoreBreakdown = null;
          if (productData.score_breakdown) {
            try {
              scoreBreakdown =
                typeof productData.score_breakdown === "string"
                  ? JSON.parse(productData.score_breakdown)
                  : productData.score_breakdown;
            } catch (e) {
              scoreBreakdown = null;
            }
          }

          // Prepare blocks to insert
          var blocksToInsert = [];

          // 1. Score Breakdown - always add if it doesn't exist (will use product data if available)
          if (needsScoreBreakdown) {
            blocksToInsert.push(
              wp.blocks.createBlock("main/score-breakdown", {
                productId: productId,
              })
            );
          }

          // 2. Key Features - add with product features if available
          if (needsKeyFeatures) {
            var keyFeaturesAttrs = {};
            // Load features from product-features taxonomy if available
            if (
              productData.product_features &&
              Array.isArray(productData.product_features) &&
              productData.product_features.length > 0
            ) {
              // Add features plus a blank entry for adding more
              keyFeaturesAttrs.features = productData.product_features.concat([
                "",
              ]);
            }
            blocksToInsert.push(
              wp.blocks.createBlock("main/key-features", keyFeaturesAttrs)
            );
          }

          // 3. Pros & Cons (empty) - always add if it doesn't exist
          if (needsProsCons) {
            blocksToInsert.push(wp.blocks.createBlock("main/pros-cons", {}));
          }

          // Insert blocks if we have any to add
          if (blocksToInsert.length > 0) {
            // Use a delay to ensure the block is fully ready and DOM is updated
            setTimeout(function () {
              try {
                // Verify wp.blocks is available
                if (!wp || !wp.blocks || !wp.blocks.createBlock) {
                  setHasAutoLoaded(true);
                  return;
                }

                // Get current block again to ensure we have the latest state
                var latestBlock = getBlock(clientId);
                if (!latestBlock) {
                  setHasAutoLoaded(true);
                  return;
                }

                // Double-check blocks don't already exist (in case they were added between checks)
                var latestBlockNames = [];
                if (
                  latestBlock.innerBlocks &&
                  latestBlock.innerBlocks.length > 0
                ) {
                  latestBlockNames = latestBlock.innerBlocks.map(function (
                    block
                  ) {
                    return block.name;
                  });
                }

                // Filter out blocks that now exist
                var finalBlocksToInsert = [];
                for (var i = 0; i < blocksToInsert.length; i++) {
                  var blockToInsert = blocksToInsert[i];
                  var blockName =
                    blockToInsert && blockToInsert.name
                      ? blockToInsert.name
                      : null;
                  if (blockName && latestBlockNames.indexOf(blockName) === -1) {
                    finalBlocksToInsert.push(blockToInsert);
                  }
                }

                // Insert remaining blocks if any
                if (finalBlocksToInsert.length > 0) {
                  insertBlocks(finalBlocksToInsert, 0, clientId);
                }

                setHasAutoLoaded(true);
              } catch (e) {
                // Still mark as loaded to prevent infinite retries
                setHasAutoLoaded(true);
              }
            }, 1000);
          } else {
            setHasAutoLoaded(true);
          }
        },
        [
          productId,
          productData,
          clientId,
          hasAutoLoaded,
          insertBlocks,
          getBlock,
        ]
      );

      // Reset auto-loaded flag when product changes
      useEffect(
        function () {
          if (productId) {
            setHasAutoLoaded(false);
          }
        },
        [productId]
      );

      return el(
        "article",
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
            el(ToggleControl, {
              label: __("Highlight This Product", "main"),
              checked: attributes.isHighlighted,
              onChange: function (value) {
                setAttributes({ isHighlighted: value });
              },
              help: __("Makes this product stand out visually", "main"),
            }),
            el(ToggleControl, {
              label: __("Show Update Logs", "main"),
              checked: showUpdateLogs,
              onChange: function (value) {
                setAttributes({ showUpdateLogs: value });
              },
              help: __("Show or hide the product update log section", "main"),
            })
          )
        ),
        // Product Settings Section
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
                justifyContent: "space-between",
                marginBottom: "12px",
              },
            },
            el(
              "div",
              {
                style: {
                  fontSize: "14px",
                  fontWeight: "600",
                  color: "#374151",
                },
              },
              __("Product Settings", "main")
            )
          ),
          // Product Number (editable)
          el(
            "div",
            { style: { marginBottom: "12px" } },
            el(TextControl, {
              label: __("Product Number", "main"),
              type: "number",
              value: attributes.productNumber || blockIndex || 1,
              onChange: function (value) {
                var numValue = parseInt(value, 10);
                if (!isNaN(numValue) && numValue > 0) {
                  setAttributes({ productNumber: numValue });
                  // Mark as manually set if different from auto-generated
                  if (numValue !== blockIndex) {
                    setManuallySetNumber(true);
                  } else {
                    setManuallySetNumber(false);
                  }
                } else if (value === "" || value === null) {
                  // Reset to auto-generated number
                  setAttributes({ productNumber: blockIndex || 1 });
                  setManuallySetNumber(false);
                }
              },
              help: manuallySetNumber
                ? __(
                    "Custom number set (differs from auto-generated #" +
                      blockIndex +
                      ")",
                    "main"
                  )
                : __(
                    "Auto-generated based on position. Change to set manually.",
                    "main"
                  ),
              min: 1,
            }),
            manuallySetNumber &&
              el(
                Button,
                {
                  isSmall: true,
                  variant: "secondary",
                  onClick: function () {
                    setAttributes({ productNumber: blockIndex || 1 });
                    setManuallySetNumber(false);
                  },
                  style: { marginTop: "8px" },
                },
                __("Reset to Auto (#" + blockIndex + ")", "main")
              )
          ),
          // Product Selection Section
          el("hr", { style: { margin: "16px 0", borderColor: "#e5e7eb" } }),
          el(
            "div",
            {
              style: {
                fontSize: "14px",
                fontWeight: "600",
                color: "#374151",
                marginBottom: "12px",
              },
            },
            __("Select Product", "main")
          ),

          (showSearch || !productId) &&
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
                    !productId
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
                label: __("Search Product", "main"),
                value: searchTerm,
                onChange: function (value) {
                  setSearchTerm(value);
                  setDuplicateError(""); // Clear error when searching
                  searchProducts(value);
                },
                placeholder: __("Type to search...", "main"),
                help: __("Type at least 2 characters", "main"),
                style: { marginBottom: "12px" },
              }),

              duplicateError &&
                el(
                  "div",
                  {
                    className: "components-notice is-error",
                    style: {
                      marginTop: "8px",
                      marginBottom: "12px",
                      padding: "8px 12px",
                      backgroundColor: "#f0b849",
                      borderLeft: "4px solid #d63638",
                      borderRadius: "2px",
                    },
                  },
                  el(
                    "div",
                    {
                      className: "components-notice__content",
                      style: {
                        color: "#1d2327",
                        fontSize: "13px",
                        fontWeight: "500",
                      },
                    },
                    duplicateError
                  )
                ),

              searchResults.length > 0 &&
                el(
                  "div",
                  {
                    className: "product-search-results",
                    style: {
                      maxHeight: "200px",
                      overflowY: "auto",
                      marginTop: "8px",
                      border: "1px solid #e5e7eb",
                      borderRadius: "4px",
                      padding: "8px",
                      background: "#fff",
                    },
                  },
                  searchResults.map(function (result) {
                    var resultProductId = parseInt(result.id, 10);
                    var currentProductId = attributes.productId || 0;
                    // Only show as duplicate if it's in another block (not the current one)
                    var isDuplicate =
                      resultProductId !== currentProductId &&
                      existingProductIds.indexOf(resultProductId) !== -1;
                    return el(
                      Button,
                      {
                        key: result.id,
                        onClick: function () {
                          selectProduct(result);
                        },
                        variant: "secondary",
                        disabled: isDuplicate,
                        style: {
                          marginBottom: "4px",
                          display: "block",
                          width: "100%",
                          textAlign: "left",
                          opacity: isDuplicate ? 0.5 : 1,
                          cursor: isDuplicate ? "not-allowed" : "pointer",
                        },
                      },
                      result.title.rendered +
                        (isDuplicate
                          ? " " + __("(Already in list)", "main")
                          : "")
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

          // Show current product if selected
          productData &&
            !showSearch &&
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
                    marginBottom: "8px",
                  },
                },
                __("Current Product:", "main")
              ),
              el(
                "div",
                {
                  style: {
                    padding: "12px",
                    background: "#fff",
                    borderRadius: "6px",
                    border: "1px solid #e5e7eb",
                    fontSize: "14px",
                    fontWeight: "600",
                    color: "#111827",
                  },
                },
                productData.name || productData.title || ""
              )
            )
        ),
        // Product Number
        el(
          "div",
          {
            className:
              "product-number " + (attributes.isHighlighted ? "active" : ""),
            "aria-label":
              "Rank " + (attributes.productNumber || blockIndex || 1),
          },
          el(
            "span",
            { "aria-hidden": "true" },
            attributes.productNumber || blockIndex || 1
          )
        ),

        // Main content wrapper with padding
        el(
          "div",
          {
            className:
              "p-5 md:p-6 lg:p-7 xl:p-8 flex flex-col gap-8 md:gap-10 lg:gap-12 [&_p]:my-0 [&_p:empty]:hidden",
          },
          // Product Header
          productData
            ? el(
                "div",
                {
                  className:
                    "flex flex-col md:flex-row md:justify-between md:items-start md:flex-wrap gap-6 md:gap-y-4",
                },
                // Logo and Name (direct children, no wrapper)
                el(
                  "div",
                  {
                    className: "flex gap-4 md:gap-6 items-center",
                  },
                  logoUrl &&
                    el(
                      "div",
                      {
                        className:
                          "product-logo-wrap w-12 [&_img]:w-full [&_img]:h-auto md:w-20 [&_img]:m-0",
                      },
                      el("img", {
                        src: logoUrl,
                        alt:
                          (productData &&
                            (productData.name || productData.title)) ||
                          "",
                        width: "80",
                        height: "64",
                        loading: "lazy",
                      })
                    ),
                  el(
                    "div",
                    {
                      className:
                        "product-name-wrap flex-1 flex flex-col gap-0.5 md:gap-1.5",
                    },
                    el(
                      "h3",
                      {
                        className:
                          "text-lg md:text-2xl leading-5 md:leading-none font-bold text-gray-800 m-0",
                      },
                      (productData &&
                        (productData.name || productData.title)) ||
                        ""
                    ),
                    productData && productData.tagline
                      ? el(
                          "div",
                          {
                            className:
                              "text-gray-500 text-sm md:text-base font-medium md:tracking-2p md:leading-6",
                          },
                          productData.tagline
                        )
                      : null
                  )
                ),

                // Product Badges
                el(
                  "div",
                  {
                    className:
                      "flex flex-row flex-wrap items-start content-start p-0 gap-2 md:order-2 md:w-full md:flex-[100%]",
                  },
                  (function () {
                    var badges = [];
                    if (!productData) {
                      return badges;
                    }
                    // Get badge flags directly from product data
                    var aiPowered = !!productData.ai_powered;
                    var openSource = !!productData.open_source;
                    var hasFreePlan = !!productData.has_free_plan;
                    var hasFreeTrial = !!productData.has_free_trial;
                    var hasDemo = !!productData.has_demo;

                    // AI-Powered - gradient badge
                    if (aiPowered) {
                      badges.push(
                        el(
                          "button",
                          {
                            type: "button",
                            key: "ai-powered",
                            className:
                              "product-badge product-badge--gradient flex flex-row justify-center items-center gap-2.5 rounded-lg md:rounded-xl",
                          },
                          el(
                            "span",
                            {
                              className:
                                "text-sm md:text-base font-semibold leading-[1.375rem] text-gray-800",
                            },
                            "AI-Powered"
                          )
                        )
                      );
                    }

                    // Open Source - outline badge
                    if (openSource) {
                      badges.push(
                        el(
                          "button",
                          {
                            type: "button",
                            key: "open-source",
                            className:
                              "product-badge product-badge--outline flex flex-row justify-center items-center py-[0.1875rem] md:py-1.5 px-3 md:px-4 gap-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl",
                          },
                          el(
                            "span",
                            {
                              className:
                                "text-sm md:text-base font-medium leading-[1.375rem] text-gray-800 tracking-2p",
                            },
                            "Open Source"
                          )
                        )
                      );
                    }

                    // Free Plan
                    if (hasFreePlan) {
                      badges.push(
                        el(
                          "button",
                          {
                            type: "button",
                            key: "free-plan",
                            className:
                              "product-badge product-badge--outline flex flex-row justify-center items-center py-[0.1875rem] md:py-1.5 px-3 md:px-4 gap-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl",
                          },
                          el(
                            "span",
                            {
                              className:
                                "text-sm md:text-base font-medium leading-[1.375rem] text-gray-800 tracking-2p",
                            },
                            "Free Plan"
                          )
                        )
                      );
                    }

                    // Free Trial
                    if (hasFreeTrial) {
                      badges.push(
                        el(
                          "button",
                          {
                            type: "button",
                            key: "free-trial",
                            className:
                              "product-badge product-badge--outline flex flex-row justify-center items-center py-[0.1875rem] md:py-1.5 px-3 md:px-4 gap-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl",
                          },
                          el(
                            "span",
                            {
                              className:
                                "text-sm md:text-base font-medium leading-[1.375rem] text-gray-800 tracking-2p",
                            },
                            "Free Trial"
                          )
                        )
                      );
                    }

                    // Demo
                    if (hasDemo) {
                      badges.push(
                        el(
                          "button",
                          {
                            type: "button",
                            key: "demo",
                            className:
                              "product-badge product-badge--outline flex flex-row justify-center items-center py-[0.1875rem] md:py-1.5 px-3 md:px-4 gap-2.5 bg-gray-50 border border-gray-200 rounded-lg md:rounded-xl",
                          },
                          el(
                            "span",
                            {
                              className:
                                "text-sm md:text-base font-medium leading-[1.375rem] text-gray-800 tracking-2p",
                            },
                            "Demo"
                          )
                        )
                      );
                    }

                    return badges;
                  })()
                ),

                // CTA Button
                el(
                  "a",
                  {
                    href:
                      (productData &&
                        (productData.affiliate_link ||
                          productData.website_url ||
                          productData.permalink)) ||
                      "#",
                    className: "btn btn--primary rounded-full",
                    target: "_blank",
                    rel: "nofollow noopener",
                  },
                  __("Visit Site", "main"),
                  el(
                    "svg",
                    {
                      xmlns: "http://www.w3.org/2000/svg",
                      width: "14",
                      height: "14",
                      fill: "none",
                      viewBox: "0 0 14 14",
                    },
                    el("path", {
                      stroke: "#fff",
                      strokeLinecap: "round",
                      strokeLinejoin: "round",
                      strokeWidth: "1.5",
                      d: "M6.416 4.083H3.5A1.167 1.167 0 0 0 2.333 5.25v5.25A1.167 1.167 0 0 0 3.5 11.667h5.25A1.167 1.167 0 0 0 9.916 10.5V7.583M5.833 8.167l5.833-5.834M8.75 2.333h2.917V5.25",
                    })
                  )
                )
              )
            : el(
                "div",
                {
                  className:
                    "flex flex-col items-center justify-center p-12 text-center",
                },
                el(
                  "p",
                  {
                    className: "text-gray-400 text-sm font-medium italic",
                  },
                  __(
                    "Click 'Add Product' above to search and select a product",
                    "main"
                  )
                )
              ),
          // Product Content (InnerBlocks)
          el(InnerBlocks, {
            allowedBlocks: ALLOWED_BLOCKS,
            template: [
              [
                "core/paragraph",
                { placeholder: "Write your detailed review here..." },
              ],
            ],
          })
        ),
        // Product Update Log (outside the padding div, but inside article)
        shouldShowUpdateLog &&
          el(
            "div",
            {
              className:
                "product-update-log flex flex-col items-start p-0 gap-0 flex-none self-stretch rounded-b-2xl md:rounded-b-3xl overflow-hidden",
            },
            el(
              "button",
              {
                type: "button",
                className:
                  "product-update-log__header box-border flex flex-row justify-between items-center py-3.5 md:py-5 px-5 md:px-8 gap-5 w-full bg-gray-50 border-t border-gray-200 flex-none self-stretch transition-colors",
                "aria-expanded": "false",
                "aria-controls": previewUpdateLogId,
                "data-toggle": "product-update-log",
              },
              el(
                "h3",
                {
                  className:
                    "text-sm md:text-base font-semibold leading-6 flex items-center text-gray-800 flex-none m-0",
                },
                __("Product Update Log", "main")
              ),
              el(
                "svg",
                {
                  className:
                    "product-update-log__chevron w-6 h-6 flex-none transition-transform duration-200",
                  xmlns: "http://www.w3.org/2000/svg",
                  width: "20",
                  height: "20",
                  fill: "none",
                  viewBox: "0 0 20 20",
                },
                el("path", {
                  stroke: "#1d2939",
                  strokeLinecap: "round",
                  strokeLinejoin: "round",
                  strokeWidth: "1.5",
                  d: "m5 7.5 5 5 5-5",
                })
              )
            ),
            el(
              "div",
              {
                className:
                  "product-update-log__content accordion-panel items-start self-stretch overflow-hidden",
                id: previewUpdateLogId,
                role: "region",
                "aria-live": "polite",
                style: { maxHeight: "none" },
              },
              el(
                "div",
                {
                  className:
                    "accordion-panel__inner flex flex-col gap-2 p-6 md:px-8 border-t border-gray-200",
                },
                updateLogs.map(function (entry, index) {
                  if (
                    (!entry || (!entry.date && !entry.description)) &&
                    entry
                  ) {
                    return null;
                  }
                  return el(
                    "div",
                    {
                      key: "update-" + index,
                      className:
                        "text-base font-medium leading-6 tracking-2p text-gray-600 flex-none self-stretch",
                    },
                    entry && entry.date
                      ? el(
                          "strong",
                          { className: "font-semibold text-gray-800" },
                          entry.date + ": "
                        )
                      : null,
                    entry && entry.description ? entry.description : ""
                  );
                })
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
