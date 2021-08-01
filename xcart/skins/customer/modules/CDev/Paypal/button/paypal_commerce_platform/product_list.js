/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('pcp_button_processors_product_list', ['pcp_button_processors'], function (processors) {
  processors.push(function (options) {
    if (this.$element.is('.pcp-product-list')) {
      var product_id = this.$element.get(0).dataset.productId;

      // options.style.layout = 'horizontal';

      options.createOrder = function (data, actions) {
        var requestData = {}

        requestData.data = data
        requestData[xliteConfig.form_id_name] = xliteConfig.form_id

        return new Promise(function (resolve, reject) {
          core.post({
            'target': 'cart',
            'action': 'add'
          }, function () {
            return core.post({
                target: 'paypal_commerce_platform',
                action: 'create_order'
              }, function (XMLHttpRequest, textStatus, data, valid) {
                data = jQuery.parseJSON(data)

                if (data) {
                  if (data.result && data.result.id) {
                    resolve(data.result.id)
                  } else {
                    core.trigger('message', {type: 'error', message: data.message || core.t('Unexpected error')});
                    reject(data.name)
                  }
                } else {
                  core.trigger('message', {type: 'error', message: core.t('Unexpected error')});
                  reject('Unexpected error')
                }
              },
              requestData
            )
          }, {
            'target': 'cart',
            'action': 'add',
            product_id: product_id
          })
        })
      }
    }
  })
})
