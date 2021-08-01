/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Email input handler
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: 'input#password-current',
    canApply: function () {
      return this.$element.is('#password-current');
    },
    handler: function () {
      var form = this.getForm().$form;
      var passwordElement = $('#password', form);
      var emailElement = $('#login', form);

      var element = this;

      var handler = function () {
        var password = passwordElement.get(0);
        var email = emailElement.get(0);

        var emailTheSame = email.initialValue === email.value
            && !emailElement.closest('.table-value').hasClass('has-diff-saved-value');

        var passwordTheSame = password.initialValue === password.value
            && !passwordElement.closest('.table-value').hasClass('has-diff-saved-value');

        element.$element.closest('li.input').toggleClass(
          'hidden',
            passwordTheSame && emailTheSame
        );

        passwordElement.closest('li.input').toggleClass('hidden', !emailTheSame);
        emailElement.prop('readonly', !passwordTheSame);
        emailElement.parents('.edit-on-click-field').toggleClass('editable', passwordTheSame)
      };

      passwordElement
        .keyup(handler)
        .bind('change', handler)
        .bind('paste', handler);

      emailElement
        .keyup(handler)
        .bind('change', handler)
        .bind('paste', handler);

      handler();
    }
  }
);
