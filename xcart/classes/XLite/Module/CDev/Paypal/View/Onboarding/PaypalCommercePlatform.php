<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Onboarding;


class PaypalCommercePlatform extends \XLite\View\AView
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getMethod()
            && $this->getMethod()->getAdded();
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Paypal/onboarding/paypal_commerce_platform/payment.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/onboarding/paypal_commerce_platform/body.twig';
    }

    /**
     * @return bool
     */
    protected function isPaypalConfigured()
    {
        return $this->getMethod()
            && $this->getMethod()->getSetting('client_id')
            && $this->getMethod()->getSetting('client_secret');
    }

    /**
     * @return bool
     */
    protected function isPaypalMethodEnabled()
    {
        return $this->getMethod() && $this->getMethod()->isEnabled();
    }

    /**
     * @return int
     */
    protected function getMethodId()
    {
        return $this->getMethod()
            ? $this->getMethod()->getMethodId()
            : null;
    }

    /**
     * @return null|\XLite\Model\Payment\Method
     */
    protected function getMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy([
            'service_name' => 'PaypalCommercePlatform'
        ]);
    }

    protected function getWarningNote()
    {
        $paymentMethod = $this->getMethod();

        return $paymentMethod->getProcessor()->getWarningNote($paymentMethod);
    }
}