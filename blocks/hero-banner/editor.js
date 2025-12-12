/**
 * Hero Banner Block - Editor Preview
 * Fully editable block with exact frontend markup and higher CSS specificity
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
  var Button = components.Button;
  var __ = i18n.__;

  blocks.registerBlockType("main/hero-banner", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      // Add theme-main class for higher CSS specificity
      var blockProps = useBlockProps({
        className: "theme-main hero-banner hero-banner-editor",
      });

      var heading =
        attributes.heading || "The Definitive Software Buying Guides";
      var description =
        attributes.description ||
        "Cut through the noise with expert-written guides to choose the perfect tools for your business.";
      var heroImage = attributes.heroImage || null;
      var heroVideoWebm = attributes.heroVideoWebm || "https://cdn.geekflare.com/general/gia-standby.webm";
      var heroVideoMp4 = attributes.heroVideoMp4 || "https://cdn.geekflare.com/general/gia-standby.mp4";
      var searchSuggestions = attributes.searchSuggestions || [];
      var searchPlaceholder = attributes.searchPlaceholder || "Enter keyword";
      var searchButtonText = attributes.searchButtonText || "Search Guides";

      // Ensure searchSuggestions is an array
      if (!Array.isArray(searchSuggestions)) {
        searchSuggestions = [];
      }

      var heroImageUrl = "";
      if (heroImage && heroImage.url) {
        heroImageUrl = heroImage.url;
      } else if (heroImage && heroImage.id) {
        heroImageUrl = heroImage.url || "";
      }

      // Helper to update suggestions array
      var updateSuggestion = function (index, value) {
        var newSuggestions = searchSuggestions.slice();
        newSuggestions[index] = value;
        setAttributes({
          searchSuggestions: newSuggestions.filter(function (s) {
            return s && s.trim();
          }),
        });
      };

      // Helper to add a new suggestion
      var addSuggestion = function () {
        var newSuggestions = searchSuggestions.slice();
        newSuggestions.push("");
        setAttributes({ searchSuggestions: newSuggestions });
      };

      // Helper to remove a suggestion
      var removeSuggestion = function (index) {
        var newSuggestions = searchSuggestions.slice();
        newSuggestions.splice(index, 1);
        setAttributes({
          searchSuggestions: newSuggestions.filter(function (s) {
            return s && s.trim();
          }),
        });
      };

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Hero Content", "main"), initialOpen: true },
            el(RichText, {
              tagName: "h1",
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
              placeholder: __("Enter heading text", "main"),
              className: "hero-banner-heading-input",
            }),
            el(RichText, {
              tagName: "p",
              value: description,
              onChange: function (value) {
                setAttributes({ description: value });
              },
              placeholder: __("Enter description text", "main"),
              className: "hero-banner-description-input",
            })
          ),
          el(
            PanelBody,
            { title: __("Hero Image / Video", "main") },
            el(TextControl, {
              label: __("Video URL (WebM)", "main"),
              value: heroVideoWebm,
              onChange: function (value) {
                setAttributes({ heroVideoWebm: value });
              },
              help: __("Enter a direct link to a WebM video file. If provided, video will be shown instead of image.", "main"),
              placeholder: __("https://example.com/video.webm", "main"),
            }),
            el(TextControl, {
              label: __("Video URL (MP4)", "main"),
              value: heroVideoMp4,
              onChange: function (value) {
                setAttributes({ heroVideoMp4: value });
              },
              help: __("Enter a direct link to an MP4 video file. This is a fallback for browsers that don't support WebM.", "main"),
              placeholder: __("https://example.com/video.mp4", "main"),
            }),
            el(
              MediaUploadCheck,
              {},
              el(MediaUpload, {
                onSelect: function (media) {
                  setAttributes({
                    heroImage: { id: media.id, url: media.url },
                  });
                },
                allowedTypes: ["image"],
                value: heroImage ? heroImage.id : null,
                render: function (obj) {
                  return el(
                    "div",
                    {},
                    heroImageUrl
                      ? el(
                          "div",
                          {},
                          el("img", {
                            src: heroImageUrl,
                            alt: heading,
                            style: { maxWidth: "200px", height: "auto" },
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
                              __("Replace Image", "main")
                            ),
                            " ",
                            el(
                              Button,
                              {
                                onClick: function () {
                                  setAttributes({ heroImage: null });
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
                            variant: "primary",
                          },
                          __("Upload Hero Image", "main")
                        )
                  );
                },
              })
            )
          ),
          el(
            PanelBody,
            { title: __("Search Settings", "main") },
            el(TextControl, {
              label: __("Search Placeholder", "main"),
              value: searchPlaceholder,
              onChange: function (value) {
                setAttributes({ searchPlaceholder: value });
              },
            }),
            el(TextControl, {
              label: __("Search Button Text", "main"),
              value: searchButtonText,
              onChange: function (value) {
                setAttributes({ searchButtonText: value });
              },
            }),
            el(
              PanelBody,
              { title: __("Search Suggestions", "main"), initialOpen: false },
              searchSuggestions.map(function (suggestion, index) {
                return el(
                  "div",
                  {
                    key: index,
                    style: { marginBottom: "10px", display: "flex", gap: "8px" },
                  },
                  el(TextControl, {
                    value: suggestion,
                    onChange: function (value) {
                      return updateSuggestion(index, value);
                    },
                    placeholder: __("Enter suggestion text", "main"),
                    style: { flex: 1 },
                  }),
                  el(
                    Button,
                    {
                      isDestructive: true,
                      isSmall: true,
                      onClick: function () {
                        return removeSuggestion(index);
                      },
                      icon: "trash",
                      label: __("Remove", "main"),
                    },
                    __("Remove", "main")
                  )
                );
              }),
              el(
                Button,
                {
                  variant: "secondary",
                  onClick: addSuggestion,
                  style: { marginTop: "10px" },
                },
                __("+ Add Suggestion", "main")
              )
            )
          )
        ),
        // Editor preview - EXACT frontend markup with higher specificity wrapper
        el(
          "div",
          { className: "theme-main hero-banner" },
          el(
            "div",
            { className: "container-1056 relative z-1 flex flex-col gap-12 md:gap-12 lg:gap-14" },
            el(
              "div",
              { className: "hero-banner__content lg:mb-0.25" },
              el(RichText, {
                tagName: "h1",
                className: "text-center font-bold text-5xl leading-none lg:text-6xl xl:text-7.5xl",
                value: heading,
                onChange: function (value) {
                  setAttributes({ heading: value });
                },
                placeholder: __("Enter heading text", "main"),
                allowedFormats: [],
              }),
              el(RichText, {
                tagName: "div",
                className: "hero-banner__description text-sm leading-[1.25rem] font-medium text-center text-gray-500 tracking-2p md:text-base md:leading-normal md:tracking-1p mx-auto max-w-[31rem]",
                value: description,
                onChange: function (value) {
                  setAttributes({ description: value });
                },
                placeholder: __("Enter description text", "main"),
                allowedFormats: [],
              })
            ),
            el(
              "div",
              { className: "hero-banner__form hero-banner-form flex flex-col p-5 gap-6 md:p-7 lg:p-8 lg:flex-row" },
              ((heroVideoWebm || heroVideoMp4) || heroImageUrl) &&
                el(
                  "div",
                  { className: "hero-banner__image mx-auto w-[8.75rem] md:w-40 lg:w-[11.25rem] lg:-my-0.25" },
                  (heroVideoWebm || heroVideoMp4)
                    ? el("video", {
                        className: "max-w-[80%] max-h-[80%] object-contain mx-auto",
                        autoPlay: true,
                        loop: true,
                        muted: true,
                        playsInline: true,
                        controls: false,
                      },
                      heroVideoWebm && el("source", {
                        src: heroVideoWebm,
                        type: "video/webm"
                      }),
                      heroVideoMp4 && el("source", {
                        src: heroVideoMp4,
                        type: "video/mp4"
                      }),
                      "Your browser does not support the video tag."
                      )
                    : el("img", {
                        src: heroImageUrl,
                        alt: heading,
                        loading: "eager",
                        style: { width: "100%", height: "auto" },
                      })
                ),
              el(
                "div",
                { className: "hero-banner__form-content hero-form-content flex flex-col gap-5 lg:flex-1 lg:py-1" },
                el(
                  "form",
                  {
                    action: "#",
                    role: "search",
                    "aria-label": __("Search buying guides", "main"),
                    style: { pointerEvents: "none" },
                  },
                  el(
                    "div",
                    { className: "relative flex flex-col gap-3 md:flex-row md:gap-2" },
                    el(
                      "div",
                      { className: "relative flex-1" },
                      el(
                        "div",
                        {
                          className: "absolute inset-y-0 start-0 flex items-center ps-3 md:ps-4 md:ms-0.5 pointer-events-none [&_svg]:w-5 [&_svg]:h-5 md:[&_svg]:w-6 md:[&_svg]:h-6",
                          "aria-hidden": "true",
                        },
                        el(
                          "svg",
                          {
                            xmlns: "http://www.w3.org/2000/svg",
                            width: "20",
                            height: "20",
                            viewBox: "0 0 20 20",
                            "aria-hidden": "true",
                            role: "presentation",
                          },
                          el("path", {
                            d: "M9.17 15c1.54 0 3.03-.62 4.12-1.71A5.85 5.85 0 0 0 15 9.17c0-1.55-.62-3.03-1.71-4.13a5.85 5.85 0 0 0-4.12-1.71c-1.55 0-3.03.62-4.13 1.71a5.87 5.87 0 0 0-1.71 4.13c0 1.54.62 3.03 1.71 4.12A5.87 5.87 0 0 0 9.17 15m7.5 1.67-3.34-3.34",
                            style: {
                              fill: "none",
                              stroke: "#717680",
                              strokeLinecap: "round",
                              strokeLinejoin: "round",
                              strokeWidth: "1.5",
                            },
                          })
                        )
                      ),
                      el("label", {
                        htmlFor: "bannerSearch",
                        className: "sr-only",
                      }, __("Search for buying guides", "main")),
                      el("input", {
                        type: "text",
                        id: "bannerSearch",
                        name: "s",
                        className: "form-input ps-11 pe-4 md-large md:ps-14 md:pe-16",
                        placeholder: searchPlaceholder,
                        readOnly: true,
                        disabled: true,
                      }),
                      el("span", {
                        id: "search-description",
                        className: "sr-only",
                      }, __("Search our collection of expert-written software buying guides", "main"))
                    ),
                    el(
                      "div",
                      { className: "hidden md:absolute md:inset-y-0 md:end-3 md:flex md:items-center" },
                      el(
                        "button",
                        {
                          type: "submit",
                          className: "btn btn--primary rounded-full text-sm leading-5 py-3 px-4 w-full md:w-auto",
                          disabled: true,
                        },
                        searchButtonText
                      )
                    )
                  )
                ),
                searchSuggestions && searchSuggestions.length > 0 &&
                  el(
                    "nav",
                    { "aria-label": __("Popular search suggestions", "main") },
                    el(
                      "ul",
                      { className: "search-suggestions flex flex-wrap gap-3 list-none p-0 m-0" },
                      searchSuggestions.map(function (suggestion, index) {
                        if (!suggestion) return null;
                        return el(
                          "li",
                          { key: index },
                          el(
                            "button",
                            {
                              type: "button",
                              className: "search-suggestions-item " + (index === 0 ? "active" : ""),
                              "data-value": suggestion,
                              "aria-pressed": index === 0 ? "true" : "false",
                              disabled: true,
                            },
                            suggestion
                          )
                        );
                      })
                    )
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
