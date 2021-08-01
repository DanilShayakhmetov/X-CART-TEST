<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Input;

use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;

/**
 * Sale
 */
class Sale extends \XLite\View\FormField\Inline\Input\PriceOrPercent
{
    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $params = parent::getFieldParams($field);

        $params['placeholder'] = $this->getPlaceholder();

        return $params;
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\XC\ProductVariants\View\FormField\Input\Sale';
    }

    /**
     * @inheritdoc
     */
    protected function getPlaceholder()
    {
        return static::t('Default');
    }

    /**
     * Save widget value in entity
     *
     * @param array $field Field data
     *
     * @return void
     */
    public function saveValueSale($field)
    {
        $value = $field['widget']->getValue();

        if ($value
            && isset($value[PriceOrPercent::PRICE_VALUE])
            && isset($value[PriceOrPercent::TYPE_VALUE])
        ) {
            $saleValue = $value[PriceOrPercent::PRICE_VALUE];
            $saleType = $value[PriceOrPercent::TYPE_VALUE];
            $saleType = in_array($saleType, [AbsoluteOrPercent::TYPE_ABSOLUTE, AbsoluteOrPercent::TYPE_PERCENT])
                ? $saleType
                : AbsoluteOrPercent::TYPE_ABSOLUTE;

            $isDefault = false;

            if ('' === $saleValue) {
                $saleValue = 0;
                $isDefault = true;
            }

            $value = [
                PriceOrPercent::PRICE_VALUE => $saleValue,
                PriceOrPercent::TYPE_VALUE => $saleType,
            ];

            $this->getEntity()->setDefaultSale($isDefault);
            $this->getEntity()->setSale($value);
        }
    }
}
