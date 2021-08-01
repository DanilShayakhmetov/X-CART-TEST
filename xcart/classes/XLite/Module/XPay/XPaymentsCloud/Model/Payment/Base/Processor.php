<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment\Base;

use XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Payment processor
 */
abstract class Processor extends \XLite\Model\Payment\Base\Processor implements \XLite\Base\IDecorator
{
    /**
     * Tokenization enabled flag
     *
     * @var bool
     */
    protected static $isTokenizationEnabled = null;

    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param \XLite\Model\Order $order Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        $isApplicable = parent::isApplicable($order, $method);

        if (
            $isApplicable
            && $order->hasXpaymentsSubscriptions()
        ) {

            if ($method->isXpayments()) {

                if (null === static::$isTokenizationEnabled) {
                    try {
                        $response = XPaymentsCloud::getClient()->doGetTokenizationSettings();
                        static::$isTokenizationEnabled = (bool)$response->tokenizationEnabled;
                    } catch (\Exception $exception) {
                        XPaymentsCloud::log($exception->getMessage());
                    }
                }

                $isApplicable = static::$isTokenizationEnabled;

            } else {

                $isApplicable = false;
            }
        }

        return $isApplicable;
    }
}
