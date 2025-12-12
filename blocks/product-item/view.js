/**
 * Product Item Update Log Accordion
 * Handles toggle behavior for integrated update log section.
 */
(function () {
  "use strict";

  function initProductUpdateLogs(root) {
    var scope = root || document;
    var toggleButtons = scope.querySelectorAll(
      '[data-toggle="product-update-log"]'
    );

    toggleButtons.forEach(function (button) {
      button.addEventListener("click", function () {
        var expanded = this.getAttribute("aria-expanded") === "true";
        var contentId = this.getAttribute("aria-controls");
        var content = document.getElementById(contentId);

        if (!content) {
          return;
        }

        this.setAttribute("aria-expanded", (!expanded).toString());

        if (expanded) {
          content.style.maxHeight = "0";
        } else {
          var inner = content.querySelector(".accordion-panel__inner");
          if (inner) {
            content.style.maxHeight = inner.scrollHeight + "px";
          }
        }
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initProductUpdateLogs(document);
    });
  } else {
    initProductUpdateLogs(document);
  }

  document.addEventListener("wp-blocks-rendered", function (event) {
    var target = event && event.target ? event.target : document;
    initProductUpdateLogs(target);
  });
})();

