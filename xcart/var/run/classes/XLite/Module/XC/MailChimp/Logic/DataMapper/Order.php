<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;

class Order
{
    /**
     * Get order data
     *
     * @param string             $mc_cid
     * @param string             $mc_tc
     * @param \XLite\Model\Order $object
     *
     * @return array
     */
    public static function getDataByOrder($mc_cid, $mc_tc, \XLite\Model\Order $object)
    {
        \XLite\Core\Translation::setTmpTranslationCode(
            \XLite\Core\Config::getInstance()->General->default_language
        );

        $return = [
            'id'             => (string) $object->getOrderNumber(),
            'currency_code'  => $object->getCurrency()->getCode(),
            'order_total'    => $object->getTotal(),
            'tax_total'      => static::getTaxValue($object),
            'shipping_total' => $object->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING),
            'lines'          => static::getLines($object),

            'landing_site'         => static::getLandingSite(),
            'financial_status'     => static::mapFinancialStatus($object),
            'fulfillment_status'   => static::mapFulfillmentStatus($object),
            'order_date'           => $object->getDate(),
            'processed_at_foreign' => date('c', $object->getDate()),
            'updated_at_foreign'   => date('c', $object->getLastRenewDate()),
        ];

        if ($mc_tc) {
            $return['tracking_code'] = (string) $mc_tc;
        }

        if ($mc_cid) {
            $return['campaign_id'] = $mc_cid;
        }

        if ($object->getProfile()) {
            if ($object->getProfile()->getShippingAddress()) {
                $return['shipping_address'] = Address::getData(
                    $object->getProfile()->getShippingAddress()
                );
            }

            if ($object->getProfile()->getBillingAddress()) {
                $return['billing_address'] = Address::getData(
                    $object->getProfile()->getBillingAddress()
                );
            }
        }

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return $return;
    }

    public static function getUpdateDataByOrder(\XLite\Model\Order $order)
    {
        \XLite\Core\Translation::setTmpTranslationCode(
            \XLite\Core\Config::getInstance()->General->default_language
        );

        $return = [
            'id' => (string) $order->getOrderNumber(),

            'currency_code'  => $order->getCurrency()->getCode(),
            'order_total'    => $order->getTotal(),
            'tax_total'      => static::getTaxValue($order),
            'shipping_total' => $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING),
            'lines'          => static::getLines($order),

            'landing_site'         => static::getLandingSite(),
            'financial_status'     => static::mapFinancialStatus($order),
            'fulfillment_status'   => static::mapFulfillmentStatus($order),
            'order_date'           => $order->getDate(),
            'processed_at_foreign' => date('c', $order->getDate()),
            'updated_at_foreign'   => date('c', $order->getLastRenewDate()),
        ];

        if ($order->getProfile()) {
            if ($order->getProfile()->getShippingAddress()) {
                $return['shipping_address'] = Address::getData(
                    $order->getProfile()->getShippingAddress()
                );
            }

            if ($order->getProfile()->getBillingAddress()) {
                $return['billing_address'] = Address::getData(
                    $order->getProfile()->getBillingAddress()
                );
            }
        }

        \XLite\Core\Translation::setTmpTranslationCode(null);

        return $return;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return float
     */
    protected static function getTaxValue(\XLite\Model\Order $order): float
    {
        $total = 0;
        /** @var \XLite\Model\Order\Surcharge $surcharge */
        foreach ($order->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX) as $surcharge) {
            $total += $surcharge->getValue();
        }

        return (float) $total;
    }

    /**
     * Get lines data
     *
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    protected static function getLines(\XLite\Model\Order $order): array
    {
        $lines = [];
        /** @var \XLite\Model\OrderItem $item */
        foreach ($order->getItems() as $item) {
            $lines[] = Line::getDataByOrderItem($item);
        }

        return $lines;
    }

    /**
     * @return string
     */
    protected static function getLandingSite(): string
    {
        /** @var \XLite\Core\Request|\XLite\Module\XC\MailChimp\Core\Request $request */
        $request = \XLite\Core\Request::getInstance();

        return $request->getLandingSiteForMailchimp();
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return string
     * @see http://developer.mailchimp.com/documentation/mailchimp/guides/getting-started-with-ecommerce/#order-status-notifications
     */
    protected static function mapFinancialStatus(\XLite\Model\Order $order): string
    {
        $map = [
            \XLite\Model\Order\Status\Payment::STATUS_PAID       => 'paid',
            \XLite\Model\Order\Status\Payment::STATUS_PART_PAID  => 'paid',
            \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED => 'pending',
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED     => 'pending',
            \XLite\Model\Order\Status\Payment::STATUS_REFUNDED   => 'refunded',
            \XLite\Model\Order\Status\Payment::STATUS_CANCELED   => 'cancelled',
            \XLite\Model\Order\Status\Payment::STATUS_DECLINED   => 'cancelled',
        ];

        $result = 'unknown';

        $code = $order->getPaymentStatusCode();
        if ($code && isset($map[$code])) {
            $result = $map[$code];
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return string
     * @see http://developer.mailchimp.com/documentation/mailchimp/guides/getting-started-with-ecommerce/#order-status-notifications
     */
    protected static function mapFulfillmentStatus(\XLite\Model\Order $order): string
    {
        $map = [
            \XLite\Model\Order\Status\Shipping::STATUS_SHIPPED => 'shipped',
        ];

        $result = 'unknown';

        $code = $order->getShippingStatusCode();
        if ($code && isset($map[$code])) {
            $result = $map[$code];
        }

        return $result;
    }
}
