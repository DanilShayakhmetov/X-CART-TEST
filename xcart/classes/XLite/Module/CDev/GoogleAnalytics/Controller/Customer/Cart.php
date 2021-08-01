<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Controller\Customer;


/**
 * Class Cart
 */
class Cart extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * Returns event data
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    protected function assembleProductAddedToCartEvent($item)
    {
        $eventData = parent::assembleProductAddedToCartEvent($item);

        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

        $product = $item->getObject();
        $categoryName   = $this->getGACategoryPath($product->getCategory());
        $coupon         = '';
        $position       = '';

        $eventData['gaProductData'] = \XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\ProductDataMapper::getAddProductData(
            $product,
            $categoryName,
            $coupon,
            $position
        );

        if ($this->getGAProductList()) {
            $eventData['gaProductData']['list'] = $this->getGAProductList();
        }

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return $eventData;
    }

    /**
     * @return string
     */
    protected function getGAProductList()
    {
        return \XLite\Core\Request::getInstance()->ga_list;
    }
}