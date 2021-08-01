/* X-Payments Apple Pay */
if (!window.ApplePaySession || !ApplePaySession.canMakePayments()) {
    core.bind([
        'checkout.paymentMethods.initialize',
        'checkout.main.ready',
        'checkout.common.anyChange',
        'checkout.sections.payment.persist',
        'checkout.paymentTpl.loaded',
        'checkout.common.state.ready'
    ], function () {
        var anchor = jQuery('.xpayments-apple-pay-method');
        if (anchor.length) {
            var methodId = anchor.data('id');
            var needSwitch = jQuery('#pmethod' + parseInt(methodId)).is(':checked');
            anchor.closest('.payment-method').remove();
        }
    })
    core.bind([
        'checkout.common.state.ready',
        'resources.ready'
    ], function () {
        if (!jQuery('.payment-method input:checked').length) {
           jQuery('.payment-method input').first().click();
        }
    })
}