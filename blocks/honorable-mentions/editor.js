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
  var apiFetch = wp.apiFetch;
  var useState = wp.element.useState;

  registerBlockType("main/honorable-mentions", {
    edit: function (props) {
      var attributes = props.attributes;
      var setAttributes = props.setAttributes;
      var products = attributes.products || [];

      // Get block props to make the block selectable
      var blockProps = useBlockProps({
        className: "honorable-mentions-editor",
      });

      var searchTerm = useState("")[0];
      var setSearchTerm = useState("")[1];
      var searchResults = useState([])[0];
      var setSearchResults = useState([])[1];

      function searchProducts(term) {
        if (!term || term.length < 2) {
          setSearchResults([]);
          return;
        }

        apiFetch({
          path:
            "/wp/v2/products?search=" +
            encodeURIComponent(term) +
            "&per_page=10",
        }).then(function (results) {
          setSearchResults(results);
        });
      }

      function addProduct(product) {
        var newProducts = products.slice();
        var exists = newProducts.some(function (p) {
          return p.id === product.id;
        });

        if (!exists) {
          newProducts.push({
            id: product.id,
            name: product.title.rendered,
            rank: newProducts.length + 4,
          });
          setAttributes({ products: newProducts });
        }

        setSearchTerm("");
        setSearchResults([]);
      }

      function removeProduct(index) {
        var newProducts = products.slice();
        newProducts.splice(index, 1);
        setAttributes({ products: newProducts });
      }

      function updateRank(index, value) {
        var newProducts = products.slice();
        newProducts[index].rank = parseInt(value) || 0;
        setAttributes({ products: newProducts });
      }

      return el(
        "div",
        blockProps,
        el(
          InspectorControls,
          {},
          el(
            PanelBody,
            { title: __("Product Selection", "main"), initialOpen: true },
            el(TextControl, {
              label: __("Search Products", "main"),
              value: searchTerm,
              onChange: function (value) {
                setSearchTerm(value);
                searchProducts(value);
              },
              placeholder: __("Type to search...", "main"),
            }),

            searchResults.length > 0 &&
              el(
                "div",
                { className: "product-search-results" },
                searchResults.map(function (result) {
                  return el(
                    Button,
                    {
                      key: result.id,
                      onClick: function () {
                        addProduct(result);
                      },
                      variant: "secondary",
                      style: {
                        marginBottom: "4px",
                        display: "block",
                        width: "100%",
                      },
                    },
                    result.title.rendered
                  );
                })
              )
          )
        ),

        el(
          "section",
          { className: "honorable-mentions-preview" },
          el(RichText, {
            tagName: "h2",
            value: attributes.heading,
            onChange: function (value) {
              setAttributes({ heading: value });
            },
            placeholder: __("Section Heading", "main"),
          }),

          products.length === 0 &&
            el(
              "p",
              { className: "placeholder" },
              __(
                "No products selected. Use the sidebar to search and add products.",
                "main"
              )
            ),

          products.length > 0 &&
            el(
              "div",
              { className: "products-grid" },
              products.map(function (product, index) {
                return el(
                  "div",
                  { key: product.id, className: "product-card" },
                  el("div", { className: "rank-badge" }, "#" + product.rank),
                  el("div", { className: "product-name" }, product.name),
                  el(TextControl, {
                    label: __("Rank", "main"),
                    type: "number",
                    value: product.rank,
                    onChange: function (value) {
                      updateRank(index, value);
                    },
                  }),
                  el(
                    Button,
                    {
                      onClick: function () {
                        removeProduct(index);
                      },
                      isDestructive: true,
                    },
                    __("Remove", "main")
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
