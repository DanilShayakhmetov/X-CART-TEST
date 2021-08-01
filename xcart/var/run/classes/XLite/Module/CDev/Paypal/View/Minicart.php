<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

use \XLite\Module\CDev\Paypal;

/**
 * Minicart widget
 */
 class Minicart extends \XLite\Module\XC\CrispWhiteSkin\View\Minicart implements \XLite\Base\IDecorator
{
    /**
     * Get number of cart items to display by default
     *
     * @return int
     */
    protected function getCountCartItemsToDisplay()
    {
        if (Paypal\Main::isExpressCheckoutEnabled()
            || Paypal\Main::isPaypalCommercePlatformEnabled()
            || Paypal\Main::isPaypalForMarketplacesEnabled()
            || Paypal\Main::isPaypalAdvancedEnabled()
        ) {
            return 2;
        } else {
            return parent::getCountCartItemsToDisplay();
        }
    }

}