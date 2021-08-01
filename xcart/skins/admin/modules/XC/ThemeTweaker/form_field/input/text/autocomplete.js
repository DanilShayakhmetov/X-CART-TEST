/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Autocomplete
 *
 * Copyright (c) 2001-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'theme_tweaker_autocomplete',
  '.input-field-wrapper.input.input-text-autocomplete input[type=text]',
  function () {
    var url_data = core.getCommentedData($(this).parent()).data_source_url;

    if (url_data) {
      $(this).autocomplete({
        source: function (request, resolve) {
          url_data.term = request.term;
          core.get(URLHandler.buildURL(url_data),
            function(xhr, status, data) {
              if (xhr.readyState == 4 && xhr.status == 200) {
                resolve(JSON.parse(data));
              }
            });
        },
        minLength: 1,
        appendTo: $(this).parent()
      });
    }
  }
);