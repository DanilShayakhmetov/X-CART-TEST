<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Button\Minicart;

/**
 * Minicart buttons separator
 *
 * @ListChild (list="minicart.horizontal.buttons", weight="75")
 */
class ButtonsSeparator extends \XLite\Module\XPay\XPaymentsCloud\View\Button\AButtonsSeparator
{
    /**
     * Checks if Checkout with Apple Pay is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getNotEmptyCart();
    }
}
