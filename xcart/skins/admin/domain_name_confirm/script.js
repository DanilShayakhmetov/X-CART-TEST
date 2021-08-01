/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Change domain name confirm
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.microhandlers.add(
  'ChangeDomainNameConfirmProceed',
  '.domain-name-confirm .proceed-button',
  function () {
    jQuery(this).click(function () {
      var domain = jQuery('#domain-name').val()
      var auth_code = jQuery('.domain-name-confirm').data('auth-code')
      var cloud_action = jQuery('.domain-name-confirm').data('cloud-action')

      window.location.replace(URLHandler.buildURL({
        base: 'cloud.php',
        action: cloud_action,
        domain: domain,
        auth_code: auth_code
      }))
    })
  }
)

core.microhandlers.add(
  'ChangeDomainNameConfirmCancel',
  '.domain-name-confirm .cancel-button',
  function () {
    jQuery(this).click(function () {
      popup.close()
    })
  }
)