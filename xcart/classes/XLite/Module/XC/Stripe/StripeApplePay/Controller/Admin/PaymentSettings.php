<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\StripeApplePay\Controller\Admin;

/**
 * Add parent method in background
 */
abstract class PaymentSettings extends \XLite\Controller\Admin\PaymentSettings implements \XLite\Base\IDecorator
{
    protected function doActionAdd()
    {
        $method = $this->getMethod();
        if ($method && $method->getServiceName() == 'StripeApplePay') {
            $parent_m = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Stripe']);
            if ($parent_m && !$parent_m->getAdded()) {
                $parent_m->setAdded(true);
            }
        }

        parent::doActionAdd();
    }
}
