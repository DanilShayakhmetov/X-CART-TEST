<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

/**
 * Product
 */
class Product extends \XLite\Controller\Customer\Product implements \XLite\Base\IDecorator
{
    /**
     * Check if additional mobile breadcrumbs are shown
     *
     * @return boolean
     */
    public function isShowAdditionalMobileBreadcrumbs()
    {
        return true;
    }
}