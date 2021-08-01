<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model\ProductFeed;

/**
 * AllProductsFeed
 *
 * @Decorator\Depend({"XC\GoogleFeed", "XC\ProductVariants", "XC\SystemFields", "XC\FacebookMarketing"})
 */
class AllProductsFeedSystemFieldsVariants extends \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataGtin($entity, $fieldName)
    {
        return $entity->getDisplayUpcIsbn();
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getVariantDataMpn($entity, $fieldName)
    {
        return $entity->getDisplayMnfVendor() ?: parent::getVariantDataMpn($entity, $fieldName);
    }
}