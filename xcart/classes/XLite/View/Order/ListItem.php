<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order;

/**
 * Orders search result item widget
 */
class ListItem extends \XLite\View\AView
{
    /**
     * Widget parameter. Order.
     */
    const PARAM_ORDER = 'order';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->getParam(self::PARAM_ORDER);
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getDefaultOrder()
    {
        $ctrl = \XLite::getController();
        return $ctrl instanceof \XLite\Controller\Customer\Base\Order ? $ctrl->getOrder() : null;
    }

    /**
     * Check if the product of the order item is deleted one in the store
     *
     * @param \XLite\Model\OrderItem $item Order item
     * @param boolean                $data Flag
     *
     * @return boolean
     */
    public function checkIsAvailableToOrder(\XLite\Model\OrderItem $item, $data)
    {
        return $data !== $item->isValidToClone();
    }

    /**
     * Format price
     *
     * @param float                 $value        Price
     * @param \XLite\Model\Currency $currency     Currency OPTIONAL
     * @param boolean               $strictFormat Flag if the price format is strict (trailing zeroes and so on options) OPTIONAL
     *
     * @return string
     */
    protected function formatOrderPrice($value, \XLite\Model\Currency $currency = null, $strictFormat = false)
    {
        return static::formatPrice($value, $currency, $strictFormat);
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'items_list/order/order.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/order/order.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject('Order', $this->getDefaultOrder(), false, 'XLite\Model\Order'),
        ];
    }

    protected function getCommentedData()
    {
        return [
            'widgetTarget' => 'order',
            'widgetClass' => get_class($this),
            'widgetParams' => [
                'order_number' => $this->getOrder()->getOrderNumber()
            ]
        ];
    }

    /**
     * Check if the re-order button is shown
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return boolean
     */
    protected function showReorder(\XLite\Model\Order $order)
    {
        $items = $order->getItems();

        return (bool) \Includes\Utils\ArrayManager::findValue(
            $items,
            [$this, 'checkIsAvailableToOrder'],
            false
        );
    }
}
