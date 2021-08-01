/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('pcp_button_processors_checkout', ['pcp_button_processors', 'js/jquery'],
  function (processors, $) {
    processors.push(function (options) {
      if (this.$element.is('.pcp-checkout')) {
        var $element = this.$element

        options.onApprove = function (data) {
          assignWaitOverlay($('#page-wrapper'))

          var form = $element.closest('form').get(0)

          $('#pcp_on_approve_data', form).prop('value', JSON.stringify(data))

          form.submitBackground()
        }
      }
    })
  }
)
