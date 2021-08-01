<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

/**
 * Class PurchaseAdmin
 * @Decorator\Depend ("XC\MultiVendor")
 */
class PurchaseAdminSeparateShops extends \XLite\Module\CDev\GoogleAnalytics\Logic\Action\PurchaseAdmin implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getActionDataForBackend()
    {
        $result = parent::getActionDataForBackend();

        if (!\XLite\Module\XC\MultiVendor\Main::isWarehouseMode()) {
            $ids = [];
            $order = $this->order->isChild() ? $this->order->getParent() : $this->order;
            foreach ($order->getChildren() as $child) {
                $ids[] = $child->getOrderNumber();
            }

            $result['ti'] = implode('/', $ids);
        }

        return $result;
    }
}