<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ThemeTweaker\Core\Notifications;


/**
 * ErrorTranslator
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class ErrorTranslator extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\ErrorTranslator implements \XLite\Base\IDecorator
{
    protected static function getErrors()
    {
        return parent::getErrors() + [
                'product_variant' => [
                    'variant_nf' => 'Product variant #{{value}} not found',
                ],
            ];
    }

    protected static function getAvailabilityErrors()
    {
        return parent::getAvailabilityErrors() + [
                'product_variant' => 'No product variants available. Please create at least one.',
            ];
    }
}