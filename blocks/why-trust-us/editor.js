(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;

  registerBlockType("main/why-trust-us", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var dataPoints = attributes.dataPoints || [];

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "why-trust-us-editor",
      });

      function updateDataPoint(index, field, value) {
        var newDataPoints = dataPoints.slice();
        newDataPoints[index][field] = value;
        setAttributes({ dataPoints: newDataPoints });
      }

      function addDataPoint() {
        var newDataPoints = dataPoints.slice();
        newDataPoints.push({ value: "", label: "" });
        setAttributes({ dataPoints: newDataPoints });
      }

      function removeDataPoint(index) {
        var newDataPoints = dataPoints.slice();
        newDataPoints.splice(index, 1);
        setAttributes({ dataPoints: newDataPoints });
      }

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Data Points", "main"), initialOpen: true },
            dataPoints.map(function (point, index) {
              return el(
                "div",
                { key: index, className: "data-point-item" },
                el(TextControl, {
                  label: __("Value", "main"),
                  value: point.value,
                  onChange: function (value) {
                    updateDataPoint(index, "value", value);
                  },
                  placeholder: __("e.g., 500+", "main"),
                }),
                el(TextControl, {
                  label: __("Label", "main"),
                  value: point.label,
                  onChange: function (value) {
                    updateDataPoint(index, "label", value);
                  },
                  placeholder: __("e.g., Tools Analyzed", "main"),
                }),
                el(
                  Button,
                  {
                    isDestructive: true,
                    onClick: function () {
                      removeDataPoint(index);
                    },
                  },
                  __("Remove", "main")
                ),
                el("hr", { style: { margin: "12px 0" } })
              );
            }),
            el(
              Button,
              {
                variant: "secondary",
                onClick: addDataPoint,
              },
              __("+ Add Data Point", "main")
            )
          )
        ),

        el(
          "div",
          {
            className:
              "flex flex-col gap-2 md:gap-4 lg:gap-4.5 my-12",
          },
          el(RichText, {
            tagName: "h2",
            className:
              "text-base text-gray-800 font-bold md:text-lg md:leading-5",
            value: attributes.heading,
            onChange: function (value) {
              setAttributes({ heading: value });
            },
            placeholder: __("Section Heading", "main"),
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

          dataPoints.length > 0 &&
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
                    className:
                      "rounded-2xl border border-gray-200 bg-white px-3 py-2.5 md:py-3.5 md:px-5 flex flex-col justify-center gap-2 md:gap-2.5",
                  },
                  el(
                    "span",
                    {
                      className:
                        "text-2xl md:text-3xl leading-none font-bold text-transparent bg-gradient-to-r from-[#FF8A00] to-primary bg-clip-text",
                    },
                    point.value || __("Value", "main")
                  ),
                  el(
                    "span",
                    {
                      className:
                        "text-xs md:text-base tracking-2p font-medium text-gray-500",
                    },
                    point.label || __("Label", "main")
                  )
              );
            })
          )
        )
      );
    },

    save: function () {
      return null;
    },
  });
})(window.wp);
