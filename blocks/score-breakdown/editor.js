(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var TextControl = wp.components.TextControl;
  var PanelBody = wp.components.PanelBody;
  var useSelect = wp.data.useSelect;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var __ = wp.i18n.__;

  registerBlockType("main/score-breakdown", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var clientId = props.clientId;
      var productIdAttr = attributes.productId || 0;
      var heading = attributes.heading || __("Product Score Breakdown", "main");

      var blockProps = useBlockProps({
        className: "score-breakdown-editor",
      });

      // Try to get productId from parent product-item block
      var parentProductId = useSelect(
        function (select) {
          var blockEditor = select("core/block-editor");
          if (!blockEditor) {
            return 0;
          }

          var block = blockEditor.getBlock(clientId);
          if (!block) {
            return 0;
          }

          var parentIds = blockEditor.getBlockParents(clientId);
          if (!parentIds || parentIds.length === 0) {
            return 0;
          }

          // Check each parent until we find a product-item block
          for (var i = 0; i < parentIds.length; i++) {
            var parentBlock = blockEditor.getBlock(parentIds[i]);

            if (
              parentBlock &&
              parentBlock.name === "main/product-item" &&
              parentBlock.attributes &&
              parentBlock.attributes.productId
            ) {
              var pid = parseInt(parentBlock.attributes.productId, 10) || 0;
              return pid;
            }
          }

          return 0;
        },
        [clientId]
      );

      var productId = parentProductId || productIdAttr;

      // If we have a parent productId but it's not in attributes, save it
      useEffect(
        function () {
          if (
            parentProductId &&
            parentProductId !== productIdAttr &&
            parentProductId > 0
          ) {
            setAttributes({ productId: parentProductId });
          }
        },
        [parentProductId, productIdAttr]
      );

      // -----------------------------
      // Correct state declarations
      // -----------------------------
      const [scoreBreakdown, setScoreBreakdown] = useState(null);
      const [loading, setLoading] = useState(false);

      // Fetch score breakdown from product meta
      useEffect(
        function () {
          var currentProductId = parentProductId || productIdAttr;

          if (!currentProductId || currentProductId === 0) {
            setScoreBreakdown(null);
            return;
          }

          setLoading(true);

          apiFetch({
            path: "/wp/v2/products/" + currentProductId,
          })
            .then(function (post) {
              var meta = post && post.meta ? post.meta : {};
              var scoreBreakdownJson = meta.score_breakdown || "[]";

              try {
                var breakdown = JSON.parse(scoreBreakdownJson);
                if (Array.isArray(breakdown) && breakdown.length > 0) {
                  setScoreBreakdown(breakdown);
                } else {
                  setScoreBreakdown(null);
                }
              } catch (e) {
                // invalid JSON
                setScoreBreakdown(null);
              }

              setLoading(false);
            })
            .catch(function (error) {
              setScoreBreakdown(null);
              setLoading(false);
            });
        },
        [parentProductId, productIdAttr]
      );

      // Calculate average
      function calculateAverage(criteria) {
        if (!criteria || criteria.length === 0) return 0;
        var sum = criteria.reduce(function (acc, c) {
          return acc + (parseFloat(c.score) || 0);
        }, 0);
        return (sum / criteria.length).toFixed(1);
      }

      // Utility to pick color label (you may adapt to map to actual CSS classes)
      function getScoreColor(score) {
        if (score >= 4) return "success"; // >= 4
        if (score >= 2) return "warning"; // >= 2 and < 4
        return "error"; // < 2
      }

      // -----------------------------
      // Render
      // -----------------------------
      return el(
        Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Settings", "main"), initialOpen: true },
            el(TextControl, {
              label: __("Product ID", "main"),
              type: "number",
              value: productIdAttr || "",
              onChange: function (value) {
                setAttributes({ productId: parseInt(value, 10) || 0 });
              },
              help: parentProductId
                ? __("Auto-detected from parent product-item block", "main") +
                  " (" +
                  parentProductId +
                  ")"
                : __(
                    "Enter product ID manually or place this block inside a product-item block",
                    "main"
                  ),
            })
          )
        ),

        // Preview matching render.php
        el(
          "div",
          blockProps,
          el(
            "div",
            {
              className:
                "product-score-breakdown flex flex-col items-start p-0 gap-4 md:gap-5 flex-none self-stretch",
            },
            el(RichText, {
              tagName: "h3",
              className:
                "text-sm md:text-base font-semibold leading-5 md:leading-6 flex items-center text-gray-800 flex-none m-0",
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
              placeholder: __("Product Score Breakdown", "main"),
              allowedFormats: [],
            }),

            (function () {
              if (loading) {
                return el(
                  "p",
                  { className: "text-gray-400 italic text-center" },
                  __("Loading score breakdown...", "main")
                );
              }

              if (!productId || productId === 0) {
                return el(
                  "p",
                  { className: "text-gray-400 italic text-center" },
                  __(
                    "Place this block inside a product-item block or set a product ID to display score breakdown.",
                    "main"
                  )
                );
              }

              if (!scoreBreakdown || scoreBreakdown.length === 0) {
                return el(
                  "p",
                  { className: "text-gray-400 italic text-center" },
                  __(
                    "No score breakdown found for this product. Please add score breakdown in the product settings.",
                    "main"
                  )
                );
              }

              var average = calculateAverage(scoreBreakdown);

              // Build cards array
              var cards = scoreBreakdown.map(function (criterion, index) {
                var score = parseFloat(criterion.score) || 0;
                var fullBars = Math.floor(score);
                var halfBar = score - fullBars >= 0.5;
                var emptyBars = 5 - fullBars - (halfBar ? 1 : 0);
                var color = getScoreColor(score);

                // Full bars elements
                var fullBarEls = Array.from({ length: fullBars }).map(function (
                  _,
                  i
                ) {
                  return el("div", {
                    key: "full-" + index + "-" + i,
                    className: "h-2 flex-1 bg-" + color + "-600 rounded-full",
                  });
                });

                // Half bar element (if any)
                var halfBarEl = halfBar
                  ? el(
                      "div",
                      {
                        key: "half-" + index,
                        className:
                          "product-score-bar__half h-2 flex-1 relative",
                      },
                      el("div", {
                        className:
                          "absolute inset-0 bg-" + color + "-50 rounded-full",
                      }),
                      el("div", {
                        className:
                          "absolute inset-0 left-0 w-1/2 bg-" +
                          color +
                          "-600 rounded-l-full",
                      })
                    )
                  : null;

                // Empty bars elements
                var emptyBarEls = Array.from({ length: emptyBars }).map(
                  function (_, i) {
                    return el("div", {
                      key: "empty-" + index + "-" + i,
                      className: "h-2 flex-1 bg-" + color + "-50 rounded-full",
                    });
                  }
                );

                return el(
                  "div",
                  {
                    key: "card-" + index,
                    className:
                      "product-score-card box-border flex flex-col items-start p-4 gap-4 w-full md:w-auto flex-1 bg-white border border-gray-200 rounded-xl",
                  },
                  el(
                    "div",
                    {
                      className:
                        "flex flex-col items-start p-0 gap-1 flex-none self-stretch",
                    },
                    el(
                      "div",
                      {
                        className:
                          "text-base md:text-lg font-bold leading-[110%] md:leading-[1.25rem] flex items-center text-gray-800 flex-none self-stretch",
                      },
                      score + "/5"
                    ),
                    el(
                      "div",
                      {
                        className:
                          "text-xs md:text-base font-medium leading-4 md:leading-6 tracking-2p flex items-center text-gray-500 flex-none self-stretch",
                      },
                      (criterion.name || "") + " "
                    )
                  ),
                  el(
                    "div",
                    {
                      className:
                        "product-score-bar flex flex-row items-center p-0 gap-0.5 flex-none self-stretch mt-auto",
                    },
                    // children: full bars, half bar (if any), empty bars
                    fullBarEls
                      .concat(halfBarEl ? [halfBarEl] : [])
                      .concat(emptyBarEls)
                  )
                );
              });

              // Footer area
              var footer = el(
                "div",
                {
                  className:
                    "product-score-footer flex flex-col md:flex-row md:justify-between md:items-center p-0 gap-4 md:gap-5 flex-none self-stretch",
                },
                el(
                  "div",
                  {
                    className:
                      "geekflare-rating-badge box-border flex flex-row items-end py-1.5 px-3.5 md:px-2.5 gap-3 w-full md:w-auto bg-rating-50 border border-rating-border rounded-full flex-none",
                  },
                  el(
                    "div",
                    {
                      className:
                        "flex flex-row justify-between items-center p-0 gap-2 flex-none grow-1 md:grow-0 w-full md:w-auto",
                    },
                    el(
                      "span",
                      {
                        className:
                          "text-sm font-medium leading-5 tracking-2p text-gray-800 flex-none",
                      },
                      __("Geekflare rating:", "main")
                    ),
                    el(
                      "div",
                      {
                        className:
                          "flex flex-row items-center p-0 gap-2 flex-none",
                      },
                      el(
                        "span",
                        {
                          className:
                            "text-sm md:text-base font-bold leading-[110%] md:leading-[1.125rem] flex items-end text-gray-800 flex-none",
                        },
                        average + "/5"
                      )
                    )
                  )
                )
              );

              // Return full fragment containing cards and footer
              return el(
                Fragment,
                {},
                el(
                  "div",
                  {
                    className:
                      "product-score-cards grid grid-cols-2 lg:grid-cols-4 p-0 gap-3 md:gap-4 flex-none self-stretch",
                  },
                  cards
                ),
                footer
              );
            })()
          )
        )
      );
    },

    save: function () {
      return null;
    },
  });
})(window.wp);
