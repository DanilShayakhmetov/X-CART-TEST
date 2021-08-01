<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications;


class ErrorTranslator
{
    /**
     * @param string $providerName
     * @param string $errorCode
     * @param mixed  $value
     *
     * @return null|string
     */
    public static function translateError($providerName, $errorCode, $value)
    {
        $errors = static::getErrors();

        if (isset($errors[$providerName][$errorCode])) {
            return \XLite\Core\Translation::lbl($errors[$providerName][$errorCode], [
                'value' => $value,
            ]);
        }

        return null;
    }

    /**
     * @return array
     */
    protected static function getErrors()
    {
        return [
            'order'    => [
                'order_nf' => 'Order #{{value}} not found',
            ],
            'profile'  => [
                'profile_nf' => 'Profile {{value}} not found',
            ],
            'product'  => [
                'product_nf' => 'Product #{{value}} not found',
            ],
            'products' => [
                'product_nf' => 'Product #{{value}} not found',
            ],
        ];
    }

    /**
     * @param $providerName
     *
     * @return null|string
     */
    public static function translateAvailabilityError($providerName)
    {
        $errors = static::getAvailabilityErrors();

        if (isset($errors[$providerName])) {
            return \XLite\Core\Translation::lbl($errors[$providerName]);
        }

        return null;
    }

    /**
     * @return array
     */
    protected static function getAvailabilityErrors()
    {
        return [
            'order' => 'No orders available. Please create at least one order.',
            'product' => 'No products available. Please create at least one product.',
            'products' => 'No products available. Please create at least one product.',
            'profile' => 'No profiles available.',
        ];
    }
    /**
     * @param string $providerName
     * @param string $errorCode
     * @param mixed  $value
     *
     * @return null|string
     */
    public static function translateSuitabilityError($providerName, $errorCode, $value)
    {
        $errors = static::getSuitabilityErrors();

        if (isset($errors[$providerName][$errorCode])) {
            return \XLite\Core\Translation::lbl($errors[$providerName][$errorCode], [
                'value' => $value,
            ]);
        }

        return null;
    }

    /**
     * @return array
     */
    protected static function getSuitabilityErrors()
    {
        return [
            'order'    => [
                'no_tracking' => 'Order #{{value}} does not have tracking numbers',
            ],
        ];
    }
}