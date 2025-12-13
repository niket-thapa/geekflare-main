/**
 * Meet Experts Block - Editor Preview
 * Fully editable block with author selection and grouping
 */
(function (blocks, blockEditor, element, components, i18n) {
  var el = element.createElement;
  var RichText = blockEditor.RichText;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var SelectControl = components.SelectControl;
  var Button = components.Button;
  var __ = i18n.__;

  // Cache for authors (module-level)
  var authorsCache = null;
  var authorsLoading = false;

  // Pre-fetch authors when script loads
  (function () {
    var apiFetch = window.wp && window.wp.apiFetch ? window.wp.apiFetch : null;
    var apiRoot =
      typeof wpApiSettings !== "undefined" ? wpApiSettings.root : null;

    // Fetch authors
    if (apiFetch) {
      authorsLoading = true;
      apiFetch({ path: "/wp/v2/users?who=authors&per_page=100" })
        .then(function (authors) {
          authorsCache = authors;
          authorsLoading = false;
        })
        .catch(function () {
          authorsCache = [];
          authorsLoading = false;
        });
    } else if (apiRoot) {
      authorsLoading = true;
      fetch(apiRoot + "wp/v2/users?who=authors&per_page=100")
        .then(function (response) {
          return response.json();
        })
        .then(function (authors) {
          authorsCache = authors;
          authorsLoading = false;
        })
        .catch(function () {
          authorsCache = [];
          authorsLoading = false;
        });
    }
  })();

  blocks.registerBlockType("main/meet-experts", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      // Add theme-main class for higher CSS specificity
      var blockProps = useBlockProps({
        className: "theme-main meet-experts-section meet-experts-editor",
      });

      var heading = attributes.heading || "Meet our experts";
      var subheading =
        attributes.subheading ||
        "Discover in-depth insights, reviews, and guides crafted by our team of industry experts.";
      var authors = attributes.authors || [];

      // Ensure authors is an array
      if (!Array.isArray(authors)) {
        authors = [];
      }

      // Get author options
      var getAuthorOptions = function () {
        var options = [{ label: __("Select Author", "main"), value: "" }];
        if (authorsCache && Array.isArray(authorsCache)) {
          authorsCache.forEach(function (author) {
            var label = author.name || author.slug || "Author #" + author.id;
            options.push({ label: label, value: author.id.toString() });
          });
        }
        return options;
      };

      // Helper to add an author
      var addAuthor = function (authorId) {
        if (!authorId) return;
        var newAuthors = authors.slice();
        // Check if author already exists
        if (newAuthors.indexOf(parseInt(authorId)) !== -1) {
          return;
        }
        newAuthors.push(parseInt(authorId));
        setAttributes({ authors: newAuthors });
      };

      // Helper to remove an author
      var removeAuthor = function (index) {
        var newAuthors = authors.slice();
        newAuthors.splice(index, 1);
        setAttributes({ authors: newAuthors });
      };

      // Helper to move author up/down
      var moveAuthor = function (index, direction) {
        var newAuthors = authors.slice();
        var newIndex = direction === "up" ? index - 1 : index + 1;
        if (newIndex < 0 || newIndex >= newAuthors.length) return;
        var temp = newAuthors[index];
        newAuthors[index] = newAuthors[newIndex];
        newAuthors[newIndex] = temp;
        setAttributes({ authors: newAuthors });
      };

      // Get author data by ID
      var getAuthorData = function (authorId) {
        if (!authorsCache || !Array.isArray(authorsCache)) return null;
        return (
          authorsCache.find(function (a) {
            return a.id === parseInt(authorId);
          }) || null
        );
      };

      // Group authors by 4
      var groupAuthors = function (authorsArray) {
        var groups = [];
        for (var i = 0; i < authorsArray.length; i += 4) {
          groups.push(authorsArray.slice(i, i + 4));
        }
        return groups;
      };

      var authorGroups = groupAuthors(authors);
      var showCarousel = authors.length > 4;

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
              className: "meet-experts-heading-input",
            }),
            el(RichText, {
              tagName: "div",
              value: subheading,
              onChange: function (value) {
                setAttributes({ subheading: value });
              },
              placeholder: __("Enter subheading", "main"),
              className: "meet-experts-subheading-input",
            })
          ),
          el(
            PanelBody,
            { title: __("Authors", "main"), initialOpen: true },
            el(SelectControl, {
              label: __("Add Author", "main"),
              value: "",
              options: getAuthorOptions(),
              onChange: addAuthor,
              disabled: authorsLoading,
            }),
            authors.length > 0 &&
              authors.map(function (authorId, index) {
                var author = getAuthorData(authorId);
                var authorName = author
                  ? author.name || author.slug || "Unknown"
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
                    index + 1 + ". " + authorName
                  ),
                  el(
                    "div",
                    { style: { display: "flex", gap: "5px" } },
                    index > 0 &&
                      el(
                        Button,
                        {
                          isSmall: true,
                          onClick: function () {
                            moveAuthor(index, "up");
                          },
                        },
                        "↑"
                      ),
                    index < authors.length - 1 &&
                      el(
                        Button,
                        {
                          isSmall: true,
                          onClick: function () {
                            moveAuthor(index, "down");
                          },
                        },
                        "↓"
                      ),
                    el(
                      Button,
                      {
                        isSmall: true,
                        isDestructive: true,
                        onClick: function () {
                          removeAuthor(index);
                        },
                      },
                      __("Remove", "main")
                    )
                  )
                );
              })
          )
        ),
        // Editor preview - EXACT frontend markup with higher specificity wrapper
        el(
          "section",
          {
            className:
              "meet-experts-section relative isolate overflow-hidden bg-[#FFF8F5] py-8 md:py-24",
          },
          el("span", {
            className:
              "absolute inset-0 pointer-events-none opacity-70 bg-cover meet-experts-texture",
            style: (function () {
              // Get theme URL from localized script or construct it
              var imageUrl = "";
              if (typeof mainTheme !== "undefined" && mainTheme.imageUrl) {
                imageUrl = mainTheme.imageUrl;
              } else {
                // Fallback: construct from wpApiSettings or current URL
                var apiRoot =
                  typeof wpApiSettings !== "undefined"
                    ? wpApiSettings.root
                    : "";
                if (apiRoot) {
                  imageUrl = apiRoot.replace(
                    "/wp-json/",
                    "/wp-content/themes/main/assets/images/"
                  );
                } else {
                  var origin = window.location.origin;
                  var path = window.location.pathname;
                  path = path.replace(/\/wp-admin.*$/, "").replace(/\/.*$/, "");
                  imageUrl =
                    origin + path + "/wp-content/themes/main/assets/images/";
                }
              }

              return {
                "--texture-mobile": "url('" + imageUrl + "texture-mobile.svg')",
                "--texture-desktop":
                  "url('" + imageUrl + "texture-desktop.svg')",
                backgroundImage: "var(--texture-mobile)",
              };
            })(),
          }),
          el(
            "div",
            {
              className:
                "container-1056 relative flex flex-col items-center gap-8 md:gap-14",
            },
            el(
              "div",
              {
                className:
                  "flex flex-col items-center text-center gap-3 md:gap-3 max-w-[22.75rem] md:max-w-4xl pt-8 pb-4 md:py-0",
              },
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
              el(RichText, {
                tagName: "div",
                className:
                  "text-sm md:text-base font-medium text-gray-500 tracking-2p md:tracking-1p",
                value: subheading,
                onChange: function (value) {
                  setAttributes({ subheading: value });
                },
                placeholder: __("Enter subheading", "main"),
                allowedFormats: [],
              })
            ),
            authors.length > 0 &&
              el(
                "div",
                { className: "w-full" },
                el(
                  "div",
                  Object.assign(
                    {
                      className: showCarousel
                        ? "-mx-6 -my-12 md:-m-12 meet-experts-carousel"
                        : "",
                    },
                    showCarousel
                      ? {
                          "data-flickity": JSON.stringify({
                            cellAlign: "left",
                            wrapAround: true,
                            pageDots: true,
                            imagesLoaded: true,
                            prevNextButtons: false,
                            autoPlay: 3000,
                          }),
                        }
                      : {}
                  ),
                  authorGroups.map(function (group, groupIndex) {
                    return el(
                      "div",
                      {
                        key: groupIndex,
                        className:
                          "w-full flex flex-col gap-4 px-6 py-12 md:p-12 md:max-w-none md:grid md:grid-cols-2 md:gap-8",
                      },
                      group.map(function (authorId, authorIndex) {
                        var author = getAuthorData(authorId);
                        if (!author) {
                          return el(
                            "div",
                            { key: authorIndex },
                            __("Loading author...", "main")
                          );
                        }

                        // Get author avatar
                        var avatarUrl = "";
                        if (author.avatar_urls && author.avatar_urls[96]) {
                          avatarUrl = author.avatar_urls[96];
                        }

                        // Get author bio
                        var bio = author.description || "";

                        // Get author job title from REST API field
                        var jobTitle = author.job_title || "";

                        // Author archive URL
                        var archiveUrl = "#"; // Will be set in PHP render

                        var authorName =
                          author.name || author.slug || "Unknown";

                        return el(
                          "article",
                          {
                            key: authorIndex,
                            className:
                              "flex flex-col gap-4 md:gap-4 rounded-2xl bg-white p-6 md:p-6 shadow-[0px_56px_23px_rgba(191,191,191,0.01),0px_32px_19px_rgba(191,191,191,0.05),0px_14px_14px_rgba(191,191,191,0.09),0px_4px_8px_rgba(191,191,191,0.1)]",
                          },
                          el(
                            "div",
                            { className: "flex items-center gap-4 pb-2" },
                            avatarUrl &&
                              el("img", {
                                src: avatarUrl,
                                alt: authorName,
                                className:
                                  "h-[3.25rem] w-[3.25rem] rounded-full object-cover",
                                loading: "lazy",
                              }),
                            el(
                              "div",
                              { className: "flex flex-col gap-0.5" },
                              el(
                                "h3",
                                {
                                  className:
                                    "text-base md:text-lg font-semibold text-gray-900 md:-tracking-2p",
                                },
                                authorName
                              ),
                              jobTitle &&
                              el(
                                "div",
                                {
                                  className:
                                    "text-xs md:text-sm font-medium text-gray-500 tracking-2p md:tracking-1p",
                                },
                                jobTitle
                              )
                            )
                          ),
                          bio &&
                            el(
                              "div",
                              {
                                className:
                                  "text-sm font-medium text-gray-500 leading-5 tracking-2p md:tracking-1p",
                              },
                              bio.substring(0, 150) +
                                (bio.length > 150 ? "..." : "")
                            ),
                          el(
                            "a",
                            {
                              href: archiveUrl,
                              className:
                                "text-sm font-semibold text-primary btn-read-guide md:py-3",
                            },
                            "Read Guide"
                          )
                        );
                      })
                    );
                  })
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
