/* vim: set ts=2 sw=2 sts=2 et: */

/**
 * Common items list controller
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// Payment methods switch loaded event
core.bind(
  'payment.methods.switch.loaded',
  function (event, data)
  {
    if (data.data.responseJSON && data.data.responseJSON.href) {
      if ('XPay_XPaymentsCloud' === data.switcher.closest('.cell').data('module-name')) {
        core.trigger('xpayments.payment.methods.list.reload');
      }
    }
  }
);

// Payment methods remove loaded event
core.bind(
    'payment.methods.remove.loaded.started',
    function (event, data)
    {
        if ('XPay_XPaymentsCloud' === data.line.data('module-name')) {
            data.line.donotRemove = true;
            core.trigger('xpayments.payment.methods.list.reload');
        }
    }
);

core.bind(
  'xpayments.payment.methods.list.reload',
  function (event, data) {
    core.get(
      URLHandler.buildURL({target: 'payment_settings', action: '', widget: '\\XLite\\View\\Payment\\Configuration'}),
      function(xhr, status, data) {
        var paymentConf = jQuery(data).find('.payment-conf');
        if (paymentConf.length > 0) {
          jQuery('.payment-conf').html(paymentConf.html());
          core.microhandlers.runAll();
          core.autoload(PopupButtonAddPaymentMethod);
        }
      }
    );
  }
);

(function (addListeners) {

  ItemsListPayment.prototype.addListeners = function() 
  {
    addListeners.call(this);

    // Ugly hack for neat appearance
    jQuery(this.base).find('input[data-tremble-button]').parents('li').find('.line-row').hide()

    jQuery('.switcher', this.base).click(
      function () {
        var p = jQuery(this).parent().parent();
        if (p.hasClass('blocked-enable')) {
          var trembleId = jQuery(this).find('input[data-tremble-button]').data('tremble-button');
          if (trembleId) {
            var button = jQuery(trembleId).parents('li').find('button.configure')
            console.log(button);
            if (button) {
              button.addClass('hover');
              setTimeout(function(){
                button.removeClass('hover');
              }, 1000);
            }
          }
        }
      }
    );
  }

}(ItemsListPayment.prototype.addListeners));
