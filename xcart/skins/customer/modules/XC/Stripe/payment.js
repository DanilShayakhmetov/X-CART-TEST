/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Stripe initialize
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function confirmPayment(stripe, paymentMethodId, paymentIntentId) {
  jQuery.post(
    'cart.php',
    {
      target: 'stripe',
      action: 'confirm',
      payment_method_id: paymentMethodId,
      payment_intent_id: paymentIntentId,
    },
    function(data) {
      handleServerResponse(stripe, data);
    }
  );
}

function handleServerResponse(stripe, response) {

  if (response.requires_action) {
    stripe.handleCardAction(
      response.payment_intent_client_secret
    ).then(function(result) {
      if (result.error) {
        submitStripeForm(response.stripe_id, result.error.message);
      } else {
        confirmPayment(stripe, null, result.paymentIntent.id);
      }
    });
  } else {
    submitStripeForm(response.stripe_id, response.error);
  }
}

function submitStripeForm(stripe_id, error) {
  jQuery('.stripe-box #id').val(stripe_id);
  jQuery('.stripe-box #error').val(error);
  jQuery('body').css('overflow', 'visible');
  jQuery('form.place').submit();
  unblockCheckout();
}

function blockCheckout() {
  jQuery('.place-order')
    .addClass('disabled')
    .prop('disabled', true);
  assignShadeOverlay(jQuery('#content'));
}

function unblockCheckout() {
  jQuery('.place-order')
    .removeClass('disabled')
    .prop('disabled', false);
  assignShadeOverlay(jQuery('#content'));
}

core.bind(
  'checkout.main.initialize',
  function() {
    var handler = null;
    var updateCartBinded = false;

    core.bind(
      'checkout.paymentTpl.postprocess',
      function(event, data) {
        var box = jQuery('.stripe-box');
        if (box.length && typeof(window.StripeCheckout) != 'undefined' && !handler) {
          var stripe = Stripe(box.data('key'));

          // Configure Stripe handler
          var options = {
            key:   box.data('key'),
            locale: 'auto',
            token: function(token, args) {
              blockCheckout();
              confirmPayment(stripe, token.id, null);
            },
            opened: function() {
              core.trigger('stripe.checkout.opened');
            },
            closed: function() {
              core.trigger('stripe.checkout.closed');
            },
          };
          if (box.data('image')) {
            options.image = box.data('image');
          }
          handler = StripeCheckout.configure(options);
        }

        if (!updateCartBinded && !_.isUndefined(data) && !_.isUndefined(data.widget)) {
            // Update payment template by change of cart total
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
        }
      }
    );

    core.bind(
      'checkout.common.ready',
      function(event, state) {
        var box = jQuery('.stripe-box');

        var isNewCheckout = typeof Checkout !== 'undefined'
            && typeof Checkout.instance !== 'undefined';

        if (handler
            && box.length
            && !box.find('.token').val()
            && (!isNewCheckout || Checkout.instance.getState().sections.current.name === 'payment')
        ) {
          var email = jQuery('#email').val();
          handler.open({
            name:        box.data('name'),
            description: box.data('description'),
            amount:      box.data('total'),
            currency:    box.data('currency'),
            email:       email ? email : box.data('email')
          });

          state.state = false;
        }
      }
    );

    core.bind(
      'stripe.checkout.closed',
      function() {
        core.trigger('checkout.common.unblock');
      }
    );
  }
);
