(function (wp) {
  var registerPlugin = wp.plugins.registerPlugin;
  var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
  var ToggleControl = wp.components.ToggleControl;
  var __ = wp.i18n.__;
  var useSelect = wp.data.useSelect;
  var useDispatch = wp.data.useDispatch;
  var el = wp.element.createElement;

  function AffiliateDisclosureToggle() {
    var meta = useSelect(function (select) {
      return select("core/editor").getEditedPostAttribute("meta");
    }, []);

    var editPost = useDispatch("core/editor").editPost;

    var showAffiliateDisclosure =
      (meta && meta.show_affiliate_disclosure) || false;

    function handleToggle(value) {
      editPost({
        meta: {
          show_affiliate_disclosure: value,
        },
      });
    }

    return el(
      PluginDocumentSettingPanel,
      {
        name: "main-affiliate-disclosure",
        title: __("Affiliate Disclosure", "main"),
        className: "main-affiliate-disclosure-panel",
      },
      el("p", {
        style: { marginTop: 0 },
      }, __("Control whether to show the affiliate disclosure button in the post meta bar.", "main")),
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

