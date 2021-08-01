<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\Validator;

/**
 * Variant Qty
 */
class VariantQty extends \XLite\Core\Validator\ProductQty
{
    /**
     * Validate
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function validate($data)
    {
        if (!\XLite\Core\Converter::isEmptyString($data) && $this->qty !== $this->qty_origin) {
            $current = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\ProductVariant')->find($this->productId);
            if ($current && $this->qty_origin != $current->getAmount()) {
                $this->throwQtyError();
            }
        }
    }
}
