/**
 * Extend Core Heading Block with Number Style Option
 * 
 * Adds sidebar controls for number style with number field,
 * and applies data-number attribute and has_number_style class.
 *
 * @package Main
 * @since 1.0.0
 */
(function () {
  'use strict';

  var el = window.wp.element.createElement;
  var __ = window.wp.i18n.__;
  var addFilter = window.wp.hooks.addFilter;
  var InspectorControls = window.wp.blockEditor.InspectorControls;
  var PanelBody = window.wp.components.PanelBody;
  var ToggleControl = window.wp.components.ToggleControl;
  var TextControl = window.wp.components.TextControl;
  var getBlockSupport = window.wp.blocks.getBlockSupport;
  var applyFilters = window.wp.hooks.applyFilters;

  /**
   * Add number style controls to heading block sidebar
   */
  addFilter(
    'editor.BlockEdit',
    'main/heading-number-style',
    function (BlockEdit) {
      return function (props) {
        // Only apply to core/heading block
        if (props.name !== 'core/heading') {
          return el(BlockEdit, props);
        }

        var attributes = props.attributes;
        var setAttributes = props.setAttributes;

        // Get or initialize attributes
        var hasNumberStyle = attributes.hasNumberStyle || false;
        var numberValue = attributes.numberValue || '';

        return el(
          'div',
          null,
          el(BlockEdit, props),
          el(
            InspectorControls,
            {},
            el(
              PanelBody,
              {
                title: __('Number Style', 'main'),
                initialOpen: false,
              },
              el(ToggleControl, {
                label: __('Enable Number Style', 'main'),
                checked: hasNumberStyle,
                onChange: function (value) {
                  setAttributes({
                    hasNumberStyle: value,
                    // Keep number value when toggling - don't clear it
                    // numberValue will remain unchanged if not explicitly set
                  });
                },
              }),
              hasNumberStyle &&
                el(TextControl, {
                  label: __('Number', 'main'),
                  value: numberValue,
                  onChange: function (value) {
                    setAttributes({
                      numberValue: value,
                    });
                  },
                  placeholder: __('Enter number', 'main'),
                  type: 'text',
                  help: __('This number will be used in CSS :before pseudo-element content', 'main'),
                })
            )
          )
        );
      };
    }
  );

  /**
   * Add attributes to heading block
   */
  addFilter(
    'blocks.registerBlockType',
    'main/heading-number-style-attributes',
    function (settings, name) {
      if (name !== 'core/heading') {
        return settings;
      }

      // Add custom attributes
      if (!settings.attributes) {
        settings.attributes = {};
      }

      settings.attributes.hasNumberStyle = {
        type: 'boolean',
        default: false,
      };

      settings.attributes.numberValue = {
        type: 'string',
        default: '',
      };

      return settings;
    }
  );

  /**
   * Modify heading block save output to add data attribute and class
   */
  addFilter(
    'blocks.getSaveContent.extraProps',
    'main/heading-number-style-save',
    function (extraProps, blockType, attributes) {
      if (blockType.name !== 'core/heading') {
        return extraProps;
      }

      var hasNumberStyle = attributes.hasNumberStyle || false;
      var numberValue = attributes.numberValue || '';

      if (hasNumberStyle) {
        // Add class
        var className = extraProps.className || '';
        if (className.indexOf('has_number_style') === -1) {
          extraProps.className = className
            ? className + ' has_number_style'
            : 'has_number_style';
        }

        // Add data attribute
        if (numberValue) {
          extraProps['data-number'] = numberValue;
        }
      }

      return extraProps;
    }
  );

  /**
   * Modify heading block wrapper props in editor to add data attribute and class
   */
  addFilter(
    'editor.BlockListBlock',
    'main/heading-number-style-editor-wrapper',
    function (BlockListBlock) {
      return function (props) {
        if (props.name !== 'core/heading') {
          return el(BlockListBlock, props);
        }

        var attributes = props.attributes;
        var hasNumberStyle = attributes.hasNumberStyle || false;
        var numberValue = attributes.numberValue || '';

        if (hasNumberStyle) {
          // Clone wrapper props
          var wrapperProps = Object.assign({}, props.wrapperProps || {});
          var className = wrapperProps.className || '';

          // Add class if not already present
          if (className.indexOf('has_number_style') === -1) {
            wrapperProps.className = className
              ? className + ' has_number_style'
              : 'has_number_style';
          }

          // Add data attribute
          if (numberValue) {
            wrapperProps['data-number'] = numberValue;
          }

          // Return block with modified wrapper props
          return el(BlockListBlock, {
            ...props,
            wrapperProps: wrapperProps,
          });
        }

        return el(BlockListBlock, props);
      };
    }
  );
})();

