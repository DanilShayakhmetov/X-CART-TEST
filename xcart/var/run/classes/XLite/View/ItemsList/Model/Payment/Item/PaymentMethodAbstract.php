<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Payment\Item;


use XLite\Model\Payment\Method;

abstract class PaymentMethodAbstract extends \XLite\View\AView
{
    const PARAM_METHOD = 'method';

    protected function isVisible()
    {
        return parent::isVisible() && $this->getPayment();
    }

    protected function getDefaultTemplate()
    {
        return 'items_list/payment/online_methods/entry.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_METHOD  => new \XLite\Model\WidgetParam\TypeObject('Payment method'),
        );
    }

    /**
     * @return Method|null
     */
    protected function getPayment()
    {
        return $this->getParam(static::PARAM_METHOD);
    }

    /**
     * @return string
     */
    public function getAdminIconURL()
    {
        $method = $this->getPayment();
        $url = $method->getAdminIconURL();

        if (!$url && $method->isModuleInstalled() && !$method->isModuleEnabled()) {
            $name = explode('_', $method->getModuleName(), 2);

            $url = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                sprintf('modules/%s/%s/method_icon.png', $name[0], $name[1])
            );
        }

        return $url;
    }
}