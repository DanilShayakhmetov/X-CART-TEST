<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Model;

/**
 * Class represents an order
 */
 class Order extends \XLite\Module\XC\CustomerAttachments\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Return base part of the certain "change status" handler name
     *
     * @param mixed  $oldStatus  Old order status
     * @param mixed  $newStatus  New order status
     * @param string $type Type
     *
     * @return string|array
     */
    protected function getStatusHandlers($oldStatus, $newStatus, $type)
    {
        $result = parent::getStatusHandlers($oldStatus, $newStatus, $type);

        $oldCode = $oldStatus->getCode();
        $newCode = $newStatus->getCode();

        if (!$oldCode || !$newCode) {
            $class = '\XLite\Model\Order\Status\\' . ucfirst($type);

            $oldCode = $oldCode ?: $class::STATUS_CUSTOM;
            $newCode = $newCode ?: $class::STATUS_CUSTOM;

            $statusHandlers = $class::getStatusHandlers();

            if (isset($statusHandlers[$oldCode]) && isset($statusHandlers[$oldCode][$newCode])) {
                $result = array_merge(
                    $result,
                    is_array($statusHandlers[$oldCode][$newCode])
                        ? array_unique($statusHandlers[$oldCode][$newCode])
                        : [$statusHandlers[$oldCode][$newCode]]
                );
            }
        }

        return $result;
    }
}
