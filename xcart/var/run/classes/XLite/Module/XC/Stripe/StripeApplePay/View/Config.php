<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\StripeApplePay\View;

/**
 * Config
 */
class Config extends \XLite\View\AView
{

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Stripe/config.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Stripe/config.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = [
            'file'      => 'js/clipboard.min.js',
            'no_minify' => true,
        ];

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Stripe/StripeApplePay/config.twig';
    }

    /**
     * Check - StripeApplePay integration connected or not
     *
     * @return boolean
     */
    protected function isConnected()
    {
        /** @var \XLite\Model\Payment\Method $method */
        $method = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')
            ->find(\XLite\Core\Request::getInstance()->method_id);

        return (bool) $method->getSetting('refreshToken');
    }

    protected function getParentUrl()
    {
        $parent_mid = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Stripe']);

        if ($parent_mid) {
            return \XLite\Core\Converter::buildURL('payment_method', '', ['method_id' => $parent_mid->getMethodId()]);
        }
        return '';
    }
}
