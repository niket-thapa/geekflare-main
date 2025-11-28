(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var Button = wp.components.Button;
  var __ = wp.i18n.__;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var useRef = wp.element.useRef;

  registerBlockType("main/pros-cons", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var pros = attributes.pros || [""];
      var cons = attributes.cons || [""];
      var clientId = props.clientId;

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "pros-cons-editor",
      });

      // Track newly added items for focusing
      var focusProIndexRef = useRef(null);
      var focusConIndexRef = useRef(null);
      
      // Helper function to focus on a RichText element
      function focusRichText(element) {
        if (!element) return;
        setTimeout(function() {
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

      // Focus callback for pros items
      function createProFocusCallback(index) {
        return function(element) {
          if (element && focusProIndexRef.current === index) {
            focusRichText(element);
            focusProIndexRef.current = null;
          }
        };
      }

      // Focus callback for cons items
      function createConFocusCallback(index) {
        return function(element) {
          if (element && focusConIndexRef.current === index) {
            focusRichText(element);
            focusConIndexRef.current = null;
          }
        };
      }

      function updatePro(index, value) {
        var newPros = pros.slice();
        newPros[index] = value;
        setAttributes({ pros: newPros });
      }

      function addPro(indexToInsertAfter) {
        var newPros = pros.slice();
        var insertIndex = indexToInsertAfter !== undefined ? indexToInsertAfter + 1 : newPros.length;
        newPros.splice(insertIndex, 0, "");
        setAttributes({ pros: newPros });
        focusProIndexRef.current = insertIndex;
      }

      function removePro(index) {
        var newPros = pros.slice();
        newPros.splice(index, 1);
        if (newPros.length === 0) newPros = [""];
        setAttributes({ pros: newPros });
      }

      function updateCon(index, value) {
        var newCons = cons.slice();
        newCons[index] = value;
        setAttributes({ cons: newCons });
      }

      function addCon(indexToInsertAfter) {
        var newCons = cons.slice();
        var insertIndex = indexToInsertAfter !== undefined ? indexToInsertAfter + 1 : newCons.length;
        newCons.splice(insertIndex, 0, "");
        setAttributes({ cons: newCons });
        focusConIndexRef.current = insertIndex;
      }

      function removeCon(index) {
        var newCons = cons.slice();
        newCons.splice(index, 1);
        if (newCons.length === 0) newCons = [""];
        setAttributes({ cons: newCons });
      }

      // Ensure we always have at least one item (even if empty) for seamless editing
      if (pros.length === 0) {
        pros = [""];
      }
      if (cons.length === 0) {
        cons = [""];
      }

      return el(
        "div",
        blockProps,
        el(
          "div",
          {
            className:
              "pros-cons flex flex-col items-start p-0 gap-4 md:gap-5 flex-none self-stretch",
          },
          el(
            "h3",
            {
              className:
                "text-base font-semibold leading-6 flex items-center text-gray-800 flex-none m-0",
            },
            __("Pros & Cons of the product", "main")
          ),

          el(
            "div",
            {
              className:
                "pros-cons-cards flex flex-col md:flex-row p-0 gap-3 md:gap-4 flex-none self-stretch",
            },
            // PROS Card
            el(
              "div",
              {
                className:
                  "pros-card box-border flex flex-col items-start p-4 gap-3 flex-1 bg-white border border-gray-200 rounded-xl",
              },
              el(
                "h4",
                {
                  className:
                    "text-xs font-bold leading-4 tracking-[0.1em] uppercase text-success-600 flex-none m-0",
                },
                __("PROS", "main")
              ),
              el(
                "div",
                {
                  className:
                    "pros-list flex flex-col items-start p-0 gap-2.5 md:gap-2 flex-none self-stretch",
                },
                pros.map(function (pro, index) {
                  return el(
                    "div",
                    {
                      key: index,
                      ref: createProFocusCallback(index),
                      className:
                        "pros-item flex flex-row items-start p-0 gap-1.5 flex-none self-stretch group",
                    },
                    el(
                      "svg",
                      {
                        className: "w-5 h-5 flex-none mt-0.5",
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
                        className:
                          "flex-1 flex items-center gap-2 min-w-0",
                      },
                      el(
                        RichText,
                        {
                          tagName: "span",
                          className:
                            "text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p text-gray-900 flex-1",
                          value: pro,
                          onChange: function (value) {
                            updatePro(index, value);
                          },
                          placeholder: __("Enter a pro...", "main"),
                          allowedFormats: [],
                          __unstableOnSplitAtEnd: function () {
                            addPro(index);
                          },
                        }
                      ),
                      el(
                        Button,
                        {
                          className: "pros-cons-remove-button opacity-0 group-hover:opacity-100 transition-opacity",
                          isDestructive: true,
                          isSmall: true,
                          onClick: function () {
                            removePro(index);
                          },
                          style: { flexShrink: 0 },
                        },
                        "×"
                      )
                    )
                  );
                }),
                el(
                  Button,
                  {
                    variant: "secondary",
                    isSmall: true,
                    onClick: addPro,
                    style: {
                      marginTop: "4px",
                      alignSelf: "flex-start",
                    },
                  },
                  __("+ Add Pro", "main")
                )
              )
            ),
            // CONS Card
            el(
              "div",
              {
                className:
                  "cons-card box-border flex flex-col items-start p-4 gap-3 flex-1 bg-white border border-gray-200 rounded-xl",
              },
              el(
                "h4",
                {
                  className:
                    "text-xs font-bold leading-4 tracking-[0.1em] uppercase text-error-600 flex-none m-0",
                },
                __("CONS", "main")
              ),
              el(
                "div",
                {
                  className:
                    "cons-list flex flex-col items-start p-0 gap-2.5 md:gap-2 flex-none self-stretch",
                },
                cons.map(function (con, index) {
                  return el(
                    "div",
                    {
                      key: index,
                      ref: createConFocusCallback(index),
                      className:
                        "cons-item flex flex-row items-start p-0 gap-1.5 flex-none self-stretch group",
                    },
                    el(
                      "svg",
                      {
                        className: "w-5 h-5 flex-none mt-0.5",
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
                        className:
                          "flex-1 flex items-center gap-2 min-w-0",
                      },
                      el(
                        RichText,
                        {
                          tagName: "span",
                          className:
                            "text-sm md:text-base font-medium leading-5 md:leading-6 tracking-2p text-gray-900 flex-1",
                          value: con,
                          onChange: function (value) {
                            updateCon(index, value);
                          },
                          placeholder: __("Enter a con...", "main"),
                          allowedFormats: [],
                          __unstableOnSplitAtEnd: function () {
                            addCon(index);
                          },
                        }
                      ),
                      el(
                        Button,
                        {
                          className: "pros-cons-remove-button opacity-0 group-hover:opacity-100 transition-opacity",
                          isDestructive: true,
                          isSmall: true,
                          onClick: function () {
                            removeCon(index);
                          },
                          style: { flexShrink: 0 },
                        },
                        "×"
                      )
                    )
                  );
                }),
                el(
                  Button,
                  {
                    variant: "secondary",
                    isSmall: true,
                    onClick: addCon,
                    style: {
                      marginTop: "4px",
                      alignSelf: "flex-start",
                    },
                  },
                  __("+ Add Con", "main")
                )
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
