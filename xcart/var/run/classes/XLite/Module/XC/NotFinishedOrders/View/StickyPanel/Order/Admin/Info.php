<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\View\StickyPanel\Order\Admin;

/**
 * Order info sticky panel
 */
 class Info extends \XLite\View\StickyPanel\Order\Admin\InfoAbstract implements \XLite\Base\IDecorator
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        if ($this->getOrder()->isNotFinishedOrder()) {
            $list['sendNotification'] = $this->getDoNotSendNotificationWidget();
        }

        return $list;
    }
}