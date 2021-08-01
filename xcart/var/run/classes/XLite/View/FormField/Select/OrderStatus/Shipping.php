<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\OrderStatus;

use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Model\Order\Status\Shipping as ShippingStatus;
use XLite\View\Popup\BackstockStatusChangeNotification;
use XLite\View\AView;

/**
 * Shipping order status selector
 */
class Shipping extends \XLite\View\FormField\Select\OrderStatus\AOrderStatus
{
    use ExecuteCachedTrait;

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return !\XLite\Core\Request::getInstance()->isPost() && $this->getOrder() && $this->getOrder()->getShippingStatus()
            ? $this->getOrder()->getShippingStatus()->getId()
            : parent::getValue();
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'form_field/select/order_status/shipping/script.js',
        ], $this->getPopupWidget()->getJSFiles());
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), $this->getPopupWidget()->getCSSFiles());
    }

    protected function assembleClasses(array $classes)
    {
        return array_merge(parent::assembleClasses($classes), [
            'order-shipping-status',
        ]);
    }

    /**
     * @return AView
     */
    protected function getPopupWidget()
    {
        return $this->executeCachedRuntime(function () {
            return $this->getWidget(['order' => $this->getOrder()], BackstockStatusChangeNotification::class);
        });
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\Order\Status\Shipping';
    }

    /**
     * Return "all statuses" label
     *
     * @return string
     */
    protected function getAllStatusesLabel()
    {
        return 'All shipping statuses';
    }

    protected function getCommentedData()
    {
        $backorderStatus = $this->getRepo()->findOneByCode(ShippingStatus::STATUS_NEW_BACKORDERED);

        return [
            'backorder_id' => $backorderStatus ? $backorderStatus->getId() : null,
            'popup_content' => $this->getPopupWidget()->getContent()
        ];
    }
}
