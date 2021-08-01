<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\OrdersImport\Logic\Import\Processor;


/**
 * Orders
 *
 * @Decorator\Depend("XC\OrdersImport")
 */
class Orders extends \XLite\Module\XC\OrdersImport\Logic\Import\Processor\Orders implements \XLite\Base\IDecorator
{
    protected function detectProductBySku($sku)
    {
        if (parent::detectProductBySku($sku)) {
            return parent::detectProductBySku($sku);
        } else {
            return $this->detectProductVariantBySku($sku)
                ? $this->detectProductVariantBySku($sku)->getProduct()
                : null;
        }
    }

    /**
     * @param $sku
     *
     * @return null|\XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    protected function detectProductVariantBySku($sku)
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->findOneBy([
            'sku' => $sku,
        ]);
    }

    protected function getItemByData($data)
    {
        $item = parent::getItemByData($data);

        if (
            $item->getObject()
            && !parent::detectProductBySku($data['itemSKU'])
            && ($variant = $this->detectProductVariantBySku($data['itemSKU']))
        ) {
            $item->setVariant($variant);
        }

        return $item;
    }
}