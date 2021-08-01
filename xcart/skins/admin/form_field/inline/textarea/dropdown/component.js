/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Simple textarea inline field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push({
  pattern: '.list .line .cell .inline-field:not(.inline-textarea-dropdown)',
  handler: function () {
    this.isAffectWholeLine = false;
  }
});
