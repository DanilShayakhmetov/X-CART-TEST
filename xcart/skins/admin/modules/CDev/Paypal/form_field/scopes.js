/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
    'scopes-select',
    '.scopes-select',
    function (event) {
      var $el = jQuery(this);

      $el.select2({
        escapeMarkup: function (markup) {
          return markup;
        }
      });
    }
);
