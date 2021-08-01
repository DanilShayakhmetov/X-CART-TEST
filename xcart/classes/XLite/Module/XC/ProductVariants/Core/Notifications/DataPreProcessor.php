<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core\Notifications;


use XLite\Module\XC\ProductVariants\Model\ProductVariant;

class DataPreProcessor
{
    /**
     * @param       $dir
     * @param array $data
     *
     * @return array
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        if ($dir === 'modules/XC/ProductVariants/low_variant_limit_warning') {
            $data = static::prepareLowVariantLimitWarningData($data);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function prepareLowVariantLimitWarningData(array $data)
    {
        if (
            !empty($data['product_variant'])
            && $data['product_variant'] instanceof ProductVariant
        ) {
            $data = ['data' => $data['product_variant']->prepareDataForNotification()] + $data;
        }

        return $data;
    }
}