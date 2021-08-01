/* vim: set ts=4 sw=4 sts=4 et: */

/**
 * Braintree settings 
 *
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function checkBraintreeMerhantAccountId()
{
    return ($('#settings_merchantAccountId').val() != $('#braintree-merchant-id').text())
        || confirm(
            'Merchant account ID matches the Merchant ID. '
            + 'This will cause an error when processing the card. '
            + 'Make sure configuration is correct.'
            + 'Proceed?'
        );
}
