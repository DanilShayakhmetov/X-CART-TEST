/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Email from script
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

jQuery(document).ready(function () {
  core.microhandlers.add(
    'email_from',
    'select[name="mail_from_type"]',
    function() {
      $(this).change(function () {
          if ($(this).val() === 'manual') {
            $('.mail-from-manual-value').closest('.input').show();
          } else {
            $('.mail-from-manual-value').closest('.input').hide();
          }
      }).change();
    }
  );
});
