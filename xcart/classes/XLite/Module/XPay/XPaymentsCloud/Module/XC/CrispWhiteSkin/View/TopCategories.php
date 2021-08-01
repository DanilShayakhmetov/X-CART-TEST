<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\XC\CrispWhiteSkin\View;

/**
 * TopCategories decorator
 *
 * @Decorator\Depend({"XPay\XPaymentsCloud","XC\CrispWhiteSkin"})
 */
abstract class TopCategories extends \XLite\View\TopCategories implements \XLite\Base\IDecorator
{
    /**
     * Return list of disallowed targets
     *
     * @return string[]
     */
    public static function getDisallowedTargets()
    {
        return array_merge(parent::getDisallowedTargets(), [
            'xpayments_cards',
            'xpayments_subscriptions',
        ]);
    }
}
