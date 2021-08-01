<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order;


use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Converter;
use XLite\Model\Order;
use XLite\Model\WidgetParam\TypeObject;

class MailHistory extends \XLite\View\AView
{
    use ExecuteCachedTrait;

    const PARAM_ORDER = 'order';

    protected function getDefaultTemplate()
    {
        return 'order/mail_history/body.twig';
    }

    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => [
                'order/mail_history/style.less',
            ],
        ]);
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER => new TypeObject('Order', null),
        ];
    }

    /**
     * @return Order
     */
    protected function getOrder()
    {
        return $this->getParam(static::PARAM_ORDER);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrder()
            && 0 < count($this->getOrderHistoryEventsBlock());
    }

    /**
     * @return array
     */
    protected function getOrderHistoryEventsBlock()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineOrderHistoryEventsBlock();
        }, $this->getOrder()->getOrderId());
    }

    /**
     * @return array
     */
    protected function defineOrderHistoryEventsBlock()
    {
        $result = [];

        $list = \XLite\Core\Database::getRepo('XLite\Model\OrderHistoryEvents')->findAllByOrder($this->getOrder());
        foreach ($list as $event) {
            $result[$this->getDayDate($event->getDate())][] = $event;
        }

        return $result;
    }

    /**
     * Get day of the given date
     *
     * @param integer $date Date (UNIX timestamp)
     *
     * @return string
     */
    protected function getDayDate($date)
    {
        return \XLite\Core\Converter::formatDate($date);
    }


    /**
     * Return true if event has comment or details
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event object
     *
     * @return boolean
     */
    protected function isDisplayDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        return $event->getComment() || $this->getDetails($event);
    }

    /**
     * Date getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return string
     */
    protected function getDate(\XLite\Model\OrderHistoryEvents $event)
    {
        return \XLite\Core\Converter::formatDayTime($event->getDate());
    }

    /**
     * Description getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return string
     */
    protected function getDescription(\XLite\Model\OrderHistoryEvents $event)
    {
        return $event->getDescription();
    }

    /**
     * Comment getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return string
     */
    protected function getComment(\XLite\Model\OrderHistoryEvents $event)
    {
        $result = $event->getComment();

        $codes = [
            \XLite\Core\OrderHistory::CODE_ORDER_EDITED,
            \XLite\Core\OrderHistory::CODE_CHANGE_NOTES_ORDER,
            \XLite\Core\OrderHistory::CODE_CHANGE_CUSTOMER_NOTES_ORDER,
        ];

        if (in_array($event->getCode(), $codes)) {
            $changes = unserialize($result);
            if (is_array($changes)) {
                $widget = new \XLite\View\OrderEditHistoryData(
                    [
                        \XLite\View\OrderEditHistoryData::PARAM_CHANGES => $changes,
                    ]
                );
                $widget->init();
                $result = $widget->getContent();
            }
        }

        return $result;
    }

    /**
     * Details getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event Event
     *
     * @return array
     */
    protected function getDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        $list = [];

        $columnId = 0;

        foreach ($event->getDetails() ?: [] as $cell) {
            if ($cell->getName()) {
                $list[$columnId][] = $cell;
                $columnId++;
            }

            if ($this->getColumnsNumber() <= $columnId) {
                $columnId = 0;
            }
        }

        return $list;
    }

    /**
     * Get number of columns to display event details
     *
     * @return integer
     */
    protected function getColumnsNumber()
    {
        return 3;
    }

    /**
     * Get profile email
     *
     * @return string
     */
    protected function getProfileEmail()
    {
        return $this->getOrder()->getProfile()->getLogin();
    }

    /**
     * Get order formatted creation date
     *
     * @return string
     */
    protected function getOrderDate()
    {
        return $this->formatTime($this->getOrder()->getDate());
    }

    /**
     * Check - has profile separate modification page or not
     *
     * @return boolean
     */
    protected function hasProfilePage()
    {
        $order = $this->getOrder();

        return $order->getOrigProfile()
            && $order->getOrigProfile()->getProfileId() !== $order->getProfile()->getProfileId();
    }

    /**
     * Get profile name
     *
     * @return string
     */
    protected function getProfileName()
    {
        return $this->getOrder()->getProfile()->getName(false);
    }

    /**
     * Get profile URL
     *
     * @return string
     */
    protected function getProfileURL()
    {
        return \XLite\Core\Converter::buildFullURL(
            'profile',
            '',
            ['profile_id' => $this->getOrder()->getOrigProfile()->getProfileId()],
            \XLite::getAdminScript()
        );
    }

    /**
     * @return string
     */
    protected function getOrderURL()
    {
        return Converter::buildFullURL(
            'order',
            '',
            ['order_number' => $this->getOrder()->getOrderNumber()],
            \XLite::getAdminScript()
        );
    }
}
