<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Order\Status;

/**
 * Shipping status
 */
abstract class Shipping extends \XLite\Model\Order\Status\Shipping implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        $statuses = [
            static::STATUS_NEW,
            static::STATUS_PROCESSING,
            static::STATUS_SHIPPED,
            static::STATUS_RETURNED,
            static::STATUS_WAITING_FOR_APPROVE,
            static::STATUS_WILL_NOT_DELIVER,
            static::STATUS_NEW_BACKORDERED,
        ];

        foreach ($statuses as $status) {
            array_unshift(
                $handlers[$status][static::STATUS_DELIVERED],
                'reviewKey'
            );
        }

        return $handlers;
    }
}
