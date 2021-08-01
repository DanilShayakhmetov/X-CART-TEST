<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Onboarding;

class Payment extends \XLite\View\AView
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getMethod();
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Amazon/PayWithAmazon/onboarding/payment.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Amazon/PayWithAmazon/onboarding/body.twig';
    }

    /**
     * @return bool
     */
    protected function isAmazonConfigured()
    {
        return $this->getMethod() && $this->getMethod()->getProcessor()->isConfigured($this->getMethod());
    }

    /**
     * @return bool
     */
    protected function isAmazonMethodEnabled()
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
            'service_name' => 'PayWithAmazon',
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