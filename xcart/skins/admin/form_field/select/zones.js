/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

window.require('form_field/select2-generic', function (genericHandler) {
  CommonForm.elementControllers.push(
    {
      pattern: '.input-zones-select2 select',
      handler: genericHandler
    }
  );
});