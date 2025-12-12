/**
 * Sidebar Settings Panel
 *
 * Adds controls for sidebar options in the post editor.
 *
 * @package Main
 */

const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { ToggleControl, TextareaControl, PanelRow } = wp.components;
const { useSelect } = wp.data;
const { useEntityProp } = wp.coreData;
const { __ } = wp.i18n;
const { createElement: el } = wp.element;

const SidebarSettingsPanel = () => {
  // Get post type
  const postType = useSelect((select) => {
    return select("core/editor").getCurrentPostType();
  }, []);

  // Get post meta
  const [meta, setMeta] = useEntityProp("postType", postType, "meta");

  // Get current template
  const currentTemplate = meta?.mcb_post_template || "buying_guide";

  // Get settings
  const showToc = meta?.show_sidebar_toc !== false; // Default true
  const showFilters = meta?.show_sidebar_filters !== false; // Default true
  const customHtml = meta?.sidebar_custom_html || "";

  // Update functions
  const updateShowToc = (value) => {
    setMeta({ ...meta, show_sidebar_toc: value });
  };

  const updateShowFilters = (value) => {
    setMeta({ ...meta, show_sidebar_filters: value });
  };

  const updateCustomHtml = (value) => {
    setMeta({ ...meta, sidebar_custom_html: value });
  };

  return el(
    PluginDocumentSettingPanel,
    {
      name: "sidebar-settings",
      title: __("Sidebar Settings", "main"),
      icon: "align-pull-right",
      className: "sidebar-settings-panel",
    },

    // TOC Toggle (both templates)
    el(
      PanelRow,
      {},
      el(ToggleControl, {
        label: __("Show Table of Contents", "main"),
        help: __("Display auto-generated TOC from H2 and H3 headings", "main"),
        checked: showToc,
        onChange: updateShowToc,
      })
    ),

    // Filters Toggle (Buying Guide only)
    currentTemplate === "buying_guide" &&
      el(
        PanelRow,
        { style: { marginTop: "16px" } },
        el(ToggleControl, {
          label: __("Show Filters", "main"),
          help: __("Display product filters in sidebar", "main"),
          checked: showFilters,
          onChange: updateShowFilters,
        })
      ),

    // Custom HTML (Info template only)
    currentTemplate === "info" &&
      el(
        PanelRow,
        { style: { marginTop: "16px" } },
        el(TextareaControl, {
          label: __("Custom HTML", "main"),
          help: __(
            "Add custom content to sidebar. Shortcodes are supported.",
            "main"
          ),
          value: customHtml,
          onChange: updateCustomHtml,
          rows: 8,
        })
      )
  );
};

registerPlugin("main-sidebar-settings", {
  render: SidebarSettingsPanel,
});
