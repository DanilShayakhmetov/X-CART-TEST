/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Braintree widget for FLC 
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function initFlcBraintree() {

    core.bind(
        [
            'checkout.paymentTpl.postprocess',
            'checkout.common.state.ready'
        ],
        braintreePayment.init.bind(braintreePayment)
    );

    core.bind(
        'fastlane_section_switched',
        function (event, data) {

            if (
                'undefined' != typeof data.newSection
                && 'payment' == data.newSection.name
            ) {
                braintreePayment.init();
            }

            core.bind(
                [
                    'checkout.paymentTpl.postprocess',
                    'checkout.common.state.ready'
                ],
                braintreePayment.init.bind(braintreePayment)
            );
        }
    );

    core.bind(
        [
            'checkout.sections.shipping.persist',
            'updatecart',
        ],
        function (event, data) {
            if (braintreePayment.isInitialized) {
                braintreePayment.teardown();
            }
        }
    );

    core.bind(
        'braintreetotalupdate',
        braintreePayment.updateCartTotal.bind(braintreePayment)
    );
}

if (typeof(window['slidebar']) == 'function' && jQuery.mmenu) {
    core.bind('mm-menu.created', initFlcBraintree);
    core.bind('mm-menu.before_create', initFlcBraintree);
} else {
    document.addEventListener(
        'DOMContentLoaded',
        function(event) {
            initFlcBraintree();
        }
    );
}
