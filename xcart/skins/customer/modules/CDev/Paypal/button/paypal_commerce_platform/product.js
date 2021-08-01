/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('pcp_button_processors_product', ['pcp_button_processors'], function (processors) {

  var createOrderData = null
  var resolver = null
  var rejector = null

  define(['ProductDetails'], function () {
    decorate(
      'ProductDetailsView',
      'postprocessAdd2Cart',
      function (event, data) {
        if (createOrderData === null) {
          arguments.callee.previousMethod.apply(this, arguments)
        } else {
          core.trigger('pcpProductAdded')
        }
      }
    )

    core.bind('pcpProductAdded', function () {
      var requestData = {}

      requestData.data = createOrderData
      requestData[xliteConfig.form_id_name] = xliteConfig.form_id

      return core.post({
          target: 'paypal_commerce_platform',
          action: 'create_order'
        }, function (XMLHttpRequest, textStatus, data, valid) {
          data = jQuery.parseJSON(data)

          createOrderData = null

          if (data) {
            if (data.result && data.result.id) {
              resolver(data.result.id)
            } else {
              core.trigger('message', {type: 'error', message: data.message || core.t('Unexpected error')});
              rejector(data.name)
            }
          } else {
            core.trigger('message', {type: 'error', message: core.t('Unexpected error')});
            rejector('Unexpected error')
          }
        },
        requestData
      )
    })
  })

  processors.push(function (options) {
    if (this.$element.is('.pcp-product-page')) {
      var $element = this.$element

      options.createOrder = function (data, actions) {
        createOrderData = data
        var form = $element.closest('form').get(0)
        if (form) {
          return new Promise(function (resolve, reject) {
            resolver = resolve
            rejector = reject

            form.commonController.backgroundSubmit = true
            $(form).submit()
          })
        }

        return false
      }
    }
  })
})
