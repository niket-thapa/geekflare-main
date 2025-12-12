(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var MediaUpload = wp.blockEditor.MediaUpload;
  var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
  var PanelBody = wp.components.PanelBody;
  var SelectControl = wp.components.SelectControl;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;

  registerBlockType("main/info-box", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      var blockProps = useBlockProps({
        className: "info-box-editor",
      });

      var style = attributes.style || "default";
      var iconType = attributes.iconType || "none";
      var iconUrl = attributes.iconUrl || "";
      var iconId = attributes.iconId || 0;
      var heading = attributes.heading || "";
      var content = attributes.content || "";

      var styleOptions = [
        { label: __("Default", "main"), value: "default" },
        { label: __("Success", "main"), value: "success" },
        { label: __("Warning", "main"), value: "warning" },
        { label: __("Pricing", "main"), value: "pricing" },
      ];

      // Get container-1056 classes based on style
      var getContainerClasses = function (style) {
        var baseClasses =
          "info_box_block flex flex-col justify-center items-start p-5 gap-2 md:gap-3 border border-gray-200 rounded-2xl";
        var styleClasses = {
          default: "bg-white",
          success: "bg-success-100",
          warning: "bg-warning-100",
          pricing: "bg-pricing-100",
        };
        return (
          baseClasses + " " + (styleClasses[style] || styleClasses.default)
        );
      };

      // Get heading text color based on style
      var getHeadingColor = function (style) {
        var colors = {
          default: "text-gray-800",
          success: "text-success-300",
          warning: "text-warning-300",
          pricing: "text-pricing-300",
        };
        return colors[style] || colors.default;
      };

      // Get icon color class based on style
      var getIconColor = function (style) {
        var colors = {
          default: "text-gray-800",
          success: "text-success-300",
          warning: "text-warning-300",
          pricing: "text-pricing-300",
        };
        return colors[style] || colors.default;
      };

      // Get default icon SVG based on style
      var getDefaultIcon = function (style) {
        var iconColorClass = getIconColor(style);
        var icons = {
          default: el(
            "svg",
            {
              className: "w-5 h-5 flex-shrink-0 " + iconColorClass,
              xmlns: "http://www.w3.org/2000/svg",
              width: "24",
              height: "24",
              viewBox: "0 0 24 24",
              fill: "none",
              stroke: "currentColor",
              strokeWidth: "2",
              strokeLinecap: "round",
              strokeLinejoin: "round",
            },
            el("circle", { cx: "12", cy: "12", r: "10" }),
            el("path", { d: "M12 16v-4" }),
            el("path", { d: "M12 8h.01" })
          ),
          success: el(
            "svg",
            {
              className: "w-5 h-5 flex-shrink-0 " + iconColorClass,
              xmlns: "http://www.w3.org/2000/svg",
              width: "24",
              height: "24",
              viewBox: "0 0 24 24",
              fill: "none",
              stroke: "currentColor",
              strokeWidth: "2",
              strokeLinecap: "round",
              strokeLinejoin: "round",
            },
            el("path", { d: "M20 6 9 17l-5-5" })
          ),
          warning: el(
            "svg",
            {
              className: "w-5 h-5 flex-shrink-0 " + iconColorClass,
              xmlns: "http://www.w3.org/2000/svg",
              width: "24",
              height: "24",
              viewBox: "0 0 24 24",
              fill: "none",
              stroke: "currentColor",
              strokeWidth: "2",
              strokeLinecap: "round",
              strokeLinejoin: "round",
            },
            el("path", {
              d: "M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719",
            }),
            el("path", { d: "M12 8v4" }),
            el("path", { d: "M12 16h.01" })
          ),
          pricing: el(
            "svg",
            {
              className: "w-5 h-5 flex-shrink-0 " + iconColorClass,
              xmlns: "http://www.w3.org/2000/svg",
              width: "24",
              height: "24",
              viewBox: "0 0 24 24",
              fill: "none",
              stroke: "currentColor",
              strokeWidth: "2",
              strokeLinecap: "round",
              strokeLinejoin: "round",
            },
            el("circle", { cx: "12", cy: "12", r: "10" }),
            el("path", { d: "M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" }),
            el("path", { d: "M12 18V6" })
          ),
        };
        return icons[style] || icons.default;
      };

      return el(
        Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Settings", "main"), initialOpen: true },
            el(SelectControl, {
              label: __("Style", "main"),
              value: style,
              options: styleOptions,
              onChange: function (value) {
                setAttributes({ style: value });
              },
            }),
            el(
              "div",
              { style: { marginTop: "16px" } },
              el(
                PanelBody,
                { title: __("Icon", "main"), initialOpen: false },
                el(
                  MediaUploadCheck,
                  {},
                  el(MediaUpload, {
                    onSelect: function (media) {
                      setAttributes({
                        iconId: media.id,
                        iconUrl: media.url,
                        iconType: "image",
                      });
                    },
                    allowedTypes: ["image"],
                    value: iconId,
                    render: function (obj) {
                      return el(
                        "div",
                        { style: { marginTop: "12px" } },
                        iconUrl
                          ? el(
                              "div",
                              {},
                              el("img", {
                                src: iconUrl,
                                alt: __("Icon", "main"),
                                style: {
                                  maxWidth: "80px",
                                  height: "auto",
                                  marginBottom: "10px",
                                },
                              }),
                              el(
                                Button,
                                {
                                  isSecondary: true,
                                  onClick: obj.open,
                                  style: { marginRight: "8px" },
                                },
                                __("Change Icon", "main")
                              ),
                              el(
                                Button,
                                {
                                  isDestructive: true,
                                  onClick: function () {
                                    setAttributes({
                                      iconId: 0,
                                      iconUrl: "",
                                      iconType: "none",
                                    });
                                  },
                                },
                                __("Remove Icon", "main")
                              )
                            )
                          : el(
                              Button,
                              {
                                isPrimary: true,
                                onClick: obj.open,
                              },
                              __("Select Image", "main")
                            )
                      );
                    },
                  })
                )
              )
            )
          )
        ),

        // Frontend-matching preview
        el(
          "div",
          blockProps,
          el(
            "div",
            { className: getContainerClasses(style) },
            // Icon and heading row
            el(
              "div",
              { className: "flex items-center gap-2" },
              iconUrl
                ? el("img", {
                    src: iconUrl,
                    alt: heading || __("Icon", "main"),
                    className: "w-5 h-5 flex-shrink-0",
                  })
                : getDefaultIcon(style),
              el(RichText, {
                tagName: "h4",
                className:
                  "text-base font-semibold m-0 leading-6 " +
                  getHeadingColor(style),
                value: heading,
                onChange: function (value) {
                  setAttributes({ heading: value });
                },
                placeholder: __("Enter heading...", "main"),
                allowedFormats: [],
              })
            ),
            // Content
            el(RichText, {
              tagName: "div",
              className:
                "text-sm font-medium leading-5 tracking-2p text-gray-600 [&_p]:m-0",
              value: content,
              onChange: function (value) {
                setAttributes({ content: value });
              },
              placeholder: __("Enter content...", "main"),
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
