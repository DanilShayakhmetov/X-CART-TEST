<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Module\CDev\Coupons\View;

/**
 * Cart coupons
 * @Decorator\Depend ("CDev\Coupons")
 */
 class CartCoupons extends \XLite\Module\XC\CrispWhiteSkin\View\CartCoupons implements \XLite\Base\IDecorator
{
    /**
     * Check if coupon panel 'Have a discount coupon?' is visible
     *
     * @return boolean
     */
    protected function isCouponPanelVisible()
    {
        $isAmazonRetry = 'amazon_checkout' == \XLite::getController()->getTarget()
            && \XLite\Core\Request::getInstance()->orderReference;

        return parent::isCouponPanelVisible() && !$isAmazonRetry;
    }
}
