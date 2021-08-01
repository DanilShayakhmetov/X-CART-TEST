/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Onboarding Domain name page controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CheckDomainButton () {
  CheckDomainButton.superclass.constructor.apply(this, arguments)
}

jQuery(function () {
  const form = jQuery('.domain-name-page .send-form form').eq(0)
  const submitButton = form.find('button.send-btn')
  let formSubmit = false

  extend(CheckDomainButton, ProgressStateButton)

  CheckDomainButton.autoload = function () {
    if (!submitButton.checkDomain) {
      submitButton.checkDomain = new CheckDomainButton(submitButton)

      submitButton.click(function () {
        form.submit()
      })
    }
  }

  core.autoload(CheckDomainButton)

  core.microhandlers.add(
    'CheckDomainButton',
    '.btn.check-domain',
    function (event) {
      core.autoload(CheckDomainButton)
    }
  )

  form.bind(
    'state-changed',
    function () {
      submitButton.commonController('enable')
    }
  )

  form.bind(
    'state-initial',
    function () {
      submitButton.commonController('disable')
    }
  )

  form.bind('submit', function (event) {
    const domain = jQuery(this).find('#domain-name').val()

    if (!domain) {
      return false
    }

    submitButton.checkDomain.setStateInProgress()

    if (!formSubmit) {
      disableInputsInForm(form)

      const url = URLHandler.buildURL({
        target: 'domain_is_available',
        action: 'check_domain',
        domain_name: domain
      })

      core.get(url, function (xhr, status, data) {
        const result = jQuery.parseJSON(data)
        const inputs = form.find(':input')

        if (inputs.length) {
          _.each(inputs, function (input) {
            input.enable()
          })
        }

        if (result) {
          submitButton.checkDomain.setStateStill()

          if (result.errCode) {
            if (result.isInlineError) {
              jQuery('.input-text-domain').addClass('has-error');
              jQuery('.input-text-domain .help-block').text(result.errMsg);
            } else {
              core.trigger('message', {
                  type: 'error',
                  message: core.t('An error occurred while validating the domain name', {errCode: result.errCode})
              });
            }
          } else if (result.isAvailable === true) {
            popup.load(
              URLHandler.buildURL({
                domain_name: domain,
                target: 'domain_is_available',
                widget: 'XLite\\View\\Popup\\DomainIsAvailable'
              })
            )
          } else if (result.isAvailable === false) {
            popup.load(
              URLHandler.buildURL({
                domain_name: domain,
                target: 'cloud_domain_confirm',
                widget: 'XLite\\View\\DomainNameConfirm'
              })
            )
          }
        }
      })

      return false
    }
  })

  jQuery('body').on('click', '.domain-is-on-sale .cancel-button', function () {
    popup.close()
  }).on('click', '.domain-is-on-sale .send-anyway-button', function () {
    jQuery(this).commonController('disable')
    formSubmit = true
    submitForm(form.get(0))
  })
})
