/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * MailChimp settings
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


jQuery().ready(function () {
  $('#analytics360enabled').change(function () {
    if (!$(this).is(':checked')) {
      core.trigger('message', {
        type: 'warning',
        message: core.t('e-Commerce Analytics disable warning')
      });
    }
  });
});