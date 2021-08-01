/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * File text input field controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonForm.elementControllers.push(
  {
    pattern: '.inline-field.inline-text-file',
    handler: function () {
      var field = jQuery(this);
      
      field.bind(
        'saveEmptyFieldInline',
        function(event) {
          if ($(this).data('is-escape')) {
            this.getViewValueElements().text($(this).data('empty'));
          } else {
            this.getViewValueElements().html($(this).data('empty'));
          }

          jQuery('.field :input', this).val($(this).data('empty'));
        }
      ).bind(
        'startEditInline',
        function (event) {
          var input = jQuery('.field :input', this).get(0);

          input.setSelectionRange(0, input.value.length);
        }
      );

      this.isAffectWholeLine = false;
    }
  }
);
