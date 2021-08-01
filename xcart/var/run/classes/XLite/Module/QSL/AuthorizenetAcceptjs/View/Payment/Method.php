<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs\View\Payment;

/**
 * Payment method widget
 */
 class Method extends \XLite\Module\QSL\BraintreeVZ\View\Payment\Method implements \XLite\Base\IDecorator
{

    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->getPaymentMethod() && $this->getPaymentMethod()->getServiceName() == 'AuthorizenetAcceptjs') {
            $list[] = 'modules/QSL/AuthorizenetAcceptjs/config.css';
        }

        return $list;
    }
    
}

