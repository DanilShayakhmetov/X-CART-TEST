<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Checkout;

use XLite\Module\XC\FastLaneCheckout\Main;

/**
 * Cart items
 */
 class CartItems extends \XLite\View\Checkout\CartItemsAbstract implements \XLite\Base\IDecorator
{
    protected function getItemsCountLinkAttributes()
    {
        $attrs = parent::getItemsCountLinkAttributes();

        if (Main::isFastlaneEnabled()) {
            $attrs['@click.prevent'] = 'toggleItems';
        }

        return $attrs;
    }
}
