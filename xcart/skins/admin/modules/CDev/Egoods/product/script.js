/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Product attachments admin list
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function ProductAttachmentsPrivateControl() {
  $('.product-attachments .lines .line').each(function () {
    var access = $(this).find('.cell.access');
    $(this).find('.cell.private').each(function () {
      var memberships_registered_customers = access.find('.attachment-access select option[value="R"]');

      $(this).find('input[type=checkbox]').change(function () {
        if ($(this).is(':checked')) {
          memberships_registered_customers.closest('select').val('A');
          memberships_registered_customers.hide();
        } else {
          memberships_registered_customers.show();
        }
      });
    })
  });
}

core.autoload(ProductAttachmentsPrivateControl);
