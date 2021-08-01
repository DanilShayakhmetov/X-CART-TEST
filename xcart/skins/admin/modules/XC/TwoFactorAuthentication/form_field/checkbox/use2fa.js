/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * SMS code field validation
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

CommonElement.prototype.handlers.push(
  {
    pattern: 'input#auth-2fa-enabled',
    canApply: function () {
      return this.$element.is('#auth-2fa-enabled')
    },
    handler: function () {

      var form = this.getForm().$form
      var authConfirmPass = $('#auth-confirm-password', form)

      var f_toggle_field_n_update_btn = function (show_flag) {
        /* the field will be shown in 2 cases. See below and sms_code.js */
        show_flag = Boolean(show_flag)
        authConfirmPass.closest('li').toggle(show_flag) /* is shown by default on error*/
        /* disable/enable update btn */
        authConfirmPass.closest('form').find('button').eq(0).prop('disabled', show_flag)
      }

      f_toggle_field_n_update_btn($('li.error').length >= 1);

      /* enable update btn on any typed symbol */
      (function () {
        var btn_is_visible = false
        authConfirmPass.on('input', function () {
          if (!btn_is_visible && $(this).val() != '') {
            btn_is_visible = true
            $(this).closest('form').find('button').eq(0).prop('disabled', false)
          }
        })
      }())

      var f_fill_auth_confirm_pass_by_serviceval = function () {
        if (!authConfirmPass.val()) {
          authConfirmPass.val('|__service_value__|')
        }
      }
      f_fill_auth_confirm_pass_by_serviceval()

      /* show/hide additional password field when auth_2fa_enabled disabled/enabled */
      this.$element.on('click',
        function () {
          f_toggle_field_n_update_btn(!this.checked)

          if (this.checked) {
            f_fill_auth_confirm_pass_by_serviceval()

          } else {
            if (authConfirmPass.val() == '|__service_value__|') {
              authConfirmPass.val('')
            }
          }
        }
      )

    }
  }
)
