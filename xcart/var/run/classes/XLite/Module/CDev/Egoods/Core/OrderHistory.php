<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Core;

/**
 * Order history main point of execution
 */
 class OrderHistory extends \XLite\Module\XC\NotFinishedOrders\Core\OrderHistory implements \XLite\Base\IDecorator
{
    const TXT_DELIVERED_BY_DOWNLOAD  = 'Order fulfilment status changed from {{oldStatus}} to {{newStatus}} by file download';

    /**
     * Register status order changes
     *
     * @param integer $orderId Order id
     * @param array   $change  Old,new structure
     *
     * @return void
     */
    public function registerOrderDeliveredByDownload($orderId, $change)
    {
        $this->registerEvent(
            $orderId,
            static::CODE_CHANGE_SHIPPING_STATUS_ORDER,
            static::TXT_DELIVERED_BY_DOWNLOAD,
            [
                'orderId'   => $orderId,
                'newStatus' => $change['new'],
                'oldStatus' => $change['old'],
            ]
        );
    }
}