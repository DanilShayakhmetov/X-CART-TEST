/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Float field microcontroller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '#auth-phone-number',
    handler: function () {
      f_apply_inputmask = function () {
        if (typeof (Inputmask) !== 'undefined') {
          Inputmask({mask: '999 999 9999', 'nullable': false}).mask(document.querySelectorAll('#auth-phone-number'))
        }
      }
      if (jQuery(this).val()) {
        f_apply_inputmask()
      }

      jQuery(this).on('click', function () {
          f_apply_inputmask()
        })
        .on('keyup', function (e) {
          var code = e.keyCode || e.which
          if (code === 9) {
            f_apply_inputmask()
          }
        })
    }
  }
)


CommonElement.prototype.validateAuth_phone_number = function () {
  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea')
  var value_len = this.element.value.trim().replace(/[ _]/g, '').length

  return {
    status: !apply || value_len == 10,
    message: 'Please make sure you are entering the correct phone number',
    apply: apply
  }
}
