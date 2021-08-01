<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Model\Order\Status;

/**
 * Shipping status
 *
 */
 class Shipping extends \XLite\Module\XC\NotFinishedOrders\Model\Order\Status\Shipping implements \XLite\Base\IDecorator
{
     const STATUS_CUSTOM = 'CUSTOM';

    /**
     * Set name
     *
     * @param string $name Name
     *
     * @return \XLite\Model\Order\Status\Shipping
     */
    public function setName($name)
    {
        $this->setCustomerName($name);

        return parent::setName($name);
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
             self::STATUS_CUSTOM => [
                 self::STATUS_SHIPPED => ['ship'],
             ],
         ]);
     }
}