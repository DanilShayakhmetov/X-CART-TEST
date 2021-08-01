/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Paypal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

define('pcp_hosted_fields', ['paypal_sdk', 'js/jquery', 'js/underscore', 'pcp_mmenu_loaded'],
  function (paypal, $, _) {

    var PCPHostedFields = function (selector) {
      this.$element = $(selector)

      $('#pcp-hosted-fields-submit').on('click', function (event) {
        event.preventDefault()
      })

      this.renderHostedFields(this.getHostedFieldsStyles(), this.getHostedFieldsFields())
    }

    PCPHostedFields.prototype.$element = null

    PCPHostedFields.prototype.getParams = function () {
      return core.getCommentedData(this.$element)
    }

    PCPHostedFields.prototype.createOrder = function (data, actions) {
      var requestData = {}

      requestData.data = data
      requestData[xliteConfig.form_id_name] = xliteConfig.form_id

      return new Promise(function (resolve, reject) {
        return core.post({
            target: 'paypal_commerce_platform',
            action: 'create_order',
            hostedFields: true
          }, function (XMLHttpRequest, textStatus, data, valid) {
            data = jQuery.parseJSON(data)

            if (data) {
              if (data.result && data.result.id) {
                resolve(data.result.id)
              } else {
                reject({type: 'reload'})
              }
            } else {
              reject({type: 'error', message: 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.'})
            }
          },
          requestData
        )
      })
    }

    PCPHostedFields.prototype.onApprove = function (data) {
      if (!data) {

        return
      }

      var PP_3d_secure = core.getCommentedData(jQuery('body'), 'PayPal3Dsecure');

      assignWaitOverlay($('#page-wrapper'))
      if (PP_3d_secure && !this.check3DSecureRespone(data)) {
        unassignWaitOverlay($('#page-wrapper'))

        throw {type: 'warning', message: core.t('Please, use another card or payment method.')}
      } else {


        var $element = $('.pcp-button-container.pcp-checkout')
        var form = $element.closest('form').get(0)

        $('#pcp_on_approve_data', form).prop('value', JSON.stringify(data))

        form.submitBackground()
      }
    }

    PCPHostedFields.prototype.check3DSecureRespone = function (data) {
      var params = this.getParams()

      // Buyer authenticated with 3DS and you can continue with the authorization
      if ((data.liabilityShifted === true && data.authenticationStatus === 'YES' && data.authenticationReason === 'SUCCESSFUL')
        // Continue with authorization as authentication is not required
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'ATTEMPTED')
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'CARD_INELIGIBLE')
      ) {
        return true
      }

      // You can continue with the authorization and assume liability. If you prefer not to assume liability, ask the buyer for another card
      if (data.liabilityShifted === undefined
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'BYPASSED')
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'UNAVAILABLE')
      ) {
        return params['3d_secure_soft_exception']
      }

      // Do not continue with current authorization. Prompt the buyer to re-authenticate or request buyer for another form of payment
      if ((data.liabilityShifted === false && data.authenticationStatus === 'ERROR' && data.authenticationReason === 'ERROR')
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'SKIPPED_BY_BUYER')
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'SKIPPED_BY_BUYER')
        || (data.liabilityShifted === false && data.authenticationStatus === 'NO' && data.authenticationReason === 'FAILURE')
      ) {
        return false
      }

      return false
    }

    PCPHostedFields.prototype.formHandler = function (hostedFields) {
      var onApprove = _.bind(this.onApprove, this)

      var PP_3d_secure = core.getCommentedData(jQuery('body'), 'PayPal3Dsecure');

      var self = this
      $('#pcp-hosted-fields-submit').on('click', function () {
        var state = hostedFields.getState()

        var result = true
        for (var index in state.fields) if (state.fields.hasOwnProperty(index)) {
          var fieldResult = self.validateField(state.fields[index])

          if (result && !fieldResult) {
            hostedFields.focus(index)
          }

          result = fieldResult && result;
        }

        event.preventDefault()

        if (result) {
          assignWaitOverlay($('#page-wrapper'))
          var options = {}
          if (PP_3d_secure) {
            options.contingencies = ['3D_SECURE']
          }

          hostedFields.submit(options).then(onApprove).catch(function (e) {
            if (typeof e === 'object') {
              if (e.type === 'reload') {
                window.location.reload()
              } else if (e.type && e.message) {
                unassignWaitOverlay($('#page-wrapper'))

                core.trigger('message', e);
                return
              } else if (e.details) {
                unassignWaitOverlay($('#page-wrapper'))

                core.trigger('message', {type: 'error', message: core.t('Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.')});
                return
              }
            }

            unassignWaitOverlay($('#page-wrapper'))
            core.trigger('message', {type: 'warning', message: core.t('Please, use another card or payment method.')});
          })
        }
      })
    }

    PCPHostedFields.prototype.getHostedFieldsStyles = function () {
      return {}
    }

    PCPHostedFields.prototype.getHostedFieldsFields = function () {
      return {
        number: {
          selector: '#pcp-hosted-fields-card-number',
          placeholder: 'Credit Card Number'
        },
        cvv: {
          selector: '#pcp-hosted-fields-cvv',
          placeholder: 'CVV'
        },
        expirationDate: {
          selector: '#pcp-hosted-fields-expiration-date',
          placeholder: 'MM/YYYY'
        }
      }
    }

    PCPHostedFields.prototype.validateField = function (field) {
      var element = $(field.container).parents('.value').first()

      if ((field.isValid || field.isPotentiallyValid) && !field.isEmpty) {
        element.removeClass('has-error')
      } else  {
        element.addClass('has-error')

        return false;
      }

      return true;
    }

    PCPHostedFields.prototype.renderHostedFields = function (styles, fields) {
      if (paypal.HostedFields.isEligible() === true) {

        $('#cvv2-hint').popover({ placement: 'top'});
        jQuery('#cc_cvv2')
          .focus(function() { $('#cvv2-hint').popover('show') })
          .blur(function() { $('#cvv2-hint').popover('hide') });

        var self = this

        paypal.HostedFields.render({
          createOrder: _.bind(this.createOrder, this),
          onError: function (data) {
            core.trigger('message', {type: 'error', message: data.message || core.t('Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.')});
          },
          onCancel: function () {
          },
          styles: styles,
          fields: fields
        }).then(function (hostedFieldsInstance) {

          hostedFieldsInstance.setAttribute({
            field: 'number',
            attribute: 'placeholder',
            value: 'XXXX XXXX XXXX XXXX'
          });

          hostedFieldsInstance.on('focus', function (event) {
            if (event.emittedBy === 'cvv') {
              $('#cvv2-hint').popover('show')
            }
          });

          hostedFieldsInstance.on('blur', function (event) {
            if (event.emittedBy === 'cvv') {
              $('#cvv2-hint').popover('hide')
            }
          });

          hostedFieldsInstance.on('empty', function (event) {
            if (event.emittedBy === 'number') {
              $('#pcp-hosted-fields-card-number').parent('.value').addClass('has-error')
            } else if (event.emittedBy === 'expirationDate') {
              $('#pcp-hosted-fields-expiration-date').parent('.value').addClass('has-error')
            } else if (event.emittedBy === 'cvv') {
              $('#pcp-hosted-fields-cvv').parent('.value').addClass('has-error')
            }
          });

          hostedFieldsInstance.on('notEmpty', function (event) {
            if (event.emittedBy === 'number') {
              $('#pcp-hosted-fields-card-number').parent('.value').removeClass('has-error')
            } else if (event.emittedBy === 'expirationDate') {
              $('#pcp-hosted-fields-expiration-date').parent('.value').removeClass('has-error')
            } else if (event.emittedBy === 'cvv') {
              $('#pcp-hosted-fields-cvv').parent('.value').removeClass('has-error')
            }
          });

          hostedFieldsInstance.on('validityChange', function (event) {
            !self.validateField(event.fields[event.emittedBy])
          })

          hostedFieldsInstance.on('inputSubmitRequest', function () {
            $('#pcp-hosted-fields-submit').click()
          });

          var ccTypeSprites = {
            'visa'             : 'visa',
            'maestro'          : 'mc',
            'american-express' : 'amex',
            'diners-club'      : 'dicl',
            'discover'         : 'dc',
            'jcb'              : 'jcb',
            'master-card'      : 'mc',
          };

          hostedFieldsInstance.on('cardTypeChange', function (event) {
            if (event.cards.length === 1) {
              var ccClass = ccTypeSprites[event.cards[0].type] || 'unknown'

              $('#pcp-hosted-fields-card-type').removeClass().addClass('card').addClass(ccClass)
            } else {
              $('#pcp-hosted-fields-card-type').removeClass().addClass('card').addClass('unknown')
            }
          });


          $('.pcp-hosted-fields', self.$element).show()

          return hostedFieldsInstance
        }).then(_.bind(this.formHandler, this))
      }
    }

    PCPHostedFields.init = function () {
      $('.pcp-hosted-fields-container').each(function () {
        if ($(this).data('paypal-rendered')) {
          return
        }
        $(this).data('paypal-rendered', true)

        new PCPHostedFields(this)
      })
    }

    core.microhandlers.add('PaypalHostedFields', '.pcp-hosted-fields-container', function () {
      PCPHostedFields.init();
    })

    core.bind('checkout.common.state.ready', function () {
      PCPHostedFields.init();
    })
  }
)
