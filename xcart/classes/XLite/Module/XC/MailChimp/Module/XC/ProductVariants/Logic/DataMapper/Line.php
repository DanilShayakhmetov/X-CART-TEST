<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Module\XC\ProductVariants\Logic\DataMapper;

/**
 * @Decorator\Depend ("XC\ProductVariants")
 */
class Line extends \XLite\Module\XC\MailChimp\Logic\DataMapper\Line implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\OrderItem $item
     *
     * @return array
     */
    public static function getDataByOrderItem(\XLite\Model\OrderItem $item)
    {
        /** @var \XLite\Model\OrderItem|\XLite\Module\XC\ProductVariants\Model\OrderItem $item */
        $result = parent::getDataByOrderItem($item);

        if ($item->getVariant()) {
            $result['product_variant_id'] = (string) $item->getVariant()->getId();
        }

        return $result;
    }
}