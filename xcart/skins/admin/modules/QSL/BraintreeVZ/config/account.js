/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Braintree widget
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

core.bind(
    'load',
    function() {

        if ('' != connectUrl) {

            var partner = new BraintreeOAuthConnect({
                connectUrl: connectUrl,
                container: 'bt-oauth-connect-container',
                onError: function (errorObject) {
                    core.trigger('message', { 'message': errorObject.message, 'type': MESSAGE_ERROR });
                }
            });
        }
    }
);
