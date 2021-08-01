<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model\ProductFeed;

use XLite\View\AView;

/**
 * AllProductsFeed
 *
 * @Decorator\Depend("CDev\Sale")
 */
class AllProductsFeedSale extends \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataSalePrice($entity, $fieldName)
    {
        return ($entity->getDisplayPrice() < $entity->getDisplayPriceBeforeSale())
            ? $this->formatPrice($entity->getDisplayPrice()) . ' ' . \XLite::getInstance()->getCurrency()->getCode()
            : '';
    }

    /**
     * @param \XLite\Model\AEntity $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataPrice($entity, $fieldName)
    {
        return ($entity->getDisplayPrice() > $entity->getDisplayPriceBeforeSale())
            ? $this->formatPrice($entity->getDisplayPrice()) . ' ' . \XLite::getInstance()->getCurrency()->getCode()
            : $this->formatPrice($entity->getDisplayPriceBeforeSale()) . ' ' . \XLite::getInstance()->getCurrency()->getCode();
    }
}