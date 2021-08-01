/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Braintree widget for PayPal button
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind('minicart.postprocess', function () {

    // Workaround for proper displaying of PayPal button. Step 1 - reset values. See further actions in Step 2
    var isPayPalTemp = braintreePayment.isPayPal;
    braintreePayment.isPayPal = false;
    var is3dSecureTemp = braintreePayment.is3dSecure;
    braintreePayment.is3dSecure = false;

    /**
     * Trigger error
     */
    braintreePayment.checkout.triggerError = function (message) {
        core.trigger('message', { 'message': message, 'type': MESSAGE_ERROR });
    };

    /**
     * Get URL params for AJAX request
     */
    braintreePayment.checkout.getUrlParams = function (params) {
        params[xliteConfig.form_id_name] = xliteConfig.form_id;

        return params;
    };

    /**
     * Check if Braintree is the current payment method
     */
    braintreePayment.checkout.isCurrent = function (includeSavedCards) {
        return true;
    };

    /**
     * Constructor/initializator
     */
    braintreePayment.checkout.init = function (callback) {
        var params = {
            target:   'braintree',
            action:   'get_braintree_data',
            is_button: true
        };

        var url = URLHandler.buildURL(this.getUrlParams(params));

        core.get(url, function (response) {
            callback.bind(braintreePayment, response.responseJSON)();
        });
    };

    /**
     * Process shadows
     */
    braintreePayment.checkout.processShadows = function () {
        var elm = $('#cart-right');

        if (!elm.length) {
            return;
        }

        if (braintreePayment.isInProgress || braintreePayment.isLoading) {
            assignWaitOverlay(elm);
        } else {
            unassignWaitOverlay(elm);
            $('.wait-block-overlay', '#cart-right').remove(); // Otherwise doesn't work
        }
    };

    /**
     * Get data for the PayPal payment
     */
    braintreePayment.checkout.getPayPalData = function (callback) {
        var params = this.getUrlParams({
            target: 'braintree',
            action: 'get_paypal_data'
        });        

        var url = URLHandler.buildURL(this.getUrlParams(params));

        core.get(url, function (response) {
            callback.bind(braintreePayment, response.responseJSON)();
        });
    };

    braintreePayment.init();

    // Workaround for proper displaying of PayPal button. Step2 - return initial values
    braintreePayment.isPayPal = isPayPalTemp;
    braintreePayment.is3dSecure = is3dSecureTemp;
});
