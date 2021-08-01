<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Order\Status;

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
        if (\XLite\Core\Config::getInstance()->CDev->Egoods->esd_fullfilment && $this->getCode()) {
            return in_array($this->getCode(),[
                static::STATUS_NEW,
                static::STATUS_DELIVERED,
                static::STATUS_WILL_NOT_DELIVER,
            ]);
        }

        return parent::isAllowedToSetManually();
    }
}