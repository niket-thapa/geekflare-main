/**
 * Explore Categories Block - Editor Preview
 * Fully editable block with category/tag selection
 */
(function (blocks, blockEditor, element, components, i18n) {
  var el = element.createElement;
  var RichText = blockEditor.RichText;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var MediaUpload = blockEditor.MediaUpload;
  var MediaUploadCheck = blockEditor.MediaUploadCheck;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var SelectControl = components.SelectControl;
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
      apiFetch({ path: "/wp/v2/categories?per_page=100" })
        .then(function (cats) {
          categoriesCache = cats;
        })
        .catch(function () {
          categoriesCache = [];
        });
    } else if (apiRoot) {
      fetch(apiRoot + "wp/v2/categories?per_page=100")
        .then(function (response) {
          return response.json();
        })
        .then(function (cats) {
          categoriesCache = cats;
        })
        .catch(function () {
          categoriesCache = [];
        });
    }

    // Fetch tags
    if (apiFetch) {
      apiFetch({ path: "/wp/v2/tags?per_page=100" })
        .then(function (tags) {
          tagsCache = tags;
        })
        .catch(function () {
          tagsCache = [];
        });
    } else if (apiRoot) {
      fetch(apiRoot + "wp/v2/tags?per_page=100")
        .then(function (response) {
          return response.json();
        })
        .then(function (tags) {
          tagsCache = tags;
        })
        .catch(function () {
          tagsCache = [];
        });
    }
  })();

  blocks.registerBlockType("main/explore-categories", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      // Add theme-main class for higher CSS specificity
      var blockProps = useBlockProps({
        className:
          "theme-main explore-category-section explore-categories-editor",
      });

      var heading = attributes.heading || "Explore guides by category";
      var categories = attributes.categories || [];

      // Ensure categories is an array
      if (!Array.isArray(categories)) {
        categories = [];
      }

      // Helper functions for managing categories
      var updateCategory = function (index, field, value) {
        var newCategories = categories.slice();
        if (!newCategories[index]) {
          newCategories[index] = {};
        }
        newCategories[index][field] = value;
        setAttributes({
          categories: newCategories.filter(function (c) {
            return c.title && c.title.trim();
          }),
        });
      };

      var addCategory = function () {
        var newCategories = categories.slice();
        newCategories.push({
          title: "",
          subtitle: "",
          linkType: "category",
          linkId: "",
          customUrl: "",
          icon: null,
        });
        setAttributes({ categories: newCategories });
      };

      var removeCategory = function (index) {
        var newCategories = categories.slice();
        newCategories.splice(index, 1);
        setAttributes({
          categories: newCategories.filter(function (c) {
            return c.title && c.title.trim();
          }),
        });
      };

      // Get category options
      var getCategoryOptions = function () {
        var options = [{ label: __("Select Category", "main"), value: "" }];
        if (categoriesCache && Array.isArray(categoriesCache)) {
          categoriesCache.forEach(function (cat) {
            options.push({ label: cat.name, value: cat.id.toString() });
          });
        }
        return options;
      };

      // Get tag options
      var getTagOptions = function () {
        var options = [{ label: __("Select Tag", "main"), value: "" }];
        if (tagsCache && Array.isArray(tagsCache)) {
          tagsCache.forEach(function (tag) {
            options.push({ label: tag.name, value: tag.id.toString() });
          });
        }
        return options;
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
            el(TextControl, {
              label: __("Heading", "main"),
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
              placeholder: __("Enter heading", "main"),
            })
          ),
          el(
            PanelBody,
            { title: __("Categories", "main"), initialOpen: true },
            categories.map(function (category, index) {
              var linkType = category.linkType || "category";
              var linkId = category.linkId || "";
              var customUrl = category.customUrl || "";
              var iconUrl = "";
              if (category.icon && category.icon.url) {
                iconUrl = category.icon.url;
              }

              return el(
                "div",
                {
                  key: index,
                  style: {
                    marginBottom: "20px",
                    padding: "15px",
                    border: "1px solid #ddd",
                    borderRadius: "4px",
                    backgroundColor: "#f9f9f9",
                  },
                },
                el(TextControl, {
                  label: __("Title", "main"),
                  value: category.title || "",
                  onChange: function (value) {
                    updateCategory(index, "title", value);
                  },
                  placeholder: __("e.g., AI for Business", "main"),
                  style: { marginBottom: "10px" },
                }),
                el(TextControl, {
                  label: __("Subtitle/Tagline", "main"),
                  value: category.subtitle || "",
                  onChange: function (value) {
                    updateCategory(index, "subtitle", value);
                  },
                  placeholder: __("e.g., Protect your digital assets.", "main"),
                  style: { marginBottom: "10px" },
                }),
                el(SelectControl, {
                  label: __("Link Type", "main"),
                  value: linkType,
                  options: [
                    { label: __("Category", "main"), value: "category" },
                    { label: __("Tag", "main"), value: "tag" },
                    { label: __("Custom URL", "main"), value: "custom" },
                  ],
                  onChange: function (value) {
                    updateCategory(index, "linkType", value);
                    if (value === "custom") {
                      updateCategory(index, "linkId", "");
                    } else {
                      updateCategory(index, "customUrl", "");
                    }
                  },
                  style: { marginBottom: "10px" },
                }),
                linkType === "category"
                  ? el(SelectControl, {
                      label: __("Select Category", "main"),
                      value: linkId,
                      options: getCategoryOptions(),
                      onChange: function (value) {
                        updateCategory(index, "linkId", value);
                      },
                      style: { marginBottom: "10px" },
                    })
                  : linkType === "tag"
                  ? el(SelectControl, {
                      label: __("Select Tag", "main"),
                      value: linkId,
                      options: getTagOptions(),
                      onChange: function (value) {
                        updateCategory(index, "linkId", value);
                      },
                      style: { marginBottom: "10px" },
                    })
                  : el(TextControl, {
                      label: __("Custom URL", "main"),
                      value: customUrl,
                      onChange: function (value) {
                        updateCategory(index, "customUrl", value);
                      },
                      placeholder: __("https://example.com", "main"),
                      style: { marginBottom: "10px" },
                    }),
                el(
                  MediaUploadCheck,
                  {},
                  el(
                    "div",
                    { style: { marginBottom: "10px" } },
                    el(MediaUpload, {
                      onSelect: function (media) {
                        setAttributes({
                          categories: categories.map(function (cat, i) {
                            if (i === index) {
                              return Object.assign({}, cat, {
                                icon: { id: media.id, url: media.url },
                              });
                            }
                            return cat;
                          }),
                        });
                      },
                      allowedTypes: ["image"],
                      value: category.icon ? category.icon.id : null,
                      render: function (obj) {
                        return el(
                          "div",
                          {},
                          iconUrl
                            ? el(
                                "div",
                                {},
                                el("img", {
                                  src: iconUrl,
                                  alt: category.title || "",
                                  style: { maxWidth: "100px", height: "auto" },
                                }),
                                el(
                                  "div",
                                  { style: { marginTop: "10px" } },
                                  el(
                                    Button,
                                    {
                                      onClick: obj.open,
                                      variant: "secondary",
                                      isSmall: true,
                                    },
                                    __("Replace Icon", "main")
                                  ),
                                  " ",
                                  el(
                                    Button,
                                    {
                                      onClick: function () {
                                        setAttributes({
                                          categories: categories.map(function (
                                            cat,
                                            i
                                          ) {
                                            if (i === index) {
                                              var newCat = Object.assign(
                                                {},
                                                cat
                                              );
                                              delete newCat.icon;
                                              return newCat;
                                            }
                                            return cat;
                                          }),
                                        });
                                      },
                                      variant: "link",
                                      isDestructive: true,
                                      isSmall: true,
                                    },
                                    __("Remove", "main")
                                  )
                                )
                              )
                            : el(
                                Button,
                                {
                                  onClick: obj.open,
                                  variant: "secondary",
                                  isSmall: true,
                                },
                                __("Upload Icon", "main")
                              )
                        );
                      },
                    })
                  )
                ),
                el(
                  Button,
                  {
                    isDestructive: true,
                    isSmall: true,
                    onClick: function () {
                      removeCategory(index);
                    },
                    icon: "trash",
                    label: __("Remove", "main"),
                  },
                  __("Remove Category", "main")
                )
              );
            }),
            el(
              Button,
              {
                variant: "secondary",
                onClick: addCategory,
                style: { marginTop: "10px", width: "100%" },
              },
              __("+ Add Category", "main")
            )
          )
        ),
        // Editor preview - EXACT frontend markup with higher specificity wrapper
        el(
          "div",
          {
            className:
              "theme-main explore-category-section py-6 md:py-8 lg:py-10",
          },
          el(
            "div",
            { className: "container-1280" },
            el(
              "div",
              {
                className:
                  "home-categories-container-1056 rounded-3xl md:rounded-4xl p-5 md:p-12 lg:p-16 xl:p-24 flex flex-col gap-6 md:gap-11 lg:gap-12 xl:gap-14",
              },
              heading &&
                el(
                  "h2",
                  {
                    className:
                      "text-center text-white text-3xl md:text-4xl lg:text-5xl font-bold font-gilroy leading-none pt-3 mt-0.5 pb-0.5 md:p-0 lg:-mb-0.25",
                  },
                  heading
                ),
              categories && categories.length > 0
                ? el(
                    "div",
                    {
                      className:
                        "home-categories-content pt-1 flex flex-col gap-3 md:flex-row md:flex-wrap md:gap-6 md:justify-center xl:px-6",
                    },
                    categories.map(function (category, index) {
                      if (!category.title) return null;
                      var iconUrl = "";
                      if (category.icon && category.icon.url) {
                        iconUrl = category.icon.url;
                      }

                      return el(
                        "a",
                        {
                          key: index,
                          href: "#",
                          className:
                            "home-categories-item flex w-full md:flex-[0_0_calc(50%-0.75rem)] xl:flex-[0_0_calc(33.333%-1rem)] p-3 md:p-4 bg-white rounded-xl gap-4 items-center",
                          onClick: function (e) {
                            e.preventDefault();
                          },
                          style: { cursor: "default" },
                        },
                        iconUrl &&
                          el(
                            "div",
                            { className: "home-categories-item-icon w-11" },
                            el("img", {
                              src: iconUrl,
                              alt: category.title + " category icon",
                              loading: "lazy",
                              style: { width: "100%", height: "auto" },
                            })
                          ),
                        el(
                          "div",
                          { className: "home-categories-item-text flex-1" },
                          category.title &&
                            el(
                              "h3",
                              {
                                className:
                                  "text-base font-gilroy font-semibold leading-normal md:text-lg",
                              },
                              category.title
                            ),
                          category.subtitle &&
                            el(
                              "div",
                              {
                                className:
                                  "text-xs text-gray-500 tracking-2p leading-4 md:text-sm md:leading-5 md:tracking-1p",
                              },
                              category.subtitle
                            )
                        ),
                        el(
                          "svg",
                          {
                            xmlns: "http://www.w3.org/2000/svg",
                            className: "w-5 h-auto",
                            width: "20",
                            height: "20",
                            fill: "none",
                            viewBox: "0 0 20 20",
                            "aria-hidden": "true",
                            role: "presentation",
                          },
                          el("path", {
                            stroke: "#252b37",
                            strokeLinecap: "round",
                            strokeLinejoin: "round",
                            strokeWidth: "1.5",
                            d: "M7.5 4.167 13.333 10 7.5 15.833",
                          })
                        )
                      );
                    })
                  )
                : el(
                    "div",
                    {
                      style: {
                        padding: "20px",
                        textAlign: "center",
                        color: "#999",
                      },
                    },
                    __("Add categories using the sidebar panel", "main")
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
