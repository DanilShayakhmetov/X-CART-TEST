<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\Form;

class CreateShipment extends \XLite\View\Form\AForm
{
    /**
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'order';
    }

    /**
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'usps_create_shipment';
    }

    /**
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['order_id'] = $this->getOrder()->getOrderId();

        return $list;
    }
}
