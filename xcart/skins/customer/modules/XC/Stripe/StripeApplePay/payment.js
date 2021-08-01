/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Stripe initialize
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function confirmPaymentAP(stripe, paymentMethodId, paymentIntentId) {
  jQuery.post(
    'cart.php',
    {
      target: 'stripe',
      action: 'confirm',
      payment_method_id: paymentMethodId,
      payment_intent_id: paymentIntentId,
    },
    function(data) {
      handleServerResponseAP(stripe, data);
    }
  );
}

function handleServerResponseAP(stripe, response) {

  if (response.requires_action) {
    stripe.handleCardAction(
      response.payment_intent_client_secret
    ).then(function(result) {
      if (result.error) {
        submitStripeFormAP(response.stripe_id, result.error.message);
      } else if(result.paymentIntent.id) {
        confirmPaymentAP(stripe, null, result.paymentIntent.id);
      }
    });
  } else {
    submitStripeFormAP(response.stripe_id, response.error);
  }
}

function submitStripeFormAP(stripe_id, error) {
  jQuery('.stripe_apple-box .token').val(stripe_id);
  jQuery('.stripe_apple-box .id').val(stripe_id);
  jQuery('.stripe_apple-box .error').val(error);
  jQuery('body').css('overflow', 'visible');
  jQuery('form.place').submit();
  unblockCheckoutAP();
}

function blockCheckoutAP() {
  jQuery('.place-order')
    .addClass('disabled')
    .prop('disabled', true);
  assignShadeOverlay(jQuery('#content'));
}

function unblockCheckoutAP() {
  jQuery('.place-order')
    .removeClass('disabled')
    .prop('disabled', false);
  unassignShadeOverlay(jQuery('#content'), false);
}

core.bind(
  'checkout.main.initialize',
  _.once(function() {
    var paymentRequest_obj = null;
    var updateCartBinded = false;
    var g_stripe_apple_obj = null;
    var button_hidden_by_me = false;
    var is_mounted = false;

    core.bind(
      'checkout.paymentTpl.postprocess',
      function(event, data) {
        var box = jQuery('.stripe_apple-box');
        if (box.length && typeof(window.Stripe) != 'undefined' && !paymentRequest_obj) {
          g_stripe_apple_obj = Stripe( box.data('key') );

          // Configure Stripe PaymentRequest_obj
          var options = {
            country: box.data('country'),
            currency: box.data('currency'),
            total: {
              label: 'Total',
              amount: box.data('total'),
            },
            requestPayerName: true,
            requestPayerEmail: true,
          };
          paymentRequest_obj = g_stripe_apple_obj.paymentRequest(options);
        } else if (box.length && typeof(window.Stripe) != 'undefined' && paymentRequest_obj) {
          // Update Stripe PaymentRequest_obj
          var options = {
            currency: box.data('currency'),
            total: {
              label: 'Total',
              amount: box.data('total'),
            },
          };
          var res = paymentRequest_obj.update(options);
        }

        if (!updateCartBinded && !_.isUndefined(data) && !_.isUndefined(data.widget)) {
            // Update payment template by change of cart Total
            PaymentTplView.prototype.handleUpdateCartStripe = function (event, data)
            {
              if (!this.isLoading && 'undefined' != typeof(data.total)) {
                this.load();
              }
            }

            core.bind(
              'updateCart',
              _.bind(data.widget.handleUpdateCartStripe, data.widget)
            );

            updateCartBinded = true;
        };
      }
    );

    core.bind([
      'checkout.common.state.ready',
      'fastlane_section_switched'
      ],
      function(event, state) {
        var box = jQuery('.stripe_apple-box');

        var isNewCheckout = typeof Checkout !== 'undefined'
            && typeof Checkout.instance !== 'undefined';

        if (paymentRequest_obj
            && box.length
            && !box.find('.token').val()
            && (!isNewCheckout || Checkout.instance.getState().sections.current.name === 'payment')
            && !is_mounted
        ) {
          var email = jQuery('#email').val();

          var elements = g_stripe_apple_obj.elements();
          var prButton = elements.create('paymentRequestButton', {
            paymentRequest: paymentRequest_obj,
          });

          // Check the availability of the Payment Request API first.
          paymentRequest_obj.canMakePayment().then(function(result) {
            if (result) {
              core.trigger('stripe_apple.checkout.mounted');
              prButton.mount('#payment-request-button');
              is_mounted = true;
            } else {
              /* Show 'Payment method is not available' button*/

              var std_btn_htmlcode = $('.review-step form.place .button-row');

              if (std_btn_htmlcode && std_btn_htmlcode.length) {
                std_btn_htmlcode = std_btn_htmlcode.html();
                var old_label_reg = new RegExp( core.t('Place order X', {'total': '[^<]*'}).replace(/[-\/\\$+?.()|{}]/g, '\\$&') );
              } else {
                /* flc */
                std_btn_htmlcode = $('.checkout_fastlane_section-buttons form.place .checkout_fastlane_section-place_order').get(0).outerHTML.replace(/display: none;/,'display: block;');
                var old_label_reg = new RegExp( core.t('Place order').replace(/[-\/\\$+?.()|{}]/g, '\\$&') );
              }

              $('#payment-request-button').html( std_btn_htmlcode.replace(old_label_reg, core.t('Payment method is not available')) );
              $('#payment-request-button').find('button').addClass('disabled').prop('disabled','disabled').attr('title','');
              $('#payment-request-button').find('button').removeClass('place-order order[disabled]:hover disabled:hover checkout_fastlane_section-place_order');
            }
          });


          paymentRequest_obj.on('token', function(ev) {
              jQuery('.stripe_apple-box .token').val(ev.token.id);
              blockCheckoutAP();

              // Send The Token To Your Server To Charge It!
              jQuery.post(
                'cart.php',
                {
                  target: 'stripe',
                  action: 'confirm',
                  payment_method_id: ev.token.id,
                  payment_intent_id: null,
                }
              ).then(function(response) {
                if (response.success) {
                  // Report to the browser that the payment was successful, prompting
                  // it to close the browser payment interface.
                  ev.complete('success');
                  handleServerResponseAP(g_stripe_apple_obj, response);
                } else {
                  // Report to the browser that the payment failed, prompting it to
                  // re-show the payment interface, or show an error message and close
                  // the payment interface.
                  console.log('Stripe JS (' + response.error_code + ') ' + response.error + (response.requires_action ? 'requires_action:true' : ''));

                  if (response.requires_action) {
                    // 3D Secure
                    ev.complete('success');
                    handleServerResponseAP(g_stripe_apple_obj, response);
                  } else {
                    ev.complete('fail');
                    unblockCheckoutAP();
                    if (response.human_error && response.human_error.indexOf('StripeJSerror') == -1) {
                      core.trigger('message', {type: 'warning', message: response.human_error});
                    } else {
                      core.trigger('message', {type: 'warning', message: core.t('An error occurred, please try again. If the problem persists, contact the administrator.')});
                    }

                  }
                }
              });
          });


          state.state = false;
        }
      }
    );

    core.bind(
      'stripe_apple.checkout.closed',
      function() {
        core.trigger('checkout.common.unblock');
      }
    );
  }
));
