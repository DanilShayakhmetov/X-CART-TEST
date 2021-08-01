<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper;

/**
 * Class OrderDataMapper
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderDataMapperSeparateShops extends \XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper implements \XLite\Base\IDecorator
{
    /**
     * Get purchase order data
     *
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    public static function getPurchaseData(\XLite\Model\Order $order)
    {
        $data = parent::getPurchaseData($order);

        if (!\XLite\Module\XC\MultiVendor\Main::isWarehouseMode()) {
            $ids = [];
            $shippingCost = 0;
            $order = $order->isChild() ? $order->getParent() : $order;
            foreach ($order->getChildren() as $child) {
                $ids[] = $child->getOrderNumber();
                $shippingCost += $child->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING);
            }

            $data['id'] = implode('/', $ids);
            $data['shipping'] = $shippingCost;
        }

        return $data;
    }
}