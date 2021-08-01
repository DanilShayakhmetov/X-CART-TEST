<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Logic\DataMapper;

class Cart
{
    /**
     * @param string|null       $mc_cid
     * @param string|null       $mc_tc
     * @param \XLite\Model\Cart $object
     *
     * @return array
     */
    public static function getDataByCart($mc_cid, $mc_tc, \XLite\Model\Cart $object): array
    {
        \XLite\Core\Translation::setTmpTranslationCode(
            \XLite\Core\Config::getInstance()->General->default_language
        );

        $return = [
            'id'             => (string) $object->getOrderId(),
            'currency_code'  => $object->getCurrency()->getCode(),
            'order_total'    => $object->getTotal(),
            'tax_total'      => static::getTaxValue($object),
            'shipping_total' => $object->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING),
            'lines'          => static::getLines($object),

            'checkout_url' => \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL('cart'),
                \XLite\Core\Config::getInstance()->Security->customer_security
            ),
        ];

        if ($mc_cid) {
            $return['campaign_id'] = (string) $mc_cid;
        }

        if ($mc_tc) {
            $return['tracking_code'] = (string) $mc_tc;
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

    /**
     * @param \XLite\Model\Cart $object
     *
     * @return float
     */
    protected static function getTaxValue(\XLite\Model\Cart $object): float
    {
        $total = 0;
        /** @var \XLite\Model\Order\Surcharge $surcharge */
        foreach ($object->getSurchargesByType(\XLite\Model\Base\Surcharge::TYPE_TAX) as $surcharge) {
            $total += $surcharge->getValue();
        }

        return (float) $total;
    }

    /**
     * @param \XLite\Model\Cart $object
     *
     * @return array
     */
    protected static function getLines(\XLite\Model\Cart $object): array
    {
        $lines = [];
        /** @var \XLite\Model\OrderItem $item */
        foreach ($object->getItems() as $item) {
            $lines[] = Line::getDataByOrderItem($item);
        }

        return $lines;
    }
}
