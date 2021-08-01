<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Model\Order\Status;

/**
 * Class represents an order
 */
class Shipping extends \XLite\Model\Order\Status\Shipping implements \XLite\Base\IDecorator
{
    const STATUS_NOT_FINISHED = 'NF';

    public static function getDisallowedToSetManuallyStatuses()
    {
        return array_merge(parent::getDisallowedToSetManuallyStatuses(), [
            static::STATUS_NOT_FINISHED,
        ]);
    }

    /**
     * List of change status handlers;
     * top index - old status, second index - new one
     * (<old_status> ----> <new_status>: $statusHandlers[$old][$new])
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        return array_merge_recursive(parent::getStatusHandlers(), [
            self::STATUS_NOT_FINISHED => [
                self::STATUS_NEW              => ['NFOCreated'],
                self::STATUS_PROCESSING       => ['NFOCreated'],
                self::STATUS_SHIPPED          => ['NFOCreated', 'ship'],
                self::STATUS_DELIVERED        => ['NFOCreated'],
                self::STATUS_WILL_NOT_DELIVER => ['NFOCreated'],
                self::STATUS_RETURNED         => ['NFOCreated'],
            ],
        ]);
    }
}
