<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\DTO\Product;


class Info extends \XLite\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    protected static function isSKUValid($dto)
    {
        if (parent::isSKUValid($dto)) {
            $sku = $dto->default->sku;
            return !\XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->findOneBySku($sku);
        }

        return false;
    }

    protected function init($object)
    {
        parent::init($object);

        $this->prices_and_inventory->inventory_tracking->clear_variants_inventory = false;
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        if ($this->prices_and_inventory->inventory_tracking->clear_variants_inventory) {
            /** @var \XLite\Module\XC\ProductVariants\Model\ProductVariant $variant */
            foreach ($object->getVariants() as $variant) {
                $variant->setDefaultAmount(true);
            }
        }
    }
}