<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Model\Payment;

use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Module\XC\Stripe\Main;

/**
 * Payment method model
 */
class Method extends \XLite\Model\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        $message   = parent::getNotSwitchableReason();
        $processor = $this->getProcessor();
        $method    = Main::getMethod();

        if ($processor
            && 'Stripe' === $this->getServiceName()
            && $processor->isSettingsConfigured($method)
            && !Config::getInstance()->Security->customer_security
            ) {
                $message = static::t(
                    'The "Stripe" feature requires https to be properly set up for your store.',
                    [
                        'url' => Converter::buildURL('https_settings'),
                    ]
                );
        }

        return $message;
    }
}