<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model\Order\Status;

/**
 * Shipping status
 */
class Shipping extends \XLite\Model\Order\Status\Shipping implements \XLite\Base\IDecorator
{
    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    public function isAllowedToSetManually()
    {
        if (\XLite\Core\Config::getInstance()->CDev->PINCodes->esd_fullfilment && $this->getCode()) {
            return in_array($this->getCode(),[
                static::STATUS_NEW,
                static::STATUS_DELIVERED,
                static::STATUS_WILL_NOT_DELIVER,
            ]);
        }

        return parent::isAllowedToSetManually();
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
        $handlers = parent::getStatusHandlers();
        $handlers = array_merge_recursive($handlers, [
            self::STATUS_WILL_NOT_DELIVER    => [
                self::STATUS_NEW        => ['PINCodes'],
                self::STATUS_PROCESSING => ['PINCodes'],
                self::STATUS_SHIPPED    => ['PINCodes'],
                self::STATUS_DELIVERED  => ['PINCodes'],
            ],
            self::STATUS_WAITING_FOR_APPROVE => [
                self::STATUS_NEW        => ['PINCodes'],
                self::STATUS_PROCESSING => ['PINCodes'],
                self::STATUS_SHIPPED    => ['PINCodes'],
                self::STATUS_DELIVERED  => ['PINCodes'],
            ]
        ]);

        return $handlers;
    }
}