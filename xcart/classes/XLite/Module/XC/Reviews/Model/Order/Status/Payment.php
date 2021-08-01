<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Order\Status;

/**
 * Order payment status
 */
abstract class Payment extends \XLite\Model\Order\Status\Payment implements \XLite\Base\IDecorator
{
    /**
     * Return status handlers list
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        $handlers = parent::getStatusHandlers();

        $notPaidStatuses = [
            static::STATUS_QUEUED,
            static::STATUS_REFUNDED,
            static::STATUS_PART_PAID,
            static::STATUS_DECLINED,
            static::STATUS_CANCELED,
            static::STATUS_AUTHORIZED,
        ];

        foreach ($notPaidStatuses as $status) {
            array_unshift(
                $handlers[$status][static::STATUS_PAID],
                'reviewKey'
            );
        }

        return $handlers;
    }
}