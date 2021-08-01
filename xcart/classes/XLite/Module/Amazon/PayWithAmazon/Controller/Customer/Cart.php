<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Customer;

use XLite\Module\Amazon\PayWithAmazon\Model\Payment\Processor\PayWithAmazon;

/**
 * Checkout controller
 */
class Cart extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    public function isAmazonReturn()
    {
        return \XLite\Core\Request::getInstance()->amazon_return ?: false;
    }
}
