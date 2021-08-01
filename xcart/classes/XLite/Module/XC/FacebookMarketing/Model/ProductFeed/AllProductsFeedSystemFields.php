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
 * @Decorator\Depend("XC\SystemFields")
 */
class AllProductsFeedSystemFields extends \XLite\Module\XC\FacebookMarketing\Model\ProductFeed\AllProductsFeed implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return array_merge(parent::getHeaders(), [
            [static::FIELD_PARAM_NAME => 'gtin'],
        ]);
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataGtin($entity, $fieldName)
    {
        return $entity->getUpcIsbn();
    }

    /**
     * @param \XLite\Model\Product $entity
     * @param string $fieldName
     *
     * @return string
     */
    protected function getEntityDataMpn($entity, $fieldName)
    {
        return $entity->getMnfVendor() ?: parent::getEntityDataMpn($entity, $fieldName);
    }
}