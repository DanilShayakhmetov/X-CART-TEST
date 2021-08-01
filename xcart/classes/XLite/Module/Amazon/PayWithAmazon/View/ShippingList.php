<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

/**
 * Shipping methods list
 */
class ShippingList extends \XLite\View\ShippingList implements \XLite\Base\IDecorator
{
    /**
     * Get shipping rates
     *
     * @return array
     */
    protected function getRates()
    {
        $rates = parent::getRates();

        if ('amazon_checkout' === \XLite::getController()->getTarget()
            && \XLite\Core\Request::getInstance()->orderReference
        ) {
            $_rates = [];
            foreach ($rates as $rate) {
                if ($this->isRateSelected($rate)) {
                    $_rates[] = $rate;
                    break;
                }
            }

            if (!empty($_rates)) {
                $rates = $_rates;
            }
        }

        return $rates;
    }
}
