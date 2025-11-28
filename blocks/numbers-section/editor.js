/**
 * Numbers Section Block - Editor Preview
 * Fully editable block with exact frontend markup and higher CSS specificity
 */
(function (blocks, blockEditor, element, components, i18n) {
  var el = element.createElement;
  var RichText = blockEditor.RichText;
  var useBlockProps = blockEditor.useBlockProps;
  var InspectorControls = blockEditor.InspectorControls;
  var PanelBody = components.PanelBody;
  var TextControl = components.TextControl;
  var Button = components.Button;
  var __ = i18n.__;

  blocks.registerBlockType("main/numbers-section", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      // Add theme-main class for higher CSS specificity
      var blockProps = useBlockProps({
        className: "theme-main home-numbers-section numbers-section-editor",
      });

      var heading =
        attributes.heading || "Empowering businesses like yours since 2015";
      var description = attributes.description || "";
      var stats = attributes.stats || [];

      // Ensure stats is an array
      if (!Array.isArray(stats)) {
        stats = [];
      }

      // Helper functions for managing stats
      var updateStat = function (index, field, value) {
        var newStats = stats.slice();
        if (!newStats[index]) {
          newStats[index] = {};
        }
        newStats[index][field] = value;
        setAttributes({
          stats: newStats.filter(function (s) {
            return (s.number && s.number.trim()) || (s.label && s.label.trim());
          }),
        });
      };

      var addStat = function () {
        var newStats = stats.slice();
        newStats.push({ number: "", label: "" });
        setAttributes({ stats: newStats });
      };

      var removeStat = function (index) {
        var newStats = stats.slice();
        newStats.splice(index, 1);
        setAttributes({
          stats: newStats.filter(function (s) {
            return (s.number && s.number.trim()) || (s.label && s.label.trim());
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
            { title: __("Content", "main"), initialOpen: true },
            el(RichText, {
              tagName: "h2",
              className: "numbers-section-heading-input",
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
              placeholder: __("Enter heading text", "main"),
              allowedFormats: [],
            }),
            el(RichText, {
              tagName: "p",
              className: "numbers-section-description-input",
              value: description,
              onChange: function (value) {
                setAttributes({ description: value });
              },
              placeholder: __("Enter description text", "main"),
              allowedFormats: [],
            })
          ),
          el(
            PanelBody,
            { title: __("Statistics", "main"), initialOpen: true },
            stats.map(function (stat, index) {
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
                  label: __("Number", "main"),
                  value: stat.number || "",
                  onChange: function (value) {
                    return updateStat(index, "number", value);
                  },
                  placeholder: __("e.g., 500K+", "main"),
                  style: { marginBottom: "10px" },
                }),
                el(TextControl, {
                  label: __("Label", "main"),
                  value: stat.label || "",
                  onChange: function (value) {
                    return updateStat(index, "label", value);
                  },
                  placeholder: __("e.g., Businesses Helped", "main"),
                  style: { marginBottom: "10px" },
                }),
                el(
                  Button,
                  {
                    isDestructive: true,
                    isSmall: true,
                    onClick: function () {
                      return removeStat(index);
                    },
                    icon: "trash",
                    label: __("Remove", "main"),
                  },
                  __("Remove Stat", "main")
                )
              );
            }),
            el(
              Button,
              {
                variant: "secondary",
                onClick: addStat,
                style: { marginTop: "10px", width: "100%" },
              },
              __("+ Add Statistic", "main")
            )
          )
        ),
        // Editor preview - EXACT frontend markup with higher specificity wrapper
        el(
          "div",
          { className: "theme-main home-numbers-section" },
          el(
            "div",
            { className: "container-1056 flex flex-col items-center gap-12 md:gap-14" },
            el(
              "div",
              { className: "flex flex-col items-center text-center gap-3" },
              el(RichText, {
                tagName: "h2",
                className: "text-center text-3xl md:text-4xl lg:text-5xl font-bold font-gilroy leading-none md:leading-none lg:leading-none",
                value: heading,
                onChange: function (value) {
                  setAttributes({ heading: value });
                },
                placeholder: __("Enter heading text", "main"),
                allowedFormats: [],
              }),
              el(RichText, {
                tagName: "div",
                className: "numbers-section-text md:text-base md:leading-normal text-gray-500 mx-auto text-center max-w-[53.75rem] tracking-2p md:tracking-1p text-sm leading-5",
                value: description,
                onChange: function (value) {
                  setAttributes({ description: value });
                },
                placeholder: __("Enter description text", "main"),
                allowedFormats: [],
              })
            ),
            stats && stats.length > 0 &&
              el(
                "div",
                { className: "numbers-highlight grid grid-cols-2 gap-4 md:gap-8 lg:grid-cols-3 w-full max-w-[56.5rem]" },
                stats.map(function (stat, index) {
                  if (!stat.number && !stat.label) return null;
                  
                  // Determine column span and order for responsive layout (same as frontend)
                  var colSpan = "col-span-1";
                  var order = index + 1;
                  
                  // Second stat (index 1) spans 2 columns on mobile
                  if (index === 1 && stats.length === 3) {
                    colSpan = "col-span-2";
                    order = 3;
                  } else if (index === 2 && stats.length === 3) {
                    order = 2;
                  }
                  
                  return el(
                    "div",
                    {
                      key: index,
                      className: "stats-card " + colSpan + " lg:col-span-1 order-" + order + " lg:order-none flex flex-col items-center justify-center text-center p-6 md:p-10 gap-2 md:gap-3 bg-white rounded-2xl shadow-[0px_56px_23px_rgba(191,191,191,0.01),0px_32px_19px_rgba(191,191,191,0.05),0px_14px_14px_rgba(191,191,191,0.09),0px_4px_8px_rgba(191,191,191,0.1)]",
                    },
                    stat.number &&
                      el(
                        "span",
                        {
                          className: "text-3xl md:text-5xl font-bold font-gilroy leading-none md:leading-none bg-gradient-to-r from-[#FFC33D] via-[#FF7E29] to-[#FF4A00] bg-clip-text text-transparent",
                        },
                        stat.number
                      ),
                    stat.label &&
                      el(
                        "span",
                        {
                          className: "text-sm md:text-base font-medium font-gilroy leading-5 md:leading-6 tracking-2p text-gray-500",
                        },
                        stat.label
                      )
                  );
                })
              ),
            (!stats || stats.length === 0) &&
              el(
                "div",
                {
                  style: {
                    padding: "40px",
                    textAlign: "center",
                    color: "#999",
                    fontStyle: "italic",
                  },
                },
                __("Add statistics using the block settings sidebar", "main")
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
