/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * SMS code field validation
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


CommonElement.prototype.validationCacheSMSCode = {}

CommonElement.prototype.handlers.push(
  {
    pattern: 'input#auth-phone-number',
    canApply: function () {
      return this.$element.is('#auth-sms-code')
    },
    handler: function () {

      /* run validateAuth_sms_code even for hidden sms field */
      this.$element.data('jqv', {validateNonVisibleFields: true})

      var form = this.getForm().$form
      this.$element.data('original_phone', $('#auth-phone-number', form).val().trim() + $('#auth-phone-code', form).val())

      var smsCodeElement = $('#auth-sms-code', form)

      /* the field will be shown after ajax request */
      smsCodeElement.closest('li').hide()

      /* Allow to run validation function */
      jQuery(this.getForm().form.commonController).on('local.submit.prevalidate',
        function () {
          smsCodeElement.data('_allow_validation_once', 1)
        }
      )

      var o = this
      /* Send sms to the customer phone number */
      jQuery(this.getForm().form.commonController).on('local.submit.prevalidate',
        function () {
          var phone_number = $('#auth-phone-number', form).val().trim().replace(/[ _]/g, '')

          if (phone_number.length == 10 && $('#auth-2fa-enabled', form).prop('checked')) {
            /* Step 1 send sms code, show sms field if needed */
            o.sendVerificationSmsRequest($('#login', form).val(), $('#auth-phone-code', form).val(), phone_number, smsCodeElement)
          }
          return true
        }
      )
    }
  }
)

/* Step 1 send sms code, show sms field if needed */
CommonElement.prototype.sendVerificationSmsRequest = function (email, phoneCode, phoneNumber, in_smsCodeElement) {

  core.get(
    URLHandler.buildURL({
      target: 'AuthyLogin',
      action: 'requestVerificationSms',
      email: email,
      phoneCode: phoneCode,
      phoneNumber: phoneNumber
    }),
    (function (xhr, status, data) {

      if ('"s_verification_is_passed"' === data) {
        /* The final step is already passed */
        in_smsCodeElement.data('_is_already_verified', 1)
        in_smsCodeElement.val('')
        in_smsCodeElement.closest('li').hide()
      } else {
        in_smsCodeElement.data('_is_already_verified', 0)
        in_smsCodeElement.closest('li').show()

        /* show additional password field if it exists */
        var authConfirmPass = $('#auth-confirm-password', in_smsCodeElement.closest('form'))
        if (authConfirmPass.val() == '|__service_value__|') {
          authConfirmPass.val('')
          /* disable update btn */
          authConfirmPass.closest('form').find('button').eq(0).prop('disabled', true)
        }
        authConfirmPass.closest('li').show()

      }

    }).bind(this),
    {},
    {async: true}
  )
}


/* Step 2 verify filled SMS via ajax on submit event. Stop form submit if needed */
CommonElement.prototype.validateAuth_sms_code = function () {
  this.hideInlineMessage()
  var phoneNumber = this.getForm().getElements().filter('#auth-phone-number').val()

  var apply = isElement(this.element, 'input') || isElement(this.element, 'textarea')

  var $smsField = $('#auth-sms-code', this.getForm().$form)

  var result = {
    status: $smsField.data('original_phone').length > 10 && $smsField.data('original_phone') == (phoneNumber.trim().replace(/[ _]/g, '') + $('#auth-phone-code', this.getForm().$form).val()),
    message: '',
    apply: apply
  }

  if (!$smsField.data('_allow_validation_once') || result.status) {
    if (typeof (this.validationCacheSMSCode[phoneNumber + '|' + this.element.value]) !== 'undefined') {
      /* We have a cached result */
      result = this.getSMSCodeValidationResult(result, this.validationCacheSMSCode[phoneNumber + '|' + this.element.value])
    }
    return result
  }

  /* Server has a cached result */
  if ($smsField.data('_is_already_verified')) {
    /* The phone was verified in the past */
    result.status = true
    result.message = 'SMS code is valid'
    return result
  }

  /* Only one check per one form submit */
  $smsField.data('_allow_validation_once', 0)


  /* The main part of validation via ajax request */
  if (this.element.value) {
    if (typeof (this.validationCacheSMSCode[phoneNumber + '|' + this.element.value]) !== 'undefined') {
      result = this.getSMSCodeValidationResult(result, this.validationCacheSMSCode[phoneNumber + '|' + this.element.value])

    } else if (!this.$element.hasClass('progress-mark-apply')) {
      this.markAsProgress()
      result.status = false

      this.runSmsCodeValidationRequest(result, $('#auth-phone-code', this.getForm().$form).val(), phoneNumber, this.getForm().$form)
    }

  } else if (!this.element.value) {
    this.markAsInvalid()
    result.status = false

  } else if (!result.status) {
    var message = core.t(result.message)
    this.markAsInvalid(message)
  }

  return result
}

/* ajax routine */
CommonElement.prototype.runSmsCodeValidationRequest = function (in_result, phoneCode, phoneNumber, main_form) {
  core.get(
    URLHandler.buildURL({
      target: 'AuthyLogin',
      action: 'verifyToken',
      phoneCode: phoneCode,
      phoneNumber: phoneNumber,
      smsCode: this.element.value
    }),
    (function (xhr, status, data) {
      this.unmarkAsProgress()
      this.validationCacheSMSCode[phoneNumber + '|' + this.element.value] = data
      in_result = this.getSMSCodeValidationResult(in_result, data)

      if (in_result.status) {
        $(this.element).data('_is_already_verified', 1)
        $(this.element).val('')
        $(this.element).closest('li').hide()

        $('#auth-2fa-enabled', main_form).val(1)
        main_form.submit()
      }
    }).bind(this),
    {},
    {async: true}
  )
}

/* Parse sms verification responce from server */
CommonElement.prototype.getSMSCodeValidationResult = function (result, status) {
  if ('"s_verification_is_passed"' === status) {
    result.status = true
    result.message = 'SMS code is valid'
    this.markAsValid(core.t(result.message))

  } else {
    result.status = false
    result.message = 'SMS code is invalid'
    this.markAsInvalid(core.t(result.message))
  }

  return result
}
