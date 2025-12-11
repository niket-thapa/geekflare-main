(function ($) {
  "use strict";

  var mediaFrame;

  function updateLogoPreview(url) {
    var preview = $(".main-product-meta__logo-preview");

    if (url) {
      preview.html(
        '<img src="' + url + '" alt="Product logo preview" width="150" />'
      );
    } else {
      preview.html(
        '<span class="main-product-meta__logo-placeholder">' +
          mainProductsAdmin.placeholder +
          "</span>"
      );
    }
  }

  function bindUploadButton() {
    $(document).on("click", ".main-product-upload-logo", function (event) {
      event.preventDefault();

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: "Select or Upload Logo",
        button: {
          text: "Use this logo",
        },
        library: {
          type: [
            "image/svg+xml",
            "image/png",
            "image/jpeg",
            "image/gif",
            "image/webp",
          ],
        },
        multiple: false,
      });

      mediaFrame.on("select", function () {
        var attachment = mediaFrame.state().get("selection").first().toJSON();
        var previewUrl = attachment.url;

        if (attachment.sizes && attachment.sizes.thumbnail) {
          previewUrl = attachment.sizes.thumbnail.url;
        }

        $("#main-product-logo").val(attachment.id);
        updateLogoPreview(previewUrl);
      });

      mediaFrame.open();
    });
  }

  function setButtonLoading($button, isLoading) {
    if (isLoading) {
      $button.data("original-text", $button.text());
      $button.prop("disabled", true).text(mainProductsAdmin.fetchingText);
    } else {
      $button.prop("disabled", false).text($button.data("original-text"));
    }
  }

  function reuseExistingLogo(logos) {
    if (!logos || !logos.length) {
      window.alert(mainProductsAdmin.existingNoneError);
      return false;
    }

    var selected = logos[0];

    $("#main-product-logo").val(selected.id);
    updateLogoPreview(selected.url);

    return true;
  }

  function requestLogoFetch(website, $button, forceFetch) {
    setButtonLoading($button, true);

    $.post(ajaxurl, {
      action: mainProductsAdmin.fetchAction,
      nonce: mainProductsAdmin.nonce,
      post_id: $button.data("post-id"),
      website_url: website,
      force_fetch: forceFetch ? 1 : 0,
    })
      .done(function (response) {
        if (!response || !response.success || !response.data) {
          window.alert(mainProductsAdmin.errorText);
          setButtonLoading($button, false);
          return;
        }

        if (response.data.status === "existing" && !forceFetch) {
          setButtonLoading($button, false);

          if (
            window.confirm(
              response.data.message || mainProductsAdmin.existingPrompt
            )
          ) {
            reuseExistingLogo(response.data.logos);
            return;
          }

          requestLogoFetch(website, $button, true);
          return;
        }

        if (response.data.status === "fetched") {
          $("#main-product-logo").val(response.data.id);
          updateLogoPreview(response.data.url);
          setButtonLoading($button, false);
          return;
        }

        window.alert(response.data.message || mainProductsAdmin.errorText);
        setButtonLoading($button, false);
      })
      .fail(function () {
        window.alert(mainProductsAdmin.errorText);
        setButtonLoading($button, false);
      });
  }

  function bindFetchButton() {
    $(document).on("click", ".main-product-fetch-logo", function (event) {
      event.preventDefault();

      var $button = $(this);
      var website = $("#main-product-website").val();

      if (!website) {
        window.alert("Please add the Website URL first.");
        return;
      }

      requestLogoFetch(website, $button, false);
    });
  }

  function updateLogsHiddenField() {
    var logs = [];
    $(
      "#main-product-update-logs-container .main-product-meta__update-log-item"
    ).each(function () {
      var $item = $(this);
      var date = $item.find(".main-product-update-log-date").val().trim();
      var description = $item
        .find(".main-product-update-log-description")
        .val()
        .trim();

      if (date || description) {
        logs.push({
          date: date,
          description: description,
        });
      }
    });

    // Always set the value, even if empty array
    var jsonValue = JSON.stringify(logs);
    $("#main-product-update-logs").val(jsonValue);
  }

  function formatDateForDisplay(date) {
    if (!date) return "";
    var months = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];
    var month = months[date.getMonth()];
    var year = date.getFullYear();
    return month + " " + year;
  }

  function parseDateFromDisplay(value) {
    if (!value) return null;

    // Try to parse formats like "October 2025" or "Oct 2025"
    var months = [
      "january",
      "february",
      "march",
      "april",
      "may",
      "june",
      "july",
      "august",
      "september",
      "october",
      "november",
      "december",
    ];
    var shortMonths = [
      "jan",
      "feb",
      "mar",
      "apr",
      "may",
      "jun",
      "jul",
      "aug",
      "sep",
      "oct",
      "nov",
      "dec",
    ];

    var parts = value.trim().split(/\s+/);
    if (parts.length >= 2) {
      var monthName = parts[0].toLowerCase();
      var year = parseInt(parts[parts.length - 1]);

      if (!isNaN(year)) {
        var monthIndex = months.indexOf(monthName);
        if (monthIndex === -1) {
          monthIndex = shortMonths.indexOf(monthName);
        }
        if (monthIndex !== -1) {
          return new Date(year, monthIndex, 1);
        }
      }
    }

    // Fallback: try to parse as a regular date
    var parsed = new Date(value);
    if (!isNaN(parsed.getTime())) {
      return parsed;
    }

    return null;
  }

  function initDatePicker($input) {
    if (!$input || !$.fn.datepicker) {
      return;
    }

    // Parse existing value if present
    var existingValue = $input.val();
    var existingDate = parseDateFromDisplay(existingValue);

    $input.datepicker({
      dateFormat: "mm/dd/yy",
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
      maxDate: new Date(), // Disable future dates - can only select today or past dates
      defaultDate: existingDate || new Date(),
      onClose: function (dateText, inst) {
        // Get selected date from the datepicker
        var date = $(this).datepicker("getDate");
        if (date) {
          // Prevent future dates
          var today = new Date();
          today.setHours(0, 0, 0, 0);
          var selectedDate = new Date(date);
          selectedDate.setHours(0, 0, 0, 0);

          if (selectedDate > today) {
            // If future date was selected, clear it or set to today
            alert(
              "Future dates are not allowed. Please select today or a past date."
            );
            $(this).val("");
            updateLogsHiddenField();
            return;
          }

          // Format as "Month Year" (e.g., "October 2025")
          var formatted = formatDateForDisplay(date);
          $(this).val(formatted);
          updateLogsHiddenField();
        } else {
          // Clear the field if date was removed
          $(this).val("");
          updateLogsHiddenField();
        }
      },
      beforeShow: function (input, inst) {
        // Parse existing value to set initial date
        var currentValue = $(input).val();
        var parsedDate = parseDateFromDisplay(currentValue);
        if (parsedDate) {
          setTimeout(function () {
            $(input).datepicker("setDate", parsedDate);
          }, 1);
        }
      },
    });

    // Convert existing value format if needed
    if (existingValue && existingDate) {
      var formatted = formatDateForDisplay(existingDate);
      if (formatted !== existingValue) {
        $input.val(formatted);
      }
    }
  }

  function initAllDatePickers() {
    $(".main-product-update-log-date").each(function () {
      if (!$(this).hasClass("hasDatepicker")) {
        initDatePicker($(this));
      }
    });
  }

  function addUpdateLog() {
    var $container = $("#main-product-update-logs-container");
    var index = $container.find(".main-product-meta__update-log-item").length;
    var $item = $(
      '<div class="main-product-meta__update-log-item" data-index="' +
        index +
        '">' +
        '<div class="main-product-meta__update-log-actions">' +
        '<button type="button" class="button button-small main-product-update-log-move-up">↑</button>' +
        '<button type="button" class="button button-small main-product-update-log-move-down">↓</button>' +
        '<button type="button" class="button button-small button-link-delete main-product-update-log-remove">Remove</button>' +
        "</div>" +
        '<input type="text" class="widefat main-product-update-log-date" placeholder="e.g., October 2025" />' +
        '<textarea class="widefat main-product-update-log-description" rows="2" placeholder="What was updated?"></textarea>' +
        "</div>"
    );

    $container.append($item);
    // Initialize datepicker on the new field
    initDatePicker($item.find(".main-product-update-log-date"));
    updateLogsHiddenField();
    updateMoveButtons();
  }

  function removeUpdateLog($item) {
    $item.remove();
    updateLogsHiddenField();
    updateMoveButtons();
  }

  function moveUpdateLog($item, direction) {
    if (direction === "up") {
      $item.prev().before($item);
    } else {
      $item.next().after($item);
    }
    updateLogsHiddenField();
    updateMoveButtons();
  }

  function updateMoveButtons() {
    $(
      "#main-product-update-logs-container .main-product-meta__update-log-item"
    ).each(function (index) {
      var $item = $(this);
      var total = $(
        "#main-product-update-logs-container .main-product-meta__update-log-item"
      ).length;

      $item
        .find(".main-product-update-log-move-up")
        .prop("disabled", index === 0);
      $item
        .find(".main-product-update-log-move-down")
        .prop("disabled", index === total - 1);
    });
  }

  function bindUpdateLogs() {
    $(document).on("click", ".main-product-add-update-log", function (event) {
      event.preventDefault();
      addUpdateLog();
    });

    $(document).on(
      "click",
      ".main-product-update-log-remove",
      function (event) {
        event.preventDefault();
        removeUpdateLog($(this).closest(".main-product-meta__update-log-item"));
      }
    );

    $(document).on(
      "click",
      ".main-product-update-log-move-up",
      function (event) {
        event.preventDefault();
        var $item = $(this).closest(".main-product-meta__update-log-item");
        moveUpdateLog($item, "up");
      }
    );

    $(document).on(
      "click",
      ".main-product-update-log-move-down",
      function (event) {
        event.preventDefault();
        var $item = $(this).closest(".main-product-meta__update-log-item");
        moveUpdateLog($item, "down");
      }
    );

    $(document).on(
      "input change",
      ".main-product-update-log-date, .main-product-update-log-description",
      function () {
        updateLogsHiddenField();
      }
    );

    // Initialize move buttons
    updateMoveButtons();

    // Initialize datepickers for existing fields
    initAllDatePickers();
  }

  // Update hidden field before form submission
  function updateBeforeSubmit() {
    updateLogsHiddenField();
    updateScoreBreakdownHiddenField();
  }

  // ========== Score Breakdown Functions ==========

  function updateScoreBreakdownHiddenField() {
    var criteria = [];
    $(
      "#main-product-score-breakdown-container .main-product-meta__score-criterion"
    ).each(function () {
      var $item = $(this);
      var name = $item.find(".main-product-score-criterion-name").val().trim();
      var score =
        parseFloat($item.find(".main-product-score-criterion-score").val()) ||
        0;

      if (name) {
        criteria.push({
          name: name,
          score: score,
        });
      }
    });

    var jsonValue = JSON.stringify(criteria);
    $("#main-product-score-breakdown").val(jsonValue);

    // Calculate and update auto-rating
    calculateAndUpdateRating();
  }

  function calculateAndUpdateRating() {
    var total = 0;
    var count = 0;
    var $ratingInput = $("#main-product-rating");
    var existingValue = $ratingInput.val(); // Preserve existing value

    $(
      "#main-product-score-breakdown-container .main-product-meta__score-criterion"
    ).each(function () {
      var score =
        parseFloat($(this).find(".main-product-score-criterion-score").val()) ||
        0;
      var name = $(this)
        .find(".main-product-score-criterion-name")
        .val()
        .trim();

      if (name) {
        total += score;
        count++;
      }
    });

    if (count > 0) {
      var average = (total / count).toFixed(1);
      $("#main-product-auto-rating").text(average + "/5");
      $ratingInput.val(average);
    } else {
      // Don't clear the value if no criteria - preserve existing value
      $("#main-product-auto-rating").text("N/A");
      // Only set to empty if there was no existing value
      if (!existingValue) {
        $ratingInput.val("");
      }
    }
  }

  function addScoreCriterion() {
    var $container = $("#main-product-score-breakdown-container");
    var index = $container.find(".main-product-meta__score-criterion").length;
    var $item = $(
      '<div class="main-product-meta__score-criterion" data-index="' +
        index +
        '">' +
        '<div class="main-product-meta__score-criterion-header">' +
        "<label>" +
        "Criterion Name" +
        '<input type="text" class="widefat main-product-score-criterion-name" placeholder="e.g., Ease of Use" />' +
        "</label>" +
        '<button type="button" class="button button-small button-link-delete main-product-score-criterion-remove">Remove</button>' +
        "</div>" +
        "<label>" +
        'Score: <span class="main-product-score-criterion-score-display">3</span>/5' +
        "</label>" +
        '<input type="range" class="main-product-score-criterion-score" min="0" max="5" step="0.1" value="3" />' +
        "</div>"
    );

    $container.append($item);
    updateScoreBreakdownHiddenField();
  }

  function removeScoreCriterion($item) {
    $item.remove();
    updateScoreBreakdownHiddenField();
  }

  function bindScoreBreakdown() {
    $(document).on(
      "click",
      ".main-product-add-score-criterion",
      function (event) {
        event.preventDefault();
        addScoreCriterion();
      }
    );

    $(document).on(
      "click",
      ".main-product-score-criterion-remove",
      function (event) {
        event.preventDefault();
        removeScoreCriterion(
          $(this).closest(".main-product-meta__score-criterion")
        );
      }
    );

    $(document).on(
      "input change",
      ".main-product-score-criterion-name, .main-product-score-criterion-score",
      function () {
        var $item = $(this).closest(".main-product-meta__score-criterion");
        var score =
          parseFloat($item.find(".main-product-score-criterion-score").val()) ||
          0;
        $item.find(".main-product-score-criterion-score-display").text(score);
        updateScoreBreakdownHiddenField();
      }
    );

    // Initialize on page load
    calculateAndUpdateRating();
  }

  $(function () {
    bindUploadButton();
    bindFetchButton();
    bindUpdateLogs();
    bindScoreBreakdown();

    // Initialize datepickers on page load
    initAllDatePickers();

    // Update hidden field before form submission
    var $form = $("#post");
    if ($form.length) {
      $form.on("submit", function () {
        updateBeforeSubmit();
      });

      // Also update on autosave
      $(document).on("heartbeat-tick.autosave", function () {
        updateBeforeSubmit();
      });
    }
  });
})(jQuery);
