<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar\DataSource;


use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\View\AView;

class Order extends AView implements DataSource
{
    private $data;

    static public function isApplicable(Data $data)
    {
        return in_array(
            $data->getDirectory(),
            static::getTemplateDirectories(),
            true
        );
    }

    public function __construct(Data $data)
    {
        $this->data = $data;
        parent::__construct([]);
    }

    static public function buildNew(Data $data)
    {
        return new static($data);
    }

    protected static function getTemplateDirectories()
    {
        return [
            'order_canceled',
            'order_changed',
            'order_created',
            'order_failed',
            'order_processed',
            'order_shipped',
            'order_tracking_information',
            'failed_transaction',
            'order_waiting_for_approve',
            'backorder_created',
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/data_source/order/body.twig';
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return isset($this->data->getData()['order'])
            ? $this->data->getData()['order']
            : null;
    }

    /**
     * @return string
     */
    protected function getValue()
    {
        return $this->getOrder()
            ? $this->getOrder()->getOrderNumber()
            : '';
    }
}