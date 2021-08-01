/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.input-field-wrapper input.variants-float',
    handler: function () {
      this.commonController.isEqualValues = function (oldValue, newValue, element)
      {
        var result = this.element.sanitizeValue(oldValue, element) == this.element.sanitizeValue(newValue, element);

        if (('' === oldValue || '' === newValue) && oldValue !== newValue) {
          result = false;
        }

        return result;
      }
    }
  }
);

