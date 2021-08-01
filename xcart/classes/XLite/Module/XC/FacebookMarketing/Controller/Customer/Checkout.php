<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Customer;


/**
 * Class Checkout
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    public function getPixelInitCheckoutData()
    {
        $valuePercentage = (float) \XLite\Core\Config::getInstance()->XC->FacebookMarketing->init_checkout_value;

        $currency = \XLite::getInstance()->getCurrency();
        $pixelData = [
            'currency' => $currency->getCode(),
            'value' => $currency->roundValue($this->getCart()->getSubtotal() * ($valuePercentage / 100)),
        ];

        return $pixelData;
    }
}