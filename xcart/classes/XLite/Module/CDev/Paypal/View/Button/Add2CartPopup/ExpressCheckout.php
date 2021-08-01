<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Add2CartPopup;

/**
 * Express Checkout button
 */
class ExpressCheckout extends \XLite\Module\CDev\Paypal\View\Button\AExpressCheckout
{
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pp-button';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'add2cart_popup';
    }
}
