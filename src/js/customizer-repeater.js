/**
 * Customizer Repeater Control
 *
 * Handles dynamic adding/removing of repeater items in WordPress Customizer.
 *
 * @package Main
 * @since 1.0.0
 */
(function ($) {
  "use strict";

  var RepeaterControl = {
    initialized: false,
    updateTimeout: null,
    isUpdating: false,

    init: function () {
      if (this.initialized) {
        return;
      }

      var self = this;

      $("body").on("click", ".main-repeater-add", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.addItem($(this));
        return false;
      });

      $("body").on("click", ".main-repeater-remove", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.removeItem($(this));
        return false;
      });

      $("body").on("click", ".main-repeater-upload-image", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.uploadImage($(this));
        return false;
      });

      $("body").on("click", ".main-repeater-remove-image", function (e) {
        e.preventDefault();
        e.stopPropagation();
        self.removeImage($(this));
        return false;
      });

      $("body").on("input", ".main-repeater-alt-text", function () {
        var $item = $(this).closest(".main-repeater-control-item");
        var altText = $(this).val();
        var title = altText || "New Partner";
        $item.find(".main-repeater-control-item-title").text(title);
        self.updateInputValue();
      });

      $("body").on("input", ".main-repeater-url", function () {
        self.updateInputValue();
      });

      this.initialized = true;
    },

    getItemTemplate: function () {
      return (
        '<li class="main-repeater-control-item">' +
        '<div class="main-repeater-control-item-header">' +
        '<span class="main-repeater-control-item-title">New Partner</span>' +
        '<button type="button" class="button-link main-repeater-remove" aria-label="Remove">Remove</button>' +
        "</div>" +
        '<div class="main-repeater-control-item-content">' +
        '<div class="main-repeater-control-field">' +
        "<label>" +
        '<span class="customize-control-title">Logo Image</span>' +
        '<div class="main-repeater-image-container">' +
        '<div class="main-repeater-image-placeholder">No image selected</div>' +
        '<div class="main-repeater-image-buttons">' +
        '<button type="button" class="button main-repeater-upload-image">Select Image</button>' +
        '<button type="button" class="button main-repeater-remove-image" style="display:none;">Remove</button>' +
        "</div>" +
        '<input type="hidden" class="main-repeater-image-url" value="" />' +
        "</div>" +
        "</label>" +
        "</div>" +
        '<div class="main-repeater-control-field">' +
        "<label>" +
        '<span class="customize-control-title">Alt Text</span>' +
        '<input type="text" class="main-repeater-alt-text" value="" placeholder="Partner name" />' +
        "</label>" +
        "</div>" +
        '<div class="main-repeater-control-field">' +
        "<label>" +
        '<span class="customize-control-title">Link URL</span>' +
        '<input type="url" class="main-repeater-url" value="" placeholder="https://example.com" />' +
        "</label>" +
        "</div>" +
        "</div>" +
        "</li>"
      );
    },

    addItem: function ($button) {
      var $control = $button.closest(".customize-control");
      var $list = $control.find(".main-repeater-control-list");
      if ($list.length === 0) {
        return;
      }
      var template = this.getItemTemplate();
      $list.append(template);
      this.updateInputValue();
    },

    removeItem: function ($button) {
      $button.closest(".main-repeater-control-item").remove();
      this.updateInputValue();
    },

    uploadImage: function ($button) {
      if (typeof wp === "undefined" || !wp.media) {
        alert("Media library not available. Please refresh the page.");
        return;
      }

      var self = this;
      var $item = $button.closest(".main-repeater-control-item");
      if (!$item.length) {
        return;
      }

      // Create new media frame for this upload
      var mediaFrame = wp.media({
        title: "Select Partner Logo",
        button: { text: "Use this image" },
        multiple: false,
      });

      // Handle image selection
      mediaFrame.on("select", function () {
        var attachment = mediaFrame.state().get("selection").first().toJSON();
        var imageUrl = attachment.url || "";
        if (!imageUrl) {
          return;
        }

        // Find the image input for THIS specific item
        var imageInput = $item[0].querySelector(
          "input.main-repeater-image-url"
        );
        if (!imageInput) {
          return;
        }

        // Set the image URL
        imageInput.value = imageUrl;

        // Update preview
        var container = $item[0].querySelector(
          ".main-repeater-image-container"
        );
        var placeholder = container.querySelector(
          ".main-repeater-image-placeholder"
        );
        var existingImg = container.querySelector(
          ".main-repeater-image-preview"
        );
        var removeBtn = $item[0].querySelector(".main-repeater-remove-image");

        if (placeholder) {
          placeholder.outerHTML =
            '<img src="' +
            imageUrl +
            '" alt="" class="main-repeater-image-preview" />';
        } else if (existingImg) {
          existingImg.src = imageUrl;
          existingImg.style.display = "";
        } else {
          var img = document.createElement("img");
          img.src = imageUrl;
          img.className = "main-repeater-image-preview";
          container.insertBefore(img, container.firstChild);
        }

        if (removeBtn) {
          removeBtn.style.display = "";
        }

        // Update after a delay - use longer delay to ensure DOM is fully settled
        setTimeout(function () {
          self.updateInputValue();
        }, 1000);
      });

      mediaFrame.open();
    },

    removeImage: function ($button) {
      var $item = $button.closest(".main-repeater-control-item");
      var imageInput = $item[0].querySelector("input.main-repeater-image-url");
      var container = $item[0].querySelector(".main-repeater-image-container");
      var preview = container.querySelector(".main-repeater-image-preview");

      if (imageInput) {
        imageInput.value = "";
      }
      if (preview) {
        preview.remove();
      }

      var placeholder = document.createElement("div");
      placeholder.className = "main-repeater-image-placeholder";
      placeholder.textContent = "No image selected";
      container.insertBefore(placeholder, container.firstChild);

      $button.hide();
      this.updateInputValue();
    },

    updateInputValue: function () {
      var self = this;
      clearTimeout(this.updateTimeout);
      // Longer debounce to batch multiple rapid updates
      this.updateTimeout = setTimeout(function () {
        self.doUpdateInputValue();
      }, 1200);
    },

    doUpdateInputValue: function () {
      if (this.isUpdating) {
        return;
      }

      this.isUpdating = true;
      var self = this;

      setTimeout(function () {
        try {
          $(".main-repeater-control-list").each(function () {
            var $list = $(this);
            var $control = $list.closest(".customize-control");
            var $input = $control.find(".main-repeater-setting");
            if ($input.length === 0) {
              return;
            }

            var items = [];
            var itemElements = $list[0].querySelectorAll(
              "li.main-repeater-control-item"
            );

            // Collect data from each item
            for (var i = 0; i < itemElements.length; i++) {
              var itemEl = itemElements[i];
              var imageInput = itemEl.querySelector(
                "input.main-repeater-image-url"
              );
              var altInput = itemEl.querySelector(
                "input.main-repeater-alt-text"
              );
              var urlInput = itemEl.querySelector(
                "input.main-repeater-url"
              );

              var image =
                imageInput && imageInput.value ? imageInput.value.trim() : "";
              var alt = altInput && altInput.value ? altInput.value.trim() : "";
              var url = urlInput && urlInput.value ? urlInput.value.trim() : "";

              items.push({
                image: image,
                alt: alt,
                url: url,
              });

              // Update title
              var titleEl = itemEl.querySelector(
                ".main-repeater-control-item-title"
              );
              if (titleEl) {
                titleEl.textContent = alt || "Partner " + (i + 1);
              }
            }

            // Create JSON
            var newValue = JSON.stringify(items);
            $input.val(newValue);

            // Update WordPress setting
            var settingId = $input.attr("data-customize-setting-link");
            if (settingId && typeof wp !== "undefined" && wp.customize) {
              try {
                var setting = wp.customize(settingId);
                if (setting) {
                  setting.set(JSON.stringify(items));
                }
              } catch (e) {
                // Silently fail if setting update fails
              }
            }

            // Trigger change
            $input.trigger("change");
          });
        } catch (error) {
          // Silently fail if update fails
        } finally {
          self.isUpdating = false;
        }
      }, 100);
    },
  };

  // Initialize when WordPress Customizer is ready
  if (typeof wp !== "undefined" && wp.customize) {
    wp.customize.bind("ready", function () {
      setTimeout(function () {
        RepeaterControl.init();
      }, 200);
    });
  } else {
    // Fallback if wp.customize is not yet available
    $(document).ready(function () {
      if (
        !RepeaterControl.initialized &&
        typeof wp !== "undefined" &&
        wp.customize
      ) {
        setTimeout(function () {
          RepeaterControl.init();
        }, 100);
      }
    });
  }
})(jQuery);
