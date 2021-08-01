<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AuthorizeNet\View\Payment;


use XLite\Module\CDev\AuthorizeNet\Model\Payment\Processor\AuthorizeNetSIM;

/**
 * @inheritdoc
 */
 class Method extends \XLite\Module\QSL\AuthorizenetAcceptjs\View\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isAuthorizeNetSIM()) {
            $list[] = 'modules/CDev/AuthorizeNet/script.js';
        }

        return $list;
    }

    /**
     * @return bool
     */
    protected function isAuthorizeNetSIM()
    {
        return $this->getPaymentMethod()
            && $this->getPaymentMethod()->getProcessor()
            && $this->getPaymentMethod()->getProcessor() instanceof AuthorizeNetSIM;
    }
}