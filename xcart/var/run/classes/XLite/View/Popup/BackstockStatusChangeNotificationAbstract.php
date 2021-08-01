<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Popup;


abstract class BackstockStatusChangeNotificationAbstract extends \XLite\View\AView
{
    const PARAM_ORDER = 'order';

    protected function getDefaultTemplate()
    {
        return 'popup/backstock_status_change_notification/body.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'popup/backstock_status_change_notification/style.css',
        ]);
    }

    /**
     * Define widget params
     *
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject(
                'Order', null, false, '\XLite\Model\Order'
            ),
        );
    }
}