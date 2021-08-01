<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs\View;

/**
 * Payment widget
 */
class Payment extends \XLite\View\AView
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AuthorizenetAcceptjs/checkout.twig';
    }

    /**
     * Get data attributes
     * 
     * @return string[]
     */
    protected function getDataAttributes()
    {
        $method = $this->getCart()->getPaymentMethod();

        return array(
            'data-public-key'   => $method->getSetting('public_key'),
            'data-api-login-id' => $method->getSetting('api_login_id'),
            'data-name'          => $this->getCart()->getProfile()
                ? $this->getCart()->getProfile()->getName()
                : '',
        );
    }
}

