(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var InnerBlocks = wp.blockEditor.InnerBlocks;
  var RichText = wp.blockEditor.RichText;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var __ = wp.i18n.__;

  var ALLOWED_BLOCKS = ["main/product-item"];

  registerBlockType("main/product-list", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "product-list-editor flex flex-col gap-7.5",
      });

      return el(
        "div",
        blockProps,
        el(RichText, {
          tagName: "h2",
          className:
            "text-2xl text-gray-800 font-bold md:text-4xl leading-none md:leading-none",
          value: attributes.heading,
          onChange: function (value) {
            setAttributes({ heading: value });
          },
          placeholder: __("Enter heading...", "main"),
        }),
        el(
          "div",
          { className: "product-list-inner-blocks" },
        el(InnerBlocks, {
          allowedBlocks: ALLOWED_BLOCKS,
          template: [["main/product-item", {}]],
            templateLock: false,
            orientation: "vertical",
            renderAppender: InnerBlocks.DefaultBlockAppender,
        })
        )
      );
    },

    save: function (props) {
      var InnerBlocks = wp.blockEditor.InnerBlocks;
      return el(InnerBlocks.Content, {});
    },
  });
})(window.wp);
