<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

use XLite\Module\CDev\GoogleAnalytics;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderDataMapper;
use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper;

class CheckoutComplete implements IAction
{
    /**
     * @return bool
     */
    public function isApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && \XLite::getController() instanceof \XLite\Controller\Customer\CheckoutSuccess
            && $this->getOrder();
    }

    /**
     * @return array
     */
    public function getActionData()
    {
        $result = [
            'ga-type'   => $this->getActionName(),
            'ga-action' => 'pageview',
            'data'      => $this->getCheckoutCompleteActionData($this->getOrder())
        ];

        return $result;
    }

    /**
     * @return string
     */
    protected function getActionName()
    {
        return 'checkout_complete';
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return array
     */
    protected function getCheckoutCompleteActionData(\XLite\Model\Order $order)
    {
        $productsData = [];

        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

        foreach ($order->getItems() as $item) {
            if (!$item->getObject()) {
                continue;
            }

            $productsData[] = OrderItemDataMapper::getData(
                $item,
                $item->getObject()->getCategory() ? $item->getObject()->getCategory()->getName() : ''
            );
        }

        return [
            'products'      => $productsData,
            'actionData'    => [],
        ];
    }

    /**
     * @return \XLite\Model\Order
     */
    protected function getOrder()
    {
        return method_exists(\XLite::getController(), 'getOrder')
            ? \XLite::getController()->getOrder()
            : null;
    }
}