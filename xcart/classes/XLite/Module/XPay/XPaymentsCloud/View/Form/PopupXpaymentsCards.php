<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Form;

/**
 * X-Payments cards form
 */
class PopupXpaymentsCards extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'order';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'rebill';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = [];

        if (\XLite::isAdminZone()) {
            $params = [
                'amount'       => \XLite\Core\Request::getInstance()->amount,
                'order_number' => \XLite\Core\Request::getInstance()->order_number,
            ];
        };

        return $params;

    }

    /**
     * Check and (if needed) set the return URL parameter
     *
     * @param array &$params Form params
     *
     * @return void
     */
    protected function setReturnURLParam(array &$params)
    {
        parent::setReturnURLParam($params);
        $params[\XLite\Controller\AController::RETURN_URL] = $this->buildFullURL('order', '', ['order_number' => \XLite\Core\Request::getInstance()->order_number]);

    }

    /**
     * Return form attributes
     *
     * @return array
     */
    protected function getFormAttributes()
    {
        $list = parent::getFormAttributes();

        if (!isset($list['class'])) {
            $list['class'] = '';
        }

        $list['class'] .= ' xpayments-rebill-order';
        $list['class'] = trim($list['class']);

        return $list;
    }

}
