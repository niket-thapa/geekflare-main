/**
 * Insights Section Block - Editor Preview
 * Displays sticky and regular posts from selected categories
 */
(function (blocks, blockEditor, element, components, i18n) {
  var el = element.createElement;
  var RichText = blockEditor.RichText;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var RangeControl = components.RangeControl;
  var ToggleControl = components.ToggleControl;
  var TextControl = components.TextControl;
  var Button = components.Button;
  var __ = i18n.__;

  // Cache for categories and tags (module-level)
  var categoriesCache = null;
  var tagsCache = null;
  var categoriesLoading = false;
  var tagsLoading = false;

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

  blocks.registerBlockType("main/insights-section", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      // Add theme-main class for higher CSS specificity
      var blockProps = useBlockProps({
        className: "theme-main insights-section insights-section-editor",
      });

      var heading =
        attributes.heading ||
        "Fresh insights to help you make smarter software decisions";
      var categories = attributes.categories || [];
      var stickyLimit = attributes.stickyLimit || 3;
      var regularLimit = attributes.regularLimit || 3;
      var moveStickyToRight = attributes.moveStickyToRight || false;
      var headingAlign = attributes.headingAlign || "left";
      var showButton = attributes.showButton || false;
      var buttonText = attributes.buttonText || "Read More Articles";
      var buttonLinkType = attributes.buttonLinkType || "custom";
      var buttonLinkId = attributes.buttonLinkId || "";
      var buttonCustomUrl = attributes.buttonCustomUrl || "#";

      // Ensure categories is an array
      if (!Array.isArray(categories)) {
        categories = [];
      }

      // Get category options
      var getCategoryOptions = function () {
        var options = [];
        if (categoriesCache && Array.isArray(categoriesCache)) {
          categoriesCache.forEach(function (cat) {
            options.push({ label: cat.name, value: cat.id.toString() });
          });
        }
        return options;
      };

      // Helper to add a category
      var addCategory = function (categoryId) {
        if (!categoryId) return;
        var categoryIdInt = parseInt(categoryId);
        var newCategories = categories.slice();
        // Check if category already exists
        if (newCategories.indexOf(categoryIdInt) !== -1) {
          return;
        }
        newCategories.push(categoryIdInt);
        setAttributes({ categories: newCategories });
      };

      // Helper to remove a category
      var removeCategory = function (index) {
        var newCategories = categories.slice();
        newCategories.splice(index, 1);
        setAttributes({ categories: newCategories });
      };

      // Get category data by ID
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

      // Get sticky posts class
      var stickyPostsClass = "min-w-0 sticky_posts";
      if (moveStickyToRight) {
        stickyPostsClass += " lg:order-2";
      }

      // Get heading alignment class
      var headingAlignClass =
        "text-" +
        (headingAlign === "center"
          ? "center"
          : headingAlign === "right"
          ? "right"
          : "left");

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Content", "main"), initialOpen: true },
            el(RichText, {
              tagName: "h2",
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
              placeholder: __("Enter heading", "main"),
              className: "insights-heading-input",
            }),
            el(SelectControl, {
              label: __("Heading Alignment", "main"),
              value: headingAlign,
              options: [
                { label: __("Left", "main"), value: "left" },
                { label: __("Center", "main"), value: "center" },
                { label: __("Right", "main"), value: "right" },
              ],
              onChange: function (value) {
                setAttributes({ headingAlign: value || "left" });
              },
            })
          ),
          el(
            PanelBody,
            { title: __("Categories", "main"), initialOpen: true },
            el(SelectControl, {
              label: __("Add Category", "main"),
              value: "",
              options: [
                { label: __("Select Category", "main"), value: "" },
              ].concat(getCategoryOptions()),
              onChange: addCategory,
              disabled: categoriesLoading,
            }),
            categories.length > 0 &&
              categories.map(function (categoryId, index) {
                var category = getCategoryData(categoryId);
                var categoryName = category
                  ? category.name || "Unknown"
                  : "Loading...";
                return el(
                  "div",
                  {
                    key: index,
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
                  el(
                    "span",
                    { style: { flex: 1 } },
                    index + 1 + ". " + categoryName
                  ),
                  el(
                    Button,
                    {
                      isSmall: true,
                      isDestructive: true,
                      onClick: function () {
                        removeCategory(index);
                      },
                    },
                    __("Remove", "main")
                  )
                );
              })
          ),
          el(
            PanelBody,
            { title: __("Post Limits", "main"), initialOpen: true },
            el(RangeControl, {
              label: __("Sticky Posts Limit", "main"),
              value: stickyLimit,
              onChange: function (value) {
                setAttributes({ stickyLimit: value });
              },
              min: 1,
              max: 10,
            }),
            el(RangeControl, {
              label: __("Regular Posts Limit", "main"),
              value: regularLimit,
              onChange: function (value) {
                setAttributes({ regularLimit: value });
              },
              min: 1,
              max: 10,
            })
          ),
          el(
            PanelBody,
            { title: __("Layout", "main"), initialOpen: true },
            el(ToggleControl, {
              label: __("Move sticky posts to right column", "main"),
              checked: moveStickyToRight,
              onChange: function (value) {
                setAttributes({ moveStickyToRight: value });
              },
            })
          ),
          el(
            PanelBody,
            { title: __("Button Settings", "main"), initialOpen: false },
            el(ToggleControl, {
              label: __("Show Read More Button", "main"),
              checked: showButton,
              onChange: function (value) {
                setAttributes({ showButton: value });
              },
            }),
            showButton &&
              el(TextControl, {
                label: __("Button Text", "main"),
                value: buttonText,
                onChange: function (value) {
                  setAttributes({ buttonText: value });
                },
              }),
            showButton &&
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
            showButton &&
              buttonLinkType === "category" &&
              el(SelectControl, {
                label: __("Select Category", "main"),
                value: buttonLinkId,
                options: getCategoryOptionsForButton(),
                onChange: function (value) {
                  setAttributes({ buttonLinkId: value });
                },
                disabled: categoriesLoading,
              }),
            showButton &&
              buttonLinkType === "tag" &&
              el(SelectControl, {
                label: __("Select Tag", "main"),
                value: buttonLinkId,
                options: getTagOptionsForButton(),
                onChange: function (value) {
                  setAttributes({ buttonLinkId: value });
                },
                disabled: tagsLoading,
              }),
            showButton &&
              buttonLinkType === "custom" &&
              el(TextControl, {
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
            className: "insights-section bg-gray-25 py-16 md:py-24",
          },
          el(
            "div",
            { className: "container-1056 flex flex-col gap-12 lg:gap-16" },
            el(
              "div",
              { className: "flex flex-col gap-4 " + headingAlignClass },
              el(RichText, {
                tagName: "h2",
                className:
                  "text-3xl md:text-5xl font-bold font-gilroy text-gray-900 leading-none md:leading-none max-w-[66rem]",
                value: heading,
                onChange: function (value) {
                  setAttributes({ heading: value });
                },
                placeholder: __("Enter heading", "main"),
                allowedFormats: [],
              })
            ),
            el(
              "div",
              {
                className:
                  "flex flex-col gap-20 lg:grid lg:grid-cols-2 lg:gap-16 xl:gap-24",
              },
              // Sticky posts container
              el(
                "div",
                { className: stickyPostsClass },
                el(
                  "div",
                  {
                    "data-flickity": JSON.stringify({
                      cellAlign: "left",
                      wrapAround: true,
                      pageDots: true,
                      imagesLoaded: true,
                      prevNextButtons: false,
                      autoPlay: true,
                    }),
                    className: "-m-6 feature-articles-carousel",
                  },
                  // Placeholder sticky posts (3 articles)
                  [1, 2, 3].map(function (i) {
                    return el(
                      "article",
                      { key: i, className: "w-full flex flex-col gap-6 p-6" },
                      el(
                        "div",
                        { className: "relative rounded-2xl overflow-hidden" },
                        el("img", {
                          src: "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23e5e7eb' width='400' height='300'/%3E%3C/svg%3E",
                          alt: "Featured guide",
                          className: "w-full object-cover",
                          loading: "lazy",
                        }),
                        el(
                          "div",
                          {
                            className:
                              "absolute top-3 start-3 m-0.25 inline-flex items-center gap-1 px-2.5 py-2 rounded-full bg-white text-xs leading-[1.25] font-semibold text-primary shadow-sm",
                          },
                          el(
                            "span",
                            {
                              className:
                                "inline-flex h-4 w-4 items-center justify-center",
                              "aria-hidden": "true",
                            },
                            el(
                              "svg",
                              {
                                xmlns: "http://www.w3.org/2000/svg",
                                width: "17",
                                height: "17",
                                viewBox: "0 0 17 17",
                                "aria-hidden": "true",
                                role: "presentation",
                              },
                              el("path", {
                                d: "M9.11 1.14q-.09-.08-.21-.11t-.24.01q-.12.03-.22.11-.09.09-.13.2l-1.4 3.84L5.38 3.7q-.08-.07-.18-.11-.11-.04-.22-.03-.1.01-.2.06t-.16.14Q2.55 6.48 2.54 9.15c0 1.49.59 2.91 1.64 3.96a5.592 5.592 0 0 0 9.55-3.96c0-3.78-3.23-6.86-4.62-8.02zm2.58 8.62q-.1.55-.36 1.05-.26.49-.66.89-.39.4-.89.66t-1.05.35q-.21.04-.38-.08-.17-.13-.21-.33-.03-.21.09-.38t.33-.21c1.05-.17 1.95-1.07 2.12-2.12q.05-.2.22-.32.17-.11.37-.08t.32.2q.12.16.1.37",
                                style: { fill: "#e84300" },
                              })
                            )
                          ),
                          el("span", null, "Spotlight")
                        )
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
                          categories.length > 0 && categories[0]
                            ? getCategoryData(categories[0])
                              ? getCategoryData(categories[0]).name
                              : "Category"
                            : "Business Software"
                        ),
                        el(
                          "h3",
                          {
                            className:
                              "text-xl md:text-2xl font-semibold text-gray-900 md:-tracking-2p",
                          },
                          "Sample Sticky Post Title " + i
                        ),
                        el(
                          "div",
                          {
                            className:
                              "text-sm font-medium text-gray-500 leading-5 tracking-1p",
                          },
                          "This is a sample description for a sticky post that will be replaced with real content on the frontend."
                        ),
                        el(
                          "a",
                          {
                            href: "#",
                            className:
                              "text-sm font-semibold text-primary btn-read-guide py-2 md:py-3",
                          },
                          "Read Guide"
                        )
                      )
                    );
                  })
                )
              ),
              // Regular posts container
              el(
                "div",
                { className: "flex flex-col gap-8 min-w-0 other_posts" },
                // Placeholder regular posts (3 articles)
                [1, 2, 3].map(function (i) {
                  var isLast = i === 3;
                  return el(
                    "article",
                    {
                      key: i,
                      className:
                        "flex flex-col gap-4" +
                        (isLast ? "" : " border-b border-gray-200 pb-8"),
                    },
                    el(
                      "div",
                      {
                        className:
                          "text-xs font-semibold tracking-widest uppercase text-primary",
                      },
                      categories.length > 0 && categories[0]
                        ? getCategoryData(categories[0])
                          ? getCategoryData(categories[0]).name
                          : "Category"
                        : "HR & People"
                    ),
                    el(
                      "div",
                      { className: "flex flex-col gap-2" },
                      el(
                        "h3",
                        {
                          className:
                            "text-lg md:text-xl md:-tracking-2p font-semibold text-gray-900",
                        },
                        "Sample Regular Post Title " + i
                      ),
                      el(
                        "div",
                        {
                          className:
                            "text-sm font-medium text-gray-500 leading-5 tracking-2p md:tracking-1p line-clamp-1",
                        },
                        "This is a sample description for a regular post that will be replaced with real content on the frontend."
                      )
                    ),
                    el(
                      "a",
                      {
                        href: "#",
                        className:
                          "text-sm font-semibold text-primary btn-read-guide md:py-2 md:mt-1",
                      },
                      "Read Guide"
                    )
                  );
                })
              )
            ),
            showButton &&
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
  window.wp.i18n
);
