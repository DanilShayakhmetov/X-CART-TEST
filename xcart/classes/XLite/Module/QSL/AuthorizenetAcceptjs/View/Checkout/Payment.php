<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs\View\Checkout;

/**
 * Payment template
 */
abstract class Payment extends \XLite\View\Checkout\Payment implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/AuthorizenetAcceptjs/payment.css';
        $list = array_merge($list, $this->getWidget(array(), 'XLite\Module\QSL\AuthorizenetAcceptjs\View\CreditCard')->getCSSFiles());

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        /** @var \XLite\Model\Payment\Method $method */
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(array('service_name' => 'AuthorizenetAcceptjs'));

        if ($method && $method->isEnabled()) {
            $list[] = 'modules/QSL/AuthorizenetAcceptjs/payment.js';
            if ($method->getSetting('mode') == 'test') {
                $list[] = array(
                    'url' => 'https://jstest.authorize.net/v1/Accept.js',
                );

            } else {
                $list[] = array(
                    'url' => 'https://js.authorize.net/v1/Accept.js',
                );
            }
            $list = array_merge($list, $this->getWidget(array(), 'XLite\Module\QSL\AuthorizenetAcceptjs\View\CreditCard')->getJSFiles());
        }

        return $list;
    }

}
