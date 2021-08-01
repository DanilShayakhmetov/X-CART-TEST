/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('pcp_button_processors', function () {
  var processors = []
  processors.push(function (options) {
    options.createOrder = function (data, actions) {
      var requestData = {}

      requestData.data = data
      requestData[xliteConfig.form_id_name] = xliteConfig.form_id

      return new Promise(function (resolve, reject) {
        return core.post({
            target: 'paypal_commerce_platform',
            action: 'create_order'
          }, function (XMLHttpRequest, textStatus, data, valid) {
            data = jQuery.parseJSON(data)

            if (data) {
              if (data.result && data.result.id) {
                resolve(data.result.id)
              } else {
                reject(data)
              }
            } else {
              reject({message: 'Unexpected error'})
            }
          },
          requestData
        )
      })
    }

    options.onApprove = function (data) {
      var requestData = {}

      requestData.data = data
      requestData[xliteConfig.form_id_name] = xliteConfig.form_id

      assignWaitOverlay($('#page-wrapper'))

      return core.post({
          target: 'paypal_commerce_platform',
          action: 'on_approve'
        },
        null,
        requestData
      )
    }

    options.onCancel = function () {
    }

    options.onError = function (data) {
      core.trigger('message', {type: 'error', message: data.message || core.t('Unexpected error')})
    }
  })

  return processors
})

define('pcp_button', ['paypal_sdk', 'pcp_button_processors', 'js/jquery', 'js/underscore', 'pcp_mmenu_loaded'],
  function (paypal, processors, $, _) {

    var PCPButton = function (selector, processors) {
      this.selector = selector
      this.$element = $(selector)
      this.processors = processors

      this.renderButton(this.getPaypalButton())
    }

    PCPButton.prototype.selector = null

    PCPButton.prototype.$element = null

    PCPButton.prototype.processors = []

    PCPButton.generateId = function () {
      return 'paypal_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)
    }

    PCPButton.prototype.renderButton = function (button) {
      if ($(this.selector).length) {
        button.render(this.selector)
      }
    }

    PCPButton.prototype.getButtonStyle = function () {
      return core.getCommentedData(this.$element)
    }

    PCPButton.prototype.getButtonOptions = function () {
      var options = {
        style: this.getButtonStyle(this.$element)
      }

      var self = this
      this.processors.forEach(function (callback) {
        _.bind(callback, self)(options)
      })

      return options
    }

    PCPButton.prototype.getPaypalButton = function () {
      return paypal.Buttons(this.getButtonOptions())
    }

    window.pcp = PCPButton

    PCPButton.init = function () {
      $('.pcp-button-container').each(function () {
        if ($(this).data('paypal-rendered')) {
          return
        }
        $(this).data('paypal-rendered', true)

        var id = PCPButton.generateId()
        $(this).attr('id', id)

        new PCPButton('#' + id, processors)
      })
    }

    core.microhandlers.add('PaypalButton', '.pcp-button-container', function () {
      PCPButton.init()
    })

    core.bind('checkout.common.state.ready', function () {
      PCPButton.init()
    })
  }
)
