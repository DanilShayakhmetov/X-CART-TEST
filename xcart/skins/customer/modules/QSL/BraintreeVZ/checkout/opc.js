/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Braintree widget for OPC
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Initialize Braintree at OPC
 */
function initOpcBraintree() {

    core.bind(
        'checkout.paymentTpl.postprocess',
        braintreePayment.init.bind(braintreePayment)
    );

    core.bind(
        'braintreetotalupdate',
        braintreePayment.updateCartTotal.bind(braintreePayment)
    );

    braintreePayment.init();
}

if (typeof(window['slidebar']) == 'function' && jQuery.mmenu) {
    core.bind('mm-menu.created', initOpcBraintree);
    core.bind('mm-menu.before_create', initOpcBraintree);
} else {
    document.addEventListener(
        'DOMContentLoaded',
        function(event) {
            initOpcBraintree();
        }
    );
}
