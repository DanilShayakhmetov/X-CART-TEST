<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormField\Inline\Input;


use XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice;
use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;

/**
 * WholesalePriceOrPercent
 */
class WholesalePriceOrPercent extends \XLite\View\FormField\Inline\Input\PriceOrPercent
{
    /**
     * @inheritdoc
     */
    protected function getEntityValue()
    {
        $result = null;

        $entity = $this->getEntity();

        if ($entity instanceof AWholesalePrice) {
            $type = $entity->getType() === AWholesalePrice::WHOLESALE_TYPE_PERCENT
                ? AbsoluteOrPercent::TYPE_PERCENT
                : AbsoluteOrPercent::TYPE_ABSOLUTE;

            return [
                PriceOrPercent::TYPE_VALUE => $type,
                PriceOrPercent::PRICE_VALUE => $entity->getPrice(),
            ];
        }

        return parent::getEntityValue();
    }

    /**
     * @inheritdoc
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $entity = $this->getEntity();

        if (
            $entity instanceof AWholesalePrice
            && !empty($value[PriceOrPercent::TYPE_VALUE])
            && !empty($value[PriceOrPercent::PRICE_VALUE])
        ) {
            $entity->setPrice($value[PriceOrPercent::PRICE_VALUE]);

            $type = $value[PriceOrPercent::TYPE_VALUE] === AbsoluteOrPercent::TYPE_PERCENT
                ? AWholesalePrice::WHOLESALE_TYPE_PERCENT
                : AWholesalePrice::WHOLESALE_TYPE_PRICE;

            $entity->setType($type);
        } else {
            parent::saveFieldEntityValue($field, $value);
        }
    }
}