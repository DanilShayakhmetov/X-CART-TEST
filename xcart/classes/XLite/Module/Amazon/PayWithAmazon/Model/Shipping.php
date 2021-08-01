<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Model;

/**
 * Common shipping method
 */
class Shipping extends \XLite\Model\Shipping implements \XLite\Base\IDecorator
{
    protected function shouldAllowLongCalculations()
    {
        return parent::shouldAllowLongCalculations()
            || (\XLite::getController()->isAJAX() && 'amazon_checkout' === \XLite\Core\Request::getInstance()->getAjaxRefererTarget());
    }
}
