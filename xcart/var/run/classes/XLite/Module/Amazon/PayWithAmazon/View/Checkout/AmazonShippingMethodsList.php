<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Checkout;

/**
 * Shipping methods list
 */
class AmazonShippingMethodsList extends \XLite\View\Checkout\ShippingMethodsList
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Amazon/PayWithAmazon/checkout/steps/shipping/parts/shippingMethods.js';

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Amazon/PayWithAmazon/checkout/steps/shipping/parts/shippingMethodsList.twig';
    }
}
