/**
 * Article Carousel Block - Editor Preview
 * Displays posts from selected categories in a carousel layout with navigation
 */
(function (blocks, blockEditor, element, components, i18n, data) {
  var el = element.createElement;
  var RichText = blockEditor.RichText;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var RangeControl = components.RangeControl;
  var TextControl = components.TextControl;
  var Button = components.Button;
  var __ = i18n.__;

  // Cache for categories and tags (module-level)
  var categoriesCache = null;
  var tagsCache = null;
  var categoriesLoading = false;
  var tagsLoading = false;
  // Carousel counter for unique IDs in editor (maps clientId to counter)
  var carouselCounterMap = {};
  var globalCarouselCounter = 0;

  // Pre-fetch categories and tags when script loads
  (function () {
    var apiFetch = window.wp && window.wp.apiFetch ? window.wp.apiFetch : null;
    var apiRoot =
      typeof wpApiSettings !== "undefined" ? wpApiSettings.root : null;

    // Fetch categories
    if (apiFetch) {
      categoriesLoading = true;
      apiFetch({ path: "/wp/v2/categories?per_page=100" })
        .then(function (cats) {
          categoriesCache = cats;
          categoriesLoading = false;
        })
        .catch(function () {
          categoriesCache = [];
          categoriesLoading = false;
        });
    } else if (apiRoot) {
      categoriesLoading = true;
      fetch(apiRoot + "wp/v2/categories?per_page=100")
        .then(function (response) {
          return response.json();
        })
        .then(function (cats) {
          categoriesCache = cats;
          categoriesLoading = false;
        })
        .catch(function () {
          categoriesCache = [];
          categoriesLoading = false;
        });
    }

    // Fetch tags
    if (apiFetch) {
      tagsLoading = true;
      apiFetch({ path: "/wp/v2/tags?per_page=100" })
        .then(function (tags) {
          tagsCache = tags;
          tagsLoading = false;
        })
        .catch(function () {
          tagsCache = [];
          tagsLoading = false;
        });
    } else if (apiRoot) {
      tagsLoading = true;
      fetch(apiRoot + "wp/v2/tags?per_page=100")
        .then(function (response) {
          return response.json();
        })
        .then(function (tags) {
          tagsCache = tags;
          tagsLoading = false;
        })
        .catch(function () {
          tagsCache = [];
          tagsLoading = false;
        });
    }
  })();

  blocks.registerBlockType("main/article-carousel", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var clientId = props.clientId;

      var blockProps = useBlockProps({
        className: "theme-main articles-carousel articles-carousel-editor",
      });

      var heading = attributes.heading || "Protect your digital assets.";
      var selectedCategoryIds = attributes.categories || [];
      var postLimit = attributes.postLimit || 6;
      var buttonText = attributes.buttonText || "Read More Articles";
      var buttonLinkType = attributes.buttonLinkType || "custom";
      var buttonLinkId = attributes.buttonLinkId || "";
      var buttonCustomUrl = attributes.buttonCustomUrl || "#";

      // Generate unique carousel ID using incrementing counter
      // Use a Map to ensure consistent ID for each block instance
      if (!carouselCounterMap[clientId]) {
        globalCarouselCounter++;
        carouselCounterMap[clientId] = globalCarouselCounter;
      }
      var carouselId = "articles-carousel-" + carouselCounterMap[clientId];

      // Ensure selectedCategoryIds is an array
      if (!Array.isArray(selectedCategoryIds)) {
        selectedCategoryIds = [];
      }

      // Get category options for SelectControl
      var getCategoryOptions = function () {
        var options = [{ label: __("Select Categories", "main"), value: "" }];
        if (categoriesCache && Array.isArray(categoriesCache)) {
          categoriesCache.forEach(function (cat) {
            options.push({ label: cat.name, value: cat.id.toString() });
          });
        }
        return options;
      };

      // Helper to add a category
      var addCategory = function (categoryId) {
        if (!categoryId || selectedCategoryIds.includes(parseInt(categoryId)))
          return;
        var newCategories = [...selectedCategoryIds, parseInt(categoryId)];
        setAttributes({ categories: newCategories });
      };

      // Helper to remove a category
      var removeCategory = function (categoryId) {
        var newCategories = selectedCategoryIds.filter(function (id) {
          return id !== categoryId;
        });
        setAttributes({ categories: newCategories });
      };

      // Get category data by ID for display
      var getCategoryData = function (categoryId) {
        if (!categoriesCache || !Array.isArray(categoriesCache)) return null;
        return (
          categoriesCache.find(function (c) {
            return c.id === parseInt(categoryId);
          }) || null
        );
      };

      // Get category options for button
      var getCategoryOptionsForButton = function () {
        var options = [{ label: __("Select Category", "main"), value: "" }];
        if (categoriesCache && Array.isArray(categoriesCache)) {
          categoriesCache.forEach(function (cat) {
            options.push({ label: cat.name, value: cat.id.toString() });
          });
        }
        return options;
      };

      // Get tag options for button
      var getTagOptionsForButton = function () {
        var options = [{ label: __("Select Tag", "main"), value: "" }];
        if (tagsCache && Array.isArray(tagsCache)) {
          tagsCache.forEach(function (tag) {
            options.push({ label: tag.name, value: tag.id.toString() });
          });
        }
        return options;
      };

      // Build button URL for preview
      var getButtonUrl = function () {
        if (buttonLinkType === "category" && buttonLinkId) {
          return "#"; // Will be replaced by render.php
        } else if (buttonLinkType === "tag" && buttonLinkId) {
          return "#"; // Will be replaced by render.php
        } else {
          return buttonCustomUrl || "#";
        }
      };

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Heading", "main"), initialOpen: true },
            el(RichText, {
              tagName: "h2",
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
              placeholder: __("Enter heading", "main"),
              className: "articles-carousel-heading-input",
              allowedFormats: [],
            })
          ),
          el(
            PanelBody,
            { title: __("Categories", "main"), initialOpen: true },
            el(SelectControl, {
              label: __("Add Category", "main"),
              value: "",
              options: getCategoryOptions(),
              onChange: addCategory,
              disabled: categoriesLoading,
            }),
            selectedCategoryIds.length > 0 &&
              selectedCategoryIds.map(function (categoryId, index) {
                var category = getCategoryData(categoryId);
                var categoryName = category
                  ? category.name || "Unknown Category"
                  : "Loading...";
                return el(
                  "div",
                  {
                    key: categoryId,
                    style: {
                      marginTop: "10px",
                      padding: "10px",
                      border: "1px solid #ddd",
                      borderRadius: "4px",
                      backgroundColor: "#f9f9f9",
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "space-between",
                    },
                  },
                  el("span", { style: { flex: 1 } }, categoryName),
                  el(
                    Button,
                    {
                      isSmall: true,
                      isDestructive: true,
                      onClick: function () {
                        removeCategory(categoryId);
                      },
                    },
                    __("Remove", "main")
                  )
                );
              })
          ),
          el(
            PanelBody,
            { title: __("Post Settings", "main"), initialOpen: true },
            el(RangeControl, {
              label: __("Number of Posts", "main"),
              value: postLimit,
              onChange: function (value) {
                setAttributes({ postLimit: value });
              },
              min: 1,
              max: 20,
            })
          ),
          el(
            PanelBody,
            { title: __("Button Settings", "main"), initialOpen: true },
            el(TextControl, {
              label: __("Button Text", "main"),
              value: buttonText,
              onChange: function (value) {
                setAttributes({ buttonText: value });
              },
            }),
            el(SelectControl, {
              label: __("Link Type", "main"),
              value: buttonLinkType,
              options: [
                { label: __("Category", "main"), value: "category" },
                { label: __("Tag", "main"), value: "tag" },
                { label: __("Custom URL", "main"), value: "custom" },
              ],
              onChange: function (value) {
                setAttributes({
                  buttonLinkType: value,
                  buttonLinkId: value === "custom" ? "" : buttonLinkId,
                  buttonCustomUrl: value !== "custom" ? "" : buttonCustomUrl,
                });
              },
            }),
            buttonLinkType === "category"
              ? el(SelectControl, {
                  label: __("Select Category", "main"),
                  value: buttonLinkId,
                  options: getCategoryOptionsForButton(),
                  onChange: function (value) {
                    setAttributes({ buttonLinkId: value });
                  },
                  disabled: categoriesLoading,
                })
              : buttonLinkType === "tag"
              ? el(SelectControl, {
                  label: __("Select Tag", "main"),
                  value: buttonLinkId,
                  options: getTagOptionsForButton(),
                  onChange: function (value) {
                    setAttributes({ buttonLinkId: value });
                  },
                  disabled: tagsLoading,
                })
              : el(TextControl, {
                  label: __("Custom URL", "main"),
                  value: buttonCustomUrl,
                  onChange: function (value) {
                    setAttributes({ buttonCustomUrl: value });
                  },
                  placeholder: __("https://example.com", "main"),
                })
          )
        ),
        // Editor preview - EXACT frontend markup
        el(
          "section",
          {
            className:
              "security-guides-section bg-gray-25 py-16 md:py-24 overflow-hidden",
          },
          el(
            "div",
            { className: "container-1056 flex flex-col gap-12 md:gap-14" },
            el(
              "div",
              { className: "flex items-end md:justify-between gap-6" },
              el(RichText, {
                tagName: "h2",
                className:
                  "text-3xl md:text-5xl font-bold font-gilroy text-gray-900 leading-none md:leading-none",
                value: heading,
                onChange: function (value) {
                  setAttributes({ heading: value });
                },
                placeholder: __("Enter heading", "main"),
                allowedFormats: [],
              }),
              el(
                "div",
                { className: "flex items-center gap-2 md:gap-3" },
                el(
                  "button",
                  {
                    type: "button",
                    "aria-label": __("Previous article", "main"),
                    className:
                      "btn btn--outline rounded-full max-sm:py-2 max-sm:px-3",
                    "data-flickity-control": "prev",
                    "data-flickity-target": "#" + carouselId,
                  },
                  el(
                    "svg",
                    {
                      xmlns: "http://www.w3.org/2000/svg",
                      width: "16",
                      height: "16",
                      viewBox: "0 0 100 100",
                    },
                    el(
                      "style",
                      null,
                      ".s0 { fill: none; stroke: #252b37; stroke-linecap: round; stroke-linejoin: round; stroke-width: 9.4; }"
                    ),
                    el("path", {
                      d: "M79.17 50H20.83M45.83 75l-25-25M45.83 25l-25 25",
                      className: "s0",
                    })
                  )
                ),
                el(
                  "button",
                  {
                    type: "button",
                    "aria-label": __("Next article", "main"),
                    className:
                      "btn btn--outline rounded-full max-sm:py-2 max-sm:px-3",
                    "data-flickity-control": "next",
                    "data-flickity-target": "#" + carouselId,
                  },
                  el(
                    "svg",
                    {
                      xmlns: "http://www.w3.org/2000/svg",
                      width: "16",
                      height: "16",
                      viewBox: "0 0 16 16",
                    },
                    el(
                      "style",
                      null,
                      ".se0 { fill: none; stroke: #252b37; stroke-linecap: round; stroke-linejoin: round; stroke-width: 1.5; }"
                    ),
                    el("path", {
                      d: "M3.33 8h9.34M8.67 12l4-4M8.67 4l4 4",
                      className: "se0",
                    })
                  )
                )
              )
            ),
            el(
              "div",
              {
                id: carouselId,
                className: "articles-carousel -mx-6",
                "data-flickity": JSON.stringify({
                  cellAlign: "left",
                  pageDots: false,
                  wrapAround: false,
                  imagesLoaded: true,
                  prevNextButtons: false,
                  contain: true,
                }),
              },
              // Placeholder articles
              Array.from({ length: Math.min(postLimit, 6) }, function (_, i) {
                var categoryId =
                  selectedCategoryIds.length > 0
                    ? selectedCategoryIds[0]
                    : null;
                var category = categoryId ? getCategoryData(categoryId) : null;
                var categoryName = category ? category.name : "AI for Business";

                return el(
                  "div",
                  { key: i, className: "w-[23rem] px-6" },
                  el(
                    "article",
                    { className: "flex flex-col gap-5.5 md:gap-6" },
                    el(
                      "div",
                      { className: "relative rounded-2xl overflow-hidden" },
                      el("img", {
                        src: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23e5e7eb' width='400' height='300'/%3E%3C/svg%3E",
                        alt: "",
                        className: "w-full object-cover",
                        loading: "lazy",
                      })
                    ),
                    el(
                      "div",
                      { className: "flex flex-col gap-4" },
                      el(
                        "div",
                        {
                          className:
                            "text-xs font-semibold tracking-widest uppercase text-primary",
                        },
                        categoryName
                      ),
                      el(
                        "div",
                        { className: "flex flex-col gap-2" },
                        el(
                          "h3",
                          {
                            className:
                              "text-lg font-semibold text-gray-900 md:-tracking-2p",
                          },
                          "Sample Article Title " + (i + 1)
                        ),
                        el(
                          "div",
                          {
                            className:
                              "text-sm font-medium text-gray-500 tracking-2p leading-5 line-clamp-1 md:tracking-1p",
                          },
                          "This is a sample description for an article that will be replaced with real content on the frontend."
                        )
                      )
                    )
                  )
                );
              })
            ),
            el(
              "div",
              { className: "flex justify-center" },
              el(
                "a",
                {
                  href: getButtonUrl(),
                  className: "btn btn--primary rounded-full",
                },
                buttonText || "Read More Articles",
                el(
                  "svg",
                  {
                    xmlns: "http://www.w3.org/2000/svg",
                    width: "16",
                    height: "16",
                    fill: "none",
                    viewBox: "0 0 16 16",
                  },
                  el("path", {
                    stroke: "#fff",
                    strokeLinecap: "round",
                    strokeLinejoin: "round",
                    strokeWidth: "1.5",
                    d: "M6 3.333 10.667 8 6 12.666",
                  })
                )
              )
            )
          )
        )
      );
    },
    save: function () {
      return null; // Server-side rendered
    },
  });
})(
  window.wp.blocks,
  window.wp.blockEditor,
  window.wp.element,
  window.wp.components,
  window.wp.i18n,
  window.wp.data
);
