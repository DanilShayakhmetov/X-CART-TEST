<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Core;

use XLite\Module\XC\ProductVariants\Core\Mail\LowVariantLimitWarningAdmin;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * @param array $data Data
     */
    public static function sendLowVariantLimitWarningAdmin(array $data)
    {
        (new LowVariantLimitWarningAdmin($data))->schedule();
    }
}
