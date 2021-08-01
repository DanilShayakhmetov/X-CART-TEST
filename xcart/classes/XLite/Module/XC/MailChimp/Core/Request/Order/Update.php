<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Request\Order;

use XLite\Model\Order;
use XLite\Module\XC\MailChimp\Core\Request\MailChimpRequest;
use XLite\Module\XC\MailChimp\Logic\DataMapper;

class Update extends MailChimpRequest
{
    /**
     * @param string $storeId
     * @param Order  $order
     */
    public function __construct($storeId, $order)
    {
        $orderData = DataMapper\Order::getUpdateDataByOrder($order);
        $orderId   = $orderData['id'];

        parent::__construct('Updating order', 'patch', "ecommerce/stores/{$storeId}/orders/{$orderId}", $orderData);
    }

    /**
     * @param string $storeId
     * @param Order  $order
     *
     * @return self
     */
    public static function getRequest($storeId, $order): self
    {
        return new self($storeId, $order);
    }

    /**
     * @param string $storeId
     * @param Order  $order
     *
     * @return mixed
     */
    public static function scheduleAction($storeId, $order)
    {
        return self::getRequest($storeId, $order)->schedule();
    }

    /**
     * @param string $storeId
     * @param Order  $order
     *
     * @return mixed
     */
    public static function executeAction($storeId, $order)
    {
        return self::getRequest($storeId, $order)->execute();
    }
}
