(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;
  var useRef = wp.element.useRef;

  registerBlockType("main/why-trust-us", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var dataPoints = attributes.dataPoints || [];

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "why-trust-us-editor",
      });

      // Track which item to focus after creation
      var focusValueIndexRef = useRef(null);
      var focusLabelIndexRef = useRef(null);

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

      // Focus callback for value items
      function createValueFocusCallback(index) {
        return function (element) {
          if (element && focusValueIndexRef.current === index) {
            focusRichText(element);
            focusValueIndexRef.current = null;
          }
        };
      }

      // Focus callback for label items
      function createLabelFocusCallback(index) {
        return function (element) {
          if (element && focusLabelIndexRef.current === index) {
            focusRichText(element);
            focusLabelIndexRef.current = null;
          }
        };
      }

      function updateDataPointValue(index, value) {
        var newDataPoints = dataPoints.slice();
        if (!newDataPoints[index]) {
          newDataPoints[index] = { value: "", label: "" };
        }
        newDataPoints[index].value = value;
        setAttributes({ dataPoints: newDataPoints });
      }

      function updateDataPointLabel(index, value) {
        var newDataPoints = dataPoints.slice();
        if (!newDataPoints[index]) {
          newDataPoints[index] = { value: "", label: "" };
        }
        newDataPoints[index].label = value;
        setAttributes({ dataPoints: newDataPoints });
      }

      function addDataPoint(indexToInsertAfter) {
        var newDataPoints = dataPoints.slice();
        var insertIndex;
        if (indexToInsertAfter !== undefined) {
          insertIndex = indexToInsertAfter + 1;
        } else {
          insertIndex = newDataPoints.length;
        }
        newDataPoints.splice(insertIndex, 0, { value: "", label: "" });
        setAttributes({ dataPoints: newDataPoints });
        focusValueIndexRef.current = insertIndex;
      }

      function removeDataPoint(index) {
        var newDataPoints = dataPoints.slice();
        newDataPoints.splice(index, 1);
        if (newDataPoints.length === 0)
          newDataPoints = [{ value: "", label: "" }];
        setAttributes({ dataPoints: newDataPoints });
      }

      // Ensure we always have at least one item (even if empty) for seamless editing
      if (dataPoints.length === 0) {
        dataPoints = [{ value: "", label: "" }];
      }

      return el(
        "div",
        blockProps,
        el(
          "div",
          {
            className: "why_trust_us flex flex-col gap-2 md:gap-4 lg:gap-4.5",
          },
          el(RichText, {
            tagName: "h2",
            className:
              "text-base text-gray-800 font-bold md:text-lg md:leading-5 m-0",
            value: attributes.heading,
            onChange: function (value) {
              setAttributes({ heading: value });
            },
            placeholder: __("Section Heading", "main"),
            allowedFormats: [],
          }),

          el(RichText, {
            tagName: "div",
            className:
              "text-sm text-gray-700 md:text-base md:tracking-2p font-medium",
            value: attributes.description,
            onChange: function (value) {
              setAttributes({ description: value });
            },
            placeholder: __("Enter description...", "main"),
          }),

          el(
            "div",
            {
              className:
                "grid grid-cols-2 gap-3 md:grid-cols-4 md:gap-4 lg:gap-4 pt-4 md:pt-1",
            },
            dataPoints.map(function (point, index) {
              return el(
                "div",
                {
                  key: index,
                  ref: createValueFocusCallback(index),
                  className:
                    "rounded-2xl border border-gray-200 bg-white px-3 py-2.5 md:py-3.5 md:px-5 flex flex-col justify-center gap-2 md:gap-2.5 group relative",
                },
                el(
                  "div",
                  {
                    className: "flex-1 flex flex-col gap-2 md:gap-2.5",
                  },
                  el(RichText, {
                    tagName: "span",
                    className:
                      "text-2xl md:text-3xl leading-none font-bold text-transparent bg-gradient-to-r from-[#FF8A00] to-primary bg-clip-text",
                    value: point.value || "",
                    onChange: function (value) {
                      updateDataPointValue(index, value);
                    },
                    placeholder: __("Value", "main"),
                    allowedFormats: [],
                    __unstableOnSplitAtEnd: function () {
                      addDataPoint(index);
                    },
                    style: {
                      caretColor: "#ff4a00",
                    },
                  }),
                  el(
                    "div",
                    {
                      ref: createLabelFocusCallback(index),
                      className: "flex-1 flex items-start gap-2 min-w-0",
                    },
                    el(RichText, {
                      tagName: "span",
                      className:
                        "text-xs md:text-base tracking-2p font-medium text-gray-500 flex-1",
                      value: point.label || "",
                      onChange: function (value) {
                        updateDataPointLabel(index, value);
                      },
                      placeholder: __("Label", "main"),
                      allowedFormats: [],
                      __unstableOnSplitAtEnd: function () {
                        addDataPoint(index);
                      },
                    }),
                    el(
                      Button,
                      {
                        className:
                          "why-trust-us-remove-button opacity-0 group-hover:opacity-100 transition-opacity",
                        isDestructive: true,
                        isSmall: true,
                        onClick: function () {
                          removeDataPoint(index);
                        },
                        style: { flexShrink: 0 },
                      },
                      "×"
                    )
                  )
                )
              );
            }),
            el(
              "div",
              {
                className: "col-span-2 md:col-span-1",
                style: {
                  marginTop: "8px",
                },
              },
              el(
                Button,
                {
                  variant: "secondary",
                  isSmall: true,
                  onClick: function () {
                    addDataPoint();
                  },
                },
                __("+ Add Data Point", "main")
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
