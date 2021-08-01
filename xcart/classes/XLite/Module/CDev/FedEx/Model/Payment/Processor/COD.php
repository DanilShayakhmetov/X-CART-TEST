<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\Model\Payment\Processor;

/**
 * 'Cash on Delivery' payment method class
 */
class COD extends \XLite\Model\Payment\Processor\COD
{
    /**
     * Shipping method carrier code which is allowed to make COD payment method available at checkout
     *
     * @var string
     */
    protected $carrierCode = 'fedex';

    protected static $CODNotAvailableMethods = [
        'EUROPE_FIRST_INTERNATIONAL_PRIORITY',
        'INTERNATIONAL_DISTRIBUTION_FREIGHT',
        'INTERNATIONAL_ECONOMY',
        'INTERNATIONAL_ECONOMY_DISTRIBUTION',
        'INTERNATIONAL_ECONOMY_FREIGHT',
        'INTERNATIONAL_FIRST',
        'INTERNATIONAL_PRIORITY',
        'INTERNATIONAL_PRIORITY_DISTRIBUTION',
        'INTERNATIONAL_PRIORITY_FREIGHT',
    ];

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Get selected shipping rate object
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return \XLite\Model\Shipping\Rate
     */
    protected function getShippingRate($order)
    {
        $result = null;

        $modifier = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        if ($modifier && $modifier->getMethod()
            && $this->getCarrierCode() == $modifier->getMethod()->getCarrier()
            && $this->isCODAvailable($order, $modifier->getMethod())
        ) {

            $rate = $modifier->getSelectedRate();

            $result = $rate;
        }

        return $result;
    }

    /**
     * Check if COD available
     *
     * @param $order
     * @param $method
     * @return bool
     */
    protected function isCODAvailable($order, $method)
    {
        $result = false;

        if (!in_array($method->getCode(), static::$CODNotAvailableMethods)) {
            $shippingCountryCode = $order->getProfile() && $order->getProfile()->getShippingAddress()
                ? $order->getProfile()->getShippingAddress()->getCountryCode()
                : null;
            $sourceCountryCode = $order->getSourceAddress()->getCountryCode();

            if ('PR' != $sourceCountryCode && !in_array($shippingCountryCode, ['CA', 'MX', 'PR'])) {
                $result = true;
            }
        }

        return $result;
    }
}
