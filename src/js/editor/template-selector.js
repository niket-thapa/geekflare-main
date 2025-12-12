/**
 * Template Selector for Post Editor
 *
 * Adds a dropdown in the Document sidebar to choose between
 * Buying Guide and Info Article templates.
 */

const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { SelectControl, Notice } = wp.components;
const { useSelect } = wp.data;
const { useEntityProp } = wp.coreData;
const { __ } = wp.i18n;
const { useEffect, createElement: el } = wp.element;

/**
 * Template Selector Panel Component
 */
const TemplateSelectorPanel = () => {
  // Get current post type
  const postType = useSelect((select) => {
    return select("core/editor").getCurrentPostType();
  }, []);

  // Get and set post meta
  const [meta, setMeta] = useEntityProp("postType", postType, "meta");

  // Get current template value
  const templateValue = meta?.mcb_post_template || "buying_guide";

  // Update template
  const updateTemplate = (newValue) => {
    setMeta({ ...meta, mcb_post_template: newValue });
  };

  // Buying Guide info box
  const buyingGuideInfo = el(
    "div",
    null,
    el(
      "strong",
      {
        style: { display: "block", marginBottom: "8px", color: "#007cba" },
      },
      "✨ Buying Guide Features"
    ),
    el(
      "ul",
      { style: { margin: "0", paddingLeft: "20px" } },
      el("li", null, "Affiliate disclosure toggle"),
      el("li", null, "AI summarization (ChatGPT & Gemini)"),
      el("li", null, "Product comparison filters"),
      el("li", null, "Full TOC with nested items")
    )
  );

  // Info Article info box
  const infoArticleInfo = el(
    "div",
    null,
    el(
      "strong",
      {
        style: { display: "block", marginBottom: "8px", color: "#46b450" },
      },
      "📚 Info Article Features"
    ),
    el(
      "ul",
      { style: { margin: "0", paddingLeft: "20px" } },
      el("li", null, "Always-visible sidebar"),
      el("li", null, "Simplified TOC (no filters)"),
      el("li", null, "Tutorial-focused layout"),
      el("li", null, "AI summarization (ChatGPT & Gemini)"),
      el("li", null, "No affiliate disclosure")
    )
  );

  return el(
    PluginDocumentSettingPanel,
    {
      name: "template-selector",
      title: __("Post Template", "main"),
      icon: "layout",
      className: "template-selector-panel",
    },
    // Select Control
    el(SelectControl, {
      label: __("Template Type", "main"),
      value: templateValue,
      options: [
        { label: "📋 Buying Guide", value: "buying_guide" },
        { label: "📖 Info Article", value: "info" },
      ],
      onChange: updateTemplate,
      help: __("Choose the template layout for this post", "main"),
      __nextHasNoMarginBottom: true,
    }),

    // Info box
    el(
      "div",
      {
        style: {
          marginTop: "16px",
          padding: "12px",
          backgroundColor:
            templateValue === "buying_guide" ? "#e7f5fe" : "#ecf7ed",
          border: `1px solid ${
            templateValue === "buying_guide" ? "#007cba" : "#46b450"
          }`,
          borderRadius: "4px",
          fontSize: "12px",
          lineHeight: "1.5",
        },
      },
      templateValue === "buying_guide" ? buyingGuideInfo : infoArticleInfo
    ),

    // Notice/Tip
    templateValue === "buying_guide" &&
      el(
        Notice,
        {
          status: "info",
          isDismissible: false,
          style: { marginTop: "16px", fontSize: "11px" },
        },
        el(
          "span",
          null,
          "💡 ",
          el("strong", null, "Tip:"),
          " Use this for product comparisons, reviews, and buying guides."
        )
      ),

    templateValue === "info" &&
      el(
        Notice,
        {
          status: "success",
          isDismissible: false,
          style: { marginTop: "16px", fontSize: "11px" },
        },
        el(
          "span",
          null,
          "💡 ",
          el("strong", null, "Tip:"),
          " Use this for tutorials, how-to guides, and informational content."
        )
      )
  );
};

// Register the plugin
registerPlugin("main-template-selector", {
  render: TemplateSelectorPanel,
});
