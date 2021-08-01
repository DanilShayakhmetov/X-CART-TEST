<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\MultiVendor\View\Menu\Customer;

/**
 * Orders list menu item
 *
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrdersList extends \XLite\Module\XC\CrispWhiteSkin\View\Menu\Customer\OrdersList implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getDefaultCaption()
    {
        return \XLite\Core\Auth::getInstance()->isVendor()
            ? static::t('My purchases')
            : parent::getDefaultCaption();
    }
}
