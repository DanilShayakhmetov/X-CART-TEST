<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

/**
 * Shipping status
 *
 * @Entity
 * @Table  (name="order_shipping_statuses",
 *      indexes={
 *          @Index (name="code", columns={"code"})
 *      }
 * )
 */
class Shipping extends \XLite\Model\Order\Status\AStatus
{
    /**
     * Statuses
     */
    const STATUS_NEW                 = 'N';
    const STATUS_PROCESSING          = 'P';
    const STATUS_SHIPPED             = 'S';
    const STATUS_DELIVERED           = 'D';
    const STATUS_WILL_NOT_DELIVER    = 'WND';
    const STATUS_RETURNED            = 'R';
    const STATUS_WAITING_FOR_APPROVE = 'WFA';
    const STATUS_NEW_BACKORDERED     = 'NBA';

    /**
     * Disallowed to set manually statuses
     *
     * @return array
     */
    public static function getDisallowedToSetManuallyStatuses()
    {
        return [
            static::STATUS_WAITING_FOR_APPROVE,
            static::STATUS_NEW_BACKORDERED,
        ];
    }

    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    public function isAllowedToSetManually()
    {
        return !in_array(
            $this->getCode(),
            static::getDisallowedToSetManuallyStatuses()
        );
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Shipping
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Shipping
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
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
        return [
            static::STATUS_NEW => [
                static::STATUS_SHIPPED => ['ship'],
                static::STATUS_WAITING_FOR_APPROVE => ['waitingForApprove']
            ],

            static::STATUS_PROCESSING => [
                static::STATUS_SHIPPED => ['ship'],
                static::STATUS_WAITING_FOR_APPROVE => ['waitingForApprove']
            ],

            static::STATUS_DELIVERED => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_WILL_NOT_DELIVER => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_RETURNED => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_WAITING_FOR_APPROVE => [
                static::STATUS_SHIPPED => ['ship'],
            ],

            static::STATUS_NEW_BACKORDERED => [
                static::STATUS_NEW              => ['releaseBackorder'],
                static::STATUS_PROCESSING       => ['releaseBackorder'],
                static::STATUS_SHIPPED          => ['ship', 'releaseBackorder'],
                static::STATUS_DELIVERED        => ['releaseBackorder'],
                static::STATUS_WILL_NOT_DELIVER => ['releaseBackorder'],
                static::STATUS_RETURNED         => ['releaseBackorder'],
            ],
        ];
    }
}
