(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;
  var useRef = wp.element.useRef;

  registerBlockType("main/key-features", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var features = attributes.features || [""];
      var heading = attributes.heading || __("Key Features", "main");
      var clientId = props.clientId;

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "key-features-editor",
      });

      // Track newly added items for focusing
      var focusFeatureIndexRef = useRef(null);

      // Helper function to focus on a RichText element
      function focusRichText(element) {
        if (!element) return;
        setTimeout(function () {
          var editable = element.querySelector('[contenteditable="true"]');
          if (editable) {
            editable.focus();
            // Place cursor at the end
            var range = document.createRange();
            range.selectNodeContents(editable);
            range.collapse(false);
            var sel = window.getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
          }
        }, 10);
      }

      // Focus callback for feature items
      function createFeatureFocusCallback(index) {
        return function (element) {
          if (element && focusFeatureIndexRef.current === index) {
            focusRichText(element);
            focusFeatureIndexRef.current = null;
          }
        };
      }

      function updateFeature(index, value) {
        var newFeatures = features.slice();
        newFeatures[index] = value;
        setAttributes({ features: newFeatures });
      }

      function addFeature(indexToInsertAfter) {
        var newFeatures = features.slice();
        var insertIndex =
          indexToInsertAfter !== undefined
            ? indexToInsertAfter + 1
            : newFeatures.length;
        newFeatures.splice(insertIndex, 0, "");
        setAttributes({ features: newFeatures });
        focusFeatureIndexRef.current = insertIndex;
      }

      function removeFeature(index) {
        var newFeatures = features.slice();
        newFeatures.splice(index, 1);
        if (newFeatures.length === 0) newFeatures = [""];
        setAttributes({ features: newFeatures });
      }

      // Ensure we always have at least one item (even if empty) for seamless editing
      if (features.length === 0) {
        features = [""];
      }

      return el(
        "div",
        blockProps,
        el(
          "div",
          {
            className:
              "key-features flex flex-col items-start p-0 gap-4 md:gap-5 flex-none self-stretch",
          },
          el(RichText, {
            tagName: "h3",
            className:
              "text-base font-semibold leading-6 flex items-center text-gray-800 flex-none m-0",
            value: heading,
            onChange: function (value) {
              setAttributes({ heading: value });
            },
            placeholder: __("Key Features", "main"),
            allowedFormats: [],
          }),

          el(
            "div",
            {
              className:
                "key-features-list flex flex-row flex-wrap items-start content-start p-0 gap-2 flex-none self-stretch",
            },
            features.map(function (feature, index) {
              return el(
                "div",
                {
                  key: index,
                  ref: createFeatureFocusCallback(index),
                  className:
                    "key-feature-badge box-border flex flex-row items-center py-1.5 px-2.5 md:px-3 gap-2 bg-gray-50 border border-gray-200 rounded-full group max-w-full",
                },
                el(RichText, {
                  tagName: "span",
                  className:
                    "text-sm md:text-base font-medium leading-4.5 md:leading-5.5 tracking-2p text-gray-800",
                  value: feature,
                  onChange: function (value) {
                    updateFeature(index, value);
                  },
                  placeholder: __("Enter feature...", "main"),
                  allowedFormats: [],
                  __unstableOnSplitAtEnd: function () {
                    addFeature(index);
                  },
                }),
                el(
                  Button,
                  {
                    className:
                      "key-features-remove-button opacity-0 group-hover:opacity-100 transition-opacity",
                    isDestructive: true,
                    isSmall: true,
                    onClick: function () {
                      removeFeature(index);
                    },
                    style: {
                      flexShrink: 0,
                      marginLeft: "4px",
                      padding: "2px 6px",
                      minWidth: "auto",
                      height: "auto",
                      lineHeight: 1,
                    },
                  },
                  "×"
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
