<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Controller\Admin;

/**
 * Stripe OAuth endpoint
 */
class StripeOauth extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && (!empty(\XLite\Core\Request::getInstance()->code) || $this->getAction())
            && $this->getPaymentMethod()
            && ('refresh' != $this->getAction() || $this->getPaymentMethod()->getSetting('refreshToken'))
            && ($this->getAction() || $this->checkStripeCode());
    }

    /**
     * Check Stripe return code
     *
     * @return boolean
     */
    protected function checkStripeCode()
    {
        $oauth = \XLite\Module\XC\Stripe\Core\OAuth::getInstance();

        return \XLite\Core\Request::getInstance()->state == $oauth->defineURLState();
    }

    /**
     * Disconnect
     *
     * @return void
     */
    protected function doActionDisconnect()
    {
        $this->getPaymentMethod()->setSetting('accessTokenTest', null);
        $this->getPaymentMethod()->setSetting('publishKeyTest', null);
        $this->getPaymentMethod()->setSetting('accessToken', null);
        $this->getPaymentMethod()->setSetting('publishKey', null);
        $this->getPaymentMethod()->setSetting('refreshToken', null);
        $this->getPaymentMethod()->setSetting('userId', null);
        $this->getPaymentMethod()->setSetting('mode', 'live');

        \XLite\Core\Database::getEM()->flush();

        $this->setReturnURL(
            \XLite\Core\Converter::buildURL(
                'payment_method',
                null,
                array('method_id' => $this->getPaymentMethod()->getMethodId())
            )
        );
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $method = $this->getPaymentMethod();

        list($result, $error) = \XLite\Module\XC\Stripe\Core\OAuth::getInstance()->authorize(
            $method,
            \XLite\Core\Request::getInstance()->code
        );

        if (!empty($error)) {
            \XLite\Core\TopMessage::addError($error);
        }

        \XLite\Core\Database::getEM()->flush();

        $this->setReturnURL(
            \XLite\Core\Converter::buildURL('payment_method', null, array('method_id' => $method->getMethodId()))
        );
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getPaymentMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'Stripe'));
    }
}