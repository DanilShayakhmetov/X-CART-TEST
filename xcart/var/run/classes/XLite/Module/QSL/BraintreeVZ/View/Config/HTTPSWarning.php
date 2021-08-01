<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Config;

class HTTPSWarning extends \XLite\View\AView
{
    /**
     * Check if HTTPS options are enabled
     *
     * @return boolean
     */
    protected function isEnabledHTTPS()
    {
        return \XLite\Core\Config::getInstance()->Security->admin_security
            && \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/BraintreeVZ/config/https_warning.twig';
    }

}