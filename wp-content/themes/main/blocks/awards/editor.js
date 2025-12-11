(function (wp) {
  var registerBlockType = wp.blocks.registerBlockType;
  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var TextControl = wp.components.TextControl;
  var PanelBody = wp.components.PanelBody;
  var useSelect = wp.data.useSelect;
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;
  var useEffect = wp.element.useEffect;
  var __ = wp.i18n.__;

  registerBlockType("main/awards", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var clientId = props.clientId;
      var heading = attributes.heading || __("Awards", "main");
      var productIdAttr = attributes.productId || 0;

      var blockProps = useBlockProps({
        className: "awards-editor",
      });

      /* -------------------------------------------
       * Detect Parent Product ID
       * ------------------------------------------- */
      var parentProductId = useSelect(
        function (select) {
          var blockEditor = select("core/block-editor");
          if (!blockEditor) return 0;

          var block = blockEditor.getBlock(clientId);
          if (!block) return 0;

          var parentIds = blockEditor.getBlockParents(clientId);
          if (!parentIds || parentIds.length === 0) return 0;

          for (var i = 0; i < parentIds.length; i++) {
            var parentBlock = blockEditor.getBlock(parentIds[i]);

            if (
              parentBlock &&
              parentBlock.name === "main/product-item" &&
              parentBlock.attributes &&
              parentBlock.attributes.productId
            ) {
              return parseInt(parentBlock.attributes.productId, 10) || 0;
            }
          }

          return 0;
        },
        [clientId]
      );

      var productId = parentProductId || productIdAttr;

      useEffect(
        function () {
          if (
            parentProductId &&
            parentProductId !== productIdAttr &&
            parentProductId > 0
          ) {
            setAttributes({ productId: parentProductId });
          }
        },
        [parentProductId, productIdAttr]
      );

      const [productData, setProductData] = useState(null);
      const [awardData, setAwardData] = useState(null);
      const [awardsConfig, setAwardsConfig] = useState(null);
      const [loading, setLoading] = useState(false);

      /* -------------------------------------------
       * Fetch Product and Awards Data
       * ------------------------------------------- */
      useEffect(() => {
        const currentProductId = parentProductId || productIdAttr;

        if (!currentProductId || currentProductId === 0) {
          setProductData(null);
          setAwardData(null);
          setAwardsConfig(null);
          return;
        }

        setLoading(true);

        Promise.all([
          apiFetch({ path: "/wp/v2/products/" + currentProductId })
            .then((post) => {
              const meta = post?.meta || {};
              return {
                id: post.id,
                name: meta.product_name || post.title?.rendered || "",
                award: (meta.award || "").trim(),
              };
            })
            .catch(() => null),

          apiFetch({ path: "/main/v1/awards" }).catch(() => ({})),
        ])
          .then(([product, awards]) => {
            setProductData(product);
            setAwardsConfig(awards);

            const awardKey = (product?.award || "").trim();

            if (awardKey && awards && awards[awardKey]) {
              setAwardData(awards[awardKey]);
            } else {
              setAwardData(null);
            }

            setLoading(false);
          })
          .catch(() => {
            setProductData(null);
            setAwardData(null);
            setAwardsConfig(null);
            setLoading(false);
          });
      }, [parentProductId, productIdAttr]);

      /* -------------------------------------------
       * Render Output
       * ------------------------------------------- */
      return el(
        Fragment,
        {},
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Settings", "main"), initialOpen: true },

            el(TextControl, {
              label: __("Heading", "main"),
              value: heading,
              onChange: function (value) {
                setAttributes({ heading: value });
              },
            }),

            el(TextControl, {
              label: __("Product ID", "main"),
              type: "number",
              value: productIdAttr || "",
              onChange: function (value) {
                setAttributes({ productId: parseInt(value, 10) || 0 });
              },
              help: parentProductId
                ? __("Auto-detected parent product ID: ", "main") +
                  parentProductId
                : __(
                    "Enter manually or place inside Product Item block",
                    "main"
                  ),
            })
          )
        ),

        /* --- BLOCK PREVIEW --- */
        el(
          "div",
          blockProps,
          el(
            "div",
            { className: "awards flex flex-col gap-5" },

            el(
              "h3",
              {
                className:
                  "text-base font-semibold leading-6 text-gray-800 m-0",
              },
              heading
            ),

            (function () {
              /* Loading */
              if (loading) {
                return el(
                  "p",
                  { className: "text-gray-400 italic text-center" },
                  __("Loading award...", "main")
                );
              }

              /* Invalid product */
              if (!productId) {
                return el(
                  "p",
                  { className: "text-gray-400 italic text-center" },
                  __("No product detected.", "main")
                );
              }

              /* Determine final award data */
              let displayAward = null;

              if (awardData && awardData.image) {
                displayAward = awardData;
              } else if (productData && productData.award && awardsConfig) {
                let key = productData.award.trim();
                if (key && awardsConfig[key]) {
                  displayAward = awardsConfig[key];
                }
              }

              if (!displayAward) {
                return el(
                  "p",
                  { className: "text-gray-400 italic text-center" },
                  __("No award found for this product.", "main")
                );
              }

              /* Render Award */
              return el(
                "div",
                { className: "awards-list flex flex-wrap gap-6" },
                el(
                  "figure",
                  { className: "award-item w-[5.8125rem] m-0" },
                  el("img", {
                    src: displayAward.image,
                    alt: displayAward.label || "Award",
                    width: "93",
                    height: "100",
                    className: "w-full h-auto object-contain m-0",
                    loading: "lazy",
                  }),
                  el(
                    "figcaption",
                    { className: "sr-only" },
                    displayAward.label || "Award"
                  )
                )
              );
            })()
          )
        )
      );
    },

    save: function () {
      return null;
    },
  });
})(window.wp);
