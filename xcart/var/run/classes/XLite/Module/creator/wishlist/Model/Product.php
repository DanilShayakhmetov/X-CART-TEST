<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\creator\wishlist\Model;

/**
 * Product
 */
 class Product extends \XLite\Model\ProductAbstract implements \XLite\Base\IDecorator
{
    /**
     * Check if customer should choose options forced and return needable class string
     *
     * @return string
     */
    protected function getAdditionalClass()
    {
        return \XLite\Core\Config::getInstance()->General->force_choose_product_options !== ''
                && $this->hasEditableAttributes()
                ? 'need-choose-options' : '';
    }
}