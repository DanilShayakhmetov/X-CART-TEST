<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Product;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use XLite\Core\Translation;
use XLite\Model\DTO\Base\CommonCell;

class Inventory extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param Info                      $dto
     * @param ExecutionContextInterface $context
     */
    public static function validate($dto, ExecutionContextInterface $context)
    {
        if (!static::isQtyValid($dto)) {
            static::addViolation($context, 'default.quantity_in_stock', Translation::lbl('Product quantity has changed'));
        }
    }

    /**
     * @param Info $dto
     *
     * @return boolean
     */
    protected static function isQtyValid($dto)
    {
        $origin  = $dto->default->quantity_origin;
        if ($origin !== $dto->default->quantity_in_stock) {
            $current = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($dto->default->identity);
            if ($current && $origin != $current->getAmount()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        $qty           = $object->getAmount();
        $default       = [
            'identity' => $object->getProductId(),

            'arrival_date'                      => $object->getArrivalDate() ?: time(),
            'inventory_tracking_status'         => $object->getInventoryEnabled(),
            'quantity_in_stock'                 => $qty,
            'quantity_origin'                   => $qty,
            'low_stock_warning_on_product_page' => $object->getLowLimitEnabledCustomer(),
            'low_stock_admin_notification'      => $object->getLowLimitEnabled(),
            'low_stock_limit'                   => $object->getLowLimitAmount(),
        ];
        $this->default = new CommonCell($default);
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $object->setArrivalDate((int) $default->arrival_date);
        $object->setInventoryEnabled($default->inventory_tracking_status);

        $qty = $default->quantity_in_stock;
        if ($default->quantity_origin !== $qty) {
            $object->setAmount((int) $qty);
        }

        $object->setLowLimitEnabledCustomer($default->low_stock_warning_on_product_page);
        $object->setLowLimitEnabled($default->low_stock_admin_notification);
        $object->setLowLimitAmount($default->low_stock_limit);
    }
}
