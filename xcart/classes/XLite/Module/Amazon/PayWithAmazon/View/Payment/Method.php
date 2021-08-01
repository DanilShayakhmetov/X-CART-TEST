<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\Payment;

use XLite\Module\Amazon\PayWithAmazon\Main;

/**
 * Class Method
 */
class Method extends \XLite\View\Payment\Method
{
    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return Main::getMethod();
    }
}
