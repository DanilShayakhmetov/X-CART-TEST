<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Module\XPay\XPaymentsCloud\View\Onboarding;


class Payment extends \XLite\View\AView
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getMethod();
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Onboarding/online_widgets/xpayments.twig';
    }

    /**
     * @return bool
     */
    protected function isConfigured()
    {
        return $this->getMethod() && $this->getMethod()->getProcessor()->isConfigured($this->getMethod());
    }

    /**
     * @return bool
     */
    protected function isMethodEnabled()
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
            'service_name' => 'XPaymentsCloud'
        ]);
    }

    /**
     * @return string
     */
    protected function getMethodSettingsUrl()
    {
        return $this->getMethod()->getProcessor()->getConfigurationURL($this->getMethod());
    }
}