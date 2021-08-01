<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model\DTO\Product;


class Inventory extends \XLite\Model\DTO\Product\Inventory implements \XLite\Base\IDecorator
{
    protected function init($object)
    {
        parent::init($object);

        $this->default->clear_variants_inventory = false;
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        if ($this->default->clear_variants_inventory) {
            /** @var \XLite\Module\XC\ProductVariants\Model\ProductVariant $variant */
            foreach ($object->getVariants() as $variant) {
                $variant->setDefaultAmount(true);
            }
        }
    }
}