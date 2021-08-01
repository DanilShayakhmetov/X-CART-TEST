<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Module\XC\ThemeTweaker\Core\Notifications;

use XLite\Core\Mailer;

/**
 * DataPreProcessor
 *
 * @Decorator\Depend("XC\ThemeTweaker")
 */
class DataPreProcessor extends \XLite\Module\XC\ThemeTweaker\Core\Notifications\DataPreProcessor implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        $result = parent::prepareDataForNotification($dir, $data);

        if (false !== strpos($dir, Mailer::XPAYMENTS_SUBSCRIPTION_PATH_PREFIX)) {
            $subscription = $data['subscription'];
            $order = $subscription->getInitialOrder();
            $result = [
                'order' => $order,
                'subscription' => $subscription,
            ];
        }

        return $result;
    }

}
