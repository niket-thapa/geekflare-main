(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;
  var useRef = wp.element.useRef;

  registerBlockType("main/final-verdict", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var perfectFor = attributes.perfectFor || [""];
      var notIdealFor = attributes.notIdealFor || [""];
      var heading =
        attributes.heading ||
        __("Final Verdict: Who is Monday.com for?", "main");
      var perfectForHeading =
        attributes.perfectForHeading || __("Perfect for:", "main");
      var notIdealForHeading =
        attributes.notIdealForHeading || __("Not ideal for:", "main");
      var clientId = props.clientId;

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "final-verdict-editor",
      });

      // Track which item to focus after creation
      var focusPerfectIndexRef = useRef(null);
      var focusNotIdealIndexRef = useRef(null);

      // Simple focus helper
      function focusRichText(element) {
        if (!element) return;
        setTimeout(function () {
          var editable =
            element.querySelector('[contenteditable="true"]') ||
            element.querySelector(
              '.wp-block-rich-text [contenteditable="true"]'
            ) ||
            element.querySelector('span[contenteditable="true"]');
          if (editable) {
            editable.focus();
          }
        }, 50);
      }

      // Focus callback for perfect for items
      function createPerfectFocusCallback(index) {
        return function (element) {
          if (element && focusPerfectIndexRef.current === index) {
            focusRichText(element);
            focusPerfectIndexRef.current = null;
          }
        };
      }

      // Focus callback for not ideal for items
      function createNotIdealFocusCallback(index) {
        return function (element) {
          if (element && focusNotIdealIndexRef.current === index) {
            focusRichText(element);
            focusNotIdealIndexRef.current = null;
          }
        };
      }

      function updatePerfectFor(index, value) {
        var newPerfectFor = perfectFor.slice();
        newPerfectFor[index] = value;
        setAttributes({ perfectFor: newPerfectFor });
      }

      function addPerfectFor(indexToInsertAfter) {
        var newPerfectFor = perfectFor.slice();
        var insertIndex;
        if (indexToInsertAfter !== undefined) {
          insertIndex = indexToInsertAfter + 1;
        } else {
          insertIndex = newPerfectFor.length;
        }
        newPerfectFor.splice(insertIndex, 0, "");
        setAttributes({ perfectFor: newPerfectFor });
        focusPerfectIndexRef.current = insertIndex;
      }

      function removePerfectFor(index) {
        var newPerfectFor = perfectFor.slice();
        newPerfectFor.splice(index, 1);
        if (newPerfectFor.length === 0) newPerfectFor = [""];
        setAttributes({ perfectFor: newPerfectFor });
      }

      function updateNotIdealFor(index, value) {
        var newNotIdealFor = notIdealFor.slice();
        newNotIdealFor[index] = value;
        setAttributes({ notIdealFor: newNotIdealFor });
      }

      function addNotIdealFor(indexToInsertAfter) {
        var newNotIdealFor = notIdealFor.slice();
        var insertIndex;
        if (indexToInsertAfter !== undefined) {
          insertIndex = indexToInsertAfter + 1;
        } else {
          insertIndex = newNotIdealFor.length;
        }
        newNotIdealFor.splice(insertIndex, 0, "");
        setAttributes({ notIdealFor: newNotIdealFor });
        focusNotIdealIndexRef.current = insertIndex;
      }

      function removeNotIdealFor(index) {
        var newNotIdealFor = notIdealFor.slice();
        newNotIdealFor.splice(index, 1);
        if (newNotIdealFor.length === 0) newNotIdealFor = [""];
        setAttributes({ notIdealFor: newNotIdealFor });
      }

      // Ensure we always have at least one item (even if empty) for seamless editing
      if (perfectFor.length === 0) {
        perfectFor = [""];
      }
      if (notIdealFor.length === 0) {
        notIdealFor = [""];
      }

      return el(
        "div",
        blockProps,
        el(
          "div",
          {
            className: "flex flex-col gap-4 md:gap-6 lg:gap-8  [&_p]:m-0",
          },
          // Main Heading
          el(RichText, {
            tagName: "h2",
            className:
              "text-2xl md:text-4xl font-bold leading-none md:leading-none text-gray-800 m-0",
            value: heading,
            onChange: function (value) {
              setAttributes({ heading: value });
            },
            placeholder: __("Final Verdict: Who is Monday.com for?", "main"),
            allowedFormats: [],
          }),

          el(
            "div",
            {
              className: "flex flex-col gap-3 md:gap-4",
            },
            // Perfect for Section
            el(
              "div",
              {
                className:
                  "flex flex-col justify-center items-start p-5 gap-3 bg-[#F6FEF9] border border-[#D1FADF] rounded-2xl",
              },
              el(RichText, {
                tagName: "h4",
                className:
                  "text-base font-semibold leading-6 text-gray-800 flex-none m-0",
                value: perfectForHeading,
                onChange: function (value) {
                  setAttributes({ perfectForHeading: value });
                },
                placeholder: __("Perfect for:", "main"),
                allowedFormats: [],
              }),
              el(
                "div",
                {
                  className: "flex flex-col gap-0 w-full",
                },
                perfectFor.map(function (item, index) {
                  return el(
                    "div",
                    {
                      key: index,
                      ref: createPerfectFocusCallback(index),
                      className:
                        "flex flex-row items-center gap-1.5 self-stretch group",
                    },
                    el(
                      "svg",
                      {
                        className: "w-5 h-5 flex-none",
                        xmlns: "http://www.w3.org/2000/svg",
                        width: "20",
                        height: "20",
                        fill: "none",
                        viewBox: "0 0 20 20",
                      },
                      el("path", {
                        fill: "currentColor",
                        className: "text-success-600",
                        d: "M10 1.875A8.125 8.125 0 1 0 18.125 10 8.133 8.133 0 0 0 10 1.875m3.567 6.692-4.375 4.375a.626.626 0 0 1-.884 0l-1.875-1.875a.625.625 0 1 1 .884-.884l1.433 1.433 3.933-3.933a.626.626 0 0 1 .884.884",
                      })
                    ),
                    el(
                      "div",
                      {
                        className: "flex-1 flex items-center gap-2 min-w-0",
                      },
                      el(RichText, {
                        tagName: "p",
                        className:
                          "text-sm font-medium leading-5 tracking-2p text-gray-800 flex-1 m-0",
                        value: item,
                        onChange: function (value) {
                          updatePerfectFor(index, value);
                        },
                        placeholder: __("Enter an item...", "main"),
                        allowedFormats: [],
                        __unstableOnSplitAtEnd: function () {
                          addPerfectFor(index);
                        },
                      }),
                      el(
                        Button,
                        {
                          className:
                            "final-verdict-remove-button opacity-0 group-hover:opacity-100 transition-opacity",
                          isDestructive: true,
                          isSmall: true,
                          onClick: function () {
                            removePerfectFor(index);
                          },
                          style: { flexShrink: 0 },
                        },
                        "×"
                      )
                    )
                  );
                })
              )
            ),

            // Not ideal for Section
            el(
              "div",
              {
                className:
                  "flex flex-col justify-center items-start p-5 gap-3 bg-[#FEF3F2] border border-[#FEE4E2] rounded-2xl",
              },
              el(RichText, {
                tagName: "h4",
                className:
                  "text-base font-semibold leading-6 text-gray-800 flex-none m-0",
                value: notIdealForHeading,
                onChange: function (value) {
                  setAttributes({ notIdealForHeading: value });
                },
                placeholder: __("Not ideal for:", "main"),
                allowedFormats: [],
              }),
              el(
                "div",
                {
                  className: "flex flex-col gap-0 w-full",
                },
                notIdealFor.map(function (item, index) {
                  return el(
                    "div",
                    {
                      key: index,
                      ref: createNotIdealFocusCallback(index),
                      className:
                        "flex flex-row items-center gap-1.5 self-stretch group",
                    },
                    el(
                      "svg",
                      {
                        className: "w-5 h-5 flex-none",
                        xmlns: "http://www.w3.org/2000/svg",
                        width: "20",
                        height: "20",
                        fill: "none",
                        viewBox: "0 0 20 20",
                      },
                      el("path", {
                        fill: "currentColor",
                        className: "text-error-600",
                        d: "M10 1.875A8.125 8.125 0 1 0 18.125 10 8.133 8.133 0 0 0 10 1.875m2.942 10.183a.624.624 0 1 1-.884.884L10 10.884l-2.058 2.058a.624.624 0 1 1-.884-.884L9.116 10 7.058 7.942a.625.625 0 0 1 .884-.884L10 9.116l2.058-2.058a.626.626 0 0 1 .884.884L10.884 10z",
                      })
                    ),
                    el(
                      "div",
                      {
                        className: "flex-1 flex items-center gap-2 min-w-0",
                      },
                      el(RichText, {
                        tagName: "p",
                        className:
                          "text-sm font-medium leading-5 tracking-2p text-gray-800 flex-1 m-0",
                        value: item,
                        onChange: function (value) {
                          updateNotIdealFor(index, value);
                        },
                        placeholder: __("Enter an item...", "main"),
                        allowedFormats: [],
                        __unstableOnSplitAtEnd: function () {
                          addNotIdealFor(index);
                        },
                      }),
                      el(
                        Button,
                        {
                          className:
                            "final-verdict-remove-button opacity-0 group-hover:opacity-100 transition-opacity",
                          isDestructive: true,
                          isSmall: true,
                          onClick: function () {
                            removeNotIdealFor(index);
                          },
                          style: { flexShrink: 0 },
                        },
                        "×"
                      )
                    )
                  );
                })
              )
            )
          )
        )
      );
    },

    save: function () {
      return null;
    },
  });
})(window.wp);
