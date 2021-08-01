<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\ItemsList\Payment\Method\Admin;

/**
 * Abstract admin-based payment methods list
 */
abstract class AAdmin extends \XLite\View\ItemsList\Payment\Method\Admin\AAdmin implements  \XLite\Base\IDecorator
{
    /**
     * Check - method can remove or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    protected function canRemoveMethod(\XLite\Model\Payment\Method $method)
    {
        return
            parent::canRemoveMethod($method)
            && \XLite\Module\XPay\XPaymentsCloud\Main::APPLE_PAY_SERVICE_NAME !== $method->getServiceName();
    }

    /**
     * Check - display right action box or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    protected function hasRightActions(\XLite\Model\Payment\Method $method)
    {
        return parent::hasRightActions($method)
            && \XLite\Module\XPay\XPaymentsCloud\Main::APPLE_PAY_SERVICE_NAME !== $method->getServiceName();
    }

    /**
     * Defines JS files for widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/items_list/payment/methods/controller.js';
        return $list;
    }
}
