/**
 * Admin Post Settings - Affiliate Disclosure Toggle
 *
 * Adds affiliate disclosure toggle to block editor sidebar.
 * Conditionally shows/hides based on selected template.
 * Only visible for Buying Guide template.
 *
 * @package Main
 */

(function (wp) {
  var registerPlugin = wp.plugins.registerPlugin;
  var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
  var ToggleControl = wp.components.ToggleControl;
  var __ = wp.i18n.__;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;
  var useEffect = wp.element.useEffect;
  var el = wp.element.createElement;

  function AffiliateDisclosureToggle() {
    // Get post meta
    var meta = useSelect(function (select) {
      return select("core/editor").getEditedPostAttribute("meta");
    }, []);

    var editPost = useDispatch("core/editor").editPost;

    // Get current template
    var currentTemplate = (meta && meta.mcb_post_template) || "buying_guide";

    // Get affiliate disclosure setting
    var showAffiliateDisclosure =
      (meta && meta.show_affiliate_disclosure) || false;

    // Handle toggle change
    function handleToggle(value) {
      editPost({
        meta: {
          show_affiliate_disclosure: value,
        },
      });
    }

    // Auto-disable affiliate disclosure when switching to info template
    useEffect(
      function () {
        if (currentTemplate === "info" && showAffiliateDisclosure) {
          editPost({
            meta: {
              show_affiliate_disclosure: false,
            },
          });
        }
      },
      [currentTemplate]
    );

    // Only show panel for buying guide template
    if (currentTemplate !== "buying_guide") {
      return null;
    }

    return el(
      PluginDocumentSettingPanel,
      {
        name: "main-affiliate-disclosure",
        title: __("Affiliate Disclosure", "main"),
        className: "main-affiliate-disclosure-panel",
      },
      el(
        "p",
        {
          style: { marginTop: 0 },
        },
        __(
          "Control whether to show the affiliate disclosure button in the post meta bar.",
          "main"
        )
      ),
      el(ToggleControl, {
        label: __("Show Affiliate Disclosure", "main"),
        checked: showAffiliateDisclosure,
        onChange: handleToggle,
        help: __(
          "Enable to show the affiliate disclosure button in the post meta bar.",
          "main"
        ),
      })
    );
  }

  registerPlugin("main-affiliate-disclosure", {
    render: AffiliateDisclosureToggle,
    icon: null,
  });
})(window.wp);
