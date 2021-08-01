<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Controller\Admin;

/**
 * @Decorator\Depend ("CDev\FeaturedProducts")
 */
abstract class FeaturedProducts extends \XLite\Module\CDev\FeaturedProducts\Controller\Admin\FeaturedProductsAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    public function getConciergeTitle()
    {
        return (\XLite\Core\Request::getInstance()->id ? 'Category' : 'Front page') . ': Featured products';
    }
}
